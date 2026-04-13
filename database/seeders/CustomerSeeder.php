<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
{
    $customers = [
        ['email' => 'john@example.com', 'name' => 'John Doe'],
        ['email' => 'jane@example.com', 'name' => 'Jane Smith'],
        ['email' => 'joshua@example.com', 'name' => 'Joshua Arabejo'],
    ];

    foreach ($customers as $customer) {
        Customer::updateOrCreate(
            ['email' => $customer['email']], // Check if this email exists
            [
                'name' => $customer['name'], 
                'uuid' => (string) Str::uuid() // Ensures a fresh UUID if creating
            ]
        );
    }
}
}