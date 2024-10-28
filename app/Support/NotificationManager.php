<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationManager
{
    /**
     * Create a new notification
     */
    public static function create(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create a success notification
     */
    public static function success(
        User $user,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return static::create($user, 'success', $title, $message, $data);
    }

    /**
     * Create an error notification
     */
    public static function error(
        User $user,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return static::create($user, 'error', $title, $message, $data);
    }

    /**
     * Create a warning notification
     */
    public static function warning(
        User $user,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return static::create($user, 'warning', $title, $message, $data);
    }

    /**
     * Create an info notification
     */
    public static function info(
        User $user,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return static::create($user, 'info', $title, $message, $data);
    }

    /**
     * Get unread notifications for a user
     */
    public static function getUnread(User $user): Collection
    {
        return $user->notifications()
            ->whereNull('read_at')
            ->latest()
            ->get();
    }

    /**
     * Mark notifications as read
     */
    public static function markAsRead(Collection $notifications): void
    {
        $notifications->each->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead(User $user): void
    {
        $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications
     */
    public static function deleteOld(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->whereNotNull('read_at')
            ->delete();
    }
}
