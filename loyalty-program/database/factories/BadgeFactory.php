<?php

namespace Database\Factories;

use App\Enums\BadgeType;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    private static int $counter = 0;

    public function definition(): array
    {
        $badgeTypes = BadgeType::cases();

        $badgeType = $badgeTypes[self::$counter % count($badgeTypes)];
        self::$counter++;

        return [
            'user_id' => \App\Models\User::factory(),
            'badge_type' => $badgeType,
            'level' => $badgeType->getDefaultLevel(),
            'earned_at' => $this->faker->dateTime(),
        ];
    }

    public function bronzeSpender(): static
    {
        return $this->state(fn () => [
            'badge_type' => BadgeType::BRONZE_SPENDER,
            'level' => 1,
        ]);
    }

    public function silverSpender(): static
    {
        return $this->state(fn () => [
            'badge_type' => BadgeType::SILVER_SPENDER,
            'level' => 2,
        ]);
    }

    public function goldSpender(): static
    {
        return $this->state(fn () => [
            'badge_type' => BadgeType::GOLD_SPENDER,
            'level' => 3,
        ]);
    }

    public function loyalCustomer(): static
    {
        return $this->state(fn () => [
            'badge_type' => BadgeType::LOYAL_CUSTOMER,
            'level' => 1,
        ]);
    }

    public function vipMember(): static
    {
        return $this->state(fn () => [
            'badge_type' => BadgeType::VIP_MEMBER,
            'level' => 1,
        ]);
    }
}
