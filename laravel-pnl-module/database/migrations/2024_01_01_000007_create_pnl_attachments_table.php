<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('attachable'); // polymorphic - can attach to expenses, payments, vendors
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // in bytes
            $table->string('path');
            $table->enum('type', ['invoice', 'contract', 'receipt', 'proof_of_payment', 'other'])->default('other');
            $table->text('description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_attachments');
    }
};
