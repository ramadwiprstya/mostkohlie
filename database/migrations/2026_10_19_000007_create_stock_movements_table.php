<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('movement_code', 50)->unique(); // e.g. MOV-20261019-0001
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnUpdate()->restrictOnDelete();
            $table->enum('type', [
                'recap_usage',           // Pengurangan dari rekap pesanan
                'restock',               // Penambahan stok masuk
                'adjustment_in',         // Koreksi stok positif
                'adjustment_out',        // Koreksi stok negatif
                'cancellation_reversal'  // Pembalikan stok karena pembatalan rekap
            ]);
            $table->decimal('quantity_in', 14, 2)->default(0);
            $table->decimal('quantity_out', 14, 2)->default(0);
            $table->decimal('stock_before', 14, 2);
            $table->decimal('stock_after', 14, 2);
            $table->string('reference_type', 50)->nullable(); // e.g. 'order_recap', 'restock', 'adjustment'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_code', 50)->nullable(); // e.g. TR-20261019-0001
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamp('movement_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['ingredient_id', 'movement_date']);
            $table->index('reference_code');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
