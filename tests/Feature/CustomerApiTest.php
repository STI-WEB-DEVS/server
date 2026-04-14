<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'user',
                'token',
            ]);
    }

    public function test_authenticated_user_can_manage_customers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $createResponse = $this->postJson('/api/customers', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $createResponse->assertCreated()
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com');

        $customer = Customer::where('email', 'jane@example.com')->firstOrFail();

        $this->getJson('/api/customers')
            ->assertOk()
            ->assertJsonFragment(['email' => 'jane@example.com']);

        $this->getJson('/api/customers/' . $customer->uuid)
            ->assertOk()
            ->assertJsonPath('data.uuid', $customer->uuid);

        $this->putJson('/api/customers/' . $customer->uuid, [
            'name' => 'Jane Updated',
            'email' => 'jane.updated@example.com',
        ])->assertOk()
            ->assertJsonPath('data.name', 'Jane Updated')
            ->assertJsonPath('data.email', 'jane.updated@example.com');

        $this->deleteJson('/api/customers/' . $customer->uuid)
            ->assertOk()
            ->assertJson(['message' => 'Deleted successfully']);
    }
}