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
            ['name' => 'Premium Laptop', 'price' => 45000.00],
            ['name' => 'Wireless Mouse', 'price' => 1200.00],
            ['name' => 'Mechanical Keyboard', 'price' => 3500.00],
            ['name' => '4K Monitor', 'price' => 18000.00],
            ['name' => 'USB-C Hub', 'price' => 850.00],
            ['name' => 'Noise Cancelling Headphones', 'price' => 7500.00],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
