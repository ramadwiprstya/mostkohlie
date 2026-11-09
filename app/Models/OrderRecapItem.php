<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRecapItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_recap_id',
        'menu_id',
        'quantity',
        'menu_code_snapshot',
        'menu_name_snapshot',
        'menu_price_snapshot',
        'recipe_version_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'menu_price_snapshot' => 'decimal:2',
            'recipe_version_snapshot' => 'integer',
        ];
    }

    public function orderRecap(): BelongsTo
    {
        return $this->belongsTo(OrderRecap::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
