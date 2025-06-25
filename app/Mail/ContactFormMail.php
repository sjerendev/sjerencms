<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $formData)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), $this->formData['name']),
            subject: 'Nytt kontaktformulär från ' . $this->formData['name'],
            replyTo: [
                new Address($this->formData['email'], $this->formData['name'])
            ]
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
