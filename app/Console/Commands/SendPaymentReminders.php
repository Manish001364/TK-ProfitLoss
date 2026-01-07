<?php

namespace App\Console\Commands;

use App\Models\PnL\PnlPayment;
use App\Mail\PaymentReminderMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminders extends Command
{
    protected $signature = 'pnl:send-payment-reminders';
    protected $description = 'Send payment reminder emails for scheduled payments';

    public function handle(): int
    {
        $this->info('Checking for payments that need reminders...');

        $payments = PnlPayment::needsReminder()
            ->with(['expense.event', 'vendor'])
            ->get();

        $sentCount = 0;

        foreach ($payments as $payment) {
            if (!$payment->should_send_reminder) {
                continue;
            }

            if (!$payment->vendor || !$payment->vendor->email) {
                $this->warn("Skipping payment {$payment->id}: No vendor email");
                continue;
            }

            try {
                Mail::to($payment->vendor->email)->send(new PaymentReminderMail($payment));

                $payment->update([
                    'last_reminder_sent_at' => now(),
                    'reminder_count' => $payment->reminder_count + 1,
                ]);

                $this->info("Sent reminder for payment {$payment->id} to {$payment->vendor->email}");
                $sentCount++;
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for payment {$payment->id}: {$e->getMessage()}");
            }
        }

        $this->info("Completed! Sent {$sentCount} reminder(s).");

        return Command::SUCCESS;
    }
}
