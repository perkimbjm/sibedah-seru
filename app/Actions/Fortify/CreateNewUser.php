<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\RoleMappingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $environment = app()->environment();

        Log::info('User registration attempt started', [
            'email' => $input['email'],
            'ip' => request()->ip(),
            'environment' => $environment,
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ]);

        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];

        // Add captcha validation only in production
        if (!app()->environment('local', 'development')) {
            $validationRules['captcha'] = ['required', 'numeric'];

            Log::info('Captcha validation required for production', [
                'email' => $input['email'],
                'environment' => $environment
            ]);
        }

        try {
            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                Log::warning('User registration validation failed', [
                    'email' => $input['email'],
                    'errors' => $validator->errors()->toArray(),
                    'environment' => $environment
                ]);

                throw $validator;
            }

            Log::info('User registration validation passed', [
                'email' => $input['email'],
                'environment' => $environment
            ]);

        } catch (\Exception $e) {
            Log::error('User registration validation error', [
                'email' => $input['email'],
                'error' => $e->getMessage(),
                'environment' => $environment
            ]);
            throw $e;
        }

        // Log registration attempt
        Log::info('User registration attempt proceeding', [
            'email' => $input['email'],
            'ip' => request()->ip(),
            'environment' => $environment,
            'user_agent' => request()->userAgent(),
        ]);

        // Get default user role_id using RoleMappingService
        $defaultRoleId = RoleMappingService::getRoleId('User');

        // Fallback to database default if mapping not found
        if (!$defaultRoleId) {
            $defaultRoleId = 3; // Default fallback
            Log::warning('Default role mapping not found, using fallback role_id: ' . $defaultRoleId, [
                'email' => $input['email'],
                'environment' => $environment
            ]);
        }

        try {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'role_id' => $defaultRoleId,
            ]);

            // Log successful registration
            Log::info('User registration successful', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'ip' => request()->ip(),
                'environment' => $environment
            ]);

            return $user;

        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'email' => $input['email'],
                'error' => $e->getMessage(),
                'environment' => $environment
            ]);
            throw $e;
        }
    }
}
