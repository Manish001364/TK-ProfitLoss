<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpense;
use App\Models\PnL\PnlExpenseCategory;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlPayment;
use App\Models\PnL\PnlVendor;
use App\Models\PnL\PnlSettings;
use App\Mail\InvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = PnlExpense::forUser($userId)->with(['event', 'category', 'vendor', 'payment']);

        // Filters
        if ($request->filled('event_id')) {
            $query->forEvent($request->event_id);
        }
        if ($request->filled('category_id')) {
            $query->forCategory($request->category_id);
        }
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'paid') {
                $query->paid();
            } elseif ($request->payment_status === 'pending') {
                $query->pending();
            }
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort', 'expense_date');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $expenses = $query->paginate(15)->withQueryString();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $categories = PnlExpenseCategory::forUser($userId)->active()->ordered()->get();

        return view('pnl.expenses.index', compact('expenses', 'events', 'categories'));
    }

    public function create(Request $request)
    {
        $userId = auth()->id();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $categories = PnlExpenseCategory::getAllForUser($userId);
        $vendors = PnlVendor::forUser($userId)->active()->orderBy('full_name')->get();
        
        $selectedEventId = $request->get('event_id');
        
        // Get user settings for default tax rate and currency
        $settings = PnlSettings::getOrCreate($userId);
        $defaultTaxRate = $settings->default_tax_rate;
        $currencies = PnlSettings::getCurrencies();
        $defaultCurrency = $settings->default_currency ?? 'GBP';
        
        // Generate next invoice number using new format: INV-YYYYMM-XXX
        $nextInvoiceNumber = $this->generateNextInvoiceNumber($userId);

        return view('pnl.expenses.create', compact(
            'events', 'categories', 'vendors', 'selectedEventId', 
            'defaultTaxRate', 'nextInvoiceNumber', 'settings', 'currencies', 'defaultCurrency'
        ));
    }

    /**
     * Generate next invoice number in format: INV-YYYYMM-XXX
     * E.g., INV-202501-001
     */
    private function generateNextInvoiceNumber($userId)
    {
        $yearMonth = now()->format('Ym');
        $prefix = 'INV';
        
        // Get the settings or create default
        $settings = PnlSettings::getOrCreate($userId);
        $prefix = $settings->invoice_prefix ?? 'INV';
        
        // Find the last invoice for this month to determine sequence
        $lastExpense = PnlExpense::where('user_id', $userId)
            ->whereNotNull('invoice_number')
            ->where('invoice_number', 'like', $prefix . '-' . $yearMonth . '-%')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastExpense && preg_match('/-(\d+)$/', $lastExpense->invoice_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = $settings->invoice_next_number ?? 1;
        }

        return $prefix . '-' . $yearMonth . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:pnl_events,id',
            'category_id' => 'required|exists:pnl_expense_categories,id',
            'vendor_id' => 'nullable|exists:pnl_vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:3', // Currency code (GBP, USD, EUR, etc.)
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'is_taxable' => 'boolean',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            // Payment fields
            'create_payment' => 'boolean',
            'payment_status' => 'nullable|in:pending,scheduled,paid',
            'scheduled_date' => 'nullable|date',
            'payment_method' => 'nullable|in:bank_transfer,cash,cheque,card,other',
            'reminder_enabled' => 'boolean',
            'reminder_days_before' => 'nullable|integer|min:1|max:30',
            // Notification settings
            'send_email_to_vendor' => 'boolean',
        ]);
        
        // Set currency - default to user's default currency if not provided
        if (empty($validated['currency'])) {
            $settings = PnlSettings::getOrCreate(auth()->id());
            $validated['currency'] = $settings->default_currency ?? 'GBP';
        }

        $userId = auth()->id();
        $validated['user_id'] = $userId;
        
        // Handle tax
        if (!$request->boolean('is_taxable')) {
            $validated['tax_amount'] = 0;
            $validated['tax_rate'] = 0;
            $validated['is_taxable'] = false;
        } else {
            $validated['is_taxable'] = true;
            $validated['tax_rate'] = $validated['tax_rate'] ?? 20;
            $validated['tax_amount'] = $validated['tax_amount'] ?? ($validated['amount'] * $validated['tax_rate'] / 100);
        }

        // Auto-generate invoice number if empty using new format
        if (empty($validated['invoice_number'])) {
            $validated['invoice_number'] = $this->generateNextInvoiceNumber($userId);
        }

        // Calculate total
        $validated['total_amount'] = $validated['amount'] + $validated['tax_amount'];

        $expense = PnlExpense::create($validated);

        // Create payment record if requested
        $sendEmailToVendor = $request->boolean('send_email_to_vendor', true);
        
        if ($request->boolean('create_payment', true)) {
            $payment = PnlPayment::create([
                'expense_id' => $expense->id,
                'vendor_id' => $validated['vendor_id'],
                'user_id' => $userId,
                'amount' => $expense->total_amount,
                'status' => $validated['payment_status'] ?? 'pending',
                'scheduled_date' => $validated['scheduled_date'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'reminder_enabled' => $request->boolean('reminder_enabled', true),
                'reminder_days_before' => $validated['reminder_days_before'] ?? 3,
                'send_email_to_vendor' => $sendEmailToVendor,
            ]);

            // Send email notification to vendor if enabled
            if ($sendEmailToVendor && $expense->vendor && $expense->vendor->email) {
                $this->sendPaymentNotification($expense, $payment, 'created');
            }
        }

        return redirect()
            ->route('pnl.expenses.show', $expense)
            ->with('success', 'Expense created successfully!');
    }

    public function show(PnlExpense $expense)
    {
        $this->authorize('view', $expense);

        $expense->load(['event', 'category', 'vendor', 'payment', 'attachments', 'auditLogs.user']);

        return view('pnl.expenses.show', compact('expense'));
    }

    public function edit(PnlExpense $expense)
    {
        $this->authorize('update', $expense);

        $userId = auth()->id();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $categories = PnlExpenseCategory::forUser($userId)->active()->ordered()->get();
        $vendors = PnlVendor::forUser($userId)->active()->orderBy('full_name')->get();
        
        // Get user settings
        $settings = PnlSettings::getOrCreate($userId);
        $defaultTaxRate = $settings->default_tax_rate;

        $expense->load('payment');

        return view('pnl.expenses.edit', compact('expense', 'events', 'categories', 'vendors', 'defaultTaxRate'));
    }

    public function update(Request $request, PnlExpense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'event_id' => 'required|exists:pnl_events,id',
            'category_id' => 'required|exists:pnl_expense_categories,id',
            'vendor_id' => 'nullable|exists:pnl_vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'is_taxable' => 'boolean',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
        ]);

        // Handle tax
        if (!$request->boolean('is_taxable')) {
            $validated['tax_amount'] = 0;
            $validated['tax_rate'] = 0;
            $validated['is_taxable'] = false;
        } else {
            $validated['is_taxable'] = true;
            $validated['tax_amount'] = $validated['tax_amount'] ?? 0;
        }

        // Calculate total
        $validated['total_amount'] = $validated['amount'] + $validated['tax_amount'];

        $expense->update($validated);

        // Update payment amount if exists
        if ($expense->payment) {
            $expense->payment->update([
                'amount' => $expense->total_amount,
                'vendor_id' => $validated['vendor_id'],
            ]);
        }

        return redirect()
            ->route('pnl.expenses.show', $expense)
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(PnlExpense $expense)
    {
        $this->authorize('delete', $expense);

        $eventId = $expense->event_id;
        $expense->delete();

        return redirect()
            ->route('pnl.events.show', $eventId)
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Generate PDF invoice for expense
     */
    public function generatePdf(PnlExpense $expense)
    {
        $this->authorize('view', $expense);

        $expense->load(['event', 'category', 'vendor', 'payment']);
        
        // Get user settings for company info
        $settings = PnlSettings::getOrCreate(auth()->id());

        $pdf = Pdf::loadView('pnl.exports.invoice-pdf', compact('expense', 'settings'));
        
        $filename = 'Invoice_' . ($expense->invoice_number ?? $expense->id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Send invoice email to vendor
     */
    public function sendInvoiceEmail(PnlExpense $expense)
    {
        $this->authorize('view', $expense);

        if (!$expense->vendor || !$expense->vendor->email) {
            return back()->with('error', 'Vendor email not available.');
        }

        $expense->load(['event', 'category', 'vendor', 'payment']);

        try {
            Mail::to($expense->vendor->email)->send(new InvoiceMail($expense));
            
            return back()->with('success', 'Invoice email sent to ' . $expense->vendor->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Send payment notification to vendor
     */
    protected function sendPaymentNotification(PnlExpense $expense, PnlPayment $payment, $action)
    {
        if (!$payment->send_email_to_vendor) {
            return;
        }

        if (!$expense->vendor || !$expense->vendor->email) {
            return;
        }

        // Check user settings for this action type
        $settings = PnlSettings::getOrCreate(auth()->id());
        
        $shouldSend = match($action) {
            'created' => $settings->send_email_on_payment_created,
            'paid' => $settings->send_email_on_payment_paid,
            'scheduled' => $settings->send_email_on_payment_scheduled,
            default => true,
        };

        if (!$shouldSend) {
            return;
        }

        try {
            Mail::to($expense->vendor->email)->send(new InvoiceMail($expense, $action));
        } catch (\Exception $e) {
            // Log error but don't throw - email failure shouldn't block the main action
            \Log::error('Failed to send payment notification: ' . $e->getMessage());
        }
    }
}
