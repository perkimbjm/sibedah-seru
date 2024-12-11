<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousePhoto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'house_id',
        'photo_url',
        'description',
        'rtlh_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'house_id' => 'integer',
        'rtlh_id' => 'integer',
    ];

    public function rtlh(): BelongsTo
    {
        return $this->belongsTo(Rtlh::class);
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(Rtlh::class);
    }
}
