<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Notification;
use App\Models\ProcurementRequest;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IngredientController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'rekap'); // 'rekap' | 'menunggu' | 'master'
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // 1. Data Rekap Penerimaan Bahan (Selesai Diterima)
        $receiptsQuery = ProcurementRequest::with(['ingredient.unit', 'ingredient.category', 'user', 'approver', 'receiver', 'stockMovement'])
            ->completed()
            ->latest('received_at');

        if ($search && $activeTab !== 'master') {
            $receiptsQuery->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('supplier_name', 'like', "%{$search}%")
                  ->orWhereHas('ingredient', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('receiver', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($startDate) {
            $receiptsQuery->whereDate('received_at', '>=', $startDate);
        }
        if ($endDate) {
            $receiptsQuery->whereDate('received_at', '<=', $endDate);
        }

        $completedReceipts = $receiptsQuery->paginate(10, ['*'], 'receipt_page')->withQueryString();

        // 2. Data Pengadaan Menunggu Kedatangan / Siap Diterima (Status: diproses)
        $waitingArrivals = ProcurementRequest::with(['ingredient.unit', 'ingredient.category', 'user', 'approver'])
            ->inProgress()
            ->latest('reviewed_at')
            ->get();

        // 3. Data Master Bahan Baku (Untuk tab Master)
        $query = Ingredient::with(['category', 'unit']);
        if ($search && $activeTab === 'master') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($status === 'aman') {
            $query->whereColumn('current_stock', '>', 'min_stock');
        } elseif ($status === 'menipis') {
            $query->where('current_stock', '>', 0)
                  ->whereColumn('current_stock', '<=', 'min_stock');
        } elseif ($status === 'habis') {
            $query->where('current_stock', '<=', 0);
        }
        $ingredients = $query->orderBy('name', 'asc')->paginate(12, ['*'], 'ingredient_page')->withQueryString();

        // 4. KPI Statistics
        $totalReceiptsCount = ProcurementRequest::completed()->count();
        $waitingArrivalCount = $waitingArrivals->count();
        $thisMonthReceiptsCount = ProcurementRequest::completed()
            ->whereMonth('received_at', Carbon::now('Asia/Jakarta')->month)
            ->whereYear('received_at', Carbon::now('Asia/Jakarta')->year)
            ->count();
        $todayReceiptsCount = ProcurementRequest::completed()
            ->whereDate('received_at', Carbon::now('Asia/Jakarta')->toDateString())
            ->count();

        $categories = Category::forIngredients()->orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $allActiveIngredients = Ingredient::with('unit')->active()->orderBy('name')->get();

        return view('ingredients.index', compact(
            'activeTab',
            'completedReceipts',
            'waitingArrivals',
            'ingredients',
            'allActiveIngredients',
            'categories',
            'units',
            'search',
            'categoryId',
            'status',
            'startDate',
            'endDate',
            'totalReceiptsCount',
            'waitingArrivalCount',
            'thisMonthReceiptsCount',
            'todayReceiptsCount'
        ));
    }

    /**
     * Catat Penerimaan Bahan Langsung (Direct Goods Receipt)
     */
    public function directReceive(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity_received' => ['required', 'numeric', 'gt:0'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'expiry_date' => ['nullable', 'date'],
            'receipt_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'ingredient_id.required' => 'Bahan baku wajib dipilih.',
            'quantity_received.required' => 'Jumlah barang diterima wajib diisi.',
            'quantity_received.gt' => 'Jumlah harus lebih besar dari 0.',
            'supplier_name.required' => 'Nama supplier / pemasok wajib diisi.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $ingredient = Ingredient::lockForUpdate()->findOrFail($validated['ingredient_id']);
            $qty = (float) $validated['quantity_received'];
            $stockBefore = (float) $ingredient->current_stock;
            $stockAfter = $stockBefore + $qty;

            // Tambah stok
            $ingredient->current_stock = $stockAfter;
            $ingredient->save();

            // Kode unik penerimaan
            $datePrefix = Carbon::now('Asia/Jakarta')->format('Ymd');
            $movCode = 'MOV-' . Carbon::now('Asia/Jakarta')->format('YmdHis') . '-' . strtoupper(Str::random(4));
            $reqCode = 'RCV-' . $datePrefix . '-' . strtoupper(Str::random(5));

            $stockMovement = StockMovement::create([
                'movement_code' => $movCode,
                'ingredient_id' => $ingredient->id,
                'type' => 'restock',
                'quantity_in' => $qty,
                'quantity_out' => 0,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'direct_receipt',
                'reference_id' => null,
                'reference_code' => $reqCode,
                'user_id' => $request->user()->id,
                'movement_date' => Carbon::now('Asia/Jakarta'),
                'notes' => "Penerimaan langsung ({$reqCode}) dari {$validated['supplier_name']}. No Invoice: " . ($validated['invoice_number'] ?? '-'),
            ]);

            // Catat ke ProcurementRequest dengan status 'selesai' agar terekap sempurna
            ProcurementRequest::create([
                'request_code' => $reqCode,
                'ingredient_id' => $ingredient->id,
                'user_id' => $request->user()->id,
                'quantity_requested' => $qty,
                'stock_at_request' => $stockBefore,
                'min_stock_at_request' => (float) $ingredient->min_stock,
                'required_date' => Carbon::now('Asia/Jakarta')->toDateString(),
                'priority' => 'normal',
                'reason' => 'Penerimaan pasokan barang langsung (di luar alur request pengadaan)',
                'status' => 'selesai',
                'approved_by' => $request->user()->id,
                'reviewed_at' => Carbon::now('Asia/Jakarta'),
                'supplier_name' => $validated['supplier_name'],
                'quantity_received' => $qty,
                'received_by' => $request->user()->id,
                'received_at' => Carbon::now('Asia/Jakarta'),
                'expiry_date' => $validated['expiry_date'] ?? null,
                'invoice_number' => $validated['invoice_number'] ?? null,
                'receipt_notes' => $validated['receipt_notes'] ?? null,
                'stock_movement_id' => $stockMovement->id,
            ]);

            Notification::create([
                'type' => 'stock_restocked',
                'title' => "Penerimaan Bahan Langsung: {$ingredient->name}",
                'message' => "Sebanyak {$qty} {$ingredient->unit?->symbol} '{$ingredient->name}' berhasil diterima dari {$validated['supplier_name']}. Stok bertambah menjadi {$stockAfter} {$ingredient->unit?->symbol}.",
                'ingredient_id' => $ingredient->id,
                'is_read' => false,
            ]);

            return redirect()->route('ingredients.index', ['tab' => 'rekap'])
                ->with('success', "Penerimaan '{$ingredient->name}' sebanyak {$qty} {$ingredient->unit?->symbol} berhasil disimpan! Stok sistem otomatis diperbarui.");
        });
    }

    public function store(Request $request)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:ingredients,code'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode bahan wajib diisi.',
            'code.unique' => 'Kode bahan sudah digunakan.',
            'name.required' => 'Nama bahan wajib diisi.',
            'category_id.required' => 'Kategori bahan wajib dipilih.',
            'unit_id.required' => 'Satuan dasar wajib dipilih.',
            'current_stock.min' => 'Stok tidak boleh negatif.',
            'min_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $ingredient = Ingredient::create($validated);

        // Catat mutasi awal jika ada stok
        if ($ingredient->current_stock > 0) {
            $this->stockService->restock(
                $ingredient,
                (float) $ingredient->current_stock,
                $request->user(),
                'Saldo awal input bahan baru'
            );
        }

        return redirect()->route('ingredients.index')->with('success', "Bahan baku '{$ingredient->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorizeOwner();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('ingredients', 'code')->ignore($ingredient->id)],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'min_stock' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ], [
            'code.required' => 'Kode bahan wajib diisi.',
            'code.unique' => 'Kode bahan sudah digunakan.',
            'name.required' => 'Nama bahan wajib diisi.',
            'min_stock.min' => 'Batas minimum stok tidak boleh negatif.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $ingredient->update($validated);

        return redirect()->route('ingredients.index')->with('success', "Data bahan baku '{$ingredient->name}' berhasil diperbarui.");
    }

    public function destroy(Ingredient $ingredient)
    {
        $this->authorizeOwner();

        if ($ingredient->recipeItems()->exists()) {
            return redirect()->back()->with('error', "Bahan baku '{$ingredient->name}' tidak dapat dihapus karena masih digunakan dalam resep menu.");
        }

        $name = $ingredient->name;
        $ingredient->delete();

        return redirect()->route('ingredients.index')->with('success', "Bahan baku '{$name}' berhasil dihapus.");
    }

    /**
     * Tambah Stok (Restock)
     */
    public function restock(Request $request, Ingredient $ingredient)
    {
        $this->authorizeOwner();

        $request->validate([
            'quantity' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'quantity.required' => 'Jumlah stok masuk wajib diisi.',
            'quantity.gt' => 'Jumlah stok masuk harus lebih besar dari 0.',
        ]);

        $movement = $this->stockService->restock(
            $ingredient,
            (float) $request->quantity,
            $request->user(),
            $request->notes
        );

        return redirect()->back()->with('success', "Berhasil menambahkan stok {$ingredient->name} sebanyak {$request->quantity} {$ingredient->unit?->symbol}.");
    }

    /**
     * Koreksi Stok (Stock Opname / Adjustment)
     */
    public function adjust(Request $request, Ingredient $ingredient)
    {
        $this->authorizeOwner();

        $request->validate([
            'target_stock' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'target_stock.required' => 'Stok hasil penyesuaian wajib diisi.',
            'target_stock.min' => 'Stok tidak boleh bernilai negatif.',
            'reason.required' => 'Alasan penyesuaian stok wajib diisi.',
            'reason.min' => 'Alasan penyesuaian minimal 5 karakter.',
        ]);

        try {
            $this->stockService->adjustStock(
                $ingredient,
                (float) $request->target_stock,
                $request->user(),
                $request->reason
            );

            return redirect()->back()->with('success', "Stok {$ingredient->name} berhasil dikoreksi menjadi {$request->target_stock} {$ingredient->unit?->symbol}.");
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function authorizeOwner(): void
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang mengelola data bahan dan stok minimum.');
        }
    }
}
