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
        // Make sure 'uuid' is fillable if you are manually migrating it
        'uuid', 
    ];

    // This tells Laravel to use 'uuid' as the primary identifier for this model
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }
}