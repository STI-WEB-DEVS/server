<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
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

        // Seed a customer for the test user
        $customer = Customer::firstOrCreate(
            ['email' => 'test@cs.com'],
            [
                'name' => 'Test Customer',
            ]
        );

        // Seed the test user
        $user = User::updateOrCreate(
            ['email' => 'test@cs.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'customer_id' => $customer->id,
            ]
        );

        // Assign customer role to the user
        $user->assignRole('customer');
    }
}

