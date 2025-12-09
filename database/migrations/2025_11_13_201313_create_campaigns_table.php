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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_text_pt')->nullable(); // "Campanha de"
            $table->string('campaign_text_en')->nullable();
            $table->string('campaign_text_es')->nullable();
            $table->string('launch_text_pt')->nullable(); // "Lançamento"
            $table->string('launch_text_en')->nullable();
            $table->string('launch_text_es')->nullable();
            $table->string('title_pt')->nullable(); // "DAY BY DAY"
            $table->string('title_en')->nullable();
            $table->string('title_es')->nullable();
            $table->string('season_pt')->nullable(); // "Inverno 2026"
            $table->string('season_en')->nullable();
            $table->string('season_es')->nullable();
            $table->string('button_text_pt')->nullable(); // "Assista agora"
            $table->string('button_text_en')->nullable();
            $table->string('button_text_es')->nullable();
            $table->string('youtube_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
