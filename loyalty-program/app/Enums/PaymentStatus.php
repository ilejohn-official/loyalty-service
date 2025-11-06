<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case FAILED = 'failed';
}
