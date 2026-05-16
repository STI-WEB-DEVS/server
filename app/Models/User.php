<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, HasRoles;

    public function uniqueIds()
    {
        return ['uuid'];
    }

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'customer_id', // <--- This column connects the user to their customer profile
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

    /**
     * 1st Bullet Point: Add the customer function
     */
    public function customer()
    {
        /**
         * Use belongsTo because the 'customer_id' foreign key 
         * is located on this User model's table.
         */
        return $this->belongsTo(Customer::class);
    }
}