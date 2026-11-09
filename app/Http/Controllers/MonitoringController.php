<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $status = $request->get('status');

        $query = Ingredient::with(['category', 'unit'])->active();

        if ($search) {
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

        $ingredients = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        // Stat counts
        $totalCount = Ingredient::active()->count();
        $safeCount = Ingredient::safe()->count();
        $lowStockCount = Ingredient::lowStock()->count();
        $emptyCount = Ingredient::emptyStock()->count();

        $categories = Category::forIngredients()->orderBy('name')->get();

        return view('monitoring.index', compact(
            'ingredients',
            'categories',
            'search',
            'categoryId',
            'status',
            'totalCount',
            'safeCount',
            'lowStockCount',
            'emptyCount'
        ));
    }
}
