<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Aduan;
use App\Models\Usulan;
use App\Services\RoleMappingService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Ensure user has a role_id, assign default if missing
        if (!$user->role_id) {
            $this->assignDefaultRole($user);
        }

        $this->syncRoleAssignment($user, 'created');
        $this->linkPreviousComplaints($user);
        $this->linkPreviousUsulan($user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->isDirty('role_id')) {
            $this->syncRoleAssignment($user, 'updated');
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->syncRoleAssignment($user, 'restored');
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

    /**
     * Sync role assignment antara role_id dan Spatie roles
     */
    private function syncRoleAssignment(User $user, string $event = 'unknown'): void
    {
        if (!$user->role_id) {
            Log::debug("User {$user->id} has no role_id, skipping role sync");
            return;
        }

        try {
            $roleName = RoleMappingService::getRoleName($user->role_id);

            if (!$roleName) {
                Log::warning("No role mapping found for role_id {$user->role_id} (User: {$user->id})");
                return;
            }

            // Check if Spatie role exists
            if (!Role::where('name', $roleName)->exists()) {
                Log::error("Spatie role '{$roleName}' does not exist (User: {$user->id}, role_id: {$user->role_id})");
                return;
            }

            // Sync roles - remove all existing and assign new one
            $user->syncRoles([$roleName]);

            Log::info("Role synced for user {$user->id} ({$event}): role_id {$user->role_id} -> '{$roleName}'");

        } catch (\Exception $e) {
            Log::error("Error syncing role for user {$user->id}: " . $e->getMessage());
        }
    }

    /**
     * Auto-link previous complaints when user is created
     */
    private function linkPreviousComplaints(User $user): void
    {
        try {
            // Cari pengaduan lama berdasarkan email yang belum terkait dengan user_id
            $linkedCount = Aduan::where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            if ($linkedCount > 0) {
                Log::info('Auto-linked previous complaints to new user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'linked_count' => $linkedCount
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error auto-linking previous complaints:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Auto-link previous usulan when user is created
     */
    private function linkPreviousUsulan(User $user): void
    {
        try {
            // Cari usulan lama berdasarkan email yang belum terkait dengan user_id
            $linkedCount = Usulan::where('email', $user->email)
                ->whereNull('user_id')
                ->update(['user_id' => $user->id]);

            if ($linkedCount > 0) {
                Log::info('Auto-linked previous usulan to new user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'linked_count' => $linkedCount
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error auto-linking previous usulan:', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Assign default role to user if role_id is missing
     */
    private function assignDefaultRole(User $user): void
    {
        try {
            // Get default user role_id using RoleMappingService
            $defaultRoleId = RoleMappingService::getRoleId('User');

            // Fallback to database default if mapping not found
            if (!$defaultRoleId) {
                $defaultRoleId = 3; // Default fallback
                Log::warning("Default role mapping not found for user {$user->id}, using fallback role_id: {$defaultRoleId}");
            }

            $user->update(['role_id' => $defaultRoleId]);

            Log::info("Default role assigned to user {$user->id}: role_id {$defaultRoleId}");

        } catch (\Exception $e) {
            Log::error("Error assigning default role to user {$user->id}: " . $e->getMessage());
        }
    }
}
