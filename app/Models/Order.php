<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasUuids;

    protected $fillable = [
        'customer_id',
        'total_amount',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    // Relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship to OrderItems
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}