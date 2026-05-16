<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::whereNull('customer_id')->get();
foreach ($users as $u) {
    $c = \App\Models\Customer::create(['name' => $u->name, 'email' => $u->email]);
    $u->customer_id = $c->id;
    $u->save();
}
echo "Customers created for users without them.\n";
