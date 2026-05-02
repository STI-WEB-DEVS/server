<?php
 
namespace Database\Seeders;
 
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
 
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
 
        $roles = [
            'admin',
            'customer'
        ];
 
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
 
        // Assign admin role to test@cs.com
        $user = User::where('email', 'test@cs.com')->first();
        if ($user) {
            $user->syncRoles('admin');
            $this->command->info('Admin role assigned to test@cs.com');
        }
    }
}