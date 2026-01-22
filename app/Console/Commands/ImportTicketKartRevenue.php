<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PnL\PnlEvent;
use App\Models\PnL\PnlRevenue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportTicketKartRevenue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pnl:import-revenue 
                            {--event_id= : Specific PnL event ID to import revenue for}
                            {--dry-run : Show what would be imported without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import ticket sales revenue from TicketKart orders table into P&L module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('=== DRY RUN MODE - No changes will be made ===');
        }

        // Get P&L events that are linked to TicketKart events
        $query = PnlEvent::whereNotNull('ticketkart_event_id');
        
        if ($eventId = $this->option('event_id')) {
            $query->where('id', $eventId);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            $this->warn('No P&L events found with TicketKart event links.');
            $this->info('To link a P&L event to TicketKart:');
            $this->info('1. Edit the P&L event');
            $this->info('2. Select a TicketKart event from the dropdown');
            return 0;
        }

        $this->info("Found {$events->count()} P&L event(s) linked to TicketKart.");
        $this->newLine();

        foreach ($events as $pnlEvent) {
            $this->importRevenueForEvent($pnlEvent, $dryRun);
        }

        $this->newLine();
        $this->info('Revenue import completed!');
        
        return 0;
    }

    /**
     * Import revenue for a specific P&L event from TicketKart orders
     */
    private function importRevenueForEvent(PnlEvent $pnlEvent, bool $dryRun = false)
    {
        $this->info("Processing: {$pnlEvent->name} (TK Event ID: {$pnlEvent->ticketkart_event_id})");

        // Check if orders table exists
        if (!$this->tableExists('orders')) {
            $this->warn("  - 'orders' table not found. Skipping.");
            return;
        }

        // Try to get ticket sales from orders
        // Adjust this query based on your actual TicketKart schema
        try {
            // Method 1: If you have order_items table
            if ($this->tableExists('order_items')) {
                $ticketSales = $this->getRevenueFromOrderItems($pnlEvent->ticketkart_event_id);
            }
            // Method 2: If you have eventtickets/bookings tables
            elseif ($this->tableExists('eventtickets')) {
                $ticketSales = $this->getRevenueFromEventTickets($pnlEvent->ticketkart_event_id);
            }
            // Method 3: Direct from orders table
            else {
                $ticketSales = $this->getRevenueFromOrders($pnlEvent->ticketkart_event_id);
            }

            if ($ticketSales->isEmpty()) {
                $this->line("  - No ticket sales found for this event.");
                return;
            }

            foreach ($ticketSales as $sale) {
                $this->processTicketSale($pnlEvent, $sale, $dryRun);
            }

        } catch (\Exception $e) {
            $this->error("  - Error: " . $e->getMessage());
        }
    }

    /**
     * Get revenue from order_items table
     */
    private function getRevenueFromOrderItems($eventId)
    {
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->where('order_items.event_id', $eventId)
            ->where('orders.status', 'completed')
            ->whereNull('orders.deleted_at')
            ->select(
                DB::raw('COALESCE(order_items.ticket_name, "General Admission") as ticket_type'),
                DB::raw('COALESCE(order_items.unit_price, 0) as ticket_price'),
                DB::raw('SUM(COALESCE(order_items.quantity, 1)) as tickets_sold'),
                DB::raw('SUM(COALESCE(order_items.unit_price, 0) * COALESCE(order_items.quantity, 1)) as gross_revenue'),
                DB::raw('SUM(COALESCE(order_items.booking_fee, 0)) as platform_fee'),
                DB::raw('SUM(COALESCE(order_items.organiser_booking_fee, 0)) as organiser_fee')
            )
            ->groupBy('order_items.ticket_name', 'order_items.unit_price')
            ->get();
    }

    /**
     * Get revenue from eventtickets table
     */
    private function getRevenueFromEventTickets($eventId)
    {
        return DB::table('eventtickets')
            ->join('tickets', 'eventtickets.ticket_id', '=', 'tickets.id')
            ->leftJoin('orders', 'eventtickets.booking_id', '=', 'orders.id')
            ->where('eventtickets.event_id', $eventId)
            ->where(function($q) {
                $q->whereNull('orders.id')
                  ->orWhere('orders.status', 'completed');
            })
            ->select(
                DB::raw('COALESCE(tickets.name, "General Admission") as ticket_type'),
                DB::raw('COALESCE(tickets.price, 0) as ticket_price'),
                DB::raw('COUNT(eventtickets.id) as tickets_sold'),
                DB::raw('SUM(COALESCE(tickets.price, 0)) as gross_revenue'),
                DB::raw('0 as platform_fee'),
                DB::raw('0 as organiser_fee')
            )
            ->groupBy('tickets.name', 'tickets.price')
            ->get();
    }

    /**
     * Get revenue directly from orders table
     */
    private function getRevenueFromOrders($eventId)
    {
        return DB::table('orders')
            ->where('event_id', $eventId)
            ->where('status', 'completed')
            ->whereNull('deleted_at')
            ->select(
                DB::raw('"General Admission" as ticket_type'),
                DB::raw('0 as ticket_price'),
                DB::raw('COUNT(id) as tickets_sold'),
                DB::raw('SUM(COALESCE(total_amount, 0)) as gross_revenue'),
                DB::raw('SUM(COALESCE(booking_fee, 0)) as platform_fee'),
                DB::raw('SUM(COALESCE(organiser_booking_fee, 0)) as organiser_fee')
            )
            ->get();
    }

    /**
     * Process a single ticket sale and create/update revenue entry
     */
    private function processTicketSale(PnlEvent $pnlEvent, $sale, bool $dryRun)
    {
        $ticketType = $sale->ticket_type ?: 'General Admission';
        
        // Check if revenue entry already exists
        $existingRevenue = PnlRevenue::where('event_id', $pnlEvent->id)
            ->where('ticket_type', $ticketType)
            ->first();

        $data = [
            'event_id' => $pnlEvent->id,
            'user_id' => $pnlEvent->user_id,
            'ticket_type' => $ticketType,
            'ticket_price' => $sale->ticket_price ?? 0,
            'tickets_sold' => $sale->tickets_sold ?? 0,
            'gross_revenue' => $sale->gross_revenue ?? 0,
            'platform_fee' => $sale->platform_fee ?? 0,
            'gateway_fee' => 0,
            'tax_amount' => 0,
            'refunds' => 0,
        ];

        if ($existingRevenue) {
            if ($dryRun) {
                $this->line("  - [UPDATE] {$ticketType}: {$sale->tickets_sold} tickets, £" . number_format($sale->gross_revenue, 2));
            } else {
                $existingRevenue->update($data);
                $this->line("  - Updated: {$ticketType} ({$sale->tickets_sold} tickets, £" . number_format($sale->gross_revenue, 2) . ")");
            }
        } else {
            if ($dryRun) {
                $this->line("  - [CREATE] {$ticketType}: {$sale->tickets_sold} tickets, £" . number_format($sale->gross_revenue, 2));
            } else {
                PnlRevenue::create($data);
                $this->line("  - Created: {$ticketType} ({$sale->tickets_sold} tickets, £" . number_format($sale->gross_revenue, 2) . ")");
            }
        }
    }

    /**
     * Check if a table exists in the database
     */
    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }
}
