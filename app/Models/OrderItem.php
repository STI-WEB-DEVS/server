<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <--- ADD THIS LINE

class OrderItem extends Model
{
    protected $fillable = [
        'uuid', // Make sure uuid is here if you're using it
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function order(): BelongsTo // Add the return type
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo // This is what the error is looking for
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}