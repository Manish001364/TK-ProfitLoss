<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('auditable'); // polymorphic - tracks changes on any model
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('action', ['created', 'updated', 'deleted', 'restored', 'status_changed']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamp('created_at');

            $table->index(['auditable_type', 'auditable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_audit_logs');
    }
};
