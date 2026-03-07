    <?php

    namespace App\Service;

    use App\Repository\OrderRepository;
    use App\Repository\CustomerRepository;
    use App\Http\Resources\OrderResource;

    class OrderService
    {
        private OrderRepository $orderRepository;
        private CustomerRepository $customerRepository;

        public function __construct(
            OrderRepository $orderRepository,
            CustomerRepository $customerRepository
        ) {
            $this->orderRepository = $orderRepository;
            $this->customerRepository = $customerRepository;
        }
        

        public function listOrder(int $perPage = 15)
        {
            $collection = $this->orderRepository->paginate($perPage);
            return OrderResource::collection($collection);
        }

        // public function createOrder(array $payload)
        // {
        //     $customer = $this->customerRepository->findByUuid($payload->cu)

        //     $model = $this->orderRepository->create($payload);
        //     // add to payload $customer->id
        //     return new OrderResource($model);
        // }

        //'
        public function createOrder(array $payload)
        {
            if (!isset($payload['customer_uuid'])) {
                throw new \InvalidArgumentException("Customer UUID is required to place an order.");
            }

            $customer = $this->customerRepository->findByUuid($payload['customer_uuid']);
            $payload['customer_id'] = $customer->id;

            $total = 0;
            $products = [];

            foreach ($payload['products'] ?? [] as $item) {
                $lineTotal = $item['price'] * $item['quantity'];
                $total += $lineTotal;

                $products[] = [
                    'product_name' => $item['product_name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'line_total'   => $lineTotal,
                ];
            }

            // Only pass fields that exist in orders table
            $orderData = [
                'customer_id'  => $payload['customer_id'],
                'total_amount' => $total,
            ];

            $order = $this->orderRepository->create($orderData);

            // Save products separately if you have an order_items table
            foreach ($products as $product) {
                $order->items()->create($product);
            }

            return new OrderResource($order->load('items'));
        }


        //'
        public function getOrder(string $uuid)
        {
            $model = $this->orderRepository->findByUuid($uuid);
            return new OrderResource($model);
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
    }