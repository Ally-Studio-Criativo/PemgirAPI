<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all color image paths to include 'storage/' prefix
        DB::table('colors')
            ->whereNotNull('image')
            ->where('image', 'NOT LIKE', 'storage/%')
            ->update([
                'image' => DB::raw("CONCAT('storage/', image)")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'storage/' prefix from color image paths
        DB::table('colors')
            ->where('image', 'LIKE', 'storage/%')
            ->update([
                'image' => DB::raw("REPLACE(image, 'storage/', '')")
            ]);
    }
};
