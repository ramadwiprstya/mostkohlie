<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmergencyPurchaseController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderRecapController;
use App\Http\Controllers\ProcurementRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Monitoring Stok (Akses Barista & Owner)
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');

    // Input & Riwayat Rekap Pesanan (Aksi Utama Barista & Owner)
    Route::get('/order-recaps', [OrderRecapController::class, 'index'])->name('order-recaps.index');
    Route::get('/order-recaps/create', [OrderRecapController::class, 'create'])->name('order-recaps.create');
    Route::post('/order-recaps/preview', [OrderRecapController::class, 'preview'])->name('order-recaps.preview');
    Route::post('/order-recaps', [OrderRecapController::class, 'store'])->name('order-recaps.store');
    Route::get('/order-recaps/{orderRecap}', [OrderRecapController::class, 'show'])->name('order-recaps.show');

    // Pengadaan & Pembelian Darurat
    Route::get('/procurement-requests', [ProcurementRequestController::class, 'index'])->name('procurement-requests.index');
    Route::get('/procurement-requests/create', [ProcurementRequestController::class, 'create'])->name('procurement-requests.create');
    Route::post('/procurement-requests', [ProcurementRequestController::class, 'store'])->name('procurement-requests.store');
    Route::post('/procurement-requests/{procurementRequest}/receive', [ProcurementRequestController::class, 'receive'])->name('procurement-requests.receive');

    Route::get('/emergency-purchases', [EmergencyPurchaseController::class, 'index'])->name('emergency-purchases.index');
    Route::get('/emergency-purchases/create', [EmergencyPurchaseController::class, 'create'])->name('emergency-purchases.create');
    Route::post('/emergency-purchases', [EmergencyPurchaseController::class, 'store'])->name('emergency-purchases.store');
    Route::get('/emergency-purchases/{emergencyPurchase}', [EmergencyPurchaseController::class, 'show'])->name('emergency-purchases.show');

    // Master Data (Read-only untuk Barista)
    Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
    Route::post('/ingredients/direct-receive', [IngredientController::class, 'directReceive'])->name('ingredients.direct-receive');
    Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/recipe', [MenuController::class, 'recipeIndex'])->name('menus.recipe.index');
    Route::get('/menus/{menu}/recipe', [MenuController::class, 'recipe'])->name('menus.recipe');

    // Riwayat Mutasi Stok
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');

    // Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Owner-Only Actions
    Route::middleware('role:owner')->group(function () {
        // Master Bahan Baku
        Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
        Route::post('/ingredients/{ingredient}/restock', [IngredientController::class, 'restock'])->name('ingredients.restock');
        Route::post('/ingredients/{ingredient}/adjust', [IngredientController::class, 'adjust'])->name('ingredients.adjust');

        // Master Menu & Resep
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::post('/menus/{menu}/recipe', [MenuController::class, 'updateRecipe'])->name('menus.update-recipe');

        // Pembatalan Rekap dengan Pembalikan Stok
        Route::post('/order-recaps/{orderRecap}/cancel', [OrderRecapController::class, 'cancel'])->name('order-recaps.cancel');

        // Persetujuan & Penolakan Request Pengadaan
        Route::post('/procurement-requests/{procurementRequest}/approve', [ProcurementRequestController::class, 'approve'])->name('procurement-requests.approve');
        Route::post('/procurement-requests/{procurementRequest}/reject', [ProcurementRequestController::class, 'reject'])->name('procurement-requests.reject');

        // Manajemen Pengguna
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });
});
