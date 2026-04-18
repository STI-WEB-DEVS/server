<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'email',
        'company_id'
    ];

    public function company(): BelongsTo {

        return $this->belongsTo(Company::class);
    
      }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
