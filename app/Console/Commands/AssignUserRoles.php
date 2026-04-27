<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AssignUserRoles extends Command
{
    protected $signature = 'users:assign-roles';
    protected $description = 'Assign customer or admin role to users without existing roles';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'customer']);
        Role::firstOrCreate(['name' => 'admin']);

        User::query()
            ->with('roles')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    // Do not reverse/change users that already have a role
                    if ($user->roles->isNotEmpty()) {
                        $this->info("Skipped {$user->email}: already has role");
                        continue;
                    }

                    if ($user->customer_id) {
                        $user->assignRole('customer');
                        $this->info("Assigned customer role: {$user->email}");
                    } else {
                        $user->assignRole('admin');
                        $this->info("Assigned admin role: {$user->email}");
                    }
                }
            });

        $this->info('Done.');
        return self::SUCCESS;
    }
}
