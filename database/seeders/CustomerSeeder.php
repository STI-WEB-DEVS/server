<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'uuid' => (string) Str::uuid(),
            'name' => 'Test Customer',
            'email' => 'test@Customer.com',
        ]);
    }
}
