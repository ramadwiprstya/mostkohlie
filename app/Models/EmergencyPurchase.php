<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_code',
        'ingredient_id',
        'quantity',
        'amount',
        'purchase_location',
        'purchase_date',
        'purchase_time',
        'notes',
        'receipt_proof',
        'user_id',
        'stock_movement_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'quantity' => 'decimal:2',
            'amount' => 'decimal:2',
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

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getFormattedQuantityAttribute(): string
    {
        $val = (float) $this->quantity;
        return number_format($val, floor($val) == $val ? 0 : 2, ',', '.');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_proof) {
            return null;
        }

        if (str_starts_with($this->receipt_proof, 'http://') || str_starts_with($this->receipt_proof, 'https://')) {
            return $this->receipt_proof;
        }

        return asset($this->receipt_proof);
    }

    public function getIsPdfAttribute(): bool
    {
        if (!$this->receipt_proof) {
            return false;
        }
        return str_ends_with(strtolower($this->receipt_proof), '.pdf');
    }
}

