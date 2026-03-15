<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 55000,
        ]);

        Product::create([
            'name' => 'Mouse',
            'price' => 800,
        ]);

        Product::create([
            'name' => 'Keyboard',
            'price' => 1500,
        ]);

        Product::create([
            'name' => 'Monitor',
            'price' => 12000,
        ]);
    }
}