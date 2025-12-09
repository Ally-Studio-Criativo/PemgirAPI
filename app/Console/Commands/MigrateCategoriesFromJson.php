<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateCategoriesFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar categorias do JSON para o banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migração de categorias...');

        // Ler o arquivo JSON
        $jsonPath = database_path('data/categories.json');

        if (!file_exists($jsonPath)) {
            $this->error("Arquivo não encontrado: {$jsonPath}");
            return 1;
        }

        $jsonContent = file_get_contents($jsonPath);
        $categories = json_decode($jsonContent, true);

        if (!$categories) {
            $this->error('Erro ao decodificar JSON');
            return 1;
        }

        $this->info("Encontradas " . count($categories) . " categorias no JSON");

        $created = 0;
        $updated = 0;

        foreach ($categories as $index => $categoryData) {
            // Buscar categoria existente pelo nome
            $category = Category::firstOrNew(['name' => $categoryData['name']]);

            // Atualizar dados (exceto featured_product_id e image)
            $category->path = $categoryData['path'] ?? null;
            $category->order = $index; // Usar o índice do array como ordem

            if ($category->exists) {
                $category->save();
                $updated++;
                $this->line("✓ Atualizada: {$category->name}");
            } else {
                $category->save();
                $created++;
                $this->line("✓ Criada: {$category->name}");
            }
        }

        $this->newLine();
        $this->info("Migração concluída!");
        $this->info("Categorias criadas: {$created}");
        $this->info("Categorias atualizadas: {$updated}");

        return 0;
    }
}
