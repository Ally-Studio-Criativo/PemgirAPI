<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'campaign_text_pt',
        'campaign_text_en',
        'campaign_text_es',
        'launch_text_pt',
        'launch_text_en',
        'launch_text_es',
        'title_pt',
        'title_en',
        'title_es',
        'season_pt',
        'season_en',
        'season_es',
        'button_text_pt',
        'button_text_en',
        'button_text_es',
        'youtube_url',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
