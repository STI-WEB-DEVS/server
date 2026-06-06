<?php

namespace App\Service;

use App\Models\User;
use App\Http\Resources\CustomerResource;
use App\Repository\CustomerRepository;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

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

        return CustomerResource::collection($collection);
    }

    public function createCustomer(array $payload)
    {
        $model = DB::transaction(function () use ($payload) {
            $customer = $this->customerRepository->create($payload);

            $user = User::updateOrCreate(
                ['email' => $customer->email],
                [
                    'customer_id' => $customer->id,
                    'name' => $customer->name,
                    'password' => config('auth.customer_default_password', 'password'),
                ]
            );

            Role::firstOrCreate(['name' => 'customer']);
            $user->assignRole('customer');

            return $customer;
        });

        return new CustomerResource($model);
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
