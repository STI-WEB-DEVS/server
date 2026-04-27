<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample customers
        $customers = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob.johnson@example.com',
            ],
        ];

        $createdCustomers = [];
        foreach ($customers as $customer) {
            $createdCustomers[] = Customer::create($customer);
        }

        // Create sample products
        $products = [
            [
                'name' => 'Laptop',
                'price' => 999.99,
            ],
            [
                'name' => 'Mouse',
                'price' => 29.99,
            ],
            [
                'name' => 'Keyboard',
                'price' => 79.99,
            ],
            [
                'name' => 'Monitor',
                'price' => 299.99,
            ],
            [
                'name' => 'USB Hub',
                'price' => 49.99,
            ],
        ];

        $createdProducts = [];
        foreach ($products as $product) {
            $createdProducts[] = Product::create($product);
        }

        // Create sample orders with items
        // Order 1: John Doe - 1 Laptop + 2 Mice
        $order1 = Order::create([
            'customer_id' => $createdCustomers[0]->id,
            'total_amount' => (999.99 * 1) + (29.99 * 2), // 1059.97
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[0]->id, // Laptop
            'quantity' => 1,
            'unit_price' => 999.99,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $createdProducts[1]->id, // Mouse
            'quantity' => 2,
            'unit_price' => 29.99,
        ]);

        // Order 2: Jane Smith - 1 Keyboard + 1 Monitor + 3 USB Hubs
        $order2 = Order::create([
            'customer_id' => $createdCustomers[1]->id,
            'total_amount' => (79.99 * 1) + (299.99 * 1) + (49.99 * 3), // 529.95
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[2]->id, // Keyboard
            'quantity' => 1,
            'unit_price' => 79.99,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[3]->id, // Monitor
            'quantity' => 1,
            'unit_price' => 299.99,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $createdProducts[4]->id, // USB Hub
            'quantity' => 3,
            'unit_price' => 49.99,
        ]);

        // Order 3: Jane Smith - 2 Laptops
        $order3 = Order::create([
            'customer_id' => $createdCustomers[1]->id,
            'total_amount' => 999.99 * 2, // 1999.98
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $createdProducts[0]->id, // Laptop
            'quantity' => 2,
            'unit_price' => 999.99,
        ]);

        // Order 4: Bob Johnson - 5 Mice + 1 Keyboard
        $order4 = Order::create([
            'customer_id' => $createdCustomers[2]->id,
            'total_amount' => (29.99 * 5) + (79.99 * 1), // 229.94
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $createdProducts[1]->id, // Mouse
            'quantity' => 5,
            'unit_price' => 29.99,
        ]);

        OrderItem::create([
            'order_id' => $order4->id,
            'product_id' => $createdProducts[2]->id, // Keyboard
            'quantity' => 1,
            'unit_price' => 79.99,
        ]);

        $this->command->info('Demo data seeded successfully!');
        $this->command->line('Created ' . count($createdCustomers) . ' customers');
        $this->command->line('Created ' . count($createdProducts) . ' products');
        $this->command->line('Created 4 orders with multiple items');
    }
}
