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

        // Summary Statistics
        $totalRevenue = PnlRevenue::whereIn('event_id', $filteredEventIds)->sum('net_revenue_after_refunds');
        $grossRevenue = PnlRevenue::whereIn('event_id', $filteredEventIds)->sum('gross_revenue');
        $totalExpenses = PnlExpense::whereIn('event_id', $filteredEventIds)->sum('total_amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $totalTicketsSold = PnlRevenue::whereIn('event_id', $filteredEventIds)->sum('tickets_sold');

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

        // Revenue by ticket type
        $revenueByTicketType = PnlRevenue::whereIn('event_id', $filteredEventIds)
            ->select(
                'ticket_type',
                DB::raw('SUM(tickets_sold) as tickets_sold'),
                DB::raw('SUM(gross_revenue) as gross_revenue'),
                DB::raw('SUM(net_revenue_after_refunds) as net_revenue')
            )
            ->groupBy('ticket_type')
            ->get();

        // Payment Status Summary
        $paymentSummary = [
            'paid' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'paid')->sum('amount'),
            'pending' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'pending')->sum('amount'),
            'scheduled' => PnlPayment::forUser($userId)->whereHas('expense', function($q) use ($filteredEventIds) {
                $q->whereIn('event_id', $filteredEventIds);
            })->where('status', 'scheduled')->sum('amount'),
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

        // Chart data for Revenue vs Expenses trend (last 6 months)
        $trendData = $this->getMonthlyTrend($userId, 6);

        return view('pnl.dashboard.index', compact(
            'events',
            'totalRevenue',
            'grossRevenue',
            'totalExpenses',
            'netProfit',
            'profitStatus',
            'totalTicketsSold',
            'expenseByCategory',
            'revenueByTicketType',
            'paymentSummary',
            'upcomingPayments',
            'overduePayments',
            'recentEvents',
            'trendData',
            'eventId',
            'dateFrom',
            'dateTo'
        ));
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

            $revenues[] = PnlRevenue::whereIn('event_id', $eventIds)->sum('net_revenue_after_refunds');
            $expenses[] = PnlExpense::whereIn('event_id', $eventIds)->sum('total_amount');
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
        $period = $request->get('period', 30); // days

        // Upcoming payments grouped by period
        $upcoming7 = PnlPayment::forUser($userId)->upcoming(7)->sum('amount');
        $upcoming14 = PnlPayment::forUser($userId)->upcoming(14)->sum('amount');
        $upcoming30 = PnlPayment::forUser($userId)->upcoming(30)->sum('amount');

        // Outstanding (all pending/scheduled)
        $outstanding = PnlPayment::forUser($userId)->pending()->sum('amount');

        // Overdue
        $overdue = PnlPayment::forUser($userId)->overdue()->sum('amount');

        return view('pnl.dashboard.cashflow', compact(
            'upcoming7',
            'upcoming14',
            'upcoming30',
            'outstanding',
            'overdue'
        ));
    }
}
