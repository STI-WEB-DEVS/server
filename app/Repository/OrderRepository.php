<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return Order::create($data);

    }
    
    public function createItem(Order $order, array $itemData){
        return $order->items()->create ($itemData);
    }

    public function findByUuid(string $uuid)
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Order::where($field, $value)->firstOrFail();
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

    public function restore(string $uuid)
    {
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
    public function getByCustomerUuid(string $customerUuid)
    {   
    // We find the customer first, then get their orders via the relationship
    $customer = Customer::where('uuid', $customerUuid)->firstOrFail();
        
    return Order::where('customer_id', $customer->id)->with('items')->get();
    }
}