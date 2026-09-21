<?php

namespace App\Mail;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewRecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->review->stars.'★ Review Shield hold — '.($this->review->name ?: 'a customer'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-recovery',
            with: [
                'review' => $this->review,
                'title' => 'Review Shield hold',
                'preheader' => 'A '.$this->review->stars.'-star private rating was held. Google and Yelp were not shown.',
                'ctaUrl' => url('/admin/reviews/'.$this->review->id),
                'ctaLabel' => 'Open recovery ticket',
            ],
        );
    }
}
