<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('expense_id');
            $table->uuid('vendor_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'scheduled', 'paid', 'cancelled'])->default('pending');
            
            // Scheduled Payment
            $table->date('scheduled_date')->nullable();
            $table->date('actual_paid_date')->nullable();
            
            // Payment Details
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque', 'upi', 'other'])->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Reminder Settings
            $table->boolean('reminder_enabled')->default(true);
            $table->integer('reminder_days_before')->default(3); // days before scheduled date
            $table->boolean('reminder_on_due_date')->default(true);
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('expense_id')->references('id')->on('pnl_expenses')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('pnl_vendors')->onDelete('set null');

            $table->index(['status', 'scheduled_date']);
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_payments');
    }
};
