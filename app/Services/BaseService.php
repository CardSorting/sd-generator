<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    /**
     * Log an info message
     */
    protected function logInfo(string $message, array $context = []): void
    {
        Log::info(static::class . ': ' . $message, $context);
    }

    /**
     * Log an error message
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error(static::class . ': ' . $message, $context);
    }

    /**
     * Log a warning message
     */
    protected function logWarning(string $message, array $context = []): void
    {
        Log::warning(static::class . ': ' . $message, $context);
    }

    /**
     * Log a debug message
     */
    protected function logDebug(string $message, array $context = []): void
    {
        Log::debug(static::class . ': ' . $message, $context);
    }

    /**
     * Execute a callback with error handling
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    protected function executeWithErrorHandling(callable $callback, string $errorMessage)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            $this->logError($errorMessage, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Execute a callback with retries
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    protected function executeWithRetries(callable $callback, int $maxAttempts = 3, int $delay = 1000)
    {
        $attempt = 1;
        $lastException = null;

        while ($attempt <= $maxAttempts) {
            try {
                return $callback();
            } catch (\Throwable $e) {
                $lastException = $e;
                $this->logWarning("Attempt {$attempt} failed", [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                ]);

                if ($attempt < $maxAttempts) {
                    usleep($delay * 1000);
                }

                $attempt++;
            }
        }

        throw $lastException;
    }
}
