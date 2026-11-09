<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'unit_id',
        'current_stock',
        'min_stock',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'min_stock' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function recipeItems(): HasMany
    {
        return $this->hasMany(RecipeItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(OrderRecapIngredientUsage::class);
    }

    public function emergencyPurchases(): HasMany
    {
        return $this->hasMany(EmergencyPurchase::class);
    }

    /**
     * Aturan status:
     * - Habis: stok <= 0
     * - Menipis: stok > 0 && stok <= batas minimum
     * - Aman: stok > batas minimum
     */
    public function getStatusAttribute(): string
    {
        if ((float) $this->current_stock <= 0) {
            return 'habis';
        }

        if ((float) $this->current_stock <= (float) $this->min_stock) {
            return 'menipis';
        }

        return 'aman';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'habis' => 'Habis',
            'menipis' => 'Menipis',
            'aman' => 'Aman',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'habis' => 'bg-rose-100 text-rose-700 border border-rose-200',
            'menipis' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'aman' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSafe($query)
    {
        return $query->where('is_active', true)->whereColumn('current_stock', '>', 'min_stock');
    }

    public function scopeLowStock($query)
    {
        return $query->where('is_active', true)
            ->where('current_stock', '>', 0)
            ->whereColumn('current_stock', '<=', 'min_stock');
    }

    public function scopeEmptyStock($query)
    {
        return $query->where('is_active', true)->where('current_stock', '<=', 0);
    }

    public function procurementRequests(): HasMany
    {
        return $this->hasMany(ProcurementRequest::class);
    }
}
