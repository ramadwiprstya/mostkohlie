<?php

namespace App\Services;

use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\InsufficientStockException;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\OrderRecap;
use App\Models\OrderRecapIngredientUsage;
use App\Models\OrderRecapItem;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderRecapService
{
    /**
     * Normalisasi format items pesanan agar mendukung format list array maupun associative
     * [ ['menu_id' => 1, 'quantity' => 2] ] atau [ 1 => 2 ]
     */
    public function normalizeItems(array $items): array
    {
        $normalized = [];
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $menuId = $value['menu_id'] ?? $key;
                $quantity = (int) ($value['quantity'] ?? 0);
            } else {
                $menuId = $key;
                $quantity = (int) $value;
            }

            if (is_numeric($menuId) && (int) $menuId > 0 && $quantity > 0) {
                $normalized[] = [
                    'menu_id' => (int) $menuId,
                    'quantity' => $quantity,
                ];
            }
        }
        return $normalized;
    }

    /**
     * Hitung kebutuhan bahan berdasarkan item pesanan dan resep aktif
     */
    public function calculateIngredientsUsage(array $items): array
    {
        $items = $this->normalizeItems($items);
        if (empty($items)) {
            return [
                'requirements' => [],
                'is_all_sufficient' => true,
                'shortages' => [],
            ];
        }

        $requirements = [];
        $menuIds = array_column($items, 'menu_id');
        $menus = Menu::with(['activeRecipe.items.ingredient.unit', 'activeRecipe.items.unit'])
            ->whereIn('id', $menuIds)
            ->get()
            ->keyBy('id');

        foreach ($items as $item) {
            $menuId = $item['menu_id'];
            $qty = (int) ($item['quantity'] ?? 0);

            if ($qty <= 0 || !isset($menus[$menuId])) {
                continue;
            }

            $menu = $menus[$menuId];
            $recipe = $menu->activeRecipe;

            if (!$recipe || $recipe->items->isEmpty()) {
                continue;
            }

            foreach ($recipe->items as $recipeItem) {
                $ingId = $recipeItem->ingredient_id;
                $ing = $recipeItem->ingredient;
                if (!$ing) {
                    continue;
                }
                $needed = (float) $recipeItem->quantity * $qty;

                if (!isset($requirements[$ingId])) {
                    $unitSymbol = $ing->unit?->symbol ?? 'unit';
                    $requirements[$ingId] = [
                        'ingredient_id' => $ingId,
                        'ingredient_name' => $ing->name,
                        'ingredient_code' => $ing->code,
                        'unit_symbol' => $unitSymbol,
                        'unit' => $unitSymbol,
                        'current_stock' => (float) $ing->current_stock,
                        'available' => (float) $ing->current_stock,
                        'min_stock' => (float) $ing->min_stock,
                        'required_quantity' => 0.0,
                        'required' => 0.0,
                        'used_by_menus' => [],
                    ];
                }

                $requirements[$ingId]['required_quantity'] += $needed;
                $requirements[$ingId]['required'] += $needed;
                $requirements[$ingId]['used_by_menus'][] = [
                    'menu_name' => $menu->name,
                    'order_qty' => $qty,
                    'per_portion' => (float) $recipeItem->quantity,
                    'total_for_menu' => $needed,
                ];
            }
        }

        $shortages = [];
        $isAllSufficient = true;

        foreach ($requirements as $ingId => &$req) {
            $currentStock = $req['current_stock'];
            $required = $req['required_quantity'];
            $shortage = max(0, $required - $currentStock);
            $req['stock_after'] = $currentStock - $required;
            $req['is_sufficient'] = $currentStock >= $required;
            $req['shortage'] = $shortage;

            // Nilai terformat untuk tampilan UI
            $req['required_formatted'] = number_format($required, (floor($required) == $required ? 0 : 2), ',', '.');
            $req['available_formatted'] = number_format($currentStock, (floor($currentStock) == $currentStock ? 0 : 2), ',', '.');
            $req['shortage_formatted'] = number_format($shortage, (floor($shortage) == $shortage ? 0 : 2), ',', '.');
            $req['percentage'] = $currentStock > 0 ? min(100, (int) round(($required / $currentStock) * 100)) : 100;

            if (!$req['is_sufficient']) {
                $isAllSufficient = false;
                $shortages[] = [
                    'ingredient_name' => $req['ingredient_name'],
                    'ingredient_code' => $req['ingredient_code'],
                    'required' => $required,
                    'required_formatted' => $req['required_formatted'],
                    'available' => $currentStock,
                    'available_formatted' => $req['available_formatted'],
                    'shortage' => $shortage,
                    'shortage_formatted' => $req['shortage_formatted'],
                    'unit' => $req['unit_symbol'],
                ];
            }
        }
        unset($req);

        return [
            'requirements' => array_values($requirements),
            'is_all_sufficient' => $isAllSufficient,
            'shortages' => $shortages,
        ];
    }

    /**
     * Eksekusi penyimpanan rekap dan pengurangan stok secara atomik
     */
    public function processRecap(array $data, User $user): OrderRecap
    {
        $idempotencyToken = $data['idempotency_token'] ?? null;

        // Cegah eksekusi ganda jika token idempotensi sudah pernah tercatat
        if ($idempotencyToken) {
            $existing = OrderRecap::where('idempotency_token', $idempotencyToken)->first();
            if ($existing) {
                throw new DuplicateTransactionException("Transaksi dengan token ini sudah berhasil diproses sebelumnya (#{$existing->transaction_code}).");
            }
        }

        $items = $this->normalizeItems($data['items'] ?? []);
        if (empty($items)) {
            throw new \InvalidArgumentException("Pesanan tidak boleh kosong.");
        }
        $data['items'] = $items;

        // Ambil data menu dan resep aktif
        $menuIds = array_column($items, 'menu_id');
        $menus = Menu::with(['activeRecipe.items.ingredient.unit', 'activeRecipe.items.unit'])
            ->whereIn('id', $menuIds)
            ->get()
            ->keyBy('id');

        // Hitung total kebutuhan bahan yang sebenarnya di backend
        $aggregatedNeeds = []; // [ingredient_id => float]
        $recipeSnapshots = []; // [ingredient_id => details]

        foreach ($items as $item) {
            $menuId = $item['menu_id'];
            $qty = (int) ($item['quantity'] ?? 0);

            if ($qty <= 0) {
                continue;
            }

            if (!isset($menus[$menuId])) {
                throw new \InvalidArgumentException("Menu dengan ID #{$menuId} tidak ditemukan.");
            }

            $menu = $menus[$menuId];
            $recipe = $menu->activeRecipe;

            if (!$recipe || $recipe->items->isEmpty()) {
                throw new \InvalidArgumentException("Menu '{$menu->name}' belum memiliki resep aktif atau resep kosong.");
            }

            foreach ($recipe->items as $recipeItem) {
                $ingId = $recipeItem->ingredient_id;
                $portionNeed = (float) $recipeItem->quantity;
                $totalNeed = $portionNeed * $qty;

                $aggregatedNeeds[$ingId] = ($aggregatedNeeds[$ingId] ?? 0.0) + $totalNeed;

                if (!isset($recipeSnapshots[$ingId])) {
                    $recipeSnapshots[$ingId] = [
                        'ingredient_id' => $ingId,
                        'ingredient_name' => $recipeItem->ingredient->name,
                        'ingredient_code' => $recipeItem->ingredient->code,
                        'unit_symbol' => $recipeItem->ingredient->unit?->symbol ?? 'unit',
                        'components' => [],
                    ];
                }

                $recipeSnapshots[$ingId]['components'][] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'order_quantity' => $qty,
                    'per_portion' => $portionNeed,
                    'total_usage' => $totalNeed,
                ];
            }
        }

        if (empty($aggregatedNeeds)) {
            throw new \InvalidArgumentException("Tidak ada bahan yang terhitung untuk pesanan ini.");
        }

        // Jalankan transaksi database atomik dengan lockForUpdate
        return DB::transaction(function () use ($data, $user, $items, $menus, $aggregatedNeeds, $recipeSnapshots, $idempotencyToken) {
            // Urutkan ID bahan baku untuk menghindari deadlock antar transaksi bersamaan
            $ingredientIds = array_keys($aggregatedNeeds);
            sort($ingredientIds);

            // Kunci baris bahan baku di database
            $lockedIngredients = Ingredient::with('unit')
                ->whereIn('id', $ingredientIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi ketersediaan seluruh stok bahan
            $shortages = [];
            foreach ($aggregatedNeeds as $ingId => $requiredQty) {
                $ingredient = $lockedIngredients->get($ingId);

                if (!$ingredient) {
                    throw new \InvalidArgumentException("Bahan baku dengan ID #{$ingId} tidak ditemukan dalam database.");
                }

                $currentStock = (float) $ingredient->current_stock;
                if ($currentStock < $requiredQty) {
                    $shortages[] = [
                        'ingredient_name' => $ingredient->name,
                        'ingredient_code' => $ingredient->code,
                        'required' => $requiredQty,
                        'available' => $currentStock,
                        'shortage' => $requiredQty - $currentStock,
                        'unit' => $ingredient->unit?->symbol ?? 'unit',
                    ];
                }
            }

            // Jika ada bahan yang kurang, tolak seluruh transaksi tanpa mengurangi sedikitpun stok
            if (!empty($shortages)) {
                $errorList = array_map(function ($s) {
                    return "{$s['ingredient_name']} (butuh: {$s['required']} {$s['unit']}, sisa: {$s['available']} {$s['unit']})";
                }, $shortages);

                throw new InsufficientStockException(
                    "Stok bahan baku tidak mencukupi untuk memproses pesanan ini: " . implode(', ', $errorList),
                    $shortages
                );
            }

            // Buat kode transaksi unik: TR-YYYYMMDD-XXXX
            $datePrefix = Carbon::parse($data['transaction_date'] ?? now())->format('Ymd');
            $uniqueCode = 'TR-' . $datePrefix . '-' . strtoupper(Str::random(5));
            while (OrderRecap::where('transaction_code', $uniqueCode)->exists()) {
                $uniqueCode = 'TR-' . $datePrefix . '-' . strtoupper(Str::random(5));
            }

            // 1. Simpan OrderRecap
            $totalQty = array_sum(array_column($items, 'quantity'));
            $orderRecap = OrderRecap::create([
                'transaction_code' => $uniqueCode,
                'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                'shift' => $data['shift'] ?? 'pagi',
                'user_id' => $user->id,
                'status' => 'submitted',
                'total_menus_count' => count($items),
                'total_items_quantity' => $totalQty,
                'idempotency_token' => $idempotencyToken,
                'submitted_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            // 2. Simpan Item Pesanan (Snapshot)
            foreach ($items as $item) {
                $menu = $menus[$item['menu_id']];
                OrderRecapItem::create([
                    'order_recap_id' => $orderRecap->id,
                    'menu_id' => $menu->id,
                    'quantity' => (int) $item['quantity'],
                    'menu_code_snapshot' => $menu->code,
                    'menu_name_snapshot' => $menu->name,
                    'menu_price_snapshot' => (float) $menu->price,
                    'recipe_version_snapshot' => (int) ($menu->activeRecipe?->version ?? 1),
                ]);
            }

            // 3. Simpan Pemakaian Bahan & Kurangi Stok & Catat Pergerakan Stok
            foreach ($aggregatedNeeds as $ingId => $requiredQty) {
                $ingredient = $lockedIngredients->get($ingId);
                $stockBefore = (float) $ingredient->current_stock;
                $stockAfter = $stockBefore - $requiredQty;

                // Update stok di tabel ingredients
                $ingredient->current_stock = $stockAfter;
                $ingredient->save();

                $snapshotInfo = $recipeSnapshots[$ingId] ?? null;

                // Simpan detail pemakaian bahan
                OrderRecapIngredientUsage::create([
                    'order_recap_id' => $orderRecap->id,
                    'ingredient_id' => $ingredient->id,
                    'ingredient_code_snapshot' => $ingredient->code,
                    'ingredient_name_snapshot' => $ingredient->name,
                    'quantity_used' => $requiredQty,
                    'unit_symbol_snapshot' => $ingredient->unit?->symbol ?? 'unit',
                    'recipe_snapshot' => $snapshotInfo,
                ]);

                // Buat kode mutasi unik
                $movCode = 'MOV-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

                // Catat ke tabel stock_movements
                StockMovement::create([
                    'movement_code' => $movCode,
                    'ingredient_id' => $ingredient->id,
                    'type' => 'recap_usage',
                    'quantity_in' => 0,
                    'quantity_out' => $requiredQty,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_type' => 'order_recap',
                    'reference_id' => $orderRecap->id,
                    'reference_code' => $orderRecap->transaction_code,
                    'user_id' => $user->id,
                    'movement_date' => now(),
                    'notes' => "Pengurangan bahan otomatis untuk rekap {$orderRecap->transaction_code}",
                ]);

                // Periksa apakah stok menjadi menipis atau habis untuk membuat notifikasi
                if ($stockAfter <= 0) {
                    Notification::create([
                        'type' => 'stock_empty',
                        'title' => 'Stok Habis!',
                        'message' => "Stok {$ingredient->name} telah habis (0 {$ingredient->unit?->symbol}) setelah transaksi {$orderRecap->transaction_code}.",
                        'ingredient_id' => $ingredient->id,
                        'order_recap_id' => $orderRecap->id,
                    ]);
                } elseif ($stockAfter <= (float) $ingredient->min_stock) {
                    Notification::create([
                        'type' => 'stock_low',
                        'title' => 'Peringatan Stok Menipis',
                        'message' => "Stok {$ingredient->name} tersisa {$stockAfter} {$ingredient->unit?->symbol} (Batas minimum: {$ingredient->min_stock} {$ingredient->unit?->symbol}). Segera lakukan restock.",
                        'ingredient_id' => $ingredient->id,
                        'order_recap_id' => $orderRecap->id,
                    ]);
                }
            }

            return $orderRecap;
        });
    }

    /**
     * Batalkan transaksi dan kembalikan stok secara sah (Owner only)
     */
    public function cancelRecap(OrderRecap $recap, User $user, string $reason): OrderRecap
    {
        if ($recap->isCancelled()) {
            throw new \InvalidArgumentException("Transaksi ini sudah dibatalkan sebelumnya.");
        }

        return DB::transaction(function () use ($recap, $user, $reason) {
            $usages = $recap->ingredientUsages()->get();

            // Urutkan ID bahan baku
            $ingredientIds = $usages->pluck('ingredient_id')->sort()->values()->all();

            $lockedIngredients = Ingredient::with('unit')
                ->whereIn('id', $ingredientIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($usages as $usage) {
                $ingredient = $lockedIngredients->get($usage->ingredient_id);
                if (!$ingredient) {
                    continue;
                }

                $qtyToRestore = (float) $usage->quantity_used;
                $stockBefore = (float) $ingredient->current_stock;
                $stockAfter = $stockBefore + $qtyToRestore;

                $ingredient->current_stock = $stockAfter;
                $ingredient->save();

                $movCode = 'MOV-' . Carbon::now()->format('YmdHis') . '-' . strtoupper(Str::random(4));

                StockMovement::create([
                    'movement_code' => $movCode,
                    'ingredient_id' => $ingredient->id,
                    'type' => 'cancellation_reversal',
                    'quantity_in' => $qtyToRestore,
                    'quantity_out' => 0,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference_type' => 'order_recap_cancellation',
                    'reference_id' => $recap->id,
                    'reference_code' => $recap->transaction_code,
                    'user_id' => $user->id,
                    'movement_date' => now(),
                    'notes' => "Pembalikan stok akibat pembatalan rekap {$recap->transaction_code}: {$reason}",
                ]);
            }

            $recap->status = 'cancelled';
            $recap->cancelled_at = now();
            $recap->cancelled_by = $user->id;
            $recap->cancellation_reason = $reason;
            $recap->save();

            return $recap;
        });
    }
}
