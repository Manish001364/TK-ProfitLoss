<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pnl_expense_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Artist Fee, Venue, Marketing, Staff, Tech, Misc
            $table->enum('type', ['fixed', 'variable'])->default('variable');
            $table->text('description')->nullable();
            $table->decimal('default_budget_limit', 15, 2)->nullable();
            $table->string('color', 7)->default('#6366f1'); // hex color for charts
            $table->string('icon')->nullable(); // FontAwesome icon class
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
        });

        // Seed default categories
        $this->seedDefaultCategories();
    }

    private function seedDefaultCategories(): void
    {
        $categories = [
            ['name' => 'Artist Fee', 'type' => 'fixed', 'color' => '#ef4444', 'icon' => 'fas fa-music', 'sort_order' => 1],
            ['name' => 'Venue', 'type' => 'fixed', 'color' => '#f97316', 'icon' => 'fas fa-building', 'sort_order' => 2],
            ['name' => 'Marketing', 'type' => 'variable', 'color' => '#eab308', 'icon' => 'fas fa-bullhorn', 'sort_order' => 3],
            ['name' => 'Staff', 'type' => 'variable', 'color' => '#22c55e', 'icon' => 'fas fa-users', 'sort_order' => 4],
            ['name' => 'Equipment & Tech', 'type' => 'variable', 'color' => '#3b82f6', 'icon' => 'fas fa-cogs', 'sort_order' => 5],
            ['name' => 'Catering', 'type' => 'variable', 'color' => '#8b5cf6', 'icon' => 'fas fa-utensils', 'sort_order' => 6],
            ['name' => 'Security', 'type' => 'fixed', 'color' => '#ec4899', 'icon' => 'fas fa-shield-alt', 'sort_order' => 7],
            ['name' => 'Miscellaneous', 'type' => 'variable', 'color' => '#6b7280', 'icon' => 'fas fa-ellipsis-h', 'sort_order' => 8],
        ];

        // Note: In actual migration, you might want to use a seeder instead
        // This is just for reference - categories will be created per user
    }

    public function down(): void
    {
        Schema::dropIfExists('pnl_expense_categories');
    }
};
