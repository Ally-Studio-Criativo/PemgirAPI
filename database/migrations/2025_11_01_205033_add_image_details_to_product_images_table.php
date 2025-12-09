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
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('ref')->nullable()->after('product_id'); // REF code (ex: 0613, 801153)
            $table->string('color_name')->nullable()->after('ref'); // Color/variation name (ex: MARINHO, PRETO)
            $table->string('image_type')->nullable()->after('color_name'); // Type (ex: esc_fte, forte, única)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['ref', 'color_name', 'image_type']);
        });
    }
};
