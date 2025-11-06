<?php

namespace App\Notifications;

use App\Events\AchievementUnlocked;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchievementUnlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected AchievementUnlocked $event
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Achievement Unlocked! 🏆')
            ->line('Congratulations! You\'ve unlocked a new achievement:')
            ->line($this->event->achievement->name)
            ->line($this->event->achievement->description)
            ->action('View Your Achievements', url('/achievements'));
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message' => "You've unlocked the {$this->event->achievement->name} achievement!",
            'achievement_id' => $this->event->achievement->id,
            'achievement_name' => $this->event->achievement->name,
            'achievement_description' => $this->event->achievement->description,
        ];
    }
}
