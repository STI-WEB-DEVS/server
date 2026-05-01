<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added for seeding
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    // HasFactory allows php artisan db:seed to work
    // HasUuids automatically generates UUIDs for new records
    use HasFactory, HasUuids; 

    protected $fillable = [
        'uuid',  // Necessary for your Repository and Service mapping
        'name',
        'email',
    ];

    /**
     * Specifies which column receives the UUID.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Relationship with Orders.
     * This allows you to call $customer->orders in your Controllers.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}