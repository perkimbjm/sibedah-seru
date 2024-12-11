<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Review;
use Spatie\Permission\Traits\HasRoles;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Authenticatable implements Authorizable
{
    use HasFactory, HasRoles;

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
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'phone'
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
}
