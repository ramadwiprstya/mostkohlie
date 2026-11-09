<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\OrderRecapIngredientUsage;
use App\Models\ProcurementRequest;
use App\Services\ProcurementService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProcurementRequestController extends Controller
{
    protected ProcurementService $procurementService;

    public function __construct(ProcurementService $procurementService)
    {
        $this->procurementService = $procurementService;
    }

    /**
     * Halaman Daftar Request Pengadaan (Sesuai Image 1)
     */
    public function index(Request $request)
    {
        $statusTab = $request->get('tab', 'semua');
        $search = $request->get('search');

        $query = ProcurementRequest::with(['ingredient.unit', 'ingredient.category', 'user', 'approver', 'receiver'])
            ->latest('id');

        // Filter tab status
        if ($statusTab === 'menunggu') {
            $query->where('status', 'menunggu');
        } elseif ($statusTab === 'diproses') {
            $query->where('status', 'diproses');
        } elseif ($statusTab === 'selesai') {
            $query->where('status', 'selesai');
        } elseif ($statusTab === 'ditolak') {
            $query->where('status', 'ditolak');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('supplier_name', 'like', "%{$search}%")
                    ->orWhereHas('ingredient', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $requests = $query->paginate(12)->withQueryString();

        // Hitung count untuk tab badge (seperti Semua 12, Menunggu 5, Diproses 4, Selesai 3)
        $countSemua = ProcurementRequest::count();
        $countMenunggu = ProcurementRequest::where('status', 'menunggu')->count();
        $countDiproses = ProcurementRequest::where('status', 'diproses')->count();
        $countSelesai = ProcurementRequest::where('status', 'selesai')->count();
        $countDitolak = ProcurementRequest::where('status', 'ditolak')->count();

        return view('procurement_requests.index', compact(
            'requests',
            'statusTab',
            'search',
            'countSemua',
            'countMenunggu',
            'countDiproses',
            'countSelesai',
            'countDitolak'
        ));
    }

    /**
     * Halaman Form Buat Request Pengadaan (Sesuai Image 2)
     * Menghitung histori pemakaian bahan dan proyeksi kebutuhan (1 Minggu & 1 Bulan)
     */
    public function create(Request $request)
    {
        // 1. Ambil seluruh master bahan baku aktif yang terdaftar di sistem
        $ingredients = Ingredient::with(['unit', 'category'])->active()->orderBy('name')->get();
        $selectedIngredientId = $request->get('ingredient_id');

        // 2. Tentukan batas rentang tanggal: 7 hari dan 30 hari ke belakang dari hari ini
        $sub7 = Carbon::now('Asia/Jakarta')->subDays(7)->toDateString();
        $sub30 = Carbon::now('Asia/Jakarta')->subDays(30)->toDateString();

        // 3. Agregasi total pemakaian aktual per bahan dalam 7 hari terakhir dari transaksi kasir valid (submitted)
        $usages7 = OrderRecapIngredientUsage::whereHas('orderRecap', function ($q) use ($sub7) {
            $q->where('transaction_date', '>=', $sub7)->where('status', 'submitted');
        })->selectRaw('ingredient_id, SUM(quantity_used) as total')
          ->groupBy('ingredient_id')
          ->pluck('total', 'ingredient_id');

        // 4. Agregasi total pemakaian aktual per bahan dalam 30 hari terakhir dari transaksi kasir valid (submitted)
        $usages30 = OrderRecapIngredientUsage::whereHas('orderRecap', function ($q) use ($sub30) {
            $q->where('transaction_date', '>=', $sub30)->where('status', 'submitted');
        })->selectRaw('ingredient_id, SUM(quantity_used) as total')
          ->groupBy('ingredient_id')
          ->pluck('total', 'ingredient_id');

        // 5. Hitung rata-rata konsumsi harian dan rekomendasi jumlah pengadaan untuk setiap bahan baku
        foreach ($ingredients as $ing) {
            $u7 = (float) ($usages7[$ing->id] ?? 0);   // Total terpakai 7 hari terakhir
            $u30 = (float) ($usages30[$ing->id] ?? 0); // Total terpakai 30 hari terakhir

            // Logika perhitungan rata-rata harian (Daily Average):
            // - Jika ada data transaksi 30 hari: total pemakaian dibagi 30 hari
            // - Jika hanya ada data 7 hari: total pemakaian dibagi 7 hari
            // - Jika belum pernah ada transaksi penjualan: estimasi awal dari batas aman dibagi 7 hari (min_stock / 7)
            if ($u30 > 0) {
                $daily = $u30 / 30;
            } elseif ($u7 > 0) {
                $daily = $u7 / 7;
            } else {
                $daily = max(1, (float) $ing->min_stock / 7);
            }

            // Hitung apakah stok saat ini mengalami defisit di bawah ambang batas minimum (safety stock)
            $deficit = max(0, (float) $ing->min_stock - (float) $ing->current_stock);

            // Hitung estimasi konsumsi bahan untuk periode 1 minggu (7 hari) dan 1 bulan (30 hari)
            $weekConsumption = ceil($daily * 7);
            $monthConsumption = ceil($daily * 30);

            // Simpan hasil perhitungan ke atribut dinamis model Ingredient untuk digunakan di view/Alpine.js:
            $ing->usage_7_days      = $u7;                                             // Histori konsumsi 7 hari
            $ing->usage_30_days     = $u30;                                            // Histori konsumsi 30 hari
            $ing->daily_avg         = round($daily, 1);                                // Rata-rata konsumsi harian (misal: 2 ml/hari)
            $ing->recommended_week  = max(1, (int) ceil($weekConsumption + $deficit)); // Rekomendasi 1 Minggu (+ cover defisit stok)
            $ing->recommended_month = max(1, (int) ceil($monthConsumption + $deficit)); // Rekomendasi 1 Bulan (+ cover defisit stok)
            $ing->deficit           = $deficit;                                        // Kuantitas defisit stok di bawah batas aman
        }

        // 6. Tanggal default kebutuhan: besok (H+1)
        $defaultDate = Carbon::now('Asia/Jakarta')->addDay()->format('Y-m-d');

        return view('procurement_requests.create', compact('ingredients', 'selectedIngredientId', 'defaultDate'));
    }

    /**
     * Simpan Request Pengadaan Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity_requested' => ['required', 'numeric', 'gt:0'],
            'required_date' => ['required', 'date'],
            'priority' => ['required', 'in:tinggi,normal,rendah'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ], [
            'ingredient_id.required' => 'Bahan baku wajib dipilih.',
            'ingredient_id.exists' => 'Bahan baku tidak valid.',
            'quantity_requested.required' => 'Jumlah kebutuhan wajib diisi.',
            'quantity_requested.gt' => 'Jumlah kebutuhan harus lebih dari 0.',
            'required_date.required' => 'Tanggal kebutuhan wajib diisi.',
            'priority.required' => 'Prioritas pengadaan wajib ditentukan.',
        ]);

        try {
            $procRequest = $this->procurementService->createRequest($validated, $request->user());
            $ingredientName = $procRequest->ingredient?->name ?? 'Bahan';

            return redirect()->route('procurement-requests.index')
                ->with('success', "Request pengadaan '{$ingredientName}' berhasil diajukan dan sedang menunggu review Owner!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan request: ' . $e->getMessage());
        }
    }

    /**
     * Owner Menyetujui Request
     */
    public function approve(Request $request, ProcurementRequest $procurementRequest)
    {
        $this->authorizeOwner();

        if ($procurementRequest->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Hanya request dengan status Menunggu yang dapat disetujui.');
        }

        $validated = $request->validate([
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'estimated_arrival' => ['nullable', 'date'],
            'order_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->procurementService->approveRequest($procurementRequest, $request->user(), $validated);

            return redirect()->route('procurement-requests.index', ['tab' => 'diproses'])
                ->with('success', "Request '{$procurementRequest->request_code}' berhasil disetujui dan dialihkan ke Pengadaan Berjalan!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyetujui request: ' . $e->getMessage());
        }
    }

    /**
     * Owner Menolak Request
     */
    public function reject(Request $request, ProcurementRequest $procurementRequest)
    {
        $this->authorizeOwner();

        if ($procurementRequest->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Hanya request dengan status Menunggu yang dapat ditolak.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi agar barista memahami keputusan ini.',
        ]);

        try {
            $this->procurementService->rejectRequest($procurementRequest, $request->user(), $validated['rejection_reason']);

            return redirect()->route('procurement-requests.index', ['tab' => 'ditolak'])
                ->with('warning', "Request '{$procurementRequest->request_code}' telah ditolak.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak request: ' . $e->getMessage());
        }
    }

    /**
     * Barista / Owner Mencatat Penerimaan Barang & Stok Otomatis Bertambah
     */
    public function receive(Request $request, ProcurementRequest $procurementRequest)
    {
        if ($procurementRequest->status !== 'diproses') {
            return redirect()->back()->with('error', 'Hanya pengadaan dengan status Diproses yang dapat dicatat penerimaannya.');
        }

        $validated = $request->validate([
            'quantity_received' => ['required', 'numeric', 'gt:0'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'expiry_date' => ['nullable', 'date'],
            'receipt_notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'quantity_received.required' => 'Jumlah barang diterima wajib diisi.',
            'quantity_received.gt' => 'Jumlah barang diterima harus lebih dari 0.',
        ]);

        try {
            $this->procurementService->receiveGoods($procurementRequest, $request->user(), $validated);

            $ing = $procurementRequest->ingredient;
            return redirect()->route('procurement-requests.index', ['tab' => 'selesai'])
                ->with('success', "Penerimaan '{$ing->name}' berhasil dicatat! Stok bahan otomatis bertambah ke sistem.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses penerimaan: ' . $e->getMessage());
        }
    }

    /**
     * Helper Autorisasi Khusus Owner
     */
    protected function authorizeOwner(): void
    {
        if (!auth()->user() || !auth()->user()->isOwner()) {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Owner.');
        }
    }
}

