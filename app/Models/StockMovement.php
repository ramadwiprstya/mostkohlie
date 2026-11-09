<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_code',
        'ingredient_id',
        'type',
        'quantity_in',
        'quantity_out',
        'stock_before',
        'stock_after',
        'reference_type',
        'reference_id',
        'reference_code',
        'user_id',
        'movement_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'stock_before' => 'decimal:2',
            'stock_after' => 'decimal:2',
            'movement_date' => 'datetime',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'recap_usage' => 'Pemakaian Rekap Pesanan',
            'restock' => 'Penambahan Stok (Restock)',
            'adjustment_in' => 'Koreksi Stok (Masuk)',
            'adjustment_out' => 'Koreksi Stok (Keluar)',
            'cancellation_reversal' => 'Pembalikan Pembatalan Rekap',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'recap_usage' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'restock' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'adjustment_in' => 'bg-indigo-100 text-indigo-800 border border-indigo-200',
            'adjustment_out' => 'bg-amber-100 text-amber-800 border border-amber-200',
            'cancellation_reversal' => 'bg-purple-100 text-purple-800 border border-purple-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
