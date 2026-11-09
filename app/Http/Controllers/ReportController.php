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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'current_stock');
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $search = $request->get('search');

        $data = $this->getReportData($type, $startDate, $endDate, $search);

        return view('reports.index', compact('type', 'startDate', 'endDate', 'search', 'data'));
    }

    public function export(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'current_stock');
        $startDate = $request->get('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::today()->toDateString());
        $search = $request->get('search');

        $data = $this->getReportData($type, $startDate, $endDate, $search, false);
        $filename = "laporan_{$type}_" . Carbon::now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($type, $data) {
            $handle = fopen('php://output', 'w');
            // Tambahkan BOM agar karakter terbaca rapi di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($type) {
                case 'current_stock':
                case 'critical_stock':
                    fputcsv($handle, ['Kode Bahan', 'Nama Bahan', 'Kategori', 'Stok Saat Ini', 'Batas Minimum', 'Satuan', 'Status']);
                    foreach ($data as $item) {
                        fputcsv($handle, [
                            $item->code,
                            $item->name,
                            $item->category?->name ?? '-',
                            $item->current_stock,
                            $item->min_stock,
                            $item->unit?->symbol ?? '',
                            ucfirst($item->status),
                        ]);
                    }
                    break;

                case 'stock_in':
                    fputcsv($handle, ['Kode Mutasi', 'Tanggal', 'Bahan Baku', 'Tipe', 'Jumlah Masuk', 'Satuan', 'Stok Sebelum', 'Stok Sesudah', 'Oleh', 'Keterangan']);
                    foreach ($data as $m) {
                        fputcsv($handle, [
                            $m->movement_code,
                            $m->movement_date->format('Y-m-d H:i'),
                            $m->ingredient?->name ?? '-',
                            $m->type_label,
                            $m->quantity_in,
                            $m->ingredient?->unit?->symbol ?? '',
                            $m->stock_before,
                            $m->stock_after,
                            $m->user?->name ?? '-',
                            $m->notes ?? '',
                        ]);
                    }
                    break;

                case 'usage':
                case 'top_used':
                    fputcsv($handle, ['Kode Bahan', 'Nama Bahan Baku', 'Total Pemakaian', 'Satuan']);
                    foreach ($data as $u) {
                        fputcsv($handle, [
                            $u->ingredient_code_snapshot,
                            $u->ingredient_name_snapshot,
                            $u->total_quantity,
                            $u->unit_symbol_snapshot,
                        ]);
                    }
                    break;

                case 'recaps':
                    fputcsv($handle, ['Kode Transaksi', 'Tanggal', 'Status', 'Jumlah Menu', 'Total Cup/Item', 'Barista/User', 'Waktu Kirim', 'Catatan']);
                    foreach ($data as $r) {
                        fputcsv($handle, [
                            $r->transaction_code,
                            $r->transaction_date->format('Y-m-d'),
                            $r->status_label,
                            $r->total_menus_count,
                            $r->total_items_quantity,
                            $r->user?->name ?? '-',
                            $r->submitted_at?->format('Y-m-d H:i') ?? '-',
                            $r->notes ?? '',
                        ]);
                    }
                    break;
            }

            fclose($handle);
        }, 200, $headers);
    }

    private function getReportData(string $type, string $startDate, string $endDate, ?string $search = null, bool $paginate = true)
    {
        switch ($type) {
            case 'current_stock':
                $q = Ingredient::with(['category', 'unit'])->active();
                if ($search) {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
                }
                return $paginate ? $q->orderBy('name')->paginate(15)->withQueryString() : $q->orderBy('name')->get();

            case 'critical_stock':
                $q = Ingredient::with(['category', 'unit'])->active()
                    ->where(function ($sub) {
                        $sub->where('current_stock', '<=', 0)
                            ->orWhereColumn('current_stock', '<=', 'min_stock');
                    });
                if ($search) {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
                }
                return $paginate ? $q->orderBy('current_stock')->paginate(15)->withQueryString() : $q->orderBy('current_stock')->get();

            case 'stock_in':
                $q = StockMovement::with(['ingredient.unit', 'user'])
                    ->whereIn('type', ['restock', 'adjustment_in', 'cancellation_reversal'])
                    ->whereDate('movement_date', '>=', $startDate)
                    ->whereDate('movement_date', '<=', $endDate);
                if ($search) {
                    $q->whereHas('ingredient', fn($sub) => $sub->where('name', 'like', "%{$search}%"));
                }
                return $paginate ? $q->latest('movement_date')->paginate(15)->withQueryString() : $q->latest('movement_date')->get();

            case 'usage':
            case 'top_used':
                $q = OrderRecapIngredientUsage::select(
                        'ingredient_code_snapshot',
                        'ingredient_name_snapshot',
                        'unit_symbol_snapshot',
                        DB::raw('SUM(quantity_used) as total_quantity'),
                        DB::raw('COUNT(DISTINCT order_recap_id) as total_transactions')
                    )
                    ->whereHas('orderRecap', function ($sub) use ($startDate, $endDate) {
                        $sub->whereBetween('transaction_date', [$startDate, $endDate])
                            ->where('status', 'submitted');
                    })
                    ->groupBy('ingredient_code_snapshot', 'ingredient_name_snapshot', 'unit_symbol_snapshot')
                    ->orderByDesc('total_quantity');

                if ($search) {
                    $q->where('ingredient_name_snapshot', 'like', "%{$search}%");
                }
                return $paginate ? $q->paginate(15)->withQueryString() : $q->get();

            case 'recaps':
                $q = OrderRecap::with(['user', 'items'])
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
                if ($search) {
                    $q->where('transaction_code', 'like', "%{$search}%");
                }
                return $paginate ? $q->latest('transaction_date')->paginate(15)->withQueryString() : $q->latest('transaction_date')->get();

            default:
                return collect();
        }
    }
}
