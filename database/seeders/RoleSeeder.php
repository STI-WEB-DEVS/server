<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
// ADD THESE TWO IMPORTS BELOW:
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Now PHP knows exactly where to look for PermissionRegistrar
        app(PermissionRegistrar::class)->forgetCachedPermissions();
 
        $roles = [
            'admin',
            'customer'
        ];
 
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}