<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code', 50)->unique(); // e.g. EMG-20261102-ABCD
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('quantity', 14, 2);
            $table->decimal('amount', 14, 2); // Total biaya/harga beli dalam Rupiah
            $table->string('purchase_location', 255); // Tempat pembelian / nama toko
            $table->date('purchase_date');
            $table->text('notes')->nullable(); // Keterangan / alasan pembelian darurat
            $table->string('receipt_proof', 255)->nullable(); // Path bukti struk (foto/PDF)
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete(); // Barista / user yang mencatat
            $table->foreignId('stock_movement_id')->nullable()->constrained('stock_movements')->nullOnDelete();
            $table->string('status', 30)->default('completed'); // completed, verified
            $table->timestamps();

            $table->index(['purchase_date', 'ingredient_id']);
            $table->index('purchase_code');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_purchases');
    }
};

