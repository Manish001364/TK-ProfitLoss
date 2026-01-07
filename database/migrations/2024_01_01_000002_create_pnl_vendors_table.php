<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // created by organiser
            
            // Basic Info
            $table->string('full_name');
            $table->string('business_name')->nullable();
            $table->enum('type', ['artist', 'dj', 'vendor', 'staff', 'venue', 'equipment', 'catering', 'security', 'other'])->default('vendor');
            
            // Contact Details
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            
            // Addresses
            $table->text('business_address')->nullable();
            $table->text('home_address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            
            // Bank Details (for reference only)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc_code')->nullable();
            $table->string('bank_branch')->nullable();
            
            // Tax Info
            $table->string('tax_vat_reference')->nullable();
            $table->string('pan_number')->nullable();
            $table->string('gst_number')->nullable();
            
            // Additional
            $table->text('notes')->nullable();
            $table->string('preferred_payment_cycle')->nullable(); // weekly, monthly, per-event
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'type']);
            $table->index('email');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_vendors');
    }
};
