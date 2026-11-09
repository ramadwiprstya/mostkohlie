<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateTransactionException;
use App\Exceptions\InsufficientStockException;
use App\Models\Category;
use App\Models\Menu;
use App\Models\OrderRecap;
use App\Services\OrderRecapService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderRecapController extends Controller
{
    protected OrderRecapService $recapService;

    public function __construct(OrderRecapService $recapService)
    {
        $this->recapService = $recapService;
    }

    public function index(Request $request)
    {
        $date = $request->get('date');
        $shift = $request->get('shift');
        $status = $request->get('status');
        $search = $request->get('search');

        $query = OrderRecap::with(['user', 'items.menu', 'ingredientUsages']);

        if ($date) {
            $query->whereDate('transaction_date', $date);
        }

        if ($shift) {
            $query->where('shift', $shift);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $recaps = $query->latest('submitted_at')->paginate(12)->withQueryString();

        return view('order_recaps.index', compact('recaps', 'date', 'shift', 'status', 'search'));
    }

    public function create()
    {
        $menus = Menu::with(['category', 'activeRecipe.items.ingredient.unit'])
            ->active()
            ->orderBy('name')
            ->get();

        $categories = Category::forMenus()->orderBy('name')->get();
        $idempotencyToken = Str::random(32);
        $today = Carbon::today()->toDateString();

        return view('order_recaps.create', compact('menus', 'categories', 'idempotencyToken', 'today'));
    }

    /**
     * AJAX endpoint untuk kalkulasi preview pemakaian bahan baku & ketersediaan stok
     */
    public function preview(Request $request)
    {
        $items = $request->input('items', []);
        if (is_string($items)) {
            $items = json_decode($items, true) ?: [];
        }

        if (empty($items)) {
            return response()->json([
                'requirements' => [],
                'is_all_sufficient' => true,
                'shortages' => [],
            ]);
        }

        $result = $this->recapService->calculateIngredientsUsage($items);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $rawItems = $request->input('items', []);
        if (is_string($rawItems)) {
            $rawItems = json_decode($rawItems, true) ?: [];
        }
        $normalizedItems = $this->recapService->normalizeItems(is_array($rawItems) ? $rawItems : []);
        $request->merge(['items' => $normalizedItems]);

        $request->validate([
            'transaction_date' => ['required', 'date'],
            'shift' => ['nullable', 'string', 'in:pagi,siang,malam'],
            'idempotency_token' => ['required', 'string', 'max:64'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'transaction_date.required' => 'Tanggal transaksi wajib dipilih.',
            'items.required' => 'Minimal pilih 1 menu dengan jumlah pesanan.',
            'items.*.quantity.min' => 'Jumlah pesanan minimal 1 porsi.',
        ]);

        try {
            $recap = $this->recapService->processRecap($request->all(), $request->user());

            return redirect()->route('order-recaps.show', $recap)
                ->with('success', "Rekap pesanan {$recap->transaction_code} berhasil dikirim! Seluruh stok bahan baku telah otomatis diperbarui.");
        } catch (InsufficientStockException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage())
                ->with('shortages', $e->getShortages());
        } catch (DuplicateTransactionException $e) {
            return redirect()->route('order-recaps.index')
                ->with('warning', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(OrderRecap $orderRecap)
    {
        $orderRecap->load([
            'user',
            'cancelledByUser',
            'items.menu',
            'ingredientUsages.ingredient.unit',
            'stockMovements.ingredient.unit'
        ]);

        return view('order_recaps.show', compact('orderRecap'));
    }

    public function cancel(Request $request, OrderRecap $orderRecap)
    {
        if (!auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang membatalkan transaksi dan membalikkan stok.');
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancellation_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
        ]);

        try {
            $this->recapService->cancelRecap($orderRecap, $request->user(), $request->cancellation_reason);

            return redirect()->route('order-recaps.show', $orderRecap)
                ->with('success', "Transaksi {$orderRecap->transaction_code} telah dibatalkan dan seluruh stok bahan telah berhasil dikembalikan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}
