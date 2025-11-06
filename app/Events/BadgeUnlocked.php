<?php

namespace App\Events;

use App\DTOs\UserDto;
use App\Models\Badge;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public UserDto $user,
        public Badge $badge
    ) {}
}
