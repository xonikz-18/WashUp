<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'phone_number',
        'service_id',
        'service_price_per_kg',
        'weight',
        'total_price',
        'status',
        'estimated_pickup',
        'date_created',
    ];

    protected function casts(): array
    {
        return [
            'service_price_per_kg' => 'decimal:2',
            'weight' => 'decimal:2',
            'total_price' => 'decimal:2',
            'estimated_pickup' => 'datetime',
            'date_created' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function orderAddons(): HasMany
    {
        return $this->hasMany(OrderAddon::class);
    }
}