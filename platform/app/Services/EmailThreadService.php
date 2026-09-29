<?php

namespace App\Services;

use App\Mail\StaffReplyMail;
use App\Models\Conversation;
use App\Support\LeadTypeColumn;
use App\Models\EmailSend;
use App\Models\Lead;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Resend;
use RuntimeException;

class EmailThreadService
{
    public function __construct(private LeadService $leads)
    {
    }

    public function ingestFromResend(array $event): void
    {
        $emailId = trim((string) ($event['email_id'] ?? ''));
        if ($emailId === '' || Message::query()->where('external_id', $emailId)->exists()) {
            return;
        }

        $key = (string) config('services.resend.key');
        if ($key === '') {
            throw new RuntimeException('Resend key is missing.');
        }

        $fetched = Resend::client($key)->emails->receiving->get($emailId)->toArray();
        $headers = $this->headerMap($fetched['headers'] ?? []);
        $from = $this->parseMailbox((string) ($headers['from'] ?? $event['from'] ?? $fetched['from'] ?? ''));
        if ($from['email'] === '') {
            $from = $this->parseMailbox((string) ($event['from'] ?? ''));
        }

        $addresses = array_merge(
            $this->addressList($event['to'] ?? []),
            $this->addressList($event['cc'] ?? []),
            $this->addressList($event['bcc'] ?? []),
            $this->addressList($event['received_for'] ?? []),
            $this->addressList($fetched['to'] ?? []),
            $this->addressList($fetched['cc'] ?? []),
            $this->addressList($fetched['reply_to'] ?? []),
        );

        $attachments = [];
        foreach ($event['attachments'] ?? $fetched['attachments'] ?? [] as $attachment) {
            $name = is_array($attachment) ? (string) ($attachment['filename'] ?? '') : '';
            if ($name !== '') {
                $attachments[] = $name;
            }
        }

        $this->store([
            'email_id' => $emailId,
            'from_email' => $from['email'],
            'from_name' => $from['name'],
            'addresses' => $addresses,
            'subject' => (string) ($fetched['subject'] ?? $event['subject'] ?? ''),
            'text' => (string) ($fetched['text'] ?? ''),
            'html' => (string) ($fetched['html'] ?? ''),
            'message_id' => $this->firstMessageId((string) ($headers['message-id'] ?? $event['message_id'] ?? '')),
            'references' => trim((string) ($headers['in-reply-to'] ?? '').' '.(string) ($headers['references'] ?? '')),
            'auto_submitted' => strtolower((string) ($headers['auto-submitted'] ?? '')),
            'attachments' => $attachments,
        ]);
    }

    public function store(array $mail): void
    {
        $emailId = trim((string) ($mail['email_id'] ?? ''));
        $from = strtolower(trim((string) ($mail['from_email'] ?? '')));
        if ($from === '' || ! filter_var($from, FILTER_VALIDATE_EMAIL) || $this->isOurAddress($from)) {
            return;
        }

        $auto = strtolower((string) ($mail['auto_submitted'] ?? ''));
        if ($auto !== '' && $auto !== 'no') {
            return;
        }

        if ($emailId !== '' && Message::query()->where('external_id', $emailId)->exists()) {
            return;
        }

        $messageId = $this->firstMessageId((string) ($mail['message_id'] ?? ''));
        if ($messageId !== '' && Message::query()->where('message_id', $messageId)->exists()) {
            return;
        }

        $lead = $this->matchLead($mail, $from);
        $created = false;
        if (! $lead) {
            $lead = $this->createLead($from, (string) ($mail['from_name'] ?? ''), (string) ($mail['subject'] ?? ''), (string) ($mail['text'] ?? ''));
            $created = true;
        }

        $this->markReplied($lead);
        if ($created) {
            $this->leads->notifyOffice($lead);
        }

        $conversation = $this->conversationFor($lead, (string) ($mail['subject'] ?? ''));
        $body = $this->visibleText((string) ($mail['text'] ?? ''), (string) ($mail['html'] ?? ''));
        $files = array_values(array_filter($mail['attachments'] ?? []));
        if ($files) {
            $body = trim($body."\n\nAttached: ".implode(', ', $files));
        }
        if ($body === '') {
            $body = 'They replied, and the message had no text.';
        }

        try {
            $conversation->messages()->create([
                'sender' => 'visitor',
                'body' => mb_substr($body, 0, 8000),
                'external_id' => $emailId !== '' ? $emailId : null,
                'message_id' => $messageId !== '' ? $messageId : null,
            ]);
        } catch (QueryException $e) {
            if ($this->isDuplicate($e)) {
                return;
            }
            throw $e;
        }

        $conversation->forceFill([
            'status' => 'open',
            'awaiting_staff' => true,
            'last_message_at' => now(),
            'name' => $lead->name,
            'email' => $lead->email,
        ])->save();

        $snippet = preg_replace('/\s+/', ' ', $body) ?? $body;
        $lead->log(null, 'email', 'Email reply: '.mb_substr(trim((string) $snippet), 0, 180));
    }

    public function sendStaffReply(Conversation $conversation, User $user, string $body): void
    {
        $email = strtolower(trim((string) $conversation->email));
        if ($conversation->channel !== 'email' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('This thread has no email address.');
        }

        $lead = $conversation->lead;
        $replyTo = [];
        if ($lead) {
            $inbound = $lead->replyAddress();
            if ($inbound) {
                $replyTo[] = $inbound;
            }
        }
        $from = (string) config('mail.from.address');
        if ($from !== '' && ! in_array(strtolower($from), array_map('strtolower', $replyTo), true)) {
            $replyTo[] = $from;
        }

        $previous = $conversation->messages()
            ->where('sender', 'visitor')
            ->whereNotNull('message_id')
            ->latest('id')
            ->value('message_id');

        $subject = $this->replySubject((string) $conversation->subject);
        $sent = Mail::to($email)->send(new StaffReplyMail(
            $body,
            $subject,
            $user->name,
            $replyTo,
            $previous ? (string) $previous : null,
        ));

        $resendId = null;
        $header = $sent?->getOriginalMessage()->getHeaders()->get('X-Resend-Email-ID');
        if ($header) {
            $resendId = $header->getBodyAsString();
        }

        $conversation->messages()->create([
            'sender' => 'staff',
            'user_id' => $user->id,
            'body' => $body,
            'external_id' => $resendId !== '' ? $resendId : null,
        ]);
        $conversation->update([
            'assigned_to' => $user->id,
            'status' => 'open',
            'awaiting_staff' => false,
            'last_message_at' => now(),
            'subject' => $conversation->subject ?: $subject,
        ]);
        $lead?->log($user, 'email', 'Replied by email: '.mb_substr(preg_replace('/\s+/', ' ', $body) ?? $body, 0, 180));
    }

    private function matchLead(array $mail, string $from): ?Lead
    {
        $token = $this->tokenFrom($mail['addresses'] ?? []);
        if ($token) {
            $lead = Lead::query()->where('reply_token', $token)->first();
            if ($lead) {
                return $lead;
            }
        }

        $ids = $this->messageIds((string) ($mail['references'] ?? ''));
        if ($ids) {
            $message = Message::query()->whereIn('message_id', $ids)->latest('id')->first();
            if ($message?->conversation?->lead) {
                return $message->conversation->lead;
            }
        }

        return Lead::query()->whereRaw('lower(email) = ?', [$from])->latest('id')->first();
    }

    private function createLead(string $email, string $name, string $subject, string $text): Lead
    {
        $blob = strtolower($subject.' '.$text);
        $type = str_contains($blob, 'coating')
            ? 'coatings'
            : ((str_contains($blob, 'commercial') || str_contains($blob, 'building')) ? 'commercial' : 'residential');
        if ($type === 'coatings') {
            LeadTypeColumn::ensure();
        }
        $display = trim($name) !== '' ? trim($name) : ucfirst(strtok($email, '@') ?: 'Email');

        $lead = Lead::query()->create([
            'name' => mb_substr($display, 0, 120),
            'email' => $email,
            'type' => $type,
            'need' => 'Email reply',
            'status' => 'contacted',
            'source' => 'email',
        ]);
        $lead->log(null, 'created', 'Lead captured from an email reply');

        return $lead;
    }

    private function markReplied(Lead $lead): void
    {
        if ($lead->source === 'prospect') {
            $this->leads->promote($lead, null, 'They replied by email.');

            return;
        }

        EmailSend::query()
            ->where('lead_id', $lead->id)
            ->where('status', 'scheduled')
            ->update(['status' => 'skipped']);

        if ($lead->status === 'new') {
            $lead->status = 'contacted';
            $lead->save();
        }
    }

    private function conversationFor(Lead $lead, string $subject): Conversation
    {
        $lead->ensureReplyToken();
        $conversation = Conversation::query()
            ->where('channel', 'email')
            ->where(function ($query) use ($lead) {
                $query->where('lead_id', $lead->id);
                if ($lead->email) {
                    $query->orWhereRaw('lower(email) = ?', [strtolower($lead->email)]);
                }
            })
            ->latest('id')
            ->first();

        $baseSubject = $this->baseSubject($subject);
        $audience = $lead->type === 'residential' ? 'residential' : 'commercial';

        if (! $conversation) {
            return Conversation::query()->create([
                'visitor_token' => 'email-'.bin2hex(random_bytes(12)),
                'channel' => 'email',
                'name' => $lead->name,
                'email' => $lead->email,
                'audience' => $audience,
                'status' => 'open',
                'lead_id' => $lead->id,
                'reply_token' => $lead->reply_token,
                'subject' => $baseSubject !== '' ? $baseSubject : 'Core Four Roofing',
                'awaiting_staff' => true,
                'last_message_at' => now(),
            ]);
        }

        if ($baseSubject !== '' && ! $conversation->subject) {
            $conversation->subject = $baseSubject;
        }
        $conversation->reply_token = $lead->reply_token;
        $conversation->lead_id = $lead->id;
        $conversation->save();

        return $conversation;
    }

    private function visibleText(string $text, string $html): string
    {
        $text = trim($text);
        if ($text === '' && $html !== '') {
            $html = preg_replace('/<style\b[^>]*>.*?<\/style>/is', ' ', $html) ?? $html;
            $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', ' ', $html) ?? $html;
            $html = preg_replace('/<(br|\/p|\/div|\/tr|\/li)\b[^>]*>/i', "\n", $html) ?? $html;
            $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = preg_split('/\n/', $text) ?: [];
        $kept = [];
        foreach ($lines as $line) {
            $trim = ltrim($line);
            if (preg_match('/^on .+ wrote:\s*$/i', $trim)) {
                break;
            }
            if (preg_match('/^-{2,}\s*original message\s*-{2,}\s*$/i', $trim)) {
                break;
            }
            if (preg_match('/^from:\s.+/i', $trim) && count($kept) > 0) {
                break;
            }
            if (str_starts_with($trim, '>')) {
                continue;
            }
            $kept[] = rtrim($line);
        }

        $clean = trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $kept)) ?? '');
        if ($clean === '') {
            $clean = trim(mb_substr($text, 0, 1200));
        }

        return $clean;
    }

    private function tokenFrom(array $addresses): ?string
    {
        foreach ($addresses as $address) {
            if (preg_match('/r\+([a-f0-9]{16})@/i', (string) $address, $match)) {
                return strtolower($match[1]);
            }
        }

        return null;
    }

    private function addressList(mixed $value): array
    {
        if (is_string($value)) {
            return [$value];
        }
        if (! is_array($value)) {
            return [];
        }

        $addresses = [];
        foreach ($value as $item) {
            if (is_string($item)) {
                $addresses[] = $item;
            } elseif (is_array($item)) {
                $addresses[] = (string) ($item['email'] ?? $item['address'] ?? '');
            }
        }

        return $addresses;
    }

    private function headerMap(mixed $headers): array
    {
        if (! is_array($headers)) {
            return [];
        }

        $map = [];
        if (array_is_list($headers)) {
            foreach ($headers as $header) {
                if (! is_array($header)) {
                    continue;
                }
                $name = strtolower((string) ($header['name'] ?? ''));
                if ($name !== '') {
                    $map[$name] = (string) ($header['value'] ?? '');
                }
            }

            return $map;
        }

        foreach ($headers as $name => $value) {
            $map[strtolower((string) $name)] = is_array($value) ? implode(' ', $value) : (string) $value;
        }

        return $map;
    }

    private function parseMailbox(string $value): array
    {
        $value = trim($value);
        if (preg_match('/^(.*)<([^>]+)>$/', $value, $match)) {
            return [
                'name' => trim($match[1], " \t\"'"),
                'email' => strtolower(trim($match[2])),
            ];
        }

        return ['name' => '', 'email' => strtolower($value)];
    }

    private function messageIds(string $value): array
    {
        preg_match_all('/<[^<>\s]+>/', $value, $matches);

        return array_values(array_unique($matches[0] ?? []));
    }

    private function firstMessageId(string $value): string
    {
        $ids = $this->messageIds($value);
        if ($ids) {
            return $ids[0];
        }

        $value = trim($value);

        return str_contains($value, '@') ? '<'.trim($value, '<>').'>' : '';
    }

    private function baseSubject(string $subject): string
    {
        $subject = trim(preg_replace('/^((re|fw|fwd)\s*:\s*)+/i', '', trim($subject)) ?? '');

        return mb_substr($subject, 0, 180);
    }

    private function replySubject(string $subject): string
    {
        $base = $this->baseSubject($subject);

        return $base === '' ? 'Core Four Roofing' : 'Re: '.$base;
    }

    private function isOurAddress(string $email): bool
    {
        $email = strtolower($email);
        $domain = strtolower(trim((string) config('mail.inbound_domain')));
        if ($domain !== '' && str_ends_with($email, '@'.$domain)) {
            return true;
        }

        $blocked = array_map('strtolower', array_filter([
            (string) config('mail.from.address'),
            (string) config('mail.hr_address'),
            (string) config('mail.leads_address'),
        ]));

        return in_array($email, $blocked, true)
            || str_starts_with($email, 'mailer-daemon@')
            || str_starts_with($email, 'postmaster@');
    }

    private function isDuplicate(QueryException $e): bool
    {
        $message = $e->getMessage();

        return str_contains($message, '23000') || str_contains($message, '1062') || str_contains($message, 'UNIQUE');
    }
}
