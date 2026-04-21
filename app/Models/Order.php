<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'product_uuid',
        'customer_uuid',
        'status',
        'total_amount',
    ];

    /**
     * Set the columns that should receive a unique UUID.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Relationship to OrderItem
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }
}