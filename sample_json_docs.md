orders

{
  "customer_uuid": "3f5f2d2e-0d9a-4c2c-a2a7-2a3a42c01f15",
  "items": [
    {
      "product_uuid": "7c2d9b1e-ff25-4df9-95b1-0a2c2f4e5c77",
      "quantity": 2
    },
    {
      "product_uuid": "8b1b5c47-12f1-4b0c-9c1e-87a4f6fce1a0",
      "quantity": 1
    }
  ]
}



# Commands
git clone -b Oracion_Jericca https://github.com/STI-WEB-DEVS/server.git
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed