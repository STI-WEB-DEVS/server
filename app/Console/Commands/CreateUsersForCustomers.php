<?php
namespace App\Console\Commands;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
class CreateUsersForCustomers extends Command {
 protected $signature = 'customers:create-users';
 protected $description = 'Create or link user accounts for customers';
 public function handle(): int

{
 DB::transaction(function () {
 Customer::query()->chunkById(100, function ($customers) {
 foreach ($customers as $customer) {
 if (!$customer->email) {
 $this->warn("Skipped customer {$customer->id}: no email");
 continue;

}
 // Already linked to a user
 $existingLinkedUser = User::where('customer_id', $customer->id)->first();
 if ($existingLinkedUser) {
 $this->info("Already linked: {$customer->email}");
 continue;

}
 // Same email exists, link it
 $user = User::where('email', $customer->email)->first();
 if ($user) {
 $user->update(['customer_id' => $customer->id,]);
 $this->info("Linked existing user: {$customer->email}");
 continue;

}
 // No user exists, create one
 User::create([
 'company_id' => null,
 'customer_id' => $customer->id,
 'name' => $customer->name,
 'email' => $customer->email,
 'password' => 'password',]);
 $this->info("Created user: {$customer->email}");

}
 });
 });
 $this->info('Done.');
 return self::SUCCESS;

}
}