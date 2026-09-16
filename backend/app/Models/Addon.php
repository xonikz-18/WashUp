<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'addon_name',
        'addon_price',
    ];

    public function orderAddons(): HasMany
    {
        return $this->hasMany(OrderAddon::class);
    }
}
