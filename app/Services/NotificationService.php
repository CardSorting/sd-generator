<?php

namespace App\Services;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function create(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function getNotifications(
        int $userId,
        bool $unreadOnly = false,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Notification::where('user_id', $userId)
            ->latest();

        if ($unreadOnly) {
            $query->whereNull('read_at');
        }

        return $query->paginate($perPage);
    }

    public function markAsRead(Notification $notification): bool
    {
        return $notification->update(['read_at' => Carbon::now()]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }

    public function deleteNotification(Notification $notification): bool
    {
        return $notification->delete();
    }

    public function deleteOldNotifications(int $daysOld = 30): int
    {
        return Notification::where('created_at', '<', Carbon::now()->subDays($daysOld))
            ->whereNotNull('read_at')
            ->delete();
    }
}
