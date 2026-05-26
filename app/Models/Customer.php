<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Model
{
    use HasUuids, HasRoles;

    protected $fillable = [
        'name',
        'email',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
