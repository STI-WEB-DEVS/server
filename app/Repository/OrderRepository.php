<?php
 
namespace App\Repository;
 
use App\Models\Order;
use App\Models\Customer;
 
class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['items.product', 'customer'])->latest()->paginate($perPage);
    }
 
    public function create(array $payload)
    {
        return Order::create($payload);
    }
 
    public function findByUuid(string $uuid)
    {
        return Order::with(['items.product', 'customer'])
            ->where('uuid', $uuid)
            ->firstOrFail();
    }
 
    public function update(string $uuid, array $payload)
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }
 
    public function delete(string $uuid)
    {
        $model = $this->findByUuid($uuid);
        return $model->delete();
    }

    public function findByField(string $field, $value)
    {
        return Order::with(['items.product', 'customer'])
            ->where($field, $value)
            ->firstOrFail();
    }

    public function getOrdersByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();
 
        return Order::with(['items.product', 'customer'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate($perPage);
    }

    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();

        return $model;
    }
}
