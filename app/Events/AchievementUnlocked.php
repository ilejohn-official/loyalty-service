<?php

namespace App\Events;

use App\Models\Achievement;
use App\DTOs\UserDto;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AchievementUnlocked
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public function __construct(
    public UserDto $user,
    public Achievement $achievement
  ) {}
}
