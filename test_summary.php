<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$o = new \App\Models\Order();
$o->customer_id=1;
$o->total_amount=100;
$o->save();

$oi = new \App\Models\OrderItem();
$oi->order_id=$o->id;
$oi->product_id=2;
$oi->quantity=1;
$oi->unit_price=100;
$oi->save();

echo json_encode((new \App\Repository\OrderRepository())->getSummary('2020-01-01 00:00:00', '2030-01-01 00:00:00'));
