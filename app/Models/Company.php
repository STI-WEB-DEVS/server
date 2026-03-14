<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'name',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}