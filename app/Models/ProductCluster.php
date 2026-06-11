<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cluster',
        'priority_level',
        'frequency_score',
        'quantity_score',
        'urgency_score',
        'last_clustered_at',
    ];

    protected $casts = [
        'frequency_score' => 'decimal:4',
        'quantity_score' => 'decimal:4',
        'urgency_score' => 'decimal:4',
        'last_clustered_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority_level) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray'
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match($this->priority_level) {
            'high' => '🔴 Prioritas Tinggi',
            'medium' => '🟡 Prioritas Sedang',
            'low' => '🟢 Prioritas Rendah',
            default => '⚪ Belum Dianalisis'
        };
    }

    public function getClusterNameAttribute()
    {
        return "Cluster {$this->cluster}";
    }
}