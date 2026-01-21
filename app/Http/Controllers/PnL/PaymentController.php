<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlPayment;
use App\Models\PnL\PnlAuditLog;
use App\Mail\PaymentReminderMail;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = PnlPayment::forUser($userId)->with(['expense.event', 'vendor']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }
        if ($request->filled('overdue') && $request->overdue) {
            $query->overdue();
        }
        if ($request->filled('upcoming')) {
            $query->upcoming($request->upcoming);
        }

        // Sorting
        $sortBy = $request->get('sort', 'scheduled_date');
        $sortDir = $request->get('dir', 'asc');
        
        if ($sortBy === 'scheduled_date') {
            $query->orderByRaw('CASE WHEN scheduled_date IS NULL THEN 1 ELSE 0 END')
                  ->orderBy('scheduled_date', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $payments = $query->paginate(15)->withQueryString();
        $statuses = PnlPayment::getStatuses();

        return view('pnl.payments.index', compact('payments', 'statuses'));
    }

    public function show(PnlPayment $payment)
    {
        $this->authorize('view', $payment);

        $payment->load(['expense.event', 'expense.category', 'vendor', 'attachments', 'auditLogs.user']);

        return view('pnl.payments.show', compact('payment'));
    }

    public function edit(PnlPayment $payment)
    {
        $this->authorize('update', $payment);

        $payment->load(['expense', 'vendor']);
        $statuses = PnlPayment::getStatuses();
        $paymentMethods = PnlPayment::getPaymentMethods();

        return view('pnl.payments.edit', compact('payment', 'statuses', 'paymentMethods'));
    }

    public function update(Request $request, PnlPayment $payment)
    {
        $this->authorize('update', $payment);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(PnlPayment::getStatuses()))],
            'scheduled_date' => 'nullable|date',
            'actual_paid_date' => 'nullable|date',
            'payment_method' => ['nullable', Rule::in(array_keys(PnlPayment::getPaymentMethods()))],
            'transaction_reference' => 'nullable|string|max:255',
            'internal_notes' => 'nullable|string',
            'reminder_enabled' => 'boolean',
            'reminder_days_before' => 'nullable|integer|min:1|max:30',
            'reminder_on_due_date' => 'boolean',
            'send_vendor_email' => 'boolean',
            'send_organiser_email' => 'boolean',
        ]);

        $oldStatus = $payment->status;
        $validated['reminder_enabled'] = $request->boolean('reminder_enabled', true);
        $validated['reminder_on_due_date'] = $request->boolean('reminder_on_due_date', true);

        // Auto-set actual_paid_date when marked as paid
        if ($validated['status'] === 'paid' && !$validated['actual_paid_date']) {
            $validated['actual_paid_date'] = now()->toDateString();
        }

        // Remove email options from validated data before update
        unset($validated['send_vendor_email'], $validated['send_organiser_email']);

        $payment->update($validated);

        // Log status change
        if ($oldStatus !== $request->input('status')) {
            $payment->logStatusChange($oldStatus, $request->input('status'), $request->input('status_change_reason'));
            
            // Send confirmation emails if status changed to 'paid'
            if ($request->input('status') === 'paid') {
                $payment->load(['expense.event', 'vendor']);
                $emailsSent = [];

                // Send email to vendor
                $sendVendorEmail = $request->boolean('send_vendor_email', true);
                if ($sendVendorEmail && $payment->vendor && $payment->vendor->email) {
                    try {
                        Mail::to($payment->vendor->email)->send(new PaymentConfirmationMail($payment, 'vendor'));
                        $emailsSent[] = 'vendor';
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment confirmation to vendor: ' . $e->getMessage());
                    }
                }

                // Send email to organiser
                $sendOrganiserEmail = $request->boolean('send_organiser_email', true);
                $user = auth()->user();
                if ($sendOrganiserEmail && $user && $user->email) {
                    try {
                        Mail::to($user->email)->send(new PaymentConfirmationMail($payment, 'organiser'));
                        $emailsSent[] = 'organiser';
                    } catch (\Exception $e) {
                        Log::error('Failed to send payment confirmation to organiser: ' . $e->getMessage());
                    }
                }

                if (!empty($emailsSent)) {
                    return redirect()
                        ->route('pnl.payments.show', $payment)
                        ->with('success', 'Payment updated and marked as paid! Confirmation emails sent to: ' . implode(', ', $emailsSent));
                }
            }
        }

        return redirect()
            ->route('pnl.payments.show', $payment)
            ->with('success', 'Payment updated successfully!');
    }

    public function markAsPaid(Request $request, PnlPayment $payment)
    {
        $this->authorize('update', $payment);

        $validated = $request->validate([
            'actual_paid_date' => 'nullable|date',
            'payment_method' => ['nullable', Rule::in(array_keys(PnlPayment::getPaymentMethods()))],
            'transaction_reference' => 'nullable|string|max:255',
            'send_vendor_email' => 'boolean',
            'send_organiser_email' => 'boolean',
        ]);

        $oldStatus = $payment->status;

        $payment->update([
            'status' => 'paid',
            'actual_paid_date' => $validated['actual_paid_date'] ?? now()->toDateString(),
            'payment_method' => $validated['payment_method'] ?? $payment->payment_method,
            'transaction_reference' => $validated['transaction_reference'] ?? $payment->transaction_reference,
        ]);

        $payment->logStatusChange($oldStatus, 'paid', 'Marked as paid');

        // Load relationships for email
        $payment->load(['expense.event', 'vendor']);

        $emailsSent = [];

        // Send email to vendor if they have an email
        $sendVendorEmail = $request->boolean('send_vendor_email', true);
        if ($sendVendorEmail && $payment->vendor && $payment->vendor->email) {
            try {
                Mail::to($payment->vendor->email)->send(new PaymentConfirmationMail($payment, 'vendor'));
                $emailsSent[] = 'vendor (' . $payment->vendor->email . ')';
            } catch (\Exception $e) {
                Log::error('Failed to send payment confirmation to vendor: ' . $e->getMessage());
            }
        }

        // Send email to organiser (logged-in user)
        $sendOrganiserEmail = $request->boolean('send_organiser_email', true);
        $user = auth()->user();
        if ($sendOrganiserEmail && $user && $user->email) {
            try {
                Mail::to($user->email)->send(new PaymentConfirmationMail($payment, 'organiser'));
                $emailsSent[] = 'organiser (' . $user->email . ')';
            } catch (\Exception $e) {
                Log::error('Failed to send payment confirmation to organiser: ' . $e->getMessage());
            }
        }

        $successMessage = 'Payment marked as paid!';
        if (!empty($emailsSent)) {
            $successMessage .= ' Confirmation emails sent to: ' . implode(', ', $emailsSent);
        }

        return redirect()
            ->back()
            ->with('success', $successMessage);
    }

    public function sendReminder(PnlPayment $payment)
    {
        $this->authorize('update', $payment);

        if (!$payment->vendor || !$payment->vendor->email) {
            return back()->with('error', 'No vendor email address found.');
        }

        Mail::to($payment->vendor->email)->send(new PaymentReminderMail($payment));

        $payment->update([
            'last_reminder_sent_at' => now(),
            'reminder_count' => $payment->reminder_count + 1,
        ]);

        return back()->with('success', 'Reminder email sent successfully!');
    }

    public function upcoming(Request $request)
    {
        $userId = auth()->id();
        $days = $request->get('days', 30);

        $payments = PnlPayment::forUser($userId)
            ->upcoming($days)
            ->with(['expense.event', 'vendor'])
            ->orderBy('scheduled_date')
            ->get()
            ->groupBy(function ($payment) {
                if ($payment->days_until_due <= 7) return 'next_7_days';
                if ($payment->days_until_due <= 14) return 'next_14_days';
                return 'next_30_days';
            });

        $summary = [
            'next_7_days' => $payments->get('next_7_days', collect())->sum('amount'),
            'next_14_days' => $payments->get('next_14_days', collect())->sum('amount'),
            'next_30_days' => $payments->get('next_30_days', collect())->sum('amount'),
        ];

        return view('pnl.payments.upcoming', compact('payments', 'summary'));
    }

    public function overdue()
    {
        $userId = auth()->id();

        $payments = PnlPayment::forUser($userId)
            ->overdue()
            ->with(['expense.event', 'vendor'])
            ->orderBy('scheduled_date')
            ->get();

        $totalOverdue = $payments->sum('amount');

        return view('pnl.payments.overdue', compact('payments', 'totalOverdue'));
    }
}
