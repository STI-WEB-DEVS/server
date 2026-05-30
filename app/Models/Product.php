<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'price',
        'quantity',
        'description',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}
