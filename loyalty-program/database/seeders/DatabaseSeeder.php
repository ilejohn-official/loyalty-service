<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 10 random test users
        User::factory(10)->create();

        // Create specfic test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
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
