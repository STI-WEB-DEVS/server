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

    public function customer()
    {
        /**
         * Use belongsTo because the 'customer_id' foreign key 
         * is located on this User model's table.
         */
        return $this->belongsTo(Customer::class);
    }

    /**
     * The "booted" method of the model.
     * Automatically assign a role when a new user is created.
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Only assign role if user doesn't have any roles yet
            if ($user->roles->isEmpty()) {
                // Assign 'customer' role by default
                $user->assignRole('customer');
            }
        });
    }
}