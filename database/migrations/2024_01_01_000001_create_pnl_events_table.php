<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // organiser
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->string('location')->nullable();
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->enum('status', ['draft', 'planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'event_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_events');
    }
};
