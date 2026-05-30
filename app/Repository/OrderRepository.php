<?php

namespace App\Repository;

use App\Models\Order;
use App\Models\Customer;

class OrderRepository
{
    public function paginate(int $perPage = 15)
    {
        return Order::with(['customer', 'items.product'])->latest()->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
    }

    public function findByUuid(string $uuid)
    {
        return Order::with(['customer', 'items.product'])->where('uuid', $uuid)->firstOrFail();
    }

    public function findByField(string $field, $value)
    {
        return Order::where($field, $value)->firstOrFail();
    }

    public function findByCustomerUuid(string $customerUuid, int $perPage = 15)
    {
        $customer = Customer::where('uuid', $customerUuid)->firstOrFail();
        return Order::with(['customer', 'items.product'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate($perPage);
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

    public function getSummary(?string $from, ?string $to): array
{
    $query = Order::query();

    if ($from) $query->whereDate('created_at', '>=', $from);
    if ($to)   $query->whereDate('created_at', '<=', $to);

    $orders   = $query->get();
    $orderIds = $orders->pluck('id');

    $totalRevenue   = $orders->sum('total_amount');
    $totalCustomers = $orders->pluck('customer_id')->unique()->count();

    $topProducts = \App\Models\OrderItem::whereIn('order_id', $orderIds)
        ->selectRaw('product_id, SUM(quantity) as total_qty')
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->with('product')
        ->get()
        ->map(fn($item) => [
            'name'      => $item->product->name ?? 'Unknown',
            'total_qty' => (int) $item->total_qty,
        ]);

    return [
        'total_revenue'   => $totalRevenue,
        'total_customers' => $totalCustomers,
        'top_products'    => $topProducts,
    ];
}
}
