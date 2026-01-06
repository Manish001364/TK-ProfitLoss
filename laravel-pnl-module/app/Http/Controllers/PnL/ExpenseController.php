<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlExpense;
use App\Models\PnL\PnlExpenseCategory;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlPayment;
use App\Models\PnL\PnlVendor;
use Illuminate\Http\Request;

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
        $categories = PnlExpenseCategory::forUser($userId)->active()->ordered()->get();
        $vendors = PnlVendor::forUser($userId)->active()->orderBy('full_name')->get();
        
        $selectedEventId = $request->get('event_id');

        return view('pnl.expenses.create', compact('events', 'categories', 'vendors', 'selectedEventId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:pnl_events,id',
            'category_id' => 'required|uuid|exists:pnl_expense_categories,id',
            'vendor_id' => 'nullable|uuid|exists:pnl_vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
            // Payment fields
            'create_payment' => 'boolean',
            'payment_status' => 'nullable|in:pending,scheduled,paid',
            'scheduled_date' => 'nullable|date',
            'payment_method' => 'nullable|in:bank_transfer,cash,cheque,upi,other',
            'reminder_enabled' => 'boolean',
            'reminder_days_before' => 'nullable|integer|min:1|max:30',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;

        $expense = PnlExpense::create($validated);

        // Create payment record if requested
        if ($request->boolean('create_payment', true)) {
            PnlPayment::create([
                'expense_id' => $expense->id,
                'vendor_id' => $validated['vendor_id'],
                'user_id' => auth()->id(),
                'amount' => $expense->total_amount,
                'status' => $validated['payment_status'] ?? 'pending',
                'scheduled_date' => $validated['scheduled_date'] ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
                'reminder_enabled' => $request->boolean('reminder_enabled', true),
                'reminder_days_before' => $validated['reminder_days_before'] ?? 3,
            ]);
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

        $expense->load('payment');

        return view('pnl.expenses.edit', compact('expense', 'events', 'categories', 'vendors'));
    }

    public function update(Request $request, PnlExpense $expense)
    {
        $this->authorize('update', $expense);

        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:pnl_events,id',
            'category_id' => 'required|uuid|exists:pnl_expense_categories,id',
            'vendor_id' => 'nullable|uuid|exists:pnl_vendors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'expense_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:100',
        ]);

        $validated['tax_amount'] = $validated['tax_amount'] ?? 0;

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
}
