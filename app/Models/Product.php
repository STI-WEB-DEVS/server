<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 
        'description', 
        'price',
        'stock_quantity',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}