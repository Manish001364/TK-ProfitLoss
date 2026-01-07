<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = PnlEvent::forUser($userId)->with(['expenses', 'revenues']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort', 'event_date');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $events = $query->paginate(15)->withQueryString();

        return view('pnl.events.index', compact('events'));
    }

    public function create()
    {
        return view('pnl.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'budget' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['draft', 'planning', 'active', 'completed', 'cancelled'])],
        ]);

        $validated['user_id'] = auth()->id();

        $event = PnlEvent::create($validated);

        // Create default expense categories for user if not exists
        $existingCategories = PnlExpenseCategory::forUser(auth()->id())->count();
        if ($existingCategories === 0) {
            PnlExpenseCategory::createDefaultsForUser(auth()->id());
        }

        return redirect()
            ->route('pnl.events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show(PnlEvent $event)
    {
        $this->authorize('view', $event);

        $event->load([
            'expenses.category',
            'expenses.vendor',
            'expenses.payment',
            'revenues',
        ]);

        // Calculate summary
        $summary = [
            'total_revenue' => $event->total_revenue,
            'gross_revenue' => $event->gross_revenue,
            'total_expenses' => $event->total_expenses,
            'net_profit' => $event->net_profit,
            'profit_status' => $event->profit_status,
            'tickets_sold' => $event->total_tickets_sold,
            'budget_utilization' => $event->budget_utilization,
        ];

        // Expense breakdown
        $expenseByCategory = $event->expenses
            ->groupBy('category_id')
            ->map(function ($expenses) {
                $category = $expenses->first()->category;
                return [
                    'name' => $category->name,
                    'color' => $category->color,
                    'total' => $expenses->sum('total_amount'),
                ];
            })
            ->values();

        return view('pnl.events.show', compact('event', 'summary', 'expenseByCategory'));
    }

    public function edit(PnlEvent $event)
    {
        $this->authorize('update', $event);
        
        return view('pnl.events.edit', compact('event'));
    }

    public function update(Request $request, PnlEvent $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'budget' => 'nullable|numeric|min:0',
            'status' => ['required', Rule::in(['draft', 'planning', 'active', 'completed', 'cancelled'])],
        ]);

        $event->update($validated);

        return redirect()
            ->route('pnl.events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(PnlEvent $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('pnl.events.index')
            ->with('success', 'Event deleted successfully!');
    }

    public function duplicate(PnlEvent $event)
    {
        $this->authorize('view', $event);

        $newEvent = $event->replicate();
        $newEvent->name = $event->name . ' (Copy)';
        $newEvent->status = 'draft';
        $newEvent->save();

        return redirect()
            ->route('pnl.events.edit', $newEvent)
            ->with('success', 'Event duplicated successfully!');
    }
}
