<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class NewComment extends Notification
{
    use Queueable;

    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type' => 'comment',
            'comment_id' => $this->comment->id,
            'image_generation_id' => $this->comment->image_generation_id,
            'user_id' => $this->comment->user_id,
            'user_name' => $this->comment->user->name,
            'message' => "{$this->comment->user->name} commented on your image",
        ]);
    }
}
