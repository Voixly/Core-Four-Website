<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How did Core Four do on your roof?',
        );
    }

    public function content(): Content
    {
        $first = explode(' ', trim((string) $this->review->name))[0] ?: 'there';

        return new Content(
            view: 'emails.review-invite',
            with: [
                'review' => $this->review,
                'first' => $first,
                'title' => 'How did we do?',
                'preheader' => 'A 10-second private rating for Core Four Roofing — not a public review yet.',
                'ctaUrl' => $this->review->publicUrl(),
                'ctaLabel' => 'Rate this job',
            ],
        );
    }
}
