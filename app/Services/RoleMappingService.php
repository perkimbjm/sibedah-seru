<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleMappingService
{
    /**
     * Cache key for role mapping
     */
    const CACHE_KEY = 'role_mapping_dynamic';

    /**
     * Get role mapping with auto-detection and fallback
     */
    public static function getMapping(): array
    {
        $cacheKey = self::CACHE_KEY . '_' . app()->environment();
        $cacheDuration = config('role_mapping.auto_detection.cache_duration', 24);

        return Cache::remember($cacheKey, now()->addHours($cacheDuration), function () {
            return self::resolveMapping();
        });
    }

    /**
     * Resolve mapping dengan prioritas:
     * 1. Auto-detection dari database
     * 2. Environment-specific config
     * 3. Default config
     */
    private static function resolveMapping(): array
    {
        $mapping = [];

        // 1. Try auto-detection jika enabled
        if (config('role_mapping.auto_detection.enabled', true)) {
            $mapping = self::autoDetectFromDatabase();

            if (!empty($mapping)) {
                Log::info('Role mapping auto-detected from database', $mapping);
                return $mapping;
            }
        }

        // 2. Try environment-specific config
        $env = app()->environment();
        $envMapping = config("role_mapping.{$env}");

        if (!empty($envMapping)) {
            Log::info("Using environment-specific role mapping for: {$env}", $envMapping);
            return $envMapping;
        }

        // 3. Fallback to default
        $defaultMapping = config('role_mapping.default', []);
        Log::info('Using default role mapping', $defaultMapping);

        return $defaultMapping;
    }

    /**
     * Auto-detect role mapping dari database existing
     */
    public static function autoDetectFromDatabase(): array
    {
        $mapping = [];
        $sourceTable = config('role_mapping.auto_detection.source_table', 'roles');

        try {
            // Check if source table exists
            if (!Schema::hasTable($sourceTable)) {
                Log::warning("Source table '{$sourceTable}' not found for role auto-detection");
                return $mapping;
            }

            // Get roles from source table
            $roles = DB::table($sourceTable)
                ->select('id', 'name')
                ->get();

            if ($roles->isEmpty()) {
                Log::warning("No roles found in table '{$sourceTable}'");
                return $mapping;
            }

            // Map each role using pattern matching
            foreach ($roles as $role) {
                $spatieRole = self::mapToSpatieRole($role->name);
                if ($spatieRole) {
                    $mapping[$role->id] = $spatieRole;
                }
            }

            if (empty($mapping)) {
                Log::warning('No roles could be mapped using pattern matching');
            }

        } catch (\Exception $e) {
            Log::error('Error during role auto-detection: ' . $e->getMessage());
        }

        return $mapping;
    }

    /**
     * Map nama role existing ke Spatie role names menggunakan pattern matching
     */
    private static function mapToSpatieRole(string $roleName): ?string
    {
        $patterns = config('role_mapping.auto_detection.patterns', [
            '/super.?admin|superadmin/i' => 'Super Admin',
            '/admin(?!.*super)/i' => 'Admin',
            '/tfl|tim.?fasilitator|facilitator/i' => 'tfl',
            '/user|pengguna|member|customer/i' => 'User',
        ]);

        $cleanName = trim($roleName);

        foreach ($patterns as $pattern => $spatieRole) {
            if (preg_match($pattern, $cleanName)) {
                return $spatieRole;
            }
        }

        // Log unmatched roles for debugging
        Log::debug("Role '{$roleName}' could not be mapped to any Spatie role");

        return null;
    }

    /**
     * Get role name berdasarkan role_id
     */
    public static function getRoleName(int $roleId): ?string
    {
        $mapping = self::getMapping();
        return $mapping[$roleId] ?? null;
    }

    /**
     * Get role_id berdasarkan role name
     */
    public static function getRoleId(string $roleName): ?int
    {
        $mapping = array_flip(self::getMapping());
        return $mapping[$roleName] ?? null;
    }

    /**
     * Clear cache dan reload mapping
     */
    public static function refresh(): array
    {
        $cacheKey = self::CACHE_KEY . '_' . app()->environment();
        Cache::forget($cacheKey);

        return self::getMapping();
    }

    /**
     * Validate mapping - check apakah semua Spatie roles exist
     */
    public static function validateMapping(): array
    {
        $mapping = self::getMapping();
        $spatieRoles = \Spatie\Permission\Models\Role::pluck('name')->toArray();
        $validation = [];

        foreach ($mapping as $roleId => $roleName) {
            $validation[$roleId] = [
                'role_name' => $roleName,
                'exists_in_spatie' => in_array($roleName, $spatieRoles),
                'role_id' => $roleId
            ];
        }

        return $validation;
    }

    /**
     * Debug info untuk troubleshooting
     */
    public static function getDebugInfo(): array
    {
        return [
            'environment' => app()->environment(),
            'auto_detection_enabled' => config('role_mapping.auto_detection.enabled'),
            'source_table' => config('role_mapping.auto_detection.source_table'),
            'cache_key' => self::CACHE_KEY . '_' . app()->environment(),
            'current_mapping' => self::getMapping(),
            'validation' => self::validateMapping(),
            'spatie_roles' => \Spatie\Permission\Models\Role::pluck('name', 'id')->toArray(),
        ];
    }
}
