<?php

namespace App\Service;

use App\Http\Resources\CustomerResource;
use App\Repository\CustomerRepository;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function listCustomer(int $perPage = 15)
    {
        $collection = $this->customerRepository->paginate($perPage);

        // Fixed: Use CustomerResource::collection
        return CustomerResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        try {
            // Ensure roles exist
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'customer']);
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

            // Check if email already exists
            $existingUser = \App\Models\User::where('email', $payload['email'])->first();
            if ($existingUser) {
                throw new \Exception('A user with this email already exists');
            }

            $existingCustomer = \App\Models\Customer::where('email', $payload['email'])->first();
            if ($existingCustomer) {
                throw new \Exception('A customer with this email already exists');
            }

            $sharedUuid = (string) \Illuminate\Support\Str::uuid();

            // Enforce static fallback password string as explicitly requested
            $plainPassword = $payload['password'] ?? 'password';
            $hashedPassword = \Illuminate\Support\Facades\Hash::make($plainPassword);

            // Create customer record first
            $customerData = [
                'uuid' => $sharedUuid,
                'name' => $payload['name'],
                'email' => $payload['email'],
            ];
            $customer = $this->customerRepository->create($customerData);

            // Create user credentials lookup table
            $user = \App\Models\User::create([
                'uuid'        => $sharedUuid,
                'name'        => $payload['name'],
                'email'       => $payload['email'],
                'password'    => $hashedPassword,
                'customer_id' => $customer->id, // Link user to customer
            ]);

            // Assign customer role using Spatie
            $user->assignRole('customer');

            // Clear permission cache
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return new \App\Http\Resources\CustomerResource($customer);
        } catch (\Exception $e) {
            \Log::error('Error creating customer: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCustomer(string $uuid)
    {
        $model = $this->customerRepository->findByUuid($uuid);

        return new CustomerResource($model);
    }

    public function getCustomerByField(string $field, $value)
    {
        $model = $this->customerRepository->findByField($field, $value);

        return new CustomerResource($model);
    }

    public function updateCustomer(string $uuid, array $payload)
    {
        $model = $this->customerRepository->update($uuid, $payload);

        return new CustomerResource($model);
    }

    public function deleteCustomer(string $uuid)
    {
        $this->customerRepository->delete($uuid);

        return true;
    }

    public function restoreCustomer(string $uuid)
    {
        $model = $this->customerRepository->restore($uuid);

        return new CustomerResource($model);
    }
}
