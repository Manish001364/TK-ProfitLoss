<?php

namespace App\Mail;

use App\Models\PnL\PnlExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public PnlExpense $expense;
    public string $action;

    /**
     * Create a new message instance.
     */
    public function __construct(PnlExpense $expense, string $action = 'invoice')
    {
        $this->expense = $expense;
        $this->action = $action;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->action) {
            'created' => 'New Invoice: ' . $this->expense->invoice_number,
            'paid' => 'Payment Confirmed: ' . $this->expense->invoice_number,
            'scheduled' => 'Payment Scheduled: ' . $this->expense->invoice_number,
            default => 'Invoice: ' . $this->expense->invoice_number,
        };

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
            view: 'pnl.emails.invoice-email',
            with: [
                'expense' => $this->expense,
                'action' => $this->action,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Generate PDF attachment
        $this->expense->load(['event', 'category', 'vendor', 'payment']);
        
        $pdf = Pdf::loadView('pnl.exports.invoice-pdf', ['expense' => $this->expense]);
        $filename = 'Invoice_' . ($this->expense->invoice_number ?? $this->expense->id) . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
