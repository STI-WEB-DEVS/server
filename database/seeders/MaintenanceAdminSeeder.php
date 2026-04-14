<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MaintenanceAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'maintenance.admin@sti.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Maintenance Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
