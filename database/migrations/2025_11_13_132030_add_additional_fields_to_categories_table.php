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
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('featured_product_id')->nullable()->after('order')->constrained('products')->onDelete('set null');
            $table->string('image')->nullable()->after('featured_product_id');
            $table->string('path')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['featured_product_id']);
            $table->dropColumn(['featured_product_id', 'image', 'path']);
        });
    }
};
