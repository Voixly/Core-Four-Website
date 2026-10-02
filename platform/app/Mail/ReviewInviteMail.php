<?php

namespace App\Mail;

use App\Models\EmailStep;
use App\Models\Review;
use App\Support\SiteSeo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Review $review, public ?EmailStep $step = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->personalize($this->step?->subject ?: 'How did Core Four do on your roof?'),
        );
    }

    public function content(): Content
    {
        $first = $this->firstName();
        $subject = $this->personalize($this->step?->subject ?: 'How did we do?');

        return new Content(
            view: 'emails.review-invite',
            with: [
                'review' => $this->review,
                'first' => $first,
                'title' => $subject,
                'preheader' => 'A 10-second private rating for Core Four Roofing — not a public review yet.',
                'bodyHtml' => $this->step ? $this->bodyHtml() : null,
                'ctaUrl' => SiteSeo::url('/reviews/'.$this->review->token.'/'),
                'ctaLabel' => 'Rate this job',
            ],
        );
    }

    public function subjectLine(): string
    {
        return $this->personalize($this->step?->subject ?: 'How did Core Four do on your roof?');
    }

    private function firstName(): string
    {
        return explode(' ', trim((string) $this->review->name))[0] ?: 'there';
    }

    private function personalize(string $text): string
    {
        return strtr($text, [
            '{{first_name}}' => $this->firstName(),
            '{{name}}' => trim((string) $this->review->name) ?: 'there',
            '{{city}}' => trim((string) $this->review->city) ?: 'your area',
            '{{job}}' => trim((string) $this->review->job) ?: 'your roof',
        ]);
    }

    private function bodyHtml(): string
    {
        $blocks = preg_split('/\n\s*\n/', trim($this->personalize((string) $this->step->body))) ?: [];

        return collect($blocks)
            ->map(fn (string $block) => '<p style="margin:0 0 16px">'.nl2br(e($block)).'</p>')
            ->implode('');
    }
}
