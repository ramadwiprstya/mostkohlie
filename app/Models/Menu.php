<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'price',
        'description',
        'photo',
        'is_active',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            return asset($this->photo);
        }

        $imageMap = [
            'butterscotch' => 'images/butterscotch-latte.jpg',
            'matcha' => 'images/matcha-latte.jpg',
            'americano' => 'images/americano.jpg',
            'kohlie' => 'images/kohlie-latte.jpg',
            'caramel' => 'images/caramel-macchiato.jpg',
            'chocolate' => 'images/chocolate.jpg',
            'cokelat' => 'images/chocolate.jpg',
        ];

        foreach ($imageMap as $key => $img) {
            if (stripos($this->name, $key) !== false) {
                return asset($img);
            }
        }

        return asset('images/kohlie-latte.jpg');
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function activeRecipe(): HasOne
    {
        return $this->hasOne(Recipe::class)->where('is_active', true)->latestOfMany();
    }

    public function orderRecapItems(): HasMany
    {
        return $this->hasMany(OrderRecapItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
