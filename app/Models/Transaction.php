<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_code',
        'user_id',
        'customer_name',
        'subtotal',
        'discount',
        'tax',
        'total_price',
        'pay_amount',
        'change_amount',
        'payment_method',
        'payment_status',
        'va_bank',
        'va_number',
        'va_biller_code',
        'gateway_transaction_id',
        'payment_expired_at',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total_price' => 'decimal:2',
            'pay_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
            'payment_expired_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function isPaid(): bool
    {
        return ($this->payment_status ?? 'paid') === 'paid';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        if ($this->payment_method === 'va') {
            return 'VA ' . strtoupper((string) ($this->va_bank ?: 'BANK'));
        }

        return strtoupper((string) $this->payment_method);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Hitung total modal (HPP) untuk transaksi ini
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->cost_price * $item->quantity;
        });
    }

    /**
     * Hitung total laba kotor untuk transaksi ini
     */
    public function getGrossProfitAttribute(): float
    {
        return (float) ($this->total_price - $this->total_cost);
    }
}
