<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TwoFactorAuthenticationController extends Controller
{
    /**
     * Handle two-factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if 2FA is already enabled
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            return response()->json([
                'message' => 'Two factor authentication is already enabled.'
            ], 400);
        }

        $user->enableTwoFactorAuthentication();

        return response()->json([
            'message' => 'Two factor authentication has been enabled.',
            'recovery_codes' => json_decode($user->two_factor_recovery_codes)
        ]);
    }

    /**
     * Confirm two-factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->confirmTwoFactorAuthentication($request->code)) {
            return response()->json([
                'message' => 'Two factor authentication has been confirmed.'
            ]);
        }

        return response()->json([
            'message' => 'The provided code was invalid.'
        ], 422);
    }

    /**
     * Disable two-factor authentication for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
            return response()->json([
                'message' => 'Two factor authentication is not enabled.'
            ], 400);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null
        ]);

        return response()->json([
            'message' => 'Two factor authentication has been disabled.'
        ]);
    }

    /**
     * Generate new recovery codes for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function regenerateRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
            return response()->json([
                'message' => 'Two factor authentication is not enabled.'
            ], 400);
        }

        $user->regenerateRecoveryCodes();

        return response()->json([
            'message' => 'Recovery codes have been regenerated.',
            'recovery_codes' => json_decode($user->two_factor_recovery_codes)
        ]);
    }

    /**
     * Get the current recovery codes for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecoveryCodes(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
            return response()->json([
                'message' => 'Two factor authentication is not enabled.'
            ], 400);
        }

        return response()->json([
            'recovery_codes' => json_decode($user->two_factor_recovery_codes)
        ]);
    }
}
