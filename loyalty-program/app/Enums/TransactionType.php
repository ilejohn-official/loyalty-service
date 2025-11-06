<?php

namespace App\Enums;

enum TransactionType: string
{
    case PURCHASE = 'purchase';
    case BONUS = 'bonus';
    case REFERRAL = 'referral';
}
