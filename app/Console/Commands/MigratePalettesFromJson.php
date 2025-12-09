<?php

namespace App\Console\Commands;

use App\Models\ColorPalette;
use Illuminate\Console\Command;

class MigratePalettesFromJson extends Command
{
    protected $signature = 'migrate:palettes';
    protected $description = 'Migrar paletas de cores do JSON para o banco de dados';

    public function handle()
    {
        $this->info('Iniciando migração de paletas...');

        $jsonPath = database_path('data/palettes.json');

        if (!file_exists($jsonPath)) {
            $this->error('Arquivo palettes.json não encontrado!');
            return 1;
        }

        $jsonContent = file_get_contents($jsonPath);
        $palettes = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Erro ao decodificar JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info('Encontradas ' . count($palettes) . ' paletas no JSON');

        $created = 0;
        $updated = 0;

        foreach ($palettes as $paletteData) {
            $palette = ColorPalette::updateOrCreate(
                ['id' => $paletteData['id']],
                [
                    'name' => $paletteData['name'],
                    'year' => $paletteData['year'],
                    'order' => $paletteData['order']
                ]
            );

            if ($palette->wasRecentlyCreated) {
                $created++;
                $this->line('✓ Criada: ' . $palette->name);
            } else {
                $updated++;
                $this->line('↻ Atualizada: ' . $palette->name);
            }
        }

        $this->newLine();
        $this->info('Migração concluída!');
        $this->line('Paletas criadas: ' . $created);
        $this->line('Paletas atualizadas: ' . $updated);

        return 0;
    }
}
