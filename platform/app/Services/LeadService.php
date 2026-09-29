<?php

namespace App\Services;

use App\Mail\LeadNotification;
use App\Models\EmailSequence;
use App\Models\EmailSend;
use App\Models\Lead;
use App\Models\User;
use Database\Seeders\EmailSequenceSeeder;
use Illuminate\Support\Facades\Mail;

class LeadService
{
    public function capture(array $data, ?User $actor = null): Lead
    {
        $email = strtolower(trim((string) ($data['email'] ?? '')));
        if ($email !== '' && ($data['source'] ?? 'website') !== 'hiring') {
            $prospect = Lead::query()
                ->where('source', 'prospect')
                ->whereRaw('lower(email) = ?', [$email])
                ->first();
            if ($prospect) {
                $prospect->fill([
                    'name' => $data['name'] ?? $prospect->name,
                    'phone' => $data['phone'] ?? $prospect->phone,
                    'zip' => $data['zip'] ?? $prospect->zip,
                    'city' => $data['city'] ?? $prospect->city,
                    'type' => $data['type'] ?? $prospect->type,
                    'need' => $data['need'] ?? $prospect->need,
                    'page_url' => $data['page_url'] ?? $prospect->page_url,
                    'notes' => $data['notes'] ?? $prospect->notes,
                ]);
                $prospect->save();

                return $this->promote($prospect, $actor, 'They used the website form.');
            }
        }

        $lead = Lead::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'zip' => $data['zip'] ?? null,
            'city' => $data['city'] ?? null,
            'type' => $data['type'] ?? 'residential',
            'need' => $data['need'] ?? null,
            'status' => 'new',
            'source' => $data['source'] ?? 'website',
            'page_url' => $data['page_url'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $lead->log($actor, 'created', 'Lead captured from '.$lead->source);
        $this->enroll($lead);
        $this->notifyOffice($lead);

        return $lead;
    }

    public function captureProspect(array $data): Lead
    {
        $this->ensureProspectSequences();
        $email = strtolower(trim((string) $data['email']));
        $lead = Lead::query()->whereRaw('lower(email) = ?', [$email])->first();
        $fields = [
            'name' => $data['name'],
            'city' => ($data['city'] ?? '') !== '' ? $data['city'] : 'Greater Houston',
            'type' => $data['type'],
            'need' => 'Prospect outreach',
            'page_url' => $data['page_url'] ?? null,
        ];
        if (($data['phone'] ?? '') !== '') {
            $fields['phone'] = $data['phone'];
        }
        if (array_key_exists('notes', $data) && $data['notes'] !== null && $data['notes'] !== '') {
            $fields['notes'] = $data['notes'];
        }

        $switched = false;
        if ($lead) {
            if ($lead->source !== 'prospect') {
                $lead->log(null, 'note', 'Prospect intake matched an existing '.$lead->source.' lead. Left on the current email flow.');

                return $lead;
            }

            $switched = $lead->type !== $data['type'];
            if ($switched) {
                EmailSend::query()
                    ->where('lead_id', $lead->id)
                    ->where('status', 'scheduled')
                    ->update(['status' => 'skipped']);
            }

            $lead->fill($fields);
            $lead->save();
            $lead->log(null, 'updated', 'Prospect updated from intake');
        } else {
            $lead = Lead::query()->create($fields + [
                'email' => $email,
                'status' => 'new',
                'source' => 'prospect',
            ]);
            $lead->log(null, 'created', 'Lead captured from prospect');
        }

        $this->enroll($lead);
        if ($switched) {
            $this->reactivateCurrentSequence($lead);
        }

        return $lead;
    }

    public function enroll(Lead $lead): void
    {
        $name = $this->sequenceName($lead);
        if (! $lead->email || $name === null) {
            return;
        }

        $sequence = EmailSequence::query()
            ->where('name', $name)
            ->where('is_active', true)
            ->first();

        if (! $sequence) {
            return;
        }

        foreach ($sequence->steps()->where('is_active', true)->get() as $step) {
            EmailSend::query()->firstOrCreate(
                ['email_step_id' => $step->id, 'lead_id' => $lead->id],
                [
                    'scheduled_at' => now()->addDays($step->delay_days),
                    'status' => 'scheduled',
                ]
            );
        }
    }

    private function ensureProspectSequences(): void
    {
        $ready = EmailSequence::query()->where('name', 'Residential prospect outreach')->exists()
            && EmailSequence::query()->where('name', 'Commercial prospect outreach')->exists();
        if ($ready) {
            return;
        }

        try {
            (new EmailSequenceSeeder)->run();
        } catch (\Throwable) {
            // The prospect is still saved if the email steps cannot be created.
        }
    }

    private function reactivateCurrentSequence(Lead $lead): void
    {
        $name = $this->sequenceName($lead);
        if ($name === null) {
            return;
        }

        $sequence = EmailSequence::query()->where('name', $name)->where('is_active', true)->first();
        if (! $sequence) {
            return;
        }

        foreach ($sequence->steps()->where('is_active', true)->get() as $step) {
            EmailSend::query()
                ->where('email_step_id', $step->id)
                ->where('lead_id', $lead->id)
                ->whereNull('sent_at')
                ->where('status', 'skipped')
                ->update([
                    'status' => 'scheduled',
                    'scheduled_at' => now()->addDays($step->delay_days),
                ]);
        }
    }

    private function sequenceName(Lead $lead): ?string
    {
        if ($lead->source === 'hiring' || ! $lead->email) {
            return null;
        }

        $commercial = $lead->type === 'commercial';

        if ($lead->source === 'prospect') {
            return $commercial ? 'Commercial prospect outreach' : 'Residential prospect outreach';
        }

        if ($lead->source === 'responded') {
            return null;
        }

        return $commercial ? 'Commercial 12-month nurture' : 'Residential 12-month nurture';
    }

    public function promote(Lead $lead, ?User $actor = null, string $reason = 'Marked as responded.'): Lead
    {
        if ($lead->source !== 'prospect') {
            return $lead;
        }

        $lead->update([
            'source' => 'responded',
            'status' => 'contacted',
        ]);
        EmailSend::query()
            ->where('lead_id', $lead->id)
            ->where('status', 'scheduled')
            ->update(['status' => 'skipped']);
        $lead->log($actor, 'updated', $reason.' Moved into Leads. Remaining prospect emails were stopped.');
        $this->notifyOffice($lead);

        return $lead;
    }

    protected function notifyOffice(Lead $lead): void
    {
        $raw = $lead->source === 'hiring'
            ? config('mail.hr_address')
            : config('mail.leads_address');
        $emails = array_values(array_filter(array_map('trim', explode(',', (string) $raw))));

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new LeadNotification($lead));
            } catch (\Throwable) {
                // Mail may not be configured on first boot.
            }
        }
    }
}
