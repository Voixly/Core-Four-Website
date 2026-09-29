<?php

namespace App\Mail;

use App\Models\EmailStep;
use App\Models\Lead;
use App\Support\SiteSeo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NurtureMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EmailStep $step, public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        $replyTo = [new Address((string) config('mail.from.address'), (string) config('mail.from.name'))];
        $inbound = $this->lead->replyAddress();
        if ($inbound && strtolower($inbound) !== strtolower((string) config('mail.from.address'))) {
            array_unshift($replyTo, new Address($inbound, 'Core Four Roofing'));
        }

        return new Envelope(
            subject: $this->personalize($this->step->subject),
            replyTo: $replyTo,
        );
    }

    public function content(): Content
    {
        $this->step->loadMissing('sequence');
        [$ctaLabel, $ctaUrl] = $this->cta();

        return new Content(
            view: 'emails.nurture',
            with: [
                'lead' => $this->lead,
                'title' => $this->personalize($this->step->subject),
                'preheader' => $this->preheader(),
                'bodyHtml' => $this->bodyHtml(),
                'ctaLabel' => $ctaLabel,
                'ctaUrl' => $ctaUrl,
            ],
        );
    }

    protected function cta(): array
    {
        $name = (string) ($this->step->sequence?->name ?? '');
        $contact = SiteSeo::url('/contact-core-four-roofing/');

        if (str_contains($name, 'coatings')) {
            return ['Get a coating quote', $contact];
        }

        if (str_contains($name, 'Commercial')) {
            return ['Request a roof survey', $contact];
        }

        return ['Get a free inspection', $contact];
    }

    protected function personalize(string $text): string
    {
        $first = explode(' ', trim($this->lead->name))[0] ?? 'there';

        return strtr($text, [
            '{{first_name}}' => $first,
            '{{name}}' => $this->lead->name,
            '{{city}}' => $this->lead->city ?: 'your area',
        ]);
    }

    protected function preheader(): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $this->personalize($this->step->body)) ?? '');
        $text = preg_replace('/^(Hi|Hello)\s+[^,]+,\s*/i', '', $text) ?? $text;

        return mb_substr(trim($text), 0, 110);
    }

    protected function bodyHtml(): string
    {
        $blocks = preg_split('/\n\s*\n/', trim($this->personalize($this->step->body))) ?: [];

        return collect($blocks)
            ->map(function (string $block) {
                $html = nl2br(e($block));
                $html = preg_replace(
                    '#(https://[^\s<]+)#',
                    '<a href="$1" style="color:#0f2418">$1</a>',
                    $html
                ) ?? $html;
                $html = str_replace(
                    '(281) 541-0027',
                    '<a href="tel:2815410027" style="color:#0f2418">(281) 541-0027</a>',
                    $html
                );

                return '<p style="margin:0 0 16px">'.$html.'</p>';
            })
            ->implode('');
    }
}
