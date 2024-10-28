<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewFollower extends Notification
{
    use Queueable;

    protected $follower;

    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'follow',
            'user_id' => $this->follower->id,
            'user_name' => $this->follower->name,
            'message' => "{$this->follower->name} started following you",
        ]);
    }
}
