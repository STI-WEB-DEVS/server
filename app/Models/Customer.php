<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'uuid', 
        'name',
        'email',
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
