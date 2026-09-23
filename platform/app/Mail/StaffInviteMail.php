<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $setPasswordUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Set your Core Four staff password',
        );
    }

    public function content(): Content
    {
        $first = explode(' ', trim((string) $this->user->name))[0] ?: 'there';

        return new Content(
            view: 'emails.staff-invite',
            with: [
                'first' => $first,
                'role' => $this->user->role,
                'title' => 'Set your password',
                'preheader' => 'You have a Core Four staff login. Set a password to open it.',
                'ctaUrl' => $this->setPasswordUrl,
                'ctaLabel' => 'Set your password',
            ],
        );
    }
}
