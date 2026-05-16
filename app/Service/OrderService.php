<?php

namespace App\Service;

use App\Models\Order;
use App\Models\Product;
use App\Repository\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function placeOrder($customer, array $items)
    {
        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $product = Product::where('uuid', $item['product_uuid'])->firstOrFail();
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $order = $this->orderRepository->create([
                'customer_id' => $customer->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                $this->orderRepository->createItem($itemData);
            }

            DB::commit();

            return $order->load('items.product');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function listOrdersForCustomer(int $customerId, int $perPage = 15)
    {
        return $this->orderRepository->paginateForCustomer($customerId, $perPage);
    }

    public function getOrder(string $uuid)
    {
        return $this->orderRepository->findByUuid($uuid)->load('items.product');
    }

    public function updateOrder(string $uuid, array $data)
    {
        return $this->orderRepository->update($uuid, $data);
    }

    public function deleteOrder(string $uuid)
    {
        return $this->orderRepository->delete($uuid);
    }

    public function restoreOrder(string $uuid)
    {
        return $this->orderRepository->restore($uuid);
    }

    public function getAllOrders()
    {
        return $this->orderRepository->getAll();
    }

    public function getOrderSummary($startDate = null, $endDate = null)
    {
        $revenueQuery = DB::table('orders');
        $orderQuery = DB::table('orders');
        $topProductsQuery = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5);

        $salesHistoryQuery = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as amount'))
            ->groupBy('date')
            ->orderBy('date');

        if ($startDate) {
            $revenueQuery->where('created_at', '>=', $startDate . ' 00:00:00');
            $orderQuery->where('created_at', '>=', $startDate . ' 00:00:00');
            $topProductsQuery->where('order_items.created_at', '>=', $startDate . ' 00:00:00');
            $salesHistoryQuery->where('created_at', '>=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $revenueQuery->where('created_at', '<=', $endDate . ' 23:59:59');
            $orderQuery->where('created_at', '<=', $endDate . ' 23:59:59');
            $topProductsQuery->where('order_items.created_at', '<=', $endDate . ' 23:59:59');
            $salesHistoryQuery->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        if (!$startDate && !$endDate) {
            $salesHistoryQuery->where('created_at', '>=', now()->subDays(7));
        }

        return [
            'total_revenue' => $revenueQuery->sum('total_amount'),
            'total_customers' => DB::table('customers')->count(),
            'total_orders' => $orderQuery->count(),
            'top_products' => $topProductsQuery->get(),
            'sales_history' => $salesHistoryQuery->get()
        ];
    }
}
