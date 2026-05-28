<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name'     => 'Admin User',
                'password' => Hash::make('admin123'),
            ]
        );
        $admin->assignRole('admin');

        // Customer record
        $customer = Customer::firstOrCreate(
            ['email' => 'customer@test.com'],
            ['name' => 'Test Customer']
        );

        // Customer user linked to customer record
        $customerUser = User::firstOrCreate(
            ['email' => 'customer@test.com'],
            [
                'name'        => 'Test Customer',
                'password'    => Hash::make('customer123'),
                'customer_id' => $customer->id,
            ]
        );

        // Ensure customer_id is set if user already existed
        if (! $customerUser->customer_id) {
            $customerUser->update(['customer_id' => $customer->id]);
        }

        $customerUser->assignRole('customer');
    }
}
