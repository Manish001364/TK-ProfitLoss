<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlExpense;
use App\Models\PnL\PnlPayment;
use App\Models\PnL\PnlRevenue;
use App\Models\PnL\PnlVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $eventId = $request->get('event_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $chartPeriod = $request->get('chart_period', '6'); // Default 6 months

        // Get all events for filter dropdown
        $events = PnlEvent::forUser($userId)->orderBy('event_date', 'desc')->get();

        // Build query based on filters
        $eventQuery = PnlEvent::forUser($userId);
        if ($eventId) {
            $eventQuery->where('id', $eventId);
        }
        if ($dateFrom) {
            $eventQuery->where('event_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $eventQuery->where('event_date', '<=', $dateTo);
        }

        $filteredEventIds = $eventQuery->pluck('id');

        // Summary Statistics - Calculate from actual columns
        // Gross Revenue = ticket_price * tickets_sold
        // Net Revenue = Gross - platform_fees - payment_gateway_fees - taxes - refund_amount
        $revenueStats = PnlRevenue::whereIn('event_id', $filteredEventIds)
            ->select(
                DB::raw('SUM(ticket_price * tickets_sold) as gross_revenue'),
                DB::raw('SUM((ticket_price * tickets_sold) - platform_fees - payment_gateway_fees - taxes - refund_amount) as net_revenue'),
                DB::raw('SUM(tickets_sold) as tickets_sold')
            )
            ->first();

        $grossRevenue = $revenueStats->gross_revenue ?? 0;
        $totalRevenue = $revenueStats->net_revenue ?? 0;
        $totalTicketsSold = $revenueStats->tickets_sold ?? 0;

        $totalExpenses = PnlExpense::whereIn('event_id', $filteredEventIds)->sum('total_amount') ?? 0;
        
        // Calculate total budget
        $totalBudget = PnlEvent::whereIn('id', $filteredEventIds)->sum('budget') ?? 0;
        
        $netProfit = $totalRevenue - $totalExpenses;

        // Profit Status
        $profitStatus = 'break-even';
        if ($netProfit > 0) $profitStatus = 'profit';
        elseif ($netProfit < 0) $profitStatus = 'loss';

        // Expense breakdown by category
        $expenseByCategory = DB::table('pnl_expenses')
            ->join('pnl_expense_categories', 'pnl_expenses.category_id', '=', 'pnl_expense_categories.id')
            ->whereIn('pnl_expenses.event_id', $filteredEventIds)
            ->whereNull('pnl_expenses.deleted_at')
            ->select(
                'pnl_expense_categories.name',
                'pnl_expense_categories.color',
                DB::raw('SUM(pnl_expenses.total_amount) as total')
            )
            ->groupBy('pnl_expense_categories.id', 'pnl_expense_categories.name', 'pnl_expense_categories.color')
            ->orderByDesc('total')
            ->get();

        // Expense breakdown by vendor type (Artist, DJ, Venue, Equipment, etc.)
        $expenseByVendorType = DB::table('pnl_expenses')
            ->join('pnl_vendors', 'pnl_expenses.vendor_id', '=', 'pnl_vendors.id')
            ->whereIn('pnl_expenses.event_id', $filteredEventIds)
            ->whereNull('pnl_expenses.deleted_at')
            ->select(
                'pnl_vendors.type',
                DB::raw('SUM(pnl_expenses.total_amount) as total'),
                DB::raw('COUNT(DISTINCT pnl_expenses.id) as count')
            )
            ->groupBy('pnl_vendors.type')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                $colors = [
                    'artist' => '#dc3545',
                    'dj' => '#6f42c1',
                    'vendor' => '#0d6efd',
                    'caterer' => '#fd7e14',
                    'security' => '#6c757d',
                    'equipment' => '#20c997',
                    'venue' => '#0dcaf0',
                    'marketing' => '#d63384',
                    'staff' => '#198754',
                    'other' => '#adb5bd',
                ];
                $item->color = $colors[$item->type] ?? '#6c757d';
                $item->label = ucfirst($item->type);
                return $item;
            });

        // Revenue by ticket type - using calculated fields
        $revenueByTicketType = PnlRevenue::whereIn('event_id', $filteredEventIds)
            ->select(
                'ticket_type',
                DB::raw('SUM(tickets_sold) as tickets_sold'),
                DB::raw('SUM(ticket_price * tickets_sold) as gross_revenue'),
                DB::raw('SUM((ticket_price * tickets_sold) - platform_fees - payment_gateway_fees - taxes - refund_amount) as net_revenue')
            )
            ->groupBy('ticket_type')
            ->get();

        // Payment Status Summary
        $paymentSummary = [
            'paid' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'paid')->sum('amount') ?? 0,
            'pending' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'pending')->sum('amount') ?? 0,
            'scheduled' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'scheduled')->sum('amount') ?? 0,
        ];

        // Upcoming Payments (next 30 days)
        $upcomingPayments = PnlPayment::forUser($userId)
            ->upcoming(30)
            ->with(['expense', 'vendor'])
            ->orderBy('scheduled_date')
            ->limit(10)
            ->get();

        // Overdue Payments
        $overduePayments = PnlPayment::forUser($userId)
            ->overdue()
            ->with(['expense', 'vendor'])
            ->orderBy('scheduled_date')
            ->get();

        // Recent Events Performance
        $recentEvents = PnlEvent::forUser($userId)
            ->with(['expenses', 'revenues'])
            ->orderBy('event_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => $event->event_date,
                    'status' => $event->status,
                    'revenue' => $event->total_revenue,
                    'expenses' => $event->total_expenses,
                    'profit' => $event->net_profit,
                    'profit_status' => $event->profit_status,
                ];
            });

        // Vendor Summary with payment totals
        $vendorSummary = PnlVendor::forUser($userId)
            ->active()
            ->withCount('payments')
            ->get()
            ->map(function ($vendor) {
                $totalPaid = $vendor->payments()->where('status', 'paid')->sum('amount');
                $totalPending = $vendor->payments()->whereIn('status', ['pending', 'scheduled'])->sum('amount');
                return [
                    'id' => $vendor->id,
                    'name' => $vendor->display_name,
                    'type' => $vendor->type,
                    'email' => $vendor->email,
                    'total_paid' => $totalPaid,
                    'total_pending' => $totalPending,
                    'payments_count' => $vendor->payments_count,
                ];
            })
            ->sortByDesc('total_paid')
            ->take(10)
            ->values();

        // Chart data for Revenue vs Expenses trend (configurable period)
        $chartMonths = $this->getChartMonths($chartPeriod);
        $trendData = $this->getMonthlyTrend($userId, $chartMonths);

        // Check if walkthrough should be shown
        $settings = \App\Models\PnL\PnlSettings::where('user_id', $userId)->first();
        $showWalkthrough = !$settings?->walkthrough_dismissed && $events->count() == 0;

        return view('pnl.dashboard.index', compact(
            'events',
            'totalRevenue',
            'grossRevenue',
            'totalExpenses',
            'totalBudget',
            'netProfit',
            'profitStatus',
            'totalTicketsSold',
            'expenseByCategory',
            'expenseByVendorType',
            'revenueByTicketType',
            'paymentSummary',
            'upcomingPayments',
            'overduePayments',
            'recentEvents',
            'vendorSummary',
            'trendData',
            'eventId',
            'dateFrom',
            'dateTo',
            'chartPeriod',
            'showWalkthrough'
        ));
    }

    /**
     * Convert chart period selection to months
     */
    private function getChartMonths($period): int
    {
        return match($period) {
            '3' => 3,
            '6' => 6,
            '12' => 12,
            'ytd' => now()->month, // Year to date
            default => 6,
        };
    }

    private function getMonthlyTrend($userId, $months = 6): array
    {
        $labels = [];
        $revenues = [];
        $expenses = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $eventIds = PnlEvent::forUser($userId)
                ->whereYear('event_date', $date->year)
                ->whereMonth('event_date', $date->month)
                ->pluck('id');

            // Calculate net revenue from actual columns
            $netRevenue = PnlRevenue::whereIn('event_id', $eventIds)
                ->select(DB::raw('SUM((ticket_price * tickets_sold) - platform_fees - payment_gateway_fees - taxes - refund_amount) as net'))
                ->value('net') ?? 0;

            $revenues[] = (float) $netRevenue;
            $expenses[] = (float) (PnlExpense::whereIn('event_id', $eventIds)->sum('total_amount') ?? 0);
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'expenses' => $expenses,
        ];
    }

    public function cashFlow(Request $request)
    {
        $userId = auth()->id();
        $period = $request->get('period', '30'); // days: 30, 60, 90

        // Get settings for currency
        $settings = \App\Models\PnL\PnlSettings::getOrCreate($userId);
        $currencySymbol = $settings->currency_symbol;

        // Upcoming payments grouped by period
        $upcoming7 = PnlPayment::forUser($userId)->upcoming(7)->sum('amount') ?? 0;
        $upcoming14 = PnlPayment::forUser($userId)->upcoming(14)->sum('amount') ?? 0;
        $upcoming30 = PnlPayment::forUser($userId)->upcoming(30)->sum('amount') ?? 0;
        $upcoming60 = PnlPayment::forUser($userId)->upcoming(60)->sum('amount') ?? 0;
        $upcoming90 = PnlPayment::forUser($userId)->upcoming(90)->sum('amount') ?? 0;

        // Outstanding (all pending/scheduled)
        $outstanding = PnlPayment::forUser($userId)->pending()->sum('amount') ?? 0;

        // Overdue
        $overdue = PnlPayment::forUser($userId)->overdue()->sum('amount') ?? 0;

        // Get detailed upcoming payments
        $upcomingPaymentsList = PnlPayment::forUser($userId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->where(function ($q) {
                $q->whereNull('scheduled_date')
                    ->orWhere('scheduled_date', '>=', now()->toDateString());
            })
            ->with(['expense.event', 'vendor'])
            ->orderBy('scheduled_date')
            ->get()
            ->map(function ($payment) {
                $daysUntil = $payment->scheduled_date ? now()->diffInDays($payment->scheduled_date, false) : null;
                return [
                    'id' => $payment->id,
                    'vendor_name' => $payment->vendor?->display_name ?? 'Unknown Vendor',
                    'event_name' => $payment->expense?->event?->name ?? 'Unknown Event',
                    'amount' => $payment->amount,
                    'scheduled_date' => $payment->scheduled_date,
                    'days_until' => $daysUntil,
                    'status' => $payment->status,
                    'urgency' => $daysUntil !== null ? ($daysUntil <= 7 ? 'high' : ($daysUntil <= 14 ? 'medium' : 'low')) : 'unknown',
                ];
            });

        // Get upcoming events with expected revenue
        $upcomingEvents = PnlEvent::forUser($userId)
            ->upcoming()
            ->with(['revenues', 'expenses'])
            ->orderBy('event_date')
            ->limit(10)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => $event->event_date,
                    'expected_revenue' => $event->expected_revenue ?? 0,
                    'current_revenue' => $event->total_revenue,
                    'total_expenses' => $event->total_expenses,
                    'projected_profit' => ($event->expected_revenue ?? $event->total_revenue) - $event->total_expenses,
                    'days_until' => now()->diffInDays($event->event_date, false),
                ];
            });

        // Calculate projections for timeline chart
        $projectionData = $this->calculateProjections($userId, (int) $period);

        // Cash flow summary
        $totalProjectedOutflow = $upcomingPaymentsList->sum('amount');
        $totalProjectedInflow = $upcomingEvents->sum('expected_revenue');

        return view('pnl.dashboard.cashflow', compact(
            'upcoming7',
            'upcoming14',
            'upcoming30',
            'upcoming60',
            'upcoming90',
            'outstanding',
            'overdue',
            'upcomingPaymentsList',
            'upcomingEvents',
            'projectionData',
            'totalProjectedOutflow',
            'totalProjectedInflow',
            'period',
            'currencySymbol',
            'settings'
        ));
    }

    /**
     * Calculate cash flow projections for chart
     */
    private function calculateProjections($userId, $days = 30): array
    {
        $labels = [];
        $outflows = [];
        $inflows = [];
        $cumulative = [];
        $runningTotal = 0;

        // Get all scheduled payments
        $payments = PnlPayment::forUser($userId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->whereNotNull('scheduled_date')
            ->where('scheduled_date', '>=', now()->toDateString())
            ->where('scheduled_date', '<=', now()->addDays($days)->toDateString())
            ->select('scheduled_date', DB::raw('SUM(amount) as total'))
            ->groupBy('scheduled_date')
            ->pluck('total', 'scheduled_date')
            ->toArray();

        // Get expected revenue from upcoming events
        $eventRevenues = PnlEvent::forUser($userId)
            ->where('event_date', '>=', now()->toDateString())
            ->where('event_date', '<=', now()->addDays($days)->toDateString())
            ->select('event_date', DB::raw('SUM(COALESCE(expected_revenue, 0)) as total'))
            ->groupBy('event_date')
            ->pluck('total', 'event_date')
            ->toArray();

        // Build daily projections
        for ($i = 0; $i <= $days; $i += 7) { // Weekly intervals for cleaner chart
            $date = now()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('M d');

            // Sum payments for this week
            $weekOutflow = 0;
            $weekInflow = 0;
            for ($j = 0; $j < 7 && ($i + $j) <= $days; $j++) {
                $checkDate = now()->addDays($i + $j)->format('Y-m-d');
                $weekOutflow += $payments[$checkDate] ?? 0;
                $weekInflow += $eventRevenues[$checkDate] ?? 0;
            }

            $outflows[] = (float) $weekOutflow;
            $inflows[] = (float) $weekInflow;
            $runningTotal += ($weekInflow - $weekOutflow);
            $cumulative[] = (float) $runningTotal;
        }

        return [
            'labels' => $labels,
            'outflows' => $outflows,
            'inflows' => $inflows,
            'cumulative' => $cumulative,
        ];
    }
}
