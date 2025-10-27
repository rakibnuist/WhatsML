<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DebugHelper
{
    /**
     * Log debug information with context
     */
    public static function log(string $message, array $context = [], string $level = 'info'): void
    {
        $context = array_merge($context, [
            'timestamp' => now()->toISOString(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ]);

        Log::channel('debug')->{$level}($message, $context);
    }

    /**
     * Log database queries for debugging
     */
    public static function logQueries(): void
    {
        if (config('app.debug')) {
            DB::listen(function ($query) {
                self::log('Database Query', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ]);
            });
        }
    }

    /**
     * Log API requests and responses
     */
    public static function logApiCall(string $method, string $url, array $data = [], $response = null): void
    {
        self::log('API Call', [
            'method' => $method,
            'url' => $url,
            'data' => $data,
            'response_status' => $response ? $response->status() : null,
            'response_body' => $response ? $response->body() : null,
        ]);
    }

    /**
     * Log WhatsApp service operations
     */
    public static function logWhatsAppOperation(string $operation, array $data = [], $result = null): void
    {
        self::log('WhatsApp Operation', [
            'operation' => $operation,
            'data' => $data,
            'result' => $result,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Log OpenAI service operations
     */
    public static function logOpenAIOperation(string $operation, array $data = [], $result = null): void
    {
        self::log('OpenAI Operation', [
            'operation' => $operation,
            'data' => $data,
            'result' => $result,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Log performance metrics
     */
    public static function logPerformance(string $operation, float $startTime, array $additionalData = []): void
    {
        $executionTime = microtime(true) - $startTime;
        
        self::log('Performance Metric', array_merge([
            'operation' => $operation,
            'execution_time' => $executionTime,
            'memory_usage' => memory_get_usage(true),
        ], $additionalData));
    }

    /**
     * Log user actions for debugging
     */
    public static function logUserAction(string $action, array $data = []): void
    {
        self::log('User Action', array_merge([
            'action' => $action,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $data));
    }

    /**
     * Log module operations
     */
    public static function logModuleOperation(string $module, string $operation, array $data = []): void
    {
        self::log('Module Operation', [
            'module' => $module,
            'operation' => $operation,
            'data' => $data,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Create a debug timer
     */
    public static function startTimer(string $name): float
    {
        $GLOBALS['debug_timers'][$name] = microtime(true);
        return $GLOBALS['debug_timers'][$name];
    }

    /**
     * End a debug timer and log the result
     */
    public static function endTimer(string $name): float
    {
        if (!isset($GLOBALS['debug_timers'][$name])) {
            return 0;
        }

        $executionTime = microtime(true) - $GLOBALS['debug_timers'][$name];
        unset($GLOBALS['debug_timers'][$name]);

        self::log('Timer Result', [
            'timer_name' => $name,
            'execution_time' => $executionTime,
        ]);

        return $executionTime;
    }
}
