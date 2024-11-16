<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'description',
        'location',
        'latitude',
        'longitude',
        'user_id'

    ];
}
