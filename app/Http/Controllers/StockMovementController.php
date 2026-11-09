<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $ingredientId = $request->get('ingredient_id');
        $type = $request->get('type');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $userId = $request->get('user_id');
        $search = $request->get('search');

        $query = StockMovement::with(['ingredient.unit', 'user']);

        if ($ingredientId) {
            $query->where('ingredient_id', $ingredientId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($startDate) {
            $query->whereDate('movement_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('movement_date', '<=', $endDate);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('movement_code', 'like', "%{$search}%")
                  ->orWhere('reference_code', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $movements = $query->latest('movement_date')->paginate(15)->withQueryString();

        $ingredients = Ingredient::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('stock_movements.index', compact(
            'movements',
            'ingredients',
            'users',
            'ingredientId',
            'type',
            'startDate',
            'endDate',
            'userId',
            'search'
        ));
    }
}
