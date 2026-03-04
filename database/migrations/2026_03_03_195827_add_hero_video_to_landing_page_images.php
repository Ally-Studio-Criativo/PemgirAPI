<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Inserir registro para o vídeo hero
        \DB::table('landing_page_images')->insert([
            'type' => 'hero_video',
            'title' => 'Vídeo Hero Section',
            'filename' => 'hero_video.mp4',
            'path' => 'https://www.youtube.com/embed/Dm1SZccxNRo?autoplay=1&mute=1&controls=0&loop=1&playlist=Dm1SZccxNRo&modestbranding=1&showinfo=0&rel=0',
            'size' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('landing_page_images')->where('type', 'hero_video')->delete();
    }
};
