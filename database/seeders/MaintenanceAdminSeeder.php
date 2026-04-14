<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MaintenanceAdminSeeder extends Seeder
{
    /**
     * Seed the application's admin account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sti.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Admin User',
                'password' => 'admin123',
            ]
        );
    }
}