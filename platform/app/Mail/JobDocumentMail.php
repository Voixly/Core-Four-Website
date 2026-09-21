<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\JobDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobDocumentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Job $job, public JobDocument $document)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Customer uploaded a file on '.$this->job->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-document',
            with: [
                'job' => $this->job,
                'document' => $this->document,
                'title' => 'New job document',
                'preheader' => $this->document->original_name.' landed on '.$this->job->number.'.',
                'ctaUrl' => url('/admin/jobs/'.$this->job->id),
                'ctaLabel' => 'Open the job',
            ],
        );
    }
}
