<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    /**
     * Tambah stok (Restock)
     */
    public function restock(Ingredient $ingredient, float $quantity, User $user, ?string $notes = null): StockMovement
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Jumlah penambahan stok harus lebih besar dari 0.");
        }

        return DB::transaction(function () use ($ingredient, $quantity, $user, $notes) {
            $lockedIngredient = Ingredient::where('id', $ingredient->id)->lockForUpdate()->firstOrFail();

            $stockBefore = (float) $lockedIngredient->current_stock;
            $stockAfter = $stockBefore + $quantity;

            $lockedIngredient->current_stock = $stockAfter;
            $lockedIngredient->save();

            $movCode = 'MOV-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

            $movement = StockMovement::create([
                'movement_code' => $movCode,
                'ingredient_id' => $lockedIngredient->id,
                'type' => 'restock',
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'manual_restock',
                'reference_id' => null,
                'reference_code' => $movCode,
                'user_id' => $user->id,
                'movement_date' => now(),
                'notes' => $notes ?: "Penambahan stok masuk oleh {$user->name}",
            ]);

            // Jika sebelumnya habis/kritis dan sekarang aman, buat notifikasi info
            if ($stockBefore <= (float) $lockedIngredient->min_stock && $stockAfter > (float) $lockedIngredient->min_stock) {
                Notification::create([
                    'type' => 'stock_restocked',
                    'title' => 'Stok Dipulihkan',
                    'message' => "Stok {$lockedIngredient->name} telah ditambah sebanyak {$quantity} {$lockedIngredient->unit?->symbol}. Status kini AMAN ({$stockAfter} {$lockedIngredient->unit?->symbol}).",
                    'ingredient_id' => $lockedIngredient->id,
                ]);
            }

            return $movement;
        });
    }

    /**
     * Koreksi stok (Stock Correction / Opname) dengan alasan wajib
     */
    public function adjustStock(Ingredient $ingredient, float $targetStock, User $user, string $reason): StockMovement
    {
        if ($targetStock < 0) {
            throw new \InvalidArgumentException("Stok tidak boleh negatif.");
        }

        return DB::transaction(function () use ($ingredient, $targetStock, $user, $reason) {
            $lockedIngredient = Ingredient::where('id', $ingredient->id)->lockForUpdate()->firstOrFail();

            $stockBefore = (float) $lockedIngredient->current_stock;
            $diff = $targetStock - $stockBefore;

            if (abs($diff) < 0.0001) {
                throw new \InvalidArgumentException("Jumlah stok target sama dengan stok saat ini.");
            }

            $lockedIngredient->current_stock = $targetStock;
            $lockedIngredient->save();

            $isIncrease = $diff > 0;
            $movCode = 'MOV-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

            return StockMovement::create([
                'movement_code' => $movCode,
                'ingredient_id' => $lockedIngredient->id,
                'type' => $isIncrease ? 'adjustment_in' : 'adjustment_out',
                'quantity_in' => $isIncrease ? abs($diff) : 0,
                'quantity_out' => $isIncrease ? 0 : abs($diff),
                'stock_before' => $stockBefore,
                'stock_after' => $targetStock,
                'reference_type' => 'stock_adjustment',
                'reference_id' => null,
                'reference_code' => $movCode,
                'user_id' => $user->id,
                'movement_date' => now(),
                'notes' => "Koreksi stok: {$reason}",
            ]);
        });
    }
}
