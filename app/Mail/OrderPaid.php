<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** The receipt. $loginUrl is null for admins, who never get a login link. Text is placeholder, see TODO-INNEHALL.md. */
class OrderPaid extends Mailable
{
    public function __construct(public Order $order, public ?string $loginUrl) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('shop.receiptSubject', ['number' => $this->order->number()]));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.order-paid');
    }
}
