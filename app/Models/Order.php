<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    // Relationships
    public function customer()
    {
        // If your customers table uses UUID as PK, adjust accordingly
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }


    public function products()
    {
        // Explicitly point to order_items pivot table
        return $this->belongsToMany(Product::class, 'order_items', 'order_id', 'product_id')
                    ->withPivot('quantity', 'unit_price')
                    ->withTimestamps();
    }
}

