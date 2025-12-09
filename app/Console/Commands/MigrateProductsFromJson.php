<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MigrateProductsFromJson extends Command
{
    protected $signature = 'migrate:products';
    protected $description = 'Migrar produtos e imagens do JSON para o banco de dados';

    private $stats = [
        'products_created' => 0,
        'products_updated' => 0,
        'images_moved' => 0,
        'images_failed' => 0,
    ];

    public function handle()
    {
        $this->info('Iniciando migração de produtos...');

        // Garantir que o link simbólico do storage existe
        if (!file_exists(public_path('storage'))) {
            $this->warn('Link simbólico do storage não existe. Criando...');
            $this->call('storage:link');
        }

        // Ler o arquivo JSON
        $jsonPath = database_path('data/products.json');

        if (!file_exists($jsonPath)) {
            $this->error("Arquivo não encontrado: {$jsonPath}");
            return 1;
        }

        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['categories'])) {
            $this->error('Erro ao decodificar JSON ou estrutura inválida');
            return 1;
        }

        $this->info("Encontradas " . count($data['categories']) . " categorias no JSON");

        // Criar diretório storage se não existir
        $storageImagesPath = storage_path('app/public/images/products');
        if (!File::exists($storageImagesPath)) {
            File::makeDirectory($storageImagesPath, 0755, true);
            $this->info("Diretório criado: {$storageImagesPath}");
        }

        // Processar cada categoria
        foreach ($data['categories'] as $categoryData) {
            $this->processCategoryProducts($categoryData);
        }

        $this->displayStats();

        return 0;
    }

    private function processCategoryProducts($categoryData)
    {
        $categoryName = $categoryData['name'];

        // Buscar categoria no banco pelo nome
        $category = Category::where('name', $categoryName)->first();

        if (!$category) {
            $this->warn("Categoria não encontrada no banco: {$categoryName} - Pulando produtos...");
            return;
        }

        $this->line("Processando categoria: {$categoryName}");

        if (!isset($categoryData['products']) || !is_array($categoryData['products'])) {
            return;
        }

        foreach ($categoryData['products'] as $index => $productData) {
            $this->processProduct($productData, $category, $categoryName, $index);
        }
    }

    private function processProduct($productData, $category, $categoryName, $order)
    {
        // Buscar ou criar produto
        $product = Product::firstOrNew([
            'name' => $productData['name'],
            'category_id' => $category->id
        ]);

        $isNew = !$product->exists;

        // Atualizar dados do produto
        $product->composition = $productData['composition'] ?? null;
        $product->gramatura = $productData['gramatura'] ?? null;
        $product->width = $productData['width'] ?? null;
        $product->yield = $productData['yield'] ?? null;
        $product->reference = $this->extractReference($productData['name']);
        $product->order = $order;

        $product->save();

        if ($isNew) {
            $this->stats['products_created']++;
            $this->line("  ✓ Criado: {$product->name}");
        } else {
            $this->stats['products_updated']++;
            $this->line("  ✓ Atualizado: {$product->name}");
        }

        // Processar imagens
        if (isset($productData['images']) && is_array($productData['images'])) {
            $this->processProductImages($product, $productData['images'], $categoryName, $productData['name']);
        }
    }

    private function processProductImages($product, $images, $categoryName, $productName)
    {
        // Limpar imagens antigas do produto
        ProductImage::where('product_id', $product->id)->delete();

        foreach ($images as $index => $imageName) {
            // Caminho origem
            $sourcePath = database_path("data/categories/{$categoryName}/{$productName}/{$imageName}");

            if (!file_exists($sourcePath)) {
                $this->warn("    ⚠ Imagem não encontrada: {$imageName}");
                $this->stats['images_failed']++;
                continue;
            }

            // Criar nome seguro para arquivo
            $safeFileName = $this->generateSafeFileName($product->id, $index, $imageName);
            $destinationPath = storage_path("app/public/images/products/{$safeFileName}");

            // Copiar arquivo
            if (File::copy($sourcePath, $destinationPath)) {
                // Garantir permissões corretas
                chmod($destinationPath, 0644);

                // Extrair metadados do nome da imagem
                $metadata = $this->extractImageMetadata($imageName);

                // Criar registro no banco
                ProductImage::create([
                    'product_id' => $product->id,
                    'filename' => $safeFileName,
                    'path' => "storage/images/products/{$safeFileName}",
                    'ref' => $metadata['ref'],
                    'color_name' => $metadata['color'],
                    'image_type' => $metadata['type'],
                    'order' => $index
                ]);

                $this->stats['images_moved']++;
            } else {
                $this->warn("    ⚠ Erro ao copiar: {$imageName} -> {$destinationPath}");
                $this->stats['images_failed']++;
            }
        }
    }

    private function extractReference($productName)
    {
        // Extrair REF do nome do produto (ex: "REF 4550 MALHÃO..." -> "4550")
        if (preg_match('/REF\s+(\d+)/', $productName, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractImageMetadata($imageName)
    {
        // Exemplo: "REF 0613 - MARINHO - esc_fte.png"
        // Retorna: ['ref' => '0613', 'color' => 'MARINHO', 'type' => 'esc_fte']

        $metadata = [
            'ref' => null,
            'color' => null,
            'type' => null
        ];

        // Remover extensão
        $nameWithoutExt = pathinfo($imageName, PATHINFO_FILENAME);

        // Dividir por " - "
        $parts = explode(' - ', $nameWithoutExt);

        if (count($parts) >= 1) {
            // Primeira parte: REF XXXX
            if (preg_match('/REF\s+(.+)/', $parts[0], $matches)) {
                $metadata['ref'] = trim($matches[1]);
            }
        }

        if (count($parts) >= 2) {
            // Segunda parte: cor
            $metadata['color'] = trim($parts[1]);
        }

        if (count($parts) >= 3) {
            // Terceira parte: tipo
            $metadata['type'] = trim($parts[2]);
        }

        return $metadata;
    }

    private function generateSafeFileName($productId, $index, $originalName)
    {
        // Gerar nome seguro mantendo a extensão original
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return "product_{$productId}_img_{$index}.{$extension}";
    }

    private function displayStats()
    {
        $this->newLine();
        $this->info('═══════════════════════════════════');
        $this->info('Migração concluída!');
        $this->info('═══════════════════════════════════');
        $this->info("Produtos criados: {$this->stats['products_created']}");
        $this->info("Produtos atualizados: {$this->stats['products_updated']}");
        $this->info("Imagens movidas: {$this->stats['images_moved']}");

        if ($this->stats['images_failed'] > 0) {
            $this->warn("Imagens com erro: {$this->stats['images_failed']}");
        }

        $this->info('═══════════════════════════════════');
    }
}
