<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Events\AchievementUnlocked;
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

    Notification::send($event->user, new $notificationClass($event));
  }
}
