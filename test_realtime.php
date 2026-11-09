<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Ingredient;
use App\Services\EmergencyPurchaseService;

$user = User::where('role', 'barista')->first() ?? User::first();
$ing = Ingredient::first();
$service = app(EmergencyPurchaseService::class);

echo "Laravel default timezone: " . config('app.timezone') . "\n";
echo "Current Carbon::now() time: " . \Carbon\Carbon::now()->format('H:i:s') . "\n";
echo "Current Carbon::now('Asia/Jakarta') time: " . \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s') . "\n";

$purchase = $service->recordPurchase([
    'ingredient_id' => $ing->id,
    'quantity' => 1,
    'amount' => 10000,
    'purchase_location' => 'Toko Uji Jam',
    'purchase_date' => date('Y-m-d'),
], $user);

echo "Created EmergencyPurchase ID: " . $purchase->id . "\n";
echo "Saved purchase_time: " . $purchase->purchase_time . "\n";

// Rollback / delete test record & restore stock
$ing->current_stock = $ing->current_stock - 1;
$ing->save();
$purchase->stockMovement?->delete();
$purchase->delete();
echo "Test purchase cleaned up successfully.\n";

