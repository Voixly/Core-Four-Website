<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobStageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job)
    {
    }

    public function envelope(): Envelope
    {
        $label = $this->job->stage?->customer_label ?: $this->job->stage?->name;

        return new Envelope(
            subject: 'Update on job '.$this->job->number.($label ? ' — '.$label : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-stage',
            with: [
                'job' => $this->job,
                'title' => 'Job update',
                'preheader' => $this->job->stage?->customer_label ?: 'Your Core Four job moved forward.',
                'ctaUrl' => url('/account/jobs/'.$this->job->id.'/'),
                'ctaLabel' => 'View your job',
            ],
        );
    }
}
