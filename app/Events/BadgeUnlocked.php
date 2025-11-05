<?php

namespace App\Events;

use App\Models\Badge;
use App\DTOs\UserDto;
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
