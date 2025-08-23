<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SecurityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'ip_address',
        'user_agent',
        'email',
        'details',
        'status',
        'attempt_count',
    ];

    protected $casts = [
        'details' => 'array',
        'attempt_count' => 'integer',
    ];

    /**
     * Log a security event
     */
    public static function logEvent($eventType, $data = [])
    {
        $logData = [
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'email' => $data['email'] ?? null,
            'details' => $data,
            'status' => $data['status'] ?? 'info',
            'attempt_count' => $data['attempt_count'] ?? 1,
        ];

        // Log security events for debugging
        Log::info('Security event logged', $logData);

        return self::create($logData);
    }

    /**
     * Get failed attempts for an IP
     */
    public static function getFailedAttempts($ip, $eventType, $minutes = 60)
    {
        $count = self::where('ip_address', $ip)
            ->where('event_type', $eventType)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();

        // Log failed attempts for debugging
        if ($count > 0) {
            Log::info('Failed attempts count', [
                'ip' => $ip,
                'event_type' => $eventType,
                'count' => $count,
                'minutes' => $minutes,
                'environment' => app()->environment()
            ]);
        }

        return $count;
    }

    /**
     * Check if IP is blocked
     */
    public static function isIpBlocked($ip, $eventType)
    {
        $failedAttempts = self::getFailedAttempts($ip, $eventType, 60);
        $environment = app()->environment();

        // More permissive in development
        if (app()->environment('local', 'development')) {
            $threshold = 20; // Higher threshold for development
        } else {
            // Increase production threshold from 10 to 15
            $threshold = 15;
        }

        $isBlocked = $failedAttempts >= $threshold;

        // Log blocking decision for debugging
        Log::info('IP blocking check', [
            'ip' => $ip,
            'event_type' => $eventType,
            'failed_attempts' => $failedAttempts,
            'threshold' => $threshold,
            'is_blocked' => $isBlocked,
            'environment' => $environment
        ]);

        return $isBlocked;
    }
}
