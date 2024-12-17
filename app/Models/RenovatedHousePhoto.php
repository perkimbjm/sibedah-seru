<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RenovatedHousePhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'renovated_house_id',
        'photo_url',
        'description',
        'progres',
        'is_primary',
        'is_best',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'renovated_house_id' => 'integer',
        'progres' => 'float',
        'is_primary' => 'boolean',
        'is_best' => 'boolean',
    ];

    public function house() : BelongsTo
    {
        return $this->belongsTo(House::class, 'renovated_house_id');
    }
}
