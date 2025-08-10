<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        return self::create([
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'email' => $data['email'] ?? null,
            'details' => $data,
            'status' => $data['status'] ?? 'info',
            'attempt_count' => $data['attempt_count'] ?? 1,
        ]);
    }

    /**
     * Get failed attempts for an IP
     */
    public static function getFailedAttempts($ip, $eventType, $minutes = 60)
    {
        return self::where('ip_address', $ip)
            ->where('event_type', $eventType)
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    /**
     * Check if IP is blocked
     */
    public static function isIpBlocked($ip, $eventType)
    {
        $failedAttempts = self::getFailedAttempts($ip, $eventType, 60);

        // More permissive in development
        if (app()->environment('local', 'development')) {
            return $failedAttempts >= 20; // Higher threshold for development
        }

        // Block after 10 failed attempts in production
        return $failedAttempts >= 10;
    }
}
