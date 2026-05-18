<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            $customer = Customer::where('uuid', $payload['customer_uuid'])->firstOrFail();

            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => 0,
            ]);

            // Gather all product UUIDs
            $productUuids = array_column($payload['items'], 'product_uuid');

            // Query all products at once
            $products = Product::whereIn('uuid', $productUuids)->get()->keyBy('uuid');

            $orderItems = [];
            $total = 0;

            foreach ($payload['items'] as $item) {
                $uuid = $item['product_uuid'];
                if (!$products->has($uuid)) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Product with UUID {$uuid} not found.");
                }

                $product = $products->get($uuid);
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                $orderItems[] = [
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $total += $unitPrice * $quantity;
            }

            // Batch insert all order items in a single query
            if (!empty($orderItems)) {
                \App\Models\OrderItem::insert($orderItems);
            }

            $order->update(['total_amount' => $total]);
            $order->load(['customer', 'items.product']);

            return new OrderResource($order);
        });
    }

    public function getOrder(string $uuid)
    {
        $model = $this->orderRepository->findByUuid($uuid);
        return new OrderResource($model);
    }

    public function listOrdersByCustomer(string $customerUuid, int $perPage = 15)
    {
        $collection = $this->orderRepository->findByCustomerUuid($customerUuid, $perPage);
        return OrderResource::collection($collection);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function getSummary(string $from = null, string $to = null, string $customerUuid = null)
    {
        $query = \App\Models\Order::query();

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($customerUuid) {
            $customer = Customer::where('uuid', $customerUuid)->first();
            if ($customer) {
                $query->where('customer_id', $customer->id);
            } else {
                // No matching customer – return empty summary
                return [
                    'total_revenue' => 0.0,
                    'unique_customers' => 0,
                    'top_products' => collect([]),
                ];
            }
        }

        $orderIds = $query->pluck('id');

        $totalRevenue = (float) $query->sum('total_amount');
        $uniqueCustomersCount = $query->distinct('customer_id')->count('customer_id');

        // Top 5 most purchased products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->whereIn('order_items.order_id', $orderIds)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get()
            ->map(function($item) {
                $item->total_quantity = (int) $item->total_quantity;
                return $item;
            });

        return [
            'total_revenue' => $totalRevenue,
            'unique_customers' => $uniqueCustomersCount,
            'top_products' => $topProducts,
        ];
    }
}
