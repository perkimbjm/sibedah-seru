<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Review;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements Authorizable
{
    use HasFactory, HasRoles, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'avatar',
        'password',
        'role_id',
        'remember_token',
        'email_verified_at',
        'phone',
        'profile_photo_path',
        'two_factor_secret',          // Kolom untuk menyimpan secret 2FA
        'two_factor_recovery_codes',   // Kolom untuk menyimpan recovery codes
        'two_factor_confirmed_at',     // Kolom untuk menyimpan waktu konfirmasi 2FA
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'phone',
        'remember_token',
        'two_factor_secret',          // Sembunyikan secret 2FA
        'two_factor_recovery_codes',   // Sembunyikan recovery codes
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'role_id' => 'integer',
        'email_verified_at' => 'timestamp',
        'two_factor_confirmed_at' => 'datetime', // Cast ke datetime
    ];

    public function isSuperAdmin()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        if ($this->avatar) {
            return $this->avatar;
        }

        return asset('img/profile.webp');
    }

    // Enable two-factor authentication
    public function enableTwoFactorAuthentication()
    {
        $this->two_factor_secret = $this->createTwoFactorSecret();
        $this->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $this->two_factor_confirmed_at = null; // Reset konfirmasi saat mengaktifkan
        $this->save();
    }

    // Confirm two-factor authentication
    public function confirmTwoFactorAuthentication($code)
    {
        if ($this->twoFactorConfirmation($code)) {
            $this->two_factor_confirmed_at = now(); // Set waktu konfirmasi
            $this->save();
            return true;
        }
        return false;
    }

    // Regenerate recovery codes
    public function regenerateRecoveryCodes()
    {
        $this->two_factor_recovery_codes = json_encode($this->generateRecoveryCodes());
        $this->save();
    }

    // Generate recovery codes
    protected function generateRecoveryCodes()
    {
        return array_map(function () {
            return strtoupper(bin2hex(random_bytes(4)));
        }, range(1, 8));
    }

    // Create a new two-factor secret
    protected function createTwoFactorSecret()
    {
        return app('auth.password.broker')->createToken($this);
    }
}
