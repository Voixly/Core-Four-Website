<?php

namespace App\Mail;

use App\Models\CustomerInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public CustomerInvite $invite)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Track your Core Four roofing job',
        );
    }

    public function content(): Content
    {
        $first = explode(' ', trim((string) $this->invite->name))[0] ?: 'there';

        return new Content(
            view: 'emails.job-invite',
            with: [
                'invite' => $this->invite,
                'first' => $first,
                'title' => 'Track your job',
                'preheader' => 'Set a password and see where your Core Four job stands.',
                'ctaUrl' => $this->invite->url(),
                'ctaLabel' => 'Open your job portal',
            ],
        );
    }
}
