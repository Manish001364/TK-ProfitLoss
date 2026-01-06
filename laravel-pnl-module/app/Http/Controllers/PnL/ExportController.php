<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlVendor;
use App\Exports\PnlSummaryExport;
use App\Exports\EventPnlExport;
use App\Exports\VendorsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    /**
     * Export P&L Summary
     */
    public function pnlSummary(Request $request)
    {
        $userId = auth()->id();
        $format = $request->get('format', 'xlsx');
        $eventId = $request->get('event_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $filename = 'pnl_summary_' . now()->format('Y-m-d_His');

        if ($format === 'pdf') {
            $data = $this->getPnlData($userId, $eventId, $dateFrom, $dateTo);
            $pdf = Pdf::loadView('pnl.exports.pnl-summary-pdf', $data);
            return $pdf->download($filename . '.pdf');
        }

        return Excel::download(
            new PnlSummaryExport($userId, $eventId, $dateFrom, $dateTo),
            $filename . '.' . $format
        );
    }

    /**
     * Export Event-wise P&L Report
     */
    public function eventPnl(PnlEvent $event, Request $request)
    {
        $this->authorize('view', $event);

        $format = $request->get('format', 'xlsx');
        $filename = 'event_pnl_' . Str::slug($event->name) . '_' . now()->format('Y-m-d_His');

        if ($format === 'pdf') {
            $event->load(['expenses.category', 'expenses.vendor', 'expenses.payment', 'revenues']);
            $pdf = Pdf::loadView('pnl.exports.event-pnl-pdf', ['event' => $event]);
            return $pdf->download($filename . '.pdf');
        }

        return Excel::download(
            new EventPnlExport($event),
            $filename . '.' . $format
        );
    }

    /**
     * Export Vendors/Contacts
     */
    public function vendors(Request $request)
    {
        $userId = auth()->id();
        $format = $request->get('format', 'xlsx');
        $type = $request->get('type'); // filter by vendor type

        $filename = 'contacts_' . now()->format('Y-m-d_His');

        return Excel::download(
            new VendorsExport($userId, ['type' => $type]),
            $filename . '.' . $format
        );
    }

    /**
     * Export Category-wise Expense Report
     */
    public function categoryExpenses(Request $request)
    {
        $userId = auth()->id();
        $format = $request->get('format', 'xlsx');
        $eventId = $request->get('event_id');

        $filename = 'category_expenses_' . now()->format('Y-m-d_His');

        // For simplicity, using P&L Summary export with category focus
        return Excel::download(
            new PnlSummaryExport($userId, $eventId, null, null, 'category'),
            $filename . '.' . $format
        );
    }

    /**
     * Get P&L data for PDF generation
     */
    private function getPnlData($userId, $eventId = null, $dateFrom = null, $dateTo = null): array
    {
        $query = PnlEvent::forUser($userId);
        
        if ($eventId) {
            $query->where('id', $eventId);
        }
        if ($dateFrom) {
            $query->where('event_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('event_date', '<=', $dateTo);
        }

        $events = $query->with(['expenses.category', 'revenues'])->get();

        $totalRevenue = 0;
        $totalExpenses = 0;
        $totalTicketsSold = 0;

        foreach ($events as $event) {
            $totalRevenue += $event->total_revenue;
            $totalExpenses += $event->total_expenses;
            $totalTicketsSold += $event->total_tickets_sold;
        }

        return [
            'events' => $events,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $totalRevenue - $totalExpenses,
            'totalTicketsSold' => $totalTicketsSold,
            'generatedAt' => now(),
            'filters' => [
                'event_id' => $eventId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ];
    }
}
