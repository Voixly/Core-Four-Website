<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class StaffReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $text,
        public string $subjectLine,
        public string $staffName,
        public array $replyAddresses,
        public ?string $inReplyTo,
    ) {
    }

    public function envelope(): Envelope
    {
        $replyTo = [];
        foreach ($this->replyAddresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                $replyTo[] = new Address($address, 'Core Four Roofing');
            }
        }

        return new Envelope(
            subject: $this->subjectLine,
            replyTo: $replyTo,
            using: [
                function (Email $message) {
                    $id = trim((string) $this->inReplyTo);
                    if ($id === '') {
                        return;
                    }
                    if (! str_starts_with($id, '<')) {
                        $id = '<'.$id.'>';
                    }
                    $message->getHeaders()->addTextHeader('In-Reply-To', $id);
                    $message->getHeaders()->addTextHeader('References', $id);
                },
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.staff-reply',
            text: 'emails.staff-reply-text',
            with: [
                'text' => $this->text,
                'staffName' => $this->staffName,
                'phone' => '(281) 541-0027',
            ],
        );
    }
}
