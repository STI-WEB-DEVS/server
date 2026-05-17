<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    public function findByField(string $field, $value)
    {
        return User::where($field, $value)->first();
    }
}