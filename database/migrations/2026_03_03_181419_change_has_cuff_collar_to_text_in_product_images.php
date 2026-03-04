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
        // Primeiro, tornar a coluna nullable
        Schema::table('product_images', function (Blueprint $table) {
            $table->boolean('has_cuff_collar')->nullable()->change();
        });

        // Depois, limpar os valores existentes
        \DB::statement('UPDATE product_images SET has_cuff_collar = NULL');

        // Por fim, alterar o tipo da coluna para text
        Schema::table('product_images', function (Blueprint $table) {
            $table->text('has_cuff_collar')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Reverter para boolean
            $table->boolean('has_cuff_collar')->default(false)->change();
        });
    }
};
