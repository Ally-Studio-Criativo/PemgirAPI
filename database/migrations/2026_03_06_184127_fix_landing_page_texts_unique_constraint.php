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
        Schema::table('landing_page_texts', function (Blueprint $table) {
            // Remove a constraint unique de 'section'
            $table->dropUnique(['section']);

            // Adiciona uma constraint unique para a combinação section + key
            $table->unique(['section', 'key'], 'landing_page_texts_section_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_texts', function (Blueprint $table) {
            // Remove a constraint unique de section + key
            $table->dropUnique('landing_page_texts_section_key_unique');

            // Re-adiciona a constraint unique em 'section'
            $table->unique('section');
        });
    }
};
