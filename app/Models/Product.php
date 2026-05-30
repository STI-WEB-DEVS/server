<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    // 🚨 Add these two configurations to fix key lookup failures 👇
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'price',
        'stock',
        'description',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}