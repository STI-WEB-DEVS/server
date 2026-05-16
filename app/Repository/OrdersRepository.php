<?php

namespace App\Repository;

use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdersRepository
{
    public function paginate(int $perPage = 15)
    {
        // return Order::latest()->paginate($perPage);
        return Order::with(['customer', 'orderItems.product'])
        ->paginate($perPage);
    }
    
    public function paginateByCustomerId(int $customerId, int $perPage = 15)
    {
        return Order::with(['customer', 'orderItems.product'])
            ->where('customer_id', $customerId)
            ->paginate($perPage);
    }

    public function create(array $payload)
    {
        return Order::create($payload);
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
    public function getDailySalesTrend($fromDate, $toDate)
    {
        return Order::query()
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(total_amount) as sales')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getSummary(string $from, string $to): array
    {
        $fromDate = Carbon::parse($from)->startOfDay();
        $toDate = Carbon::parse($to)->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [$fromDate, $toDate]);

        /*
        |--------------------------------------------------------------------------
        | Total Sales
        |--------------------------------------------------------------------------
        */

        $totalSales = (clone $ordersQuery)
            ->sum('total_amount');

        /*
        |--------------------------------------------------------------------------
        | Total Customers
        |--------------------------------------------------------------------------
        */

        $totalCustomers = (clone $ordersQuery)
            ->distinct('customer_id')
            ->count('customer_id');

        /*
        |--------------------------------------------------------------------------
        | Top Products
        |--------------------------------------------------------------------------
        */

        $topProducts = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $dailySalesTrend = $this->getDailySalesTrend(
            $fromDate,
            $toDate
        );

        return [
            'from' => $fromDate,
            'to' => $toDate,
            'total_sales' => $totalSales,
            'total_customers' => $totalCustomers,
            'top_products' => $topProducts,
            'daily_sales_trend' => $dailySalesTrend,
        ];
    }
}

