<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasUuid; // If your project uses a trait for UUIDs

class Order extends Model
{
    use HasFactory;

    // VERY IMPORTANT: Add these so the Repository can save data
    protected $fillable = [
        'uuid',
        'customer_id',
        'product_id',
        'quantity',
    ];

    /**
     * Relationship: An Order belongs to a Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship: An Order belongs to a Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}