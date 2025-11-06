<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Enums\BadgeType;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = app(\Faker\Generator::class);
        $userIds = range(1, 10);
        $badgeTypes = BadgeType::cases();
        foreach ($userIds as $userId) {
            // Randomly assign 0-3 badges to each user
            $assignedTypes = $faker->randomElements(
                $badgeTypes,
                $faker->numberBetween(0, 3)
            );

            foreach ($assignedTypes as $type) {
                $level = $type === BadgeType::LOYAL_CUSTOMER
                    ? $faker->numberBetween(1, 2)
                    : $type->getDefaultLevel();
                Badge::create([
                    'user_id' => $userId,
                    'badge_type' => $type,
                    'level' => $level,
                    'earned_at' => $faker->dateTimeBetween('-2 months', 'now'),
                ]);
            }
        }
    }
}
