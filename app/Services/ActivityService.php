<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityService
{
    public function log(
        int $userId,
        string $type,
        string $subjectType,
        int $subjectId,
        string $description,
        array $data = []
    ): Activity {
        return Activity::create([
            'user_id' => $userId,
            'type' => $type,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'data' => $data,
        ]);
    }

    public function getActivities(
        int $userId,
        ?string $type = null,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = Activity::with(['subject', 'user'])
            ->where('user_id', $userId)
            ->latest();

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('description', 'like', "%{$search}%")
                    ->orWhereHas('subject', function (Builder $query) use ($search) {
                        $query->where('prompt', 'like', "%{$search}%");
                    });
            });
        }

        return $query->paginate($perPage);
    }

    public function getRecentActivities(int $userId, int $limit = 5): array
    {
        return Activity::with(['subject', 'user'])
            ->where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function deleteActivity(Activity $activity): bool
    {
        return $activity->delete();
    }
}
