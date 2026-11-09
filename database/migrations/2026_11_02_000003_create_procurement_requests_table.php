<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 50)->unique();
            $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete(); // Pembuat request (Barista)
            $table->decimal('quantity_requested', 14, 2);
            $table->decimal('stock_at_request', 14, 2)->default(0);
            $table->decimal('min_stock_at_request', 14, 2)->default(0);
            $table->date('required_date');
            $table->enum('priority', ['tinggi', 'normal', 'rendah'])->default('normal');
            $table->text('reason')->nullable();
            
            // Status pengadaan: menunggu -> diproses (disetujui) -> selesai (barang diterima) | ditolak
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            
            // Kolom Persetujuan / Penolakan Owner
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->text('order_notes')->nullable();

            // Kolom Penerimaan Barang (Stok Masuk)
            $table->decimal('quantity_received', 14, 2)->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('invoice_number', 100)->nullable();
            $table->text('receipt_notes')->nullable();
            $table->foreignId('stock_movement_id')->nullable()->constrained('stock_movements')->nullOnDelete();

            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('ingredient_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_requests');
    }
};

