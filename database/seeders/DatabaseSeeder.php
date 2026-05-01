<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
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
        // 1. Create a Test User
        // This ensures you can log in or use auth-guarded routes if needed.
        User::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@cs.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create a Test Customer
        // This gives you the customer_uuid for your Hoppscotch body.
        Customer::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Mark Caguioa',
            'email' => 'mark@example.com',
        ]);

        // 3. Create a Test Product
        // This gives you the product_uuid for your Hoppscotch body.
        Product::create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Sample Product',
            'description' => 'High-quality test product for order verification.',
            'price' => 150.00,
        ]);
    }
}