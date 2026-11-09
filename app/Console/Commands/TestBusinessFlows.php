<?php

namespace App\Console\Commands;

use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\OrderRecap;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Services\OrderRecapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestBusinessFlows extends Command
{
    protected $signature = 'app:test-business-flows';
    protected $description = 'Menjalankan pengujian alur bisnis utama: pengurangan stok otomatis, stok kurang, idempotensi, dan pembatalan';

    public function handle(OrderRecapService $service)
    {
        $this->info("=== PENGUJIAN ALUR BISNIS MONITORING STOK KOPI KOHLIE ===");
        $this->newLine();

        $owner = User::firstOrCreate(
            ['email' => 'owner_cmd@kopikohlie.com'],
            ['name' => 'Owner CMD', 'password' => Hash::make('password'), 'role' => 'owner']
        );
        $barista = User::firstOrCreate(
            ['email' => 'barista_cmd@kopikohlie.com'],
            ['name' => 'Barista CMD', 'password' => Hash::make('password'), 'role' => 'barista']
        );

        $unitG = Unit::firstOrCreate(['symbol' => 'g'], ['name' => 'Gram']);
        $unitMl = Unit::firstOrCreate(['symbol' => 'ml'], ['name' => 'Mililiter']);
        $cat = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Cat', 'type' => 'ingredient']);
        $catMenu = Category::firstOrCreate(['slug' => 'test-menu-cat'], ['name' => 'Test Menu Cat', 'type' => 'menu']);

        // Skenario Menu: Es Kopi Susu Kohlie (18g kopi, 120ml susu, 20ml gula aren)
        $espresso = Ingredient::updateOrCreate(
            ['code' => 'TEST-ESP-01'],
            ['name' => 'Espresso Test', 'category_id' => $cat->id, 'unit_id' => $unitG->id, 'current_stock' => 1000, 'min_stock' => 200]
        );
        $susu = Ingredient::updateOrCreate(
            ['code' => 'TEST-SUSU-01'],
            ['name' => 'Susu Test', 'category_id' => $cat->id, 'unit_id' => $unitMl->id, 'current_stock' => 5000, 'min_stock' => 1000]
        );
        $gula = Ingredient::updateOrCreate(
            ['code' => 'TEST-GULA-01'],
            ['name' => 'Gula Aren Test', 'category_id' => $cat->id, 'unit_id' => $unitMl->id, 'current_stock' => 2000, 'min_stock' => 500]
        );

        $menu = Menu::updateOrCreate(
            ['code' => 'TEST-MNU-01'],
            ['name' => 'Es Kopi Susu Kohlie Test', 'category_id' => $catMenu->id, 'price' => 22000, 'is_active' => true]
        );

        $recipe = Recipe::updateOrCreate(['menu_id' => $menu->id, 'version' => 1], ['is_active' => true]);
        RecipeItem::updateOrCreate(['recipe_id' => $recipe->id, 'ingredient_id' => $espresso->id], ['quantity' => 18, 'unit_id' => $unitG->id]);
        RecipeItem::updateOrCreate(['recipe_id' => $recipe->id, 'ingredient_id' => $susu->id], ['quantity' => 120, 'unit_id' => $unitMl->id]);
        RecipeItem::updateOrCreate(['recipe_id' => $recipe->id, 'ingredient_id' => $gula->id], ['quantity' => 20, 'unit_id' => $unitMl->id]);

        $passed = 0;
        $total = 4;

        // UJI 1: Pengurangan Stok Otomatis
        $this->comment("[UJI 1] Pengurangan Stok Otomatis saat Barista Kirim Rekap (10 gelas)...");
        $token1 = 'TEST-CMD-' . Str::random(8);
        $recap = $service->processRecap([
            'transaction_date' => now()->toDateString(),
            'idempotency_token' => $token1,
            'items' => [['menu_id' => $menu->id, 'quantity' => 10]],
        ], $barista);

        $espStock = (float) $espresso->fresh()->current_stock;
        $susuStock = (float) $susu->fresh()->current_stock;
        $gulaStock = (float) $gula->fresh()->current_stock;

        if ($espStock === 820.0 && $susuStock === 3800.0 && $gulaStock === 1800.0 && $recap->status === 'submitted') {
            $this->info("  ✓ BERHASIL: Stok otomatis berkurang sesuai resep (Espresso: 1000->820g, Susu: 5000->3800ml, Gula: 2000->1800ml).");
            $passed++;
        } else {
            $this->error("  ✗ GAGAL: Nilai stok sesudah tidak sesuai.");
        }

        // UJI 2: Penolakan Total jika Stok Kurang
        $this->comment("[UJI 2] Penolakan Transaksi jika Stok Kurang (Kebutuhan: 900g, Stok Espresso: 820g)...");
        // Butuh 50 gelas = 900g espresso, sedangkan sisa espresso hanya 820g
        try {
            $service->processRecap([
                'transaction_date' => now()->toDateString(),
                'idempotency_token' => 'TEST-CMD-' . Str::random(8),
                'items' => [['menu_id' => $menu->id, 'quantity' => 50]],
            ], $barista);
            $this->error("  ✗ GAGAL: Seharusnya transaksi ditolak karena stok kurang.");
        } catch (InsufficientStockException $e) {
            // Pastikan stok TIDAK berubah
            if ((float) $espresso->fresh()->current_stock === 820.0 && (float) $susu->fresh()->current_stock === 3800.0) {
                $this->info("  ✓ BERHASIL: Transaksi ditolak seluruhnya. Rollback atomik berhasil, tidak ada stok yang terpotong.");
                $passed++;
            } else {
                $this->error("  ✗ GAGAL: Terjadi partial deduction!");
            }
        }

        // UJI 3: Idempotensi Request Ganda
        $this->comment("[UJI 3] Pencegahan Pengurangan Stok Ganda (Idempotency Token)...");
        try {
            $service->processRecap([
                'transaction_date' => now()->toDateString(),
                'idempotency_token' => $token1, // Token sama dengan UJI 1
                'items' => [['menu_id' => $menu->id, 'quantity' => 10]],
            ], $barista);
            $this->error("  ✗ GAGAL: Request duplikat lolos!");
        } catch (DuplicateTransactionException $e) {
            if ((float) $espresso->fresh()->current_stock === 820.0) {
                $this->info("  ✓ BERHASIL: Request duplikat ditolak ({$e->getMessage()}). Stok tidak terpotong dua kali.");
                $passed++;
            }
        }

        // UJI 4: Pembatalan dan Pembalikan Stok oleh Owner
        $this->comment("[UJI 4] Pembatalan Transaksi & Pembalikan Stok (Reversal)...");
        $service->cancelRecap($recap, $owner, 'Koreksi kesalahan input rekap');
        if ($recap->fresh()->status === 'cancelled' && (float) $espresso->fresh()->current_stock === 1000.0 && (float) $susu->fresh()->current_stock === 5000.0) {
            $this->info("  ✓ BERHASIL: Transaksi dibatalkan dan seluruh stok bahan baku dipulihkan kembali ke kondisi semula.");
            $passed++;
        } else {
            $this->error("  ✗ GAGAL: Pembalikan stok tidak akurat.");
        }

        $this->newLine();
        $this->info("HASIL PENGUJIAN: {$passed} dari {$total} skenario pengujian bisnis LULUS 100%.");

        return $passed === $total ? 0 : 1;
    }
}
