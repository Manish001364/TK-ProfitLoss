<?php

namespace App\Exports;

use App\Models\PnL\PnlEvent;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EventPnlExport implements WithMultipleSheets
{
    protected $event;

    public function __construct(PnlEvent $event)
    {
        $this->event = $event->load(['expenses.category', 'expenses.vendor', 'expenses.payment', 'revenues']);
    }

    public function sheets(): array
    {
        return [
            new EventSummarySheet($this->event),
            new EventExpensesSheet($this->event),
            new EventRevenuesSheet($this->event),
        ];
    }
}

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class EventSummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $event;

    public function __construct(PnlEvent $event)
    {
        $this->event = $event;
    }

    public function collection(): Collection
    {
        return collect([
            ['Metric', 'Value'],
            ['Event Name', $this->event->name],
            ['Event Date', $this->event->event_date->format('Y-m-d')],
            ['Venue', $this->event->venue ?? 'N/A'],
            ['Location', $this->event->location ?? 'N/A'],
            ['Status', ucfirst($this->event->status)],
            ['Budget', number_format($this->event->budget, 2)],
            ['', ''],
            ['FINANCIAL SUMMARY', ''],
            ['Gross Revenue', number_format($this->event->gross_revenue, 2)],
            ['Net Revenue', number_format($this->event->total_revenue, 2)],
            ['Total Expenses', number_format($this->event->total_expenses, 2)],
            ['Net Profit/Loss', number_format($this->event->net_profit, 2)],
            ['P&L Status', ucfirst($this->event->profit_status)],
            ['', ''],
            ['TICKET SUMMARY', ''],
            ['Total Tickets Sold', $this->event->total_tickets_sold],
            ['Budget Utilization', number_format($this->event->budget_utilization, 1) . '%'],
        ]);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Event Summary';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            9 => ['font' => ['bold' => true]],
            16 => ['font' => ['bold' => true]],
        ];
    }
}

class EventExpensesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $event;

    public function __construct(PnlEvent $event)
    {
        $this->event = $event;
    }

    public function collection(): Collection
    {
        return $this->event->expenses->map(function ($expense) {
            return [
                $expense->category->name,
                $expense->vendor?->display_name ?? 'N/A',
                $expense->title,
                $expense->amount,
                $expense->tax_amount,
                $expense->total_amount,
                $expense->expense_date->format('Y-m-d'),
                ucfirst($expense->payment?->status ?? 'No Payment'),
                $expense->invoice_number,
            ];
        });
    }

    public function headings(): array
    {
        return [
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

class EventRevenuesSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    protected $event;

    public function __construct(PnlEvent $event)
    {
        $this->event = $event;
    }

    public function collection(): Collection
    {
        return $this->event->revenues->map(function ($revenue) {
            return [
                $revenue->display_name,
                $revenue->tickets_available,
                $revenue->tickets_sold,
                $revenue->ticket_price,
                $revenue->gross_revenue,
                $revenue->platform_fees,
                $revenue->payment_gateway_fees,
                $revenue->taxes,
                $revenue->net_revenue,
                $revenue->tickets_refunded,
                $revenue->refund_amount,
                $revenue->net_revenue_after_refunds,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Ticket Type',
            'Available',
            'Sold',
            'Price',
            'Gross Revenue',
            'Platform Fees',
            'Gateway Fees',
            'Taxes',
            'Net Revenue',
            'Refunded',
            'Refund Amount',
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
