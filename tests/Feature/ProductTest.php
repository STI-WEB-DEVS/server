<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_create_product_with_negative_or_zero_price(): void
    {
        // 0 Price
        $response = $this->postJson('/api/products', [
            'name' => 'Zero Price Product',
            'description' => 'Some description',
            'price' => 0,
            'stock' => 10,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);

        // Negative Price
        $response = $this->postJson('/api/products', [
            'name' => 'Negative Price Product',
            'description' => 'Some description',
            'price' => -5,
            'stock' => 10,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    }

    public function test_can_create_product_with_positive_price(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Positive Price Product',
            'description' => 'Some description',
            'price' => 10.99,
            'stock' => 10,
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('products', [
            'name' => 'Positive Price Product',
            'price' => 10.99,
            'stock' => 10,
        ]);
    }

    public function test_cannot_update_product_with_negative_or_zero_price(): void
    {
        $product = Product::create([
            'name' => 'Initial Product',
            'description' => 'Some desc',
            'price' => 15.00,
            'stock' => 20,
        ]);

        // Try updating to negative price
        $response = $this->putJson("/api/products/{$product->uuid}", [
            'name' => 'Updated Product',
            'description' => 'Some desc',
            'price' => -2.50,
            'stock' => 5,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['price']);
    }

    public function test_updating_product_stock_adds_to_previous_stock(): void
    {
        $product = Product::create([
            'name' => 'Stock Test Product',
            'description' => 'Some desc',
            'price' => 15.00,
            'stock' => 20,
        ]);

        // Send 'stock' = 5 in the update payload. It should be added, resulting in 25.
        $response = $this->putJson("/api/products/{$product->uuid}", [
            'name' => 'Stock Test Product Updated',
            'description' => 'Some desc',
            'price' => 15.00,
            'stock' => 5,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', [
            'uuid' => $product->uuid,
            'stock' => 25,
        ]);
    }

    public function test_cannot_create_product_with_numeric_name(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => '12345',
            'description' => 'Some description',
            'price' => 10.00,
            'stock' => 10,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    public function test_cannot_update_product_with_numeric_name(): void
    {
        $product = Product::create([
            'name' => 'Valid Product Name',
            'description' => 'Some desc',
            'price' => 15.00,
            'stock' => 20,
        ]);

        $response = $this->putJson("/api/products/{$product->uuid}", [
            'name' => '999.99',
            'price' => 15.00,
            'stock' => 10,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}

