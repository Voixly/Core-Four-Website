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
                'preheader' => 'A note from Core Four Roofing in Tomball, TX',
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

    protected function bodyHtml(): string
    {
        $blocks = preg_split('/\n\s*\n/', trim($this->personalize($this->step->body))) ?: [];

        return collect($blocks)
            ->map(fn (string $block) => '<p style="margin:0 0 16px">'.nl2br(e($block)).'</p>')
            ->implode('');
    }
}
