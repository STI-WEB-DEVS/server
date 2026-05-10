<?php

namespace App\Service;

use App\Models\Customer; 

use App\Repository\CustomersRepository;
use App\Http\Resources\CustomersResource;

class CustomersService
{
    private CustomersRepository $customersRepository;

    public function __construct(CustomersRepository $customersRepository) 
    {
        $this->customersRepository = $customersRepository;
    }

    public function listCustomers(int $perPage = 15)
    {
        $collection = $this->customersRepository->paginate($perPage);
        return CustomersResource::collection($collection);
    }

    public function createCustomers(array $payload)
    {
        // Create the customer record
        $model = $this->customersRepository->create($payload);
        
        // Create a corresponding user account with customer role
        $user = \App\Models\User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => \Illuminate\Support\Facades\Hash::make('password'), // Default password
            'customer_id' => $model->id,
        ]);
        
        // Assign customer role
        $user->assignRole('customer');
        
        return new CustomersResource($model);
    }

    public function getCustomers(string $uuid)
    {
        $model = $this->customersRepository->findByUuid($uuid);
        return new CustomersResource($model);
    }


    public function getCustomersByField(string $field, $value)
    {
        $model = $this->customersRepository->findByField($field, $value);
        return new CustomersResource($model);
    }

    public function updateCustomers(string $uuid, array $payload)
    {
        $model = $this->customersRepository->update($uuid, $payload);
        return new CustomersResource($model);
    }

    public function deleteCustomers(string $uuid)
    {
        $this->customersRepository->delete($uuid);
        
        // Renumber all customer IDs sequentially
        $this->renumberCustomerIds();
        
        return true;
    }

    /**
     * Renumbers all customer IDs sequentially after deletion
     * Also updates all foreign key references in orders table
     */
    private function renumberCustomerIds()
    {
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Get all customers ordered by current ID
        $customers = Customer::orderBy('id')->get();
        
        // Create a mapping of old IDs to new IDs
        $idMapping = [];
        $newId = 1;
        
        foreach ($customers as $customer) {
            $oldId = $customer->id;
            $idMapping[$oldId] = $newId;
            $newId++;
        }
        
        // Update orders table to use new customer IDs
        foreach ($idMapping as $oldId => $newCustomerId) {
            \DB::table('orders')
                ->where('customer_id', $oldId)
                ->update(['customer_id' => $newCustomerId + 10000]); // Temporary ID to avoid conflicts
        }
        
        // Update customer IDs
        foreach ($idMapping as $oldId => $newCustomerId) {
            \DB::table('customers')
                ->where('id', $oldId)
                ->update(['id' => $newCustomerId]);
        }
        
        // Update orders back to final customer IDs
        foreach ($idMapping as $oldId => $newCustomerId) {
            \DB::table('orders')
                ->where('customer_id', $newCustomerId + 10000)
                ->update(['customer_id' => $newCustomerId]);
        }
        
        // Reset auto-increment to next available ID
        \DB::statement('ALTER TABLE customers AUTO_INCREMENT = ' . $newId);
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function restoreCustomers(string $uuid)
    {
        $model = $this->customersRepository->restore($uuid);
        return new CustomersResource($model);
    }


    public function getCustomerOrders(string $uuid)
    {
        $customer = Customer::where('uuid', $uuid)->firstOrFail();

        // ✅ This is where your line goes
        $orders = $customer->orders()->with(['items.product'])->get();

        return [
            'customer' => [
                'uuid' => $customer->uuid,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
            'orders' => $orders->map(function ($order) {
                return [
                    'uuid' => $order->uuid,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'product_uuid' => $item->product->uuid,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'subtotal' => $item->quantity * $item->unit_price,
                        ];
                    }),
                ];
            }),
        ];
    }

}