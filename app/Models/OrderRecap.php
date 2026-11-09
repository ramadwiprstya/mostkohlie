<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'shift',
        'user_id',
        'status',
        'total_menus_count',
        'total_items_quantity',
        'idempotency_token',
        'submitted_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'submitted_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'total_menus_count' => 'integer',
            'total_items_quantity' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderRecapItem::class);
    }

    public function ingredientUsages(): HasMany
    {
        return $this->hasMany(OrderRecapIngredientUsage::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'reference_code', 'transaction_code');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getRecapNumberAttribute(): string
    {
        return $this->transaction_code;
    }

    public function getCashierReferenceAttribute(): string
    {
        return $this->notes ?: $this->transaction_code;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'Terkirim & Stok Dikurangi',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted' => 'bg-emerald-100 text-emerald-800 border border-emerald-200',
            'cancelled' => 'bg-rose-100 text-rose-800 border border-rose-200',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getShiftLabelAttribute(): string
    {
        return match (strtolower($this->shift ?? 'pagi')) {
            'pagi' => 'Pagi (07:00 - 15:00)',
            'siang' => 'Siang (15:00 - 20:00)',
            'malam' => 'Malam (20:00 - 23:00)',
            default => ucfirst($this->shift ?? 'Pagi'),
        };
    }

    public function getShiftBadgeClassAttribute(): string
    {
        return match (strtolower($this->shift ?? 'pagi')) {
            'pagi' => 'bg-[#FAF0DD] text-[#A16A28] border border-[#A16A28]/30',
            'siang' => 'bg-[#EAF4EF] text-[#39715A] border border-[#39715A]/30',
            'malam' => 'bg-[#305785]/15 text-[#305785] border border-[#305785]/30',
            default => 'bg-[#FAF8F5] text-[#70746F] border border-[#D8CDBD]',
        };
    }
}
