<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::latest()->paginate($perPage);
    }

    public function create(array $data): Order
{
    return Order::create([
        'customer_id'  => $data['customer_id'], // Now using numeric ID
        'total_amount' => $data['total_amount'],
        'status'       => $data['status'] ?? 'pending',
    ]);
}

    public function findByUuid(string $uuid): Order
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

    public function findByCustomerUuid(string $customerUuid)
    {
        return Order::whereHas('customer', function ($query) use ($customerUuid) {
        $query->where('uuid', $customerUuid);
        })
        ->with(['items.product'])
        ->latest()
        ->get();
    }

    public function findByField(string $field, $value): Order
    {
        return Order::where($field, $value)->firstOrFail();
    }

    public function update(string $uuid, array $payload): Order
    {
        $model = $this->findByUuid($uuid);
        $model->update($payload);
        return $model;
    }

    public function delete(string $uuid): bool
    {
        $model = $this->findByUuid($uuid);
        return (bool) $model->delete();
    }

    public function restore(string $uuid): Order
    {
        // Ensure your Order model uses the SoftDeletes trait for this to work
        $model = Order::withTrashed()->where('uuid', $uuid)->firstOrFail();
        $model->restore();
        return $model;
    }
}