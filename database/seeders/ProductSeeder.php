<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Standard Parking Pass', 'price' => 5.00],
            ['name' => 'Monthly Permit', 'price' => 50.00],
            ['name' => 'VIP Reserved Space', 'price' => 150.00],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['name' => $p['name']],
                ['price' => $p['price']]
            );
        }
    }
}
