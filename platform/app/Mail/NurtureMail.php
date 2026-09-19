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
                'bodyHtml' => nl2br(e($this->personalize($this->step->body))),
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
}
