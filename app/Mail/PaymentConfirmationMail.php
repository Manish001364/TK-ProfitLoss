<?php

namespace App\Mail;

use App\Models\PnL\PnlPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public PnlPayment $payment;
    public string $recipientType; // 'vendor' or 'organiser'

    /**
     * Create a new message instance.
     */
    public function __construct(PnlPayment $payment, string $recipientType = 'vendor')
    {
        $this->payment = $payment;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $expenseTitle = $this->payment->expense->title ?? 'Payment';
        $eventName = $this->payment->expense->event->name ?? 'Event';
        
        if ($this->recipientType === 'vendor') {
            $subject = "Payment Received - {$expenseTitle} ({$eventName})";
        } else {
            $subject = "Payment Confirmation - {$expenseTitle} ({$eventName})";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'pnl.emails.payment-confirmation',
            with: [
                'payment' => $this->payment,
                'recipientType' => $this->recipientType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
