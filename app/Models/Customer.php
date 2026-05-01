<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid',  // Add this so the Repository can save/map it
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
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}