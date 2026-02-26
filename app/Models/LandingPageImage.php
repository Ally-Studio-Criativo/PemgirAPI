<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageImage extends Model
{
    protected $fillable = [
        'type',
        'title',
        'filename',
        'path',
        'size'
    ];
}
