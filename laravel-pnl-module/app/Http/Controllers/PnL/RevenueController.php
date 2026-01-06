<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlRevenue;
use App\Models\PnL\PnlEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        
        $query = PnlRevenue::forUser($userId)->with('event');

        // Filters
        if ($request->filled('event_id')) {
            $query->forEvent($request->event_id);
        }
        if ($request->filled('ticket_type')) {
            $query->ofType($request->ticket_type);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $revenues = $query->paginate(15)->withQueryString();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $ticketTypes = PnlRevenue::getTicketTypes();

        return view('pnl.revenues.index', compact('revenues', 'events', 'ticketTypes'));
    }

    public function create(Request $request)
    {
        $userId = auth()->id();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $ticketTypes = PnlRevenue::getTicketTypes();
        
        $selectedEventId = $request->get('event_id');

        return view('pnl.revenues.create', compact('events', 'ticketTypes', 'selectedEventId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:pnl_events,id',
            'ticket_type' => ['required', Rule::in(array_keys(PnlRevenue::getTicketTypes()))],
            'ticket_name' => 'nullable|string|max:255',
            'tickets_available' => 'required|integer|min:0',
            'tickets_sold' => 'required|integer|min:0',
            'ticket_price' => 'required|numeric|min:0',
            'platform_fees' => 'nullable|numeric|min:0',
            'payment_gateway_fees' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'tickets_refunded' => 'nullable|integer|min:0',
            'refund_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['platform_fees'] = $validated['platform_fees'] ?? 0;
        $validated['payment_gateway_fees'] = $validated['payment_gateway_fees'] ?? 0;
        $validated['taxes'] = $validated['taxes'] ?? 0;
        $validated['tickets_refunded'] = $validated['tickets_refunded'] ?? 0;
        $validated['refund_amount'] = $validated['refund_amount'] ?? 0;

        $revenue = PnlRevenue::create($validated);

        return redirect()
            ->route('pnl.revenues.show', $revenue)
            ->with('success', 'Revenue entry created successfully!');
    }

    public function show(PnlRevenue $revenue)
    {
        $this->authorize('view', $revenue);

        $revenue->load('event');

        return view('pnl.revenues.show', compact('revenue'));
    }

    public function edit(PnlRevenue $revenue)
    {
        $this->authorize('update', $revenue);

        $userId = auth()->id();
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();
        $ticketTypes = PnlRevenue::getTicketTypes();

        return view('pnl.revenues.edit', compact('revenue', 'events', 'ticketTypes'));
    }

    public function update(Request $request, PnlRevenue $revenue)
    {
        $this->authorize('update', $revenue);

        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:pnl_events,id',
            'ticket_type' => ['required', Rule::in(array_keys(PnlRevenue::getTicketTypes()))],
            'ticket_name' => 'nullable|string|max:255',
            'tickets_available' => 'required|integer|min:0',
            'tickets_sold' => 'required|integer|min:0',
            'ticket_price' => 'required|numeric|min:0',
            'platform_fees' => 'nullable|numeric|min:0',
            'payment_gateway_fees' => 'nullable|numeric|min:0',
            'taxes' => 'nullable|numeric|min:0',
            'tickets_refunded' => 'nullable|integer|min:0',
            'refund_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['platform_fees'] = $validated['platform_fees'] ?? 0;
        $validated['payment_gateway_fees'] = $validated['payment_gateway_fees'] ?? 0;
        $validated['taxes'] = $validated['taxes'] ?? 0;
        $validated['tickets_refunded'] = $validated['tickets_refunded'] ?? 0;
        $validated['refund_amount'] = $validated['refund_amount'] ?? 0;

        $revenue->update($validated);

        return redirect()
            ->route('pnl.revenues.show', $revenue)
            ->with('success', 'Revenue entry updated successfully!');
    }

    public function destroy(PnlRevenue $revenue)
    {
        $this->authorize('delete', $revenue);

        $eventId = $revenue->event_id;
        $revenue->delete();

        return redirect()
            ->route('pnl.events.show', $eventId)
            ->with('success', 'Revenue entry deleted successfully!');
    }
}
