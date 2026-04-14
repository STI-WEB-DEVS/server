<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Seed sample customers for API testing.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'John Carter',
                'email' => 'john.carter@example.com',
            ],
            [
                'name' => 'Maria Reyes',
                'email' => 'maria.reyes@example.com',
            ],
            [
                'name' => 'Liam Santos',
                'email' => 'liam.santos@example.com',
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::updateOrCreate(
                ['email' => $customerData['email']],
                [
                    'name' => $customerData['name'],
                ]
            );
        }
    }
}