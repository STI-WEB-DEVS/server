<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            MaintenanceAdminSeeder::class,
            CustomerSeeder::class,
        ]);

        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@sti.com',
                'password' => 'test123',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                ]
            );
        }
    }
}
