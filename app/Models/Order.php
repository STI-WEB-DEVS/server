<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'total_amount', 'items'];

    protected $casts = [
        'items' => 'array',
    ];

    public function customer(): BelongsTo
    {

        return $this->belongsTo(User::class, 'customer_id');
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}