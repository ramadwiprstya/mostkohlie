<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRecapIngredientUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_recap_id',
        'ingredient_id',
        'ingredient_code_snapshot',
        'ingredient_name_snapshot',
        'quantity_used',
        'unit_symbol_snapshot',
        'recipe_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'decimal:2',
            'recipe_snapshot' => 'array',
        ];
    }

    public function orderRecap(): BelongsTo
    {
        return $this->belongsTo(OrderRecap::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
