<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageText extends Model
{
    protected $fillable = [
        'section',
        'key',
        'text_pt',
        'text_en',
        'text_es',
    ];

    /**
     * Get text for specific locale
     */
    public function getText($locale = 'pt')
    {
        $field = 'text_' . $locale;
        return $this->$field ?? $this->text_pt;
    }
}
