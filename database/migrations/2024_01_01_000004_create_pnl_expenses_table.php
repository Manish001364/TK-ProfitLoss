<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id');
            $table->uuid('category_id');
            $table->uuid('vendor_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2); // amount + tax
            $table->date('expense_date');
            $table->string('invoice_number')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_id')->references('id')->on('pnl_events')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('pnl_expense_categories')->onDelete('restrict');
            $table->foreign('vendor_id')->references('id')->on('pnl_vendors')->onDelete('set null');

            $table->index(['event_id', 'category_id']);
            $table->index('expense_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_expenses');
    }
};
