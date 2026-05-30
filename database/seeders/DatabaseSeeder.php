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
        $this->call(RoleSeeder::class);

        // 1. Admin User
        $admin = User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Test User',
            'email' => 'test@cs.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // 2. Customer
        $customerRecord = \App\Models\Customer::create([
            'name' => 'Customer User',
            'email' => 'customer@cs.com',
        ]);

        $customer = User::factory()->create([
            'uuid' => (string) Str::uuid(),
            'name' => 'Customer User',
            'email' => 'customer@cs.com',
            'password' => Hash::make('password'),
            'customer_id' => $customerRecord->id,
        ]);
        $customer->assignRole('customer');

        // 3. Products
        $products = [
            ['name' => 'Premium Laptop', 'price' => 45000.00, 'description' => 'A high-performance laptop for professionals.', 'quantity' => 10],
            ['name' => 'Wireless Mouse', 'price' => 1200.00, 'description' => 'Ergonomic wireless mouse with long battery life.', 'quantity' => 50],
            ['name' => 'Mechanical Keyboard', 'price' => 3500.00, 'description' => 'RGB mechanical keyboard with tactile switches.', 'quantity' => 0], // Out of stock example
            ['name' => '4K Monitor', 'price' => 18000.00, 'description' => 'Ultra HD 4K monitor with vibrant colors.', 'quantity' => 15],
            ['name' => 'USB-C Hub', 'price' => 850.00, 'description' => 'Multi-port USB-C hub for versatile connectivity.', 'quantity' => 30],
            ['name' => 'Noise Cancelling Headphones', 'price' => 7500.00, 'description' => 'Over-ear headphones with active noise cancellation.', 'quantity' => 5],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
