<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'price'
    ];

    /**
     * Use 'uuid' as the primary key for the API.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}