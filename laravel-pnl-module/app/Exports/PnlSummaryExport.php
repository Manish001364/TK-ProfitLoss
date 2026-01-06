<?php

namespace App\Exports;

use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlExpense;
use App\Models\PnL\PnlRevenue;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PnlSummaryExport implements WithMultipleSheets
{
    protected $userId;
    protected $eventId;
    protected $dateFrom;
    protected $dateTo;
    protected $focus;

    public function __construct($userId, $eventId = null, $dateFrom = null, $dateTo = null, $focus = 'all')
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->focus = $focus;
    }

    public function sheets(): array
    {
        return [
            new PnlSummarySheet($this->userId, $this->eventId, $this->dateFrom, $this->dateTo),
            new ExpenseDetailSheet($this->userId, $this->eventId, $this->dateFrom, $this->dateTo),
            new RevenueDetailSheet($this->userId, $this->eventId, $this->dateFrom, $this->dateTo),
        ];
    }
}

class PnlSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $userId;
    protected $eventId;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($userId, $eventId, $dateFrom, $dateTo)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection(): Collection
    {
        $query = PnlEvent::forUser($this->userId);
        
        if ($this->eventId) {
            $query->where('id', $this->eventId);
        }
        if ($this->dateFrom) {
            $query->where('event_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->where('event_date', '<=', $this->dateTo);
        }

        return $query->get()->map(function ($event) {
            return [
                'event_name' => $event->name,
                'event_date' => $event->event_date->format('Y-m-d'),
                'venue' => $event->venue,
                'status' => ucfirst($event->status),
                'budget' => $event->budget,
                'total_revenue' => $event->total_revenue,
                'total_expenses' => $event->total_expenses,
                'net_profit' => $event->net_profit,
                'profit_status' => ucfirst($event->profit_status),
                'tickets_sold' => $event->total_tickets_sold,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Event Name',
            'Event Date',
            'Venue',
            'Status',
            'Budget',
            'Total Revenue',
            'Total Expenses',
            'Net Profit/Loss',
            'P&L Status',
            'Tickets Sold',
        ];
    }

    public function title(): string
    {
        return 'P&L Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class ExpenseDetailSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $userId;
    protected $eventId;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($userId, $eventId, $dateFrom, $dateTo)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection(): Collection
    {
        $query = PnlExpense::forUser($this->userId)->with(['event', 'category', 'vendor', 'payment']);

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        } else {
            $eventQuery = PnlEvent::forUser($this->userId);
            if ($this->dateFrom) {
                $eventQuery->where('event_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $eventQuery->where('event_date', '<=', $this->dateTo);
            }
            $query->whereIn('event_id', $eventQuery->pluck('id'));
        }

        return $query->get()->map(function ($expense) {
            return [
                'event_name' => $expense->event->name,
                'category' => $expense->category->name,
                'vendor' => $expense->vendor?->display_name ?? 'N/A',
                'title' => $expense->title,
                'amount' => $expense->amount,
                'tax' => $expense->tax_amount,
                'total' => $expense->total_amount,
                'date' => $expense->expense_date->format('Y-m-d'),
                'payment_status' => ucfirst($expense->payment?->status ?? 'No Payment'),
                'invoice_number' => $expense->invoice_number,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Event',
            'Category',
            'Vendor',
            'Title',
            'Amount',
            'Tax',
            'Total',
            'Date',
            'Payment Status',
            'Invoice #',
        ];
    }

    public function title(): string
    {
        return 'Expenses';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

class RevenueDetailSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $userId;
    protected $eventId;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($userId, $eventId, $dateFrom, $dateTo)
    {
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection(): Collection
    {
        $query = PnlRevenue::forUser($this->userId)->with('event');

        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        } else {
            $eventQuery = PnlEvent::forUser($this->userId);
            if ($this->dateFrom) {
                $eventQuery->where('event_date', '>=', $this->dateFrom);
            }
            if ($this->dateTo) {
                $eventQuery->where('event_date', '<=', $this->dateTo);
            }
            $query->whereIn('event_id', $eventQuery->pluck('id'));
        }

        return $query->get()->map(function ($revenue) {
            return [
                'event_name' => $revenue->event->name,
                'ticket_type' => $revenue->display_name,
                'tickets_available' => $revenue->tickets_available,
                'tickets_sold' => $revenue->tickets_sold,
                'ticket_price' => $revenue->ticket_price,
                'gross_revenue' => $revenue->gross_revenue,
                'platform_fees' => $revenue->platform_fees,
                'gateway_fees' => $revenue->payment_gateway_fees,
                'taxes' => $revenue->taxes,
                'net_revenue' => $revenue->net_revenue,
                'refunds' => $revenue->refund_amount,
                'final_revenue' => $revenue->net_revenue_after_refunds,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Event',
            'Ticket Type',
            'Available',
            'Sold',
            'Price',
            'Gross Revenue',
            'Platform Fees',
            'Gateway Fees',
            'Taxes',
            'Net Revenue',
            'Refunds',
            'Final Revenue',
        ];
    }

    public function title(): string
    {
        return 'Revenue';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
