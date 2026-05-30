<?php

namespace App\Service;

use App\Models\Customer;
use App\Models\Product;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            $total = 0;
            foreach ($payload['items'] as $item) {
                $product = Product::where('uuid', $item['product_uuid'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $unitPrice = $product->price;
                $quantity  = $item['quantity'];

                if ($product->quantity < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product->name}.",
                    ]);
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'unit_price' => $unitPrice,
                ]);

                $product->decrement('quantity', $quantity);

                $total += $unitPrice * $quantity;
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

    public function getSummary(array $filters)
    {
        $startDate = $filters['from'] ?? $filters['start_date'] ?? null;
        $endDate = $filters['to'] ?? $filters['end_date'] ?? null;

        $query = \App\Models\Order::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $totalRevenue = (float) (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();
        
        // Get unique customers who ordered in this period with their details
        $customerDetails = \App\Models\Customer::query()
            ->whereIn('id', (clone $query)->pluck('customer_id'))
            ->get(['name', 'email', 'uuid']);
            
        $customerCount = $customerDetails->count();

        $topProducts = \App\Models\OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->when($startDate, fn($q) => $q->whereDate('orders.created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('orders.created_at', '<=', $endDate))
            ->select(
                'products.id',
                'products.uuid',
                'products.name',
                'products.price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.uuid', 'products.name', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'from'           => $startDate,
            'to'             => $endDate,
            'total_revenue'  => $totalRevenue,
            'total_orders'   => $totalOrders,
            'customer_count' => $customerCount,
            'customers'      => $customerDetails,
            'top_products'   => $topProducts
        ];
    }
}
