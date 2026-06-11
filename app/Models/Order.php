<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_code',
        'status',
        'total_amount',
        'payment_method',
        'payment_status',
        'shipping_address',
        'note',
        'pickup_method',
        'pickup_deadline',
        'estimated_ready_at',
        'ready_at',
        'shipped_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'pickup_deadline' => 'datetime',
        'estimated_ready_at' => 'datetime',
        'ready_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'indigo',
            'shipped' => 'purple',
            'completed' => 'green',
            'cancelled' => 'red',
            'menunggu_diambil' => 'orange',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '⏳ Menunggu',
            'confirmed' => '✅ Dikonfirmasi',
            'processing' => '📦 Diproses',
            'shipped' => '🚚 Dikirim',
            'completed' => '✅ Selesai',
            'cancelled' => '❌ Dibatalkan',
            'menunggu_diambil' => '🏪 Menunggu Diambil',
        ];
        
        return $badges[$this->status] ?? '❓ Unknown';
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getTotalItemsAttribute()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Calculate estimated preparation minutes based on order items.
     * Base: 10 min + 3 min per unique product + 1 min per 5 quantity units.
     * Min: 10, Max: 120 minutes.
     */
    public static function calculateEstimatedMinutes($orderItems): int
    {
        $uniqueProducts = count($orderItems);
        $totalQuantity = collect($orderItems)->sum(function ($item) {
            return is_array($item) ? ($item['quantity'] ?? 0) : $item->quantity;
        });

        $minutes = 10 + ($uniqueProducts * 3) + intval($totalQuantity / 5);

        return max(10, min($minutes, 120));
    }

    /**
     * Check if order is ready for pickup.
     */
    public function isReadyForPickup(): bool
    {
        return $this->ready_at !== null;
    }

    /**
     * Get human-readable estimation label.
     */
    public function getEstimatedReadyLabelAttribute(): string
    {
        if ($this->ready_at) {
            return '✅ Siap Diambil';
        }

        if (!$this->estimated_ready_at) {
            return '-';
        }

        if ($this->estimated_ready_at->isPast()) {
            return '⏰ Seharusnya Sudah Siap';
        }

        return '⏳ ~' . $this->estimated_ready_at->format('H:i');
    }
}