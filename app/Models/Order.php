<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'total_amount',
        'uuid',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
