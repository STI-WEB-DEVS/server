<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\DB;

class OrderService
{
    // Define all required repositories
    protected OrderRepository $orderRepository;
    protected CustomerRepository $customerRepo;
    protected ProductRepository $productRepo;

    // Inject all three repositories via the constructor
    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepo,
        ProductRepository $productRepo
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepo = $customerRepo;
        $this->productRepo = $productRepo;
    }

    public function listOrder(int $perPage = 15)
    {
        $collection = $this->orderRepository->paginate($perPage);
        return OrderResource::collection($collection);
    }

    public function createOrder(array $payload)
    {
        return DB::transaction(function () use ($payload) {
            // 1. Find the customer
            $customer = $this->customerRepo->findByUuid($payload['customer_uuid']);
            
            $totalAmount = 0;
            $orderItems = [];
    
            // 2. Loop through items to calculate total and verify products
            foreach ($payload['items'] as $item) {
                $product = $this->productRepo->findByUuid($item['product_uuid']);
                
                $linePrice = $product->price * $item['quantity'];
                $totalAmount += $linePrice;
    
                // Prepare data for the OrderItem creation later
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }
    
            // 3. Create the parent Order (Table: orders)
            // Matches your $fillable: ['customer_id', 'total_amount']
            $order = $this->orderRepository->create([
                'customer_id'  => $customer->id,
                'total_amount' => $totalAmount,
            ]);
    
            // 4. Create each Order Item (Table: order_items)
            foreach ($orderItems as $itemData) {
                $itemData['order_id'] = $order->id;
                $this->orderRepository->createOrderItem($itemData);
            }
    
            return new OrderResource($order);
        });
    }

    public function getOrder(string $uuid)
    {
        // 1. Convert the UUID into an internal ID using your existing customerRepo
        $customer = $this->customerRepo->findByUuid($uuid);

        if (!$customer) {
            throw new \Exception("Customer not found.");
        }

        // 2. Call the method we just added to the Repository
        $orders = $this->orderRepository->getByCustomerId($customer->id);

        return OrderResource::collection($orders);
    }

    public function getOrderByField(string $field, $value)
    {
        $model = $this->orderRepository->findByField($field, $value);
        return new OrderResource($model);
    }

    public function updateOrder(string $uuid, array $payload)
    {
        $model = $this->orderRepository->update($uuid, $payload);
        return new OrderResource($model);
    }

    public function deleteOrder(string $uuid)
    {
        $this->orderRepository->delete($uuid);
        return true;
    }

    public function restoreOrder(string $uuid)
    {
        $model = $this->orderRepository->restore($uuid);
        return new OrderResource($model);
    }

    public function getOrdersByCustomer(string $customerUuid)
    {
    // 1. Convert the external UUID to an internal integer ID
    $customer = $this->customerRepo->findByUuid($customerUuid);

    if (!$customer) {
        // You can return a 404 or throw an exception depending on your setup
        abort(404, 'Customer not found');
    }

    // 2. Fetch the orders from the repository using the internal ID
    $orders = $this->orderRepository->getByCustomerId($customer->id);

    // 3. Return via the resource for consistent formatting
    return OrderResource::collection($orders);
    }
}