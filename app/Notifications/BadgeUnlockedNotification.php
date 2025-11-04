<?php

namespace App\Notifications;

use App\Events\BadgeUnlocked;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeUnlockedNotification extends Notification implements ShouldQueue
{
  use Queueable;

  public function __construct(
    protected BadgeUnlocked $event
  ) {}

  public function via($notifiable): array
  {
    return ['mail', 'database'];
  }

  public function toMail($notifiable): MailMessage
  {
    return (new MailMessage)
      ->subject('New Badge Unlocked! 🎖')
      ->line('Congratulations! You\'ve unlocked a new badge:')
      ->line($this->event->badge->name)
      ->line($this->event->badge->description)
      ->action('View Your Badges', url('/badges'));
  }

  public function toDatabase($notifiable): array
  {
    return [
      'message' => "You've unlocked the {$this->event->badge->name} badge!",
      'badge_id' => $this->event->badge->id,
      'badge_name' => $this->event->badge->name,
      'badge_description' => $this->event->badge->description,
    ];
  }
}
