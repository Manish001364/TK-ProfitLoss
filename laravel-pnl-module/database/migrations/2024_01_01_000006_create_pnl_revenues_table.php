<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_revenues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Ticket Type
            $table->enum('ticket_type', ['regular', 'vip', 'early_bird', 'group', 'complimentary', 'other'])->default('regular');
            $table->string('ticket_name')->nullable(); // Custom name if needed
            
            // Quantities
            $table->integer('tickets_available')->default(0);
            $table->integer('tickets_sold')->default(0);
            $table->decimal('ticket_price', 15, 2);
            
            // Revenue Calculation
            $table->decimal('gross_revenue', 15, 2)->default(0); // tickets_sold * ticket_price
            $table->decimal('platform_fees', 15, 2)->default(0); // TicketKart fees
            $table->decimal('payment_gateway_fees', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('net_revenue', 15, 2)->default(0); // gross - fees - taxes
            
            // Refunds
            $table->integer('tickets_refunded')->default(0);
            $table->decimal('refund_amount', 15, 2)->default(0);
            $table->decimal('net_revenue_after_refunds', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('pnl_events')->onDelete('cascade');
            $table->index(['event_id', 'ticket_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_revenues');
    }
};
