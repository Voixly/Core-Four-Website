<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->lead->source === 'hiring'
            ? 'Job application — '.$this->lead->name
            : 'New '.$this->lead->type.' lead — '.$this->lead->name;

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-notification',
            with: [
                'title' => 'New Core Four lead',
                'preheader' => $this->lead->source === 'hiring'
                    ? ($this->lead->name ?: 'Applicant').' · '.($this->lead->need ?: 'application')
                    : ($this->lead->name ?: 'A new lead').' · '.($this->lead->type ?: 'roofing'),
                'ctaUrl' => url('/admin/leads/'.$this->lead->id),
                'ctaLabel' => 'Open this lead',
            ],
        );
    }
}
