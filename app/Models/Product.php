<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'cost_price',
        'price',
        'stock',
        'unit',
        'image_data',
        'image_mime',
        'image_size',
        'description',
        'is_active',
    ];

    protected $hidden = [
        'image_data',
    ];

    protected $appends = [
        'image_url',
        'has_image',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'price' => 'decimal:2',
            'stock' => 'integer',
            'image_size' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Hitung potensi profit per unit
     */
    public function getMarginAttribute(): float
    {
        return (float) ($this->price - $this->cost_price);
    }

    /**
     * Status apakah produk memiliki foto di database
     */
    public function getHasImageAttribute(): bool
    {
        return !empty($this->image_mime);
    }

    /**
     * URL streaming foto produk dari database
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_mime) {
            return null;
        }

        return route('products.image', $this->id);
    }

    /**
     * Data URI base64 jika dibutuhkan secara inline
     */
    public function getImageDataUrlAttribute(): ?string
    {
        if (!$this->image_mime || empty($this->image_data)) {
            return null;
        }

        return 'data:' . $this->image_mime . ';base64,' . $this->image_data;
    }
}
