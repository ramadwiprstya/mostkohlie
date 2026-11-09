<?php

namespace App\Http\Controllers;

use App\Models\EmergencyPurchase;
use App\Models\Ingredient;
use App\Models\User;
use App\Services\EmergencyPurchaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmergencyPurchaseController extends Controller
{
    protected EmergencyPurchaseService $purchaseService;

    public function __construct(EmergencyPurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Halaman Rekap Pembelian Darurat (Utama untuk Owner, Barista juga dapat melihat riwayat)
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $ingredientId = $request->get('ingredient_id');
        $userId = $request->get('user_id');
        $search = $request->get('search');

        $query = EmergencyPurchase::with(['ingredient.unit', 'ingredient.category', 'user']);

        if ($startDate) {
            $query->whereDate('purchase_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('purchase_date', '<=', $endDate);
        }

        if ($ingredientId) {
            $query->where('ingredient_id', $ingredientId);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_code', 'like', "%{$search}%")
                  ->orWhere('purchase_location', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('ingredient', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Statistik Rekap Keseluruhan
        $totalAmount = (float) EmergencyPurchase::sum('amount');
        $totalCount = EmergencyPurchase::count();

        // Statistik Bulan Ini
        $thisMonthStart = Carbon::today()->startOfMonth()->toDateString();
        $thisMonthEnd = Carbon::today()->endOfMonth()->toDateString();
        $thisMonthAmount = (float) EmergencyPurchase::whereBetween('purchase_date', [$thisMonthStart, $thisMonthEnd])->sum('amount');
        $thisMonthCount = EmergencyPurchase::whereBetween('purchase_date', [$thisMonthStart, $thisMonthEnd])->count();

        // Bahan Paling Sering Dibeli Darurat
        $topIngredientStats = EmergencyPurchase::select('ingredient_id', DB::raw('count(*) as purchase_count'), DB::raw('sum(quantity) as total_qty'))
            ->groupBy('ingredient_id')
            ->orderByDesc('purchase_count')
            ->first();

        $topIngredient = null;
        if ($topIngredientStats) {
            $topIngredient = Ingredient::with('unit')->find($topIngredientStats->ingredient_id);
            if ($topIngredient) {
                $topIngredient->purchase_count = $topIngredientStats->purchase_count;
                $topIngredient->total_qty = $topIngredientStats->total_qty;
            }
        }

        $purchases = $query->orderByDesc('purchase_date')->orderByDesc('id')->paginate(12)->withQueryString();
        $ingredients = Ingredient::with('unit')->active()->orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('emergency_purchases.index', compact(
            'purchases',
            'ingredients',
            'users',
            'startDate',
            'endDate',
            'ingredientId',
            'userId',
            'search',
            'totalAmount',
            'totalCount',
            'thisMonthAmount',
            'thisMonthCount',
            'topIngredient'
        ));
    }

    /**
     * Form Input Pembelian Darurat (Sesuai Desain Barista)
     */
    public function create()
    {
        $ingredients = Ingredient::with(['unit', 'category'])->active()->orderBy('name')->get();
        $today = Carbon::today()->toDateString();

        return view('emergency_purchases.create', compact('ingredients', 'today'));
    }

    /**
     * Simpan Pembelian Darurat
     */
    public function store(Request $request)
    {
        // Bersihkan formatting Rupiah jika ada (contoh: 'Rp 25.000' -> 25000)
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleaned = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleaned !== '' ? (float) $cleaned : 0]);
        }

        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'amount' => ['required', 'numeric', 'min:0'],
            'purchase_location' => ['required', 'string', 'max:255'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'purchase_time' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'receipt_proof' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // Max 5MB sesuai teks UI
        ], [
            'ingredient_id.required' => 'Nama bahan baku wajib dipilih.',
            'ingredient_id.exists' => 'Bahan baku yang dipilih tidak valid.',
            'quantity.required' => 'Jumlah bahan wajib diisi.',
            'quantity.gt' => 'Jumlah bahan harus lebih besar dari 0.',
            'amount.required' => 'Harga pembelian wajib diisi.',
            'amount.min' => 'Harga tidak boleh negatif.',
            'purchase_location.required' => 'Tempat pembelian / nama toko wajib diisi.',
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'purchase_date.before_or_equal' => 'Tanggal pembelian tidak boleh di masa depan.',
            'receipt_proof.file' => 'Bukti pembelian harus berupa file dokumen.',
            'receipt_proof.mimes' => 'Format file bukti harus berupa JPG, PNG, atau PDF.',
            'receipt_proof.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        // Ambil waktu real-time: prioritaskan client_time dari browser saat tombol simpan diklik,
        // atau gunakan waktu real-time sekarang dalam zona waktu WIB (Asia/Jakarta)
        $clientTime = $request->input('client_time') ?: $request->input('purchase_time');
        if ($clientTime && preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $clientTime)) {
            if (strlen($clientTime) === 5) {
                $clientTime .= ':00';
            }
            $validated['purchase_time'] = $clientTime;
        } else {
            $validated['purchase_time'] = Carbon::now('Asia/Jakarta')->format('H:i:s');
        }

        try {
            $purchase = $this->purchaseService->recordPurchase(
                $validated,
                $request->user(),
                $request->file('receipt_proof')
            );

            $ing = $purchase->ingredient;
            $msg = "Pembelian darurat '{$ing->name}' sebanyak {$purchase->formatted_quantity} {$ing->unit?->symbol} berhasil disimpan! Stok telah otomatis ditambahkan ke sistem dan tercatat pada rekap Owner.";

            if ($request->user()->isOwner()) {
                return redirect()->route('emergency-purchases.index')->with('success', $msg);
            }

            return redirect()->route('emergency-purchases.create')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mencatat pembelian darurat: ' . $e->getMessage());
        }
    }

    /**
     * Detail Transaksi Pembelian Darurat
     */
    public function show(EmergencyPurchase $emergencyPurchase)
    {
        $emergencyPurchase->load(['ingredient.unit', 'ingredient.category', 'user', 'stockMovement']);

        if (request()->wantsJson()) {
            return response()->json($emergencyPurchase);
        }

        return redirect()->route('emergency-purchases.index', ['search' => $emergencyPurchase->purchase_code]);
    }
}

