<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_page_images', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // lancamentos, produtos, cartela, instagram, prancheta1, prancheta2
            $table->string('title'); // Título descritivo
            $table->string('filename'); // Nome do arquivo
            $table->string('path'); // Caminho completo
            $table->integer('size')->nullable(); // Tamanho em bytes
            $table->timestamps();
        });

        // Inserir os dados iniciais
        DB::table('landing_page_images')->insert([
            [
                'type' => 'lancamentos',
                'title' => 'Card Lançamentos',
                'filename' => 'Lançamentos.jpg',
                'path' => 'images/Lançamentos.jpg',
                'size' => 282000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'produtos',
                'title' => 'Card Produtos',
                'filename' => 'produtos.jpg',
                'path' => 'images/produtos.jpg',
                'size' => 297000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'cartela',
                'title' => 'Card Cartela',
                'filename' => 'Cartela_.jpg',
                'path' => 'images/Cartela_.jpg',
                'size' => 204000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'instagram',
                'title' => 'Card Instagram',
                'filename' => 'Intagram.jpg',
                'path' => 'images/Intagram.jpg',
                'size' => 144000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'prancheta1',
                'title' => 'Sobre a Empresa',
                'filename' => 'Prancheta 1.png',
                'path' => 'images/Prancheta 1.png',
                'size' => 9900000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type' => 'prancheta2',
                'title' => 'Mais de 130 Produtos',
                'filename' => 'Prancheta 2.png',
                'path' => 'images/Prancheta 2.png',
                'size' => 11000000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_images');
    }
};
