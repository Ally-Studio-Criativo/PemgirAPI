<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class SetFeaturedProducts extends Command
{
    protected $signature = 'categories:set-featured';
    protected $description = 'Define o primeiro produto de cada categoria como destaque';

    public function handle()
    {
        $this->info('Configurando produtos em destaque para cada categoria...');

        $categories = Category::orderBy('order')->get();
        $updated = 0;
        $skipped = 0;

        foreach ($categories as $category) {
            // Buscar o primeiro produto da categoria (ordenado por order)
            $firstProduct = Product::where('category_id', $category->id)
                ->orderBy('order')
                ->first();

            if ($firstProduct) {
                $category->update(['featured_product_id' => $firstProduct->id]);
                $this->line("  ✓ {$category->name}: {$firstProduct->name}");
                $updated++;
            } else {
                $this->warn("  ⚠ {$category->name}: Nenhum produto encontrado");
                $skipped++;
            }
        }

        $this->newLine();
        $this->line('═══════════════════════════════════');
        $this->info('Configuração concluída!');
        $this->line('═══════════════════════════════════');
        $this->line('Categorias atualizadas: ' . $updated);
        $this->line('Categorias sem produtos: ' . $skipped);
        $this->line('═══════════════════════════════════');

        return 0;
    }
}
