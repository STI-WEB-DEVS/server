<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'contact_number', 
        'address'
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
