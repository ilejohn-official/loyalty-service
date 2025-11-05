<?php

namespace App\Listeners;

use App\DTOs\UserDto;
use App\Events\BadgeUnlocked;
use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BadgeUnlockedNotification;
use App\Notifications\AchievementUnlockedNotification;

class NotifyUserOfAchievement implements ShouldQueue
{
  public function handle(AchievementUnlocked|BadgeUnlocked $event): void
  {
    $notificationClass = match (true) {
      $event instanceof AchievementUnlocked => AchievementUnlockedNotification::class,
      $event instanceof BadgeUnlocked => BadgeUnlockedNotification::class,
    };

    // Support UserDto (standalone microservice) and regular User models.
    if ($event->user instanceof UserDto) {
      if ($event->user->email) {
        Notification::route('mail', $event->user->email)->notify(new $notificationClass($event));
      } else {
        Log::warning('NotifyUserOfAchievement: user dto has no email', ['user' => $event->user->toArray()]);
      }
    } else {
      Notification::send($event->user, new $notificationClass($event));
    }
  }
}
