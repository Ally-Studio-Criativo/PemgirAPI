<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Campaign::create([
            // Português
            'campaign_text_pt' => 'Campanha de',
            'launch_text_pt' => "Lança\nmento",
            'title_pt' => 'DAY BY DAY',
            'season_pt' => 'Inverno 2026',
            'button_text_pt' => 'Assista agora',

            // English
            'campaign_text_en' => 'Campaign',
            'launch_text_en' => "Launch",
            'title_en' => 'DAY BY DAY',
            'season_en' => 'Winter 2026',
            'button_text_en' => 'Watch now',

            // Español
            'campaign_text_es' => 'Campaña de',
            'launch_text_es' => "Lanza\nmiento",
            'title_es' => 'DAY BY DAY',
            'season_es' => 'Invierno 2026',
            'button_text_es' => 'Ver ahora',

            'youtube_url' => 'https://www.youtube.com/embed/eTTJJABrtT4',
            'active' => true,
        ]);
    }
}
