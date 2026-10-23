<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Subject, body and button are placeholders, see TODO-INNEHALL.md. */
class LoginLink extends Mailable
{
    public function __construct(public string $url) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('shop.mailSubject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.login-link');
    }
}
