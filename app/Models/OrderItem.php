<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'order_id', 
        'product_id', 
        'quantity', 
        'unit_price'
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}