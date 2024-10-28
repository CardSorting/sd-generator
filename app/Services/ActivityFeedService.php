<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ActivityFeedService extends BaseService
{
    /**
     * Get paginated activities for a user
     */
    public function getActivities(
        User $user,
        ?string $type = null,
        ?string $search = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Activity::query()
            ->with(['subject'])
            ->where('user_id', $user->id)
            ->latest();

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhereHas('subject', function (Builder $query) use ($search) {
                        $query->where('prompt', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get recent activities for a user
     */
    public function getRecentActivities(User $user, int $limit = 5): array
    {
        return Activity::query()
            ->with(['subject'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit($limit)
            ->get()
            ->groupBy(function ($activity) {
                return $activity->created_at->format('Y-m-d');
            })
            ->toArray();
    }

    /**
     * Get activity statistics for a user
     */
    public function getStatistics(User $user): array
    {
        $query = Activity::query()->where('user_id', $user->id);

        return [
            'total' => $query->count(),
            'by_type' => $query->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'recent_trend' => $query->selectRaw('DATE(created_at) as date, count(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray(),
        ];
    }

    /**
     * Get activity feed filters
     */
    public function getFilters(): array
    {
        return [
            'types' => [
                'image_generation' => 'Image Generations',
                'transaction' => 'Transactions',
                'notification' => 'Notifications',
            ],
            'date_ranges' => [
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'last_7_days' => 'Last 7 Days',
                'last_30_days' => 'Last 30 Days',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
            ],
        ];
    }

    /**
     * Parse activity data for display
     */
    public function parseActivityData(Activity $activity): array
    {
        $data = [
            'id' => $activity->id,
            'type' => $activity->type,
            'description' => $activity->description,
            'created_at' => $activity->created_at->diffForHumans(),
            'date' => $activity->created_at->format('Y-m-d H:i:s'),
            'data' => $activity->data,
        ];

        if ($activity->subject) {
            $data['subject'] = match ($activity->type) {
                'image_generation' => $this->parseImageGenerationData($activity),
                'transaction' => $this->parseTransactionData($activity),
                'notification' => $this->parseNotificationData($activity),
                default => null,
            };
        }

        return $data;
    }

    private function parseImageGenerationData(Activity $activity): array
    {
        return [
            'id' => $activity->subject->id,
            'prompt' => $activity->subject->prompt,
            'status' => $activity->subject->status,
            'thumbnail_url' => $activity->subject->thumbnail_url,
            'image_url' => $activity->subject->image_url,
            'settings' => $activity->subject->settings,
        ];
    }

    private function parseTransactionData(Activity $activity): array
    {
        return [
            'id' => $activity->subject->id,
            'type' => $activity->subject->type,
            'amount' => $activity->subject->amount,
            'description' => $activity->subject->description,
            'status' => $activity->subject->status,
        ];
    }

    private function parseNotificationData(Activity $activity): array
    {
        return [
            'id' => $activity->subject->id,
            'title' => $activity->subject->title,
            'message' => $activity->subject->message,
            'type' => $activity->subject->type,
            'read_at' => $activity->subject->read_at?->diffForHumans(),
        ];
    }
}
