<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ContactFormConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $formData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tack för ditt mail, här är vad du skickade till Kalibr',
            from: new Address(config('mail.from.address'), 'Kalibr')
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form',
            with: [
                'formData' => $this->formData,
            ],
        );
    }
}
