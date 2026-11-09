<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\OrderRecap;
use App\Models\OrderRecapIngredientUsage;
use App\Models\OrderRecapItem;
use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $owner = User::firstOrCreate(
            ['email' => 'owner@kopikohlie.com'],
            [
                'name' => 'Andra (Owner)',
                'password' => Hash::make('kohlie123'),
                'role' => 'owner',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $barista = User::firstOrCreate(
            ['email' => 'barista@kopikohlie.com'],
            [
                'name' => 'Bima (Barista)',
                'password' => Hash::make('password'),
                'role' => 'barista',
                'phone' => '089876543210',
                'is_active' => true,
            ]
        );

        // 2. Units
        $unitGram = Unit::firstOrCreate(['symbol' => 'g'], ['name' => 'Gram', 'description' => 'Satuan berat gram']);
        $unitMl = Unit::firstOrCreate(['symbol' => 'ml'], ['name' => 'Mililiter', 'description' => 'Satuan volume mililiter']);
        $unitPcs = Unit::firstOrCreate(['symbol' => 'pcs'], ['name' => 'Pieces', 'description' => 'Satuan buah / cup']);
        $unitShot = Unit::firstOrCreate(['symbol' => 'shot'], ['name' => 'Shot', 'description' => 'Satuan takaran shot']);

        // 3. Categories
        $catCoffee = Category::firstOrCreate(['slug' => 'kopi-espresso'], ['name' => 'Kopi & Espresso', 'type' => 'ingredient', 'description' => 'Biji kopi pilihan dan roast bean']);
        $catDairy = Category::firstOrCreate(['slug' => 'susu-dairy'], ['name' => 'Susu & Dairy', 'type' => 'ingredient', 'description' => 'Susu hewani, nabati, dan krim']);
        $catSyrup = Category::firstOrCreate(['slug' => 'sirup-pemanis'], ['name' => 'Sirup & Pemanis', 'type' => 'ingredient', 'description' => 'Pemanis alami dan sirup perisa']);
        $catPowder = Category::firstOrCreate(['slug' => 'powder-teh'], ['name' => 'Powder & Teh', 'type' => 'ingredient', 'description' => 'Bubuk matcha, cokelat, dan daun teh']);
        $catPack = Category::firstOrCreate(['slug' => 'packaging-disposable'], ['name' => 'Packaging & Disposable', 'type' => 'ingredient', 'description' => 'Cup, tutup, sedotan']);

        $catMenuSignature = Category::firstOrCreate(['slug' => 'signature-coffee'], ['name' => 'Signature Coffee', 'type' => 'menu', 'description' => 'Menu signature racikan Kohlie']);
        $catMenuClassic = Category::firstOrCreate(['slug' => 'classic-coffee'], ['name' => 'Classic Coffee', 'type' => 'menu', 'description' => 'Menu kopi klasik']);
        $catMenuNonCoffee = Category::firstOrCreate(['slug' => 'non-coffee'], ['name' => 'Non-Coffee', 'type' => 'menu', 'description' => 'Minuman segar non kopi']);

        // 4. Ingredients
        $ingredientsData = [
            [
                'code' => 'ING-001',
                'name' => 'Biji Kopi Arabika House Blend',
                'category_id' => $catCoffee->id,
                'unit_id' => $unitGram->id,
                'current_stock' => 15000,
                'min_stock' => 2000,
                'notes' => '100% Arabika Gayo & Mandheling Medium Dark',
            ],
            [
                'code' => 'ING-002',
                'name' => 'Susu UHT Full Cream',
                'category_id' => $catDairy->id,
                'unit_id' => $unitMl->id,
                'current_stock' => 25000,
                'min_stock' => 5000,
                'notes' => 'Susu segar pasteurisasi kemasan 1 liter',
            ],
            [
                'code' => 'ING-003',
                'name' => 'Gula Aren Cair Kohlie',
                'category_id' => $catSyrup->id,
                'unit_id' => $unitMl->id,
                'current_stock' => 10000,
                'min_stock' => 2000,
                'notes' => 'Gula aren murni tanpa bahan pengawet',
            ],
            [
                'code' => 'ING-004',
                'name' => 'Sirup Vanilla Premium',
                'category_id' => $catSyrup->id,
                'unit_id' => $unitMl->id,
                'current_stock' => 3500,
                'min_stock' => 1000,
                'notes' => 'Sirup perisa madagaskar vanilla',
            ],
            [
                'code' => 'ING-005',
                'name' => 'Sirup Caramel Artisan',
                'category_id' => $catSyrup->id,
                'unit_id' => $unitMl->id,
                'current_stock' => 800, // Menipis
                'min_stock' => 1000,
                'notes' => 'Sirup karamel panggang gurih manis',
            ],
            [
                'code' => 'ING-006',
                'name' => 'Bubuk Matcha Uji Premium',
                'category_id' => $catPowder->id,
                'unit_id' => $unitGram->id,
                'current_stock' => 350, // Menipis
                'min_stock' => 500,
                'notes' => 'Ceremonial grade Matcha impor Jepang',
            ],
            [
                'code' => 'ING-007',
                'name' => 'Bubuk Cokelat Belgia',
                'category_id' => $catPowder->id,
                'unit_id' => $unitGram->id,
                'current_stock' => 2800,
                'min_stock' => 800,
                'notes' => 'Dark chocolate powder 70% cocoa',
            ],
            [
                'code' => 'ING-008',
                'name' => 'Biji Kopi Robusta Dampit',
                'category_id' => $catCoffee->id,
                'unit_id' => $unitGram->id,
                'current_stock' => 0, // Habis
                'min_stock' => 1500,
                'notes' => 'Single origin Robusta Dampit Malang',
            ],
            [
                'code' => 'ING-009',
                'name' => 'Es Batu Kristal Higienis',
                'category_id' => $catDairy->id,
                'unit_id' => $unitGram->id,
                'current_stock' => 35000,
                'min_stock' => 8000,
                'notes' => 'Ice tube kristal supplier harian',
            ],
            [
                'code' => 'ING-010',
                'name' => 'Cup Kohlie 16oz + Tutup Lid',
                'category_id' => $catPack->id,
                'unit_id' => $unitPcs->id,
                'current_stock' => 600,
                'min_stock' => 150,
                'notes' => 'Cold cup sablon logo Kopi Kohlie',
            ],
            [
                'code' => 'ING-011',
                'name' => 'Sedotan Paper Eco',
                'category_id' => $catPack->id,
                'unit_id' => $unitPcs->id,
                'current_stock' => 850,
                'min_stock' => 200,
                'notes' => 'Paper straw biodegradable ramah lingkungan',
            ],
        ];

        $ingredients = [];
        foreach ($ingredientsData as $data) {
            $ing = Ingredient::firstOrCreate(['code' => $data['code']], $data);
            $ingredients[$data['code']] = $ing;

            // Catat pergerakan awal jika belum ada
            if ($ing->wasRecentlyCreated) {
                StockMovement::create([
                    'movement_code' => 'MOV-INIT-' . $ing->code,
                    'ingredient_id' => $ing->id,
                    'type' => 'restock',
                    'quantity_in' => $ing->current_stock,
                    'quantity_out' => 0,
                    'stock_before' => 0,
                    'stock_after' => $ing->current_stock,
                    'reference_type' => 'initial_inventory',
                    'reference_code' => 'INIT-STOCK',
                    'user_id' => $owner->id,
                    'movement_date' => Carbon::now()->subDays(3),
                    'notes' => 'Saldo awal stok bahan baku Kopi Kohlie',
                ]);
            }
        }

        // 5. Menus & Recipes
        $menusData = [
            [
                'code' => 'MNU-001',
                'name' => 'Es Kopi Susu Kohlie',
                'category_id' => $catMenuSignature->id,
                'price' => 22000,
                'description' => 'Menu andalan Kopi Kohlie: paduan espresso house blend, susu creamy, dan gula aren organik.',
                'recipe' => [
                    ['ing' => 'ING-001', 'qty' => 18, 'unit' => $unitGram->id],
                    ['ing' => 'ING-002', 'qty' => 120, 'unit' => $unitMl->id],
                    ['ing' => 'ING-003', 'qty' => 20, 'unit' => $unitMl->id],
                    ['ing' => 'ING-009', 'qty' => 100, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
            [
                'code' => 'MNU-002',
                'name' => 'Iced Americano',
                'category_id' => $catMenuClassic->id,
                'price' => 18000,
                'description' => 'Double shot espresso murni dengan air dingin dan es batu segar.',
                'recipe' => [
                    ['ing' => 'ING-001', 'qty' => 18, 'unit' => $unitGram->id],
                    ['ing' => 'ING-009', 'qty' => 120, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
            [
                'code' => 'MNU-003',
                'name' => 'Iced Vanilla Latte',
                'category_id' => $catMenuClassic->id,
                'price' => 25000,
                'description' => 'Espresso dengan susu segar lembut dan aroma manis sirup vanila Madagaskar.',
                'recipe' => [
                    ['ing' => 'ING-001', 'qty' => 18, 'unit' => $unitGram->id],
                    ['ing' => 'ING-002', 'qty' => 130, 'unit' => $unitMl->id],
                    ['ing' => 'ING-004', 'qty' => 20, 'unit' => $unitMl->id],
                    ['ing' => 'ING-009', 'qty' => 100, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
            [
                'code' => 'MNU-004',
                'name' => 'Caramel Macchiato Iced',
                'category_id' => $catMenuSignature->id,
                'price' => 28000,
                'description' => 'Lapisan susu lembut beraroma vanila dipadukan espresso kaya rasa dan drizzle karamel.',
                'recipe' => [
                    ['ing' => 'ING-001', 'qty' => 18, 'unit' => $unitGram->id],
                    ['ing' => 'ING-002', 'qty' => 140, 'unit' => $unitMl->id],
                    ['ing' => 'ING-004', 'qty' => 10, 'unit' => $unitMl->id],
                    ['ing' => 'ING-005', 'qty' => 15, 'unit' => $unitMl->id],
                    ['ing' => 'ING-009', 'qty' => 100, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
            [
                'code' => 'MNU-005',
                'name' => 'Uji Matcha Latte',
                'category_id' => $catMenuNonCoffee->id,
                'price' => 26000,
                'description' => 'Matcha autentik Jepang dengan susu segar pilihan dan manis seimbang.',
                'recipe' => [
                    ['ing' => 'ING-006', 'qty' => 15, 'unit' => $unitGram->id],
                    ['ing' => 'ING-002', 'qty' => 150, 'unit' => $unitMl->id],
                    ['ing' => 'ING-003', 'qty' => 15, 'unit' => $unitMl->id],
                    ['ing' => 'ING-009', 'qty' => 100, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
            [
                'code' => 'MNU-006',
                'name' => 'Belgian Chocolate Iced',
                'category_id' => $catMenuNonCoffee->id,
                'price' => 26000,
                'description' => 'Minuman cokelat Belgia murni yang kental, pekat, dan memanjakan lidah.',
                'recipe' => [
                    ['ing' => 'ING-007', 'qty' => 25, 'unit' => $unitGram->id],
                    ['ing' => 'ING-002', 'qty' => 160, 'unit' => $unitMl->id],
                    ['ing' => 'ING-003', 'qty' => 10, 'unit' => $unitMl->id],
                    ['ing' => 'ING-009', 'qty' => 100, 'unit' => $unitGram->id],
                    ['ing' => 'ING-010', 'qty' => 1, 'unit' => $unitPcs->id],
                    ['ing' => 'ING-011', 'qty' => 1, 'unit' => $unitPcs->id],
                ],
            ],
        ];

        foreach ($menusData as $m) {
            $menu = Menu::firstOrCreate(
                ['code' => $m['code']],
                [
                    'name' => $m['name'],
                    'category_id' => $m['category_id'],
                    'price' => $m['price'],
                    'description' => $m['description'],
                    'is_active' => true,
                ]
            );

            // Resep
            $recipe = Recipe::firstOrCreate(
                ['menu_id' => $menu->id, 'version' => 1],
                ['is_active' => true, 'notes' => 'Resep standar resmi Kopi Kohlie v1']
            );

            foreach ($m['recipe'] as $rItem) {
                if (isset($ingredients[$rItem['ing']])) {
                    RecipeItem::firstOrCreate(
                        [
                            'recipe_id' => $recipe->id,
                            'ingredient_id' => $ingredients[$rItem['ing']]->id,
                        ],
                        [
                            'quantity' => $rItem['qty'],
                            'unit_id' => $rItem['unit'],
                        ]
                    );
                }
            }
        }

        // 6. Notifikasi Awal untuk Bahan Kritis
        Notification::firstOrCreate(
            ['type' => 'stock_empty', 'ingredient_id' => $ingredients['ING-008']->id],
            [
                'title' => 'Stok Habis: Biji Kopi Robusta',
                'message' => 'Stok Biji Kopi Robusta Dampit telah habis (0 g). Harap segera hubungi supplier.',
                'is_read' => false,
            ]
        );

        Notification::firstOrCreate(
            ['type' => 'stock_low', 'ingredient_id' => $ingredients['ING-006']->id],
            [
                'title' => 'Peringatan: Bubuk Matcha Menipis',
                'message' => 'Stok Bubuk Matcha Uji tersisa 350 g (batas aman: 500 g).',
                'is_read' => false,
            ]
        );

        Notification::firstOrCreate(
            ['type' => 'stock_low', 'ingredient_id' => $ingredients['ING-005']->id],
            [
                'title' => 'Peringatan: Sirup Caramel Menipis',
                'message' => 'Stok Sirup Caramel Artisan tersisa 800 ml (batas aman: 1.000 ml).',
                'is_read' => false,
            ]
        );
    }
}
