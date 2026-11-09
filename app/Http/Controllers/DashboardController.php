<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\OrderRecap;
use App\Models\OrderRecapIngredientUsage;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily');

        // Statistik Stok Bahan Baku
        $totalIngredients = Ingredient::active()->count();
        $safeCount = Ingredient::safe()->count();
        $lowStockCount = Ingredient::lowStock()->count();
        $emptyCount = Ingredient::emptyStock()->count();

        // Rekap Pesanan Hari Ini
        $todaySubmittedRecaps = OrderRecap::whereDate('transaction_date', Carbon::today())
            ->where('status', 'submitted')
            ->get();
        $todaySubmittedCount = $todaySubmittedRecaps->count();
        $todayCupsSold = (int) $todaySubmittedRecaps->sum('total_items_quantity');
        $todayRecapsCount = OrderRecap::whereDate('transaction_date', Carbon::today())->count();

        // Total Pemakaian Bahan Baku Hari Ini (Cair: Liter, Padat: Kilogram)
        $todayUsages = OrderRecapIngredientUsage::select('quantity_used', 'unit_symbol_snapshot')
            ->whereHas('orderRecap', function ($q) {
                $q->whereDate('transaction_date', Carbon::today())
                  ->where('status', 'submitted');
            })
            ->get();

        $todayLiquidL = 0.0;
        $todaySolidKg = 0.0;

        foreach ($todayUsages as $usage) {
            $unit = strtolower(trim($usage->unit_symbol_snapshot));
            $qty = (float) $usage->quantity_used;

            if (in_array($unit, ['ml', 'mili', 'mililiter'])) {
                $todayLiquidL += $qty / 1000;
            } elseif (in_array($unit, ['l', 'liter', 'litre'])) {
                $todayLiquidL += $qty;
            } elseif (in_array($unit, ['g', 'gr', 'gram'])) {
                $todaySolidKg += $qty / 1000;
            } elseif (in_array($unit, ['kg', 'kilo', 'kilogram'])) {
                $todaySolidKg += $qty;
            }
        }

        $formatUsage = function (float $val, string $unit) {
            if ($val <= 0) {
                return "0 {$unit}";
            }
            $decimals = $val < 1 ? 2 : 1;
            return number_format($val, $decimals, ',', '.') . ' ' . $unit;
        };

        $todayLiquidFormatted = $formatUsage($todayLiquidL, 'L');
        $todaySolidFormatted = $formatUsage($todaySolidKg, 'Kg');

        // Bahan Baku dengan Stok Kritis (Habis & Menipis)
        $criticalIngredients = Ingredient::with(['category', 'unit'])
            ->active()
            ->where(function ($q) {
                $q->where('current_stock', '<=', 0)
                  ->orWhereColumn('current_stock', '<=', 'min_stock');
            })
            ->orderBy('current_stock', 'asc')
            ->take(5)
            ->get();

        // 5 Rekap Pesanan Terbaru
        $recentRecaps = OrderRecap::with(['user', 'items'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // 5 Aktivitas Pergerakan Stok Terbaru
        $recentMovements = StockMovement::with(['ingredient.unit', 'user'])
            ->latest('movement_date')
            ->take(6)
            ->get();

        // Data Pemakaian Bahan Terbanyak Hari Ini
        $topUsagesToday = OrderRecapIngredientUsage::with('ingredient')
            ->select(
                'ingredient_id',
                'ingredient_name_snapshot',
                'unit_symbol_snapshot',
                DB::raw('SUM(quantity_used) as total_used')
            )
            ->whereHas('orderRecap', function ($q) {
                $q->whereDate('transaction_date', Carbon::today())
                  ->where('status', 'submitted');
            })
            ->groupBy('ingredient_id', 'ingredient_name_snapshot', 'unit_symbol_snapshot')
            ->orderByDesc('total_used')
            ->take(5)
            ->get();

        // Kondisi Kapasitas Persediaan (4 Bahan Prioritas)
        $inventoryConditions = Ingredient::with(['unit'])
            ->active()
            ->orderByRaw("CASE WHEN current_stock <= 0 THEN 0 WHEN current_stock <= min_stock THEN 1 ELSE 2 END")
            ->orderBy('current_stock', 'asc')
            ->take(4)
            ->get();

        // Siapkan Data Grafik Pemakaian Berdasarkan Periode
        $chartData = $this->getUsageChartData($period);

        return view('dashboard.index', compact(
            'totalIngredients',
            'safeCount',
            'lowStockCount',
            'emptyCount',
            'todayRecapsCount',
            'todaySubmittedCount',
            'todayCupsSold',
            'todayLiquidL',
            'todaySolidKg',
            'todayLiquidFormatted',
            'todaySolidFormatted',
            'criticalIngredients',
            'recentRecaps',
            'recentMovements',
            'topUsagesToday',
            'inventoryConditions',
            'chartData',
            'period'
        ));
    }

    private function getUsageChartData(string $period): array
    {
        $labels = [];
        $datasets = [];

        if ($period === 'monthly') {
            // 6 bulan terakhir
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();

                $total = OrderRecapIngredientUsage::whereHas('orderRecap', function ($q) use ($start, $end) {
                    $q->whereBetween('transaction_date', [$start, $end])
                      ->where('status', 'submitted');
                })->sum('quantity_used');

                $datasets[] = round((float) $total, 2);
            }
        } elseif ($period === 'weekly') {
            // 4 minggu terakhir
            for ($i = 3; $i >= 0; $i--) {
                $date = Carbon::now()->subWeeks($i);
                $start = $date->copy()->startOfWeek();
                $end = $date->copy()->endOfWeek();
                $labels[] = 'Minggu ' . $date->format('W') . ' (' . $start->format('d M') . ')';

                $total = OrderRecapIngredientUsage::whereHas('orderRecap', function ($q) use ($start, $end) {
                    $q->whereBetween('transaction_date', [$start, $end])
                      ->where('status', 'submitted');
                })->sum('quantity_used');

                $datasets[] = round((float) $total, 2);
            }
        } else {
            // Daily: 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->translatedFormat('D, d M');

                $total = OrderRecapIngredientUsage::whereHas('orderRecap', function ($q) use ($date) {
                    $q->whereDate('transaction_date', $date->toDateString())
                      ->where('status', 'submitted');
                })->sum('quantity_used');

                $datasets[] = round((float) $total, 2);
            }
        }

        return [
            'labels' => $labels,
            'values' => $datasets,
        ];
    }
}
