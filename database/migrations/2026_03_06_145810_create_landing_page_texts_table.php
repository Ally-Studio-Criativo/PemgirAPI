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
        Schema::create('landing_page_texts', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique(); // about_company, about_products, etc.
            $table->string('key')->nullable(); // title, subtitle, description, etc.

            // Textos multilíngue
            $table->text('text_pt')->nullable();
            $table->text('text_en')->nullable();
            $table->text('text_es')->nullable();

            $table->timestamps();

            // Index para busca rápida
            $table->index(['section', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_texts');
    }
};
