<?php

namespace App\Services;

use App\Models\EmergencyPurchase;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmergencyPurchaseService
{
    /**
     * Catat transaksi pembelian darurat, otomatis update stok dan buat mutasi stok & notifikasi
     */
    public function recordPurchase(array $data, User $user, ?UploadedFile $file = null): EmergencyPurchase
    {
        $receiptPath = null;
        if ($file && $file->isValid()) {
            $destination = public_path('uploads/receipts');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $filename = 'receipt_' . date('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $receiptPath = 'uploads/receipts/' . $filename;
        }

        return DB::transaction(function () use ($data, $user, $receiptPath) {
            $ingredientId = $data['ingredient_id'];
            $lockedIngredient = Ingredient::with('unit')->where('id', $ingredientId)->lockForUpdate()->firstOrFail();

            $qty = (float) $data['quantity'];
            if ($qty <= 0) {
                throw new \InvalidArgumentException("Jumlah bahan harus lebih besar dari 0.");
            }

            $amount = (float) $data['amount'];
            if ($amount < 0) {
                throw new \InvalidArgumentException("Nominal harga pembelian tidak boleh negatif.");
            }

            $stockBefore = (float) $lockedIngredient->current_stock;
            $stockAfter = $stockBefore + $qty;

            // Update stok bahan baku
            $lockedIngredient->current_stock = $stockAfter;
            $lockedIngredient->save();

            // Waktu dan tanggal pembelian real-time
            $purchaseDateStr = $data['purchase_date'] ?? Carbon::now('Asia/Jakarta')->toDateString();
            $purchaseTimeStr = $data['purchase_time'] ?? Carbon::now('Asia/Jakarta')->format('H:i:s');
            if (strlen($purchaseTimeStr) === 5) { // e.g. "16:20"
                $purchaseTimeStr .= ':00';
            }
            $fullDateTime = Carbon::parse($purchaseDateStr . ' ' . $purchaseTimeStr);

            // Buat kode transaksi unik: EMG-YYYYMMDD-XXXX
            $datePrefix = $fullDateTime->format('Ymd');
            $uniqueCode = 'EMG-' . $datePrefix . '-' . strtoupper(Str::random(5));
            while (EmergencyPurchase::where('purchase_code', $uniqueCode)->exists()) {
                $uniqueCode = 'EMG-' . $datePrefix . '-' . strtoupper(Str::random(5));
            }

            // Catat mutasi stok (tipe restock, referensi emergency_purchase)
            $movCode = 'MOV-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
            $stockMovement = StockMovement::create([
                'movement_code' => $movCode,
                'ingredient_id' => $lockedIngredient->id,
                'type' => 'restock',
                'quantity_in' => $qty,
                'quantity_out' => 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'emergency_purchase',
                'reference_id' => null,
                'reference_code' => $uniqueCode,
                'user_id' => $user->id,
                'movement_date' => $fullDateTime,
                'notes' => "Pembelian darurat di {$data['purchase_location']}: " . (!empty($data['notes']) ? $data['notes'] : 'Kebutuhan mendesak operasional bar'),
            ]);

            // Simpan record emergency purchase
            $emergencyPurchase = EmergencyPurchase::create([
                'purchase_code' => $uniqueCode,
                'ingredient_id' => $lockedIngredient->id,
                'quantity' => $qty,
                'amount' => $amount,
                'purchase_location' => $data['purchase_location'],
                'purchase_date' => $fullDateTime->toDateString(),
                'purchase_time' => $fullDateTime->format('H:i:s'),
                'notes' => $data['notes'] ?? null,
                'receipt_proof' => $receiptPath,
                'user_id' => $user->id,
                'stock_movement_id' => $stockMovement->id,
                'status' => 'completed',
            ]);

            // Update reference_id pada mutasi
            $stockMovement->update(['reference_id' => $emergencyPurchase->id]);

            // Buat notifikasi untuk Owner
            Notification::create([
                'type' => 'emergency_purchase',
                'title' => 'Pembelian Darurat: ' . $lockedIngredient->name,
                'message' => "Barista {$user->name} mencatat pembelian darurat {$qty} {$lockedIngredient->unit?->symbol} di {$data['purchase_location']} senilai Rp " . number_format($amount, 0, ',', '.') . ".",
                'ingredient_id' => $lockedIngredient->id,
            ]);

            return $emergencyPurchase;
        });
    }
}

