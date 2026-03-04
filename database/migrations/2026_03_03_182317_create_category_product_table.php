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
        // Criar tabela pivot para relacionamento many-to-many
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Evitar duplicatas
            $table->unique(['category_id', 'product_id']);

            // Índices para performance
            $table->index('category_id');
            $table->index('product_id');
        });

        // Migrar dados existentes da coluna category_id para a tabela pivot
        \DB::statement('
            INSERT INTO category_product (category_id, product_id, created_at, updated_at)
            SELECT category_id, id, NOW(), NOW()
            FROM products
            WHERE category_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
