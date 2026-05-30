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
        'quantity',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
