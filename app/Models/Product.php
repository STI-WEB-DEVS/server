<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. ADD THIS

class Product extends Model
{
    use HasUuids, HasFactory; // 2. ADD HasFactory HERE

    protected $fillable = [
        'name',
        'description',
        'price',
        'uuid' // Adding this ensures your seeder can fill the uuid column
    ];

    /**
     * Use 'uuid' as the primary key for the API.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}