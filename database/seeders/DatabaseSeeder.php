<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ALWAYS run the RoleSeeder first
        $this->call(RoleSeeder::class);
        
        // Create test user if doesn't exist
        User::firstOrCreate(
            ['email' => 'test@cs.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
    }
}