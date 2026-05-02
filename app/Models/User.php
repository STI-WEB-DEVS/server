<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Customer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, HasRoles;

    protected $with = ['roles'];

    public function uniqueIds()
    {
        return ['uuid'];
    }




    public function customer()
    {
        /**
         * Use belongsTo because the 'customer_id' foreign key
         * is located on this User model's table.
         */
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerUuidAttribute()
    {
        return $this->customer?->uuid;
    }


    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
