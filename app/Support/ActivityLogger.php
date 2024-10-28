<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log an activity
     */
    public static function log(
        string $type,
        string $description,
        ?Model $subject = null,
        array $data = [],
        ?User $user = null
    ): Activity {
        return Activity::create([
            'user_id' => $user?->id ?? Auth::id(),
            'type' => $type,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
            'data' => $data,
        ]);
    }

    /**
     * Log an image generation activity
     */
    public static function logImageGeneration(
        string $type,
        string $description,
        Model $imageGeneration,
        array $data = []
    ): Activity {
        return static::log(
            $type,
            $description,
            $imageGeneration,
            array_merge([
                'prompt' => $imageGeneration->prompt,
                'settings' => $imageGeneration->settings,
            ], $data),
            $imageGeneration->user
        );
    }

    /**
     * Log a transaction activity
     */
    public static function logTransaction(
        string $type,
        string $description,
        Model $transaction,
        array $data = []
    ): Activity {
        return static::log(
            $type,
            $description,
            $transaction,
            array_merge([
                'amount' => $transaction->amount,
                'status' => $transaction->status,
            ], $data),
            $transaction->user
        );
    }

    /**
     * Log a system notification
     */
    public static function logNotification(
        string $type,
        string $description,
        Model $notification,
        array $data = []
    ): Activity {
        return static::log(
            $type,
            $description,
            $notification,
            array_merge([
                'title' => $notification->title,
                'message' => $notification->message,
            ], $data),
            $notification->user
        );
    }
}
