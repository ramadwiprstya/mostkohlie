<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'ingredient_id',
        'user_id',
        'quantity_requested',
        'stock_at_request',
        'min_stock_at_request',
        'required_date',
        'priority',
        'reason',
        'status',
        'approved_by',
        'reviewed_at',
        'rejection_reason',
        'supplier_name',
        'estimated_arrival',
        'order_notes',
        'quantity_received',
        'received_by',
        'received_at',
        'expiry_date',
        'invoice_number',
        'receipt_notes',
        'stock_movement_id',
    ];

    protected function casts(): array
    {
        return [
            'required_date' => 'date',
            'reviewed_at' => 'datetime',
            'estimated_arrival' => 'date',
            'received_at' => 'datetime',
            'expiry_date' => 'date',
            'quantity_requested' => 'decimal:2',
            'stock_at_request' => 'decimal:2',
            'min_stock_at_request' => 'decimal:2',
            'quantity_received' => 'decimal:2',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'menunggu');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'diproses');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'ditolak');
    }

    // Accessors
    public function getFormattedQuantityRequestedAttribute(): string
    {
        $val = (float) $this->quantity_requested;
        return number_format($val, floor($val) == $val ? 0 : 2, ',', '.');
    }

    public function getFormattedQuantityReceivedAttribute(): ?string
    {
        if ($this->quantity_received === null) {
            return null;
        }
        $val = (float) $this->quantity_received;
        return number_format($val, floor($val) == $val ? 0 : 2, ',', '.');
    }

    public function getInitialsAttribute(): string
    {
        $name = trim($this->ingredient?->name ?? 'Bahan');
        $words = preg_split('/\s+/', $name);
        if (count($words) === 1) {
            return strtoupper(mb_substr($name, 0, 2));
        }
        if (str_starts_with(strtolower($name), 'biji')) {
            return 'BI';
        }
        return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'tinggi' => 'Tinggi',
            'rendah' => 'Rendah',
            default => 'Normal',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => 'Menunggu',
        };
    }
}

