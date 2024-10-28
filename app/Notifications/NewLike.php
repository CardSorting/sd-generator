<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\ImageGeneration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLike extends Notification
{
    use Queueable;

    protected $imageGeneration;
    protected $user;

    public function __construct(User $user, ImageGeneration $imageGeneration)
    {
        $this->imageGeneration = $imageGeneration;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New Like',
            'message' => "{$this->user->name} liked your image",
            'image_generation_id' => $this->imageGeneration->id,
            'user_id' => $this->user->id,
        ];
    }
}
