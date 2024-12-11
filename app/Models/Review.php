<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'renovated_house_id',
        'name',
        'comment',
        'rating',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'renovated_house_id' => 'integer',
    ];


    public function renovatedHouse(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
