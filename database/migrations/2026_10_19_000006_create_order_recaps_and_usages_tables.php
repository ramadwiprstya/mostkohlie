<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_recaps', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 50)->unique(); // e.g. TR-20261019-0001
            $table->date('transaction_date');
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('status', ['submitted', 'cancelled'])->default('submitted');
            $table->unsignedInteger('total_menus_count')->default(0);
            $table->unsignedInteger('total_items_quantity')->default(0);
            $table->string('idempotency_token', 64)->nullable()->unique();
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transaction_date');
            $table->index('status');
        });

        Schema::create('order_recap_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_recap_id')->constrained('order_recaps')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('menu_code_snapshot', 50);
            $table->string('menu_name_snapshot');
            $table->decimal('menu_price_snapshot', 14, 2)->default(0);
            $table->unsignedInteger('recipe_version_snapshot')->default(1);
            $table->timestamps();

            $table->index(['order_recap_id', 'menu_id']);
        });

        Schema::create('order_recap_ingredient_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_recap_id')->constrained('order_recaps')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('ingredient_code_snapshot', 50);
            $table->string('ingredient_name_snapshot');
            $table->decimal('quantity_used', 14, 2);
            $table->string('unit_symbol_snapshot', 20);
            $table->json('recipe_snapshot')->nullable();
            $table->timestamps();

            $table->index(['order_recap_id', 'ingredient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_recap_ingredient_usages');
        Schema::dropIfExists('order_recap_items');
        Schema::dropIfExists('order_recaps');
    }
};
