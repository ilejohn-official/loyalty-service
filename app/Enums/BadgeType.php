<?php

namespace App\Enums;

enum BadgeType: string
{
    case BRONZE_SPENDER = 'bronze_spender';
    case SILVER_SPENDER = 'silver_spender';
    case GOLD_SPENDER = 'gold_spender';
    case LOYAL_CUSTOMER = 'loyal_customer';
    case VIP_MEMBER = 'vip_member';

    public function getDefaultLevel(): int
    {
        return match ($this) {
            self::BRONZE_SPENDER => 1,
            self::SILVER_SPENDER => 2,
            self::GOLD_SPENDER => 3,
            self::LOYAL_CUSTOMER, self::VIP_MEMBER => 1,
        };
    }
}
