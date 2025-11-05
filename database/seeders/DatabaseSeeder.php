<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 test users
        User::factory(10)->create();

        // Create test user for easy login
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create an admin user for testing admin routes
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'password' => Hash::make('admin_password'),
        ]);

        // Run other seeders
        $this->call([
            PaymentTransactionSeeder::class,
            LoyaltyTransactionSeeder::class,
            AchievementProgressSeeder::class,
            AchievementSeeder::class,
            BadgeSeeder::class,
        ]);
    }
}
