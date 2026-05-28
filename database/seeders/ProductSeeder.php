<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Noise-Cancelling Headphones', 'price' => 149.99],
            ['name' => 'Mechanical Keyboard (RGB)',            'price' =>  89.99],
            ['name' => 'Ergonomic Mouse',                     'price' =>  49.99],
            ['name' => '27" 4K Monitor',                      'price' => 399.99],
            ['name' => 'USB-C Hub (7-in-1)',                  'price' =>  39.99],
            ['name' => 'Webcam 1080p',                        'price' =>  69.99],
            ['name' => 'Standing Desk Mat',                   'price' =>  34.99],
            ['name' => 'Laptop Stand (Aluminum)',             'price' =>  29.99],
            ['name' => 'Smart LED Desk Lamp',                 'price' =>  54.99],
            ['name' => 'Portable SSD 1TB',                   'price' => 109.99],
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(['name' => $product['name']], $product);
        }
    }
}
