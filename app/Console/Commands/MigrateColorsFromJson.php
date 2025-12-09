<?php

namespace App\Console\Commands;

use App\Models\Color;
use App\Models\ColorPalette;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MigrateColorsFromJson extends Command
{
    protected $signature = 'migrate:colors';
    protected $description = 'Migrar cores do JSON e imagens para o banco de dados';

    public function handle()
    {
        $this->info('Iniciando migração de cores...');

        // Garantir que o link simbólico do storage existe
        if (!file_exists(public_path('storage'))) {
            $this->warn('Link simbólico do storage não existe. Criando...');
            $this->call('storage:link');
        }

        $jsonPath = database_path('data/colors.json');

        if (!file_exists($jsonPath)) {
            $this->error('Arquivo colors.json não encontrado em: ' . $jsonPath);
            return 1;
        }

        $jsonContent = file_get_contents($jsonPath);
        $colorsData = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar JSON: ' . json_last_error_msg());
            return 1;
        }

        // Criar diretório de destino se não existir
        $storageDir = storage_path('app/public/images/colors');
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
            $this->info('Diretório criado: ' . $storageDir);
        }

        $created = 0;
        $updated = 0;
        $imagesMoved = 0;
        $imagesError = 0;

        foreach ($colorsData as $paletteName => $colors) {
            $palette = ColorPalette::where('name', $paletteName)->first();

            if (!$palette) {
                $this->warn("⚠ Paleta '{$paletteName}' não encontrada, pulando...");
                continue;
            }

            $this->info("Processando paleta: {$paletteName}");

            foreach ($colors as $index => $colorData) {
                try {
                    // Criar ou atualizar cor
                    $color = Color::updateOrCreate(
                        [
                            'palette_id' => $palette->id,
                            'ref' => $colorData['ref'],
                        ],
                        [
                            'name' => $colorData['name'],
                            'type' => $colorData['type'],
                            'order' => $index + 1
                        ]
                    );

                    // Processar imagem
                    if (isset($colorData['filename']) && $colorData['filename']) {
                        $sourcePath = public_path("paleta/{$paletteName}/{$colorData['filename']}");

                        if (file_exists($sourcePath)) {
                            $extension = pathinfo($colorData['filename'], PATHINFO_EXTENSION);
                            $newFilename = "color_{$color->id}.{$extension}";
                            $destPath = storage_path("app/public/images/colors/{$newFilename}");

                            // Copiar imagem
                            if (copy($sourcePath, $destPath)) {
                                // Garantir permissões corretas
                                chmod($destPath, 0644);
                                $color->update(['image' => "storage/images/colors/{$newFilename}"]);
                                $imagesMoved++;
                            } else {
                                $imagesError++;
                                $this->warn("  ⚠ Erro ao copiar imagem: {$colorData['filename']}");
                            }
                        } else {
                            $imagesError++;
                            $this->warn("  ⚠ Imagem não encontrada: {$sourcePath}");
                        }
                    }

                    if ($color->wasRecentlyCreated) {
                        $created++;
                        $this->line("  ✓ Criada: {$color->ref} - {$color->name}");
                    } else {
                        $updated++;
                    }

                } catch (\Exception $e) {
                    $this->error("  ✗ Erro ao processar cor: " . $e->getMessage());
                }
            }
        }

        $this->newLine();
        $this->line('═══════════════════════════════════');
        $this->info('Migração concluída!');
        $this->line('═══════════════════════════════════');
        $this->line('Cores criadas: ' . $created);
        $this->line('Cores atualizadas: ' . $updated);
        $this->line('Imagens movidas: ' . $imagesMoved);
        $this->line('Imagens com erro: ' . $imagesError);
        $this->line('═══════════════════════════════════');

        return 0;
    }
}
