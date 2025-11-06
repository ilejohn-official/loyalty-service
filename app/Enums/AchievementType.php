<?php

namespace App\Enums;

enum AchievementType: string
{
    case FIRST_PURCHASE = 'first_purchase';
    case SPEND_AMOUNT_100 = 'spend_amount_100';
    case SPEND_AMOUNT_1000 = 'spend_amount_1000';
    case PURCHASE_COUNT_5 = 'purchase_count_5';
    case PURCHASE_COUNT_10 = 'purchase_count_10';

    public function getTargetValue(): float|int
    {
        return match ($this) {
            self::FIRST_PURCHASE => 1,
            self::SPEND_AMOUNT_100 => 100,
            self::SPEND_AMOUNT_1000 => 1000,
            self::PURCHASE_COUNT_5 => 5,
            self::PURCHASE_COUNT_10 => 10,
        };
    }

    public function getMilestone(): string
    {
        return match ($this) {
            self::FIRST_PURCHASE => 'First purchase completed',
            self::SPEND_AMOUNT_100 => 'Spent $100',
            self::SPEND_AMOUNT_1000 => 'Spent $1000',
            self::PURCHASE_COUNT_5 => '5 purchases completed',
            self::PURCHASE_COUNT_10 => '10 purchases completed',
        };
    }
}
