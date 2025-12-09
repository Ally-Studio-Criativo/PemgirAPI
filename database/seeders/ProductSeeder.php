<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load products.json
        $jsonPath = resource_path('app/src/data/products.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("File not found: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        if (!isset($data['categories']) || !is_array($data['categories'])) {
            $this->command->error("Invalid JSON structure");
            return;
        }

        $this->command->info("Starting products import...");

        foreach ($data['categories'] as $categoryData) {
            // Find or create category
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                [
                    'label' => $categoryData['label'] ?? $categoryData['name'],
                    'featured' => $categoryData['featured'] ?? false,
                ]
            );

            $this->command->info("Processing category: {$category->name}");

            if (!isset($categoryData['products']) || !is_array($categoryData['products'])) {
                continue;
            }

            foreach ($categoryData['products'] as $index => $productData) {
                // Create product
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'reference' => $this->extractReference($productData['name']),
                    'composition' => $productData['composition'] ?? null,
                    'gramatura' => $productData['gramatura'] ?? null,
                    'width' => $productData['width'] ?? null,
                    'yield' => $productData['yield'] ?? null,
                    'order' => $index,
                ]);

                $this->command->info("  - Created product: {$product->name}");

                // Process images
                if (isset($productData['images']) && is_array($productData['images'])) {
                    foreach ($productData['images'] as $imageIndex => $imageName) {
                        $imageData = $this->parseImageName($imageName);

                        ProductImage::create([
                            'product_id' => $product->id,
                            'ref' => $imageData['ref'],
                            'color_name' => $imageData['color_name'],
                            'image_type' => $imageData['image_type'],
                            'filename' => $imageName,
                            'path' => "products/{$imageName}", // Assumindo que as imagens estão em storage/products
                            'order' => $imageIndex,
                        ]);
                    }

                    $this->command->info("    - Added " . count($productData['images']) . " images");
                }
            }
        }

        $this->command->info("Import completed successfully!");
    }

    /**
     * Extract reference from product name
     * Example: "REF 4550 MALHÃO PLUS" -> "4550"
     */
    private function extractReference(string $name): ?string
    {
        if (preg_match('/REF\s+(\d+)/', $name, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Parse image name to extract ref, color_name, and image_type
     * Example: "REF 0613 - MARINHO - esc_fte.png"
     */
    private function parseImageName(string $imageName): array
    {
        $result = [
            'ref' => null,
            'color_name' => null,
            'image_type' => null,
        ];

        // Remove file extension
        $nameWithoutExt = preg_replace('/\.[^.]+$/', '', $imageName);

        // Split by " - "
        $parts = array_map('trim', explode(' - ', $nameWithoutExt));

        // Extract REF code
        if (isset($parts[0]) && preg_match('/REF\s+(.+)/', $parts[0], $matches)) {
            $result['ref'] = $matches[1];
        }

        // Extract color name
        if (isset($parts[1])) {
            $result['color_name'] = $parts[1];
        }

        // Extract image type
        if (isset($parts[2])) {
            $result['image_type'] = $parts[2];
        }

        return $result;
    }
}
