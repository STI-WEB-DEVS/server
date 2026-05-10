<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first
        $this->call(RoleSeeder::class);

        // Create test admin user
        $adminUser = User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Test Admin',
            'email' => 'test@cs.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole('admin');

        // Create test customer user
        $customerUser = User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Test Customer',
            'email' => 'customer@cs.com',
            'password' => Hash::make('password'),
        ]);
        $customerUser->assignRole('customer');
    }
}
