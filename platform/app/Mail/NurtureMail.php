<?php

namespace App\Mail;

use App\Models\EmailStep;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
        return new Envelope(
            subject: $this->personalize($this->step->subject),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nurture',
            with: [
                'lead' => $this->lead,
                'title' => $this->personalize($this->step->subject),
                'preheader' => $this->preheader(),
                'bodyHtml' => $this->bodyHtml(),
            ],
        );
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
