<?php

namespace App\Mail;

use App\Models\PnL\PnlPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PnlPayment $payment;

    public function __construct(PnlPayment $payment)
    {
        $this->payment = $payment->load(['expense.event', 'vendor']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Reminder - ' . $this->payment->expense->event->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'pnl.emails.payment-reminder',
            with: [
                'payment' => $this->payment,
                'event' => $this->payment->expense->event,
                'vendor' => $this->payment->vendor,
                'amount' => $this->payment->amount,
                'scheduledDate' => $this->payment->scheduled_date,
                'daysUntilDue' => $this->payment->days_until_due,
            ],
        );
    }
}
