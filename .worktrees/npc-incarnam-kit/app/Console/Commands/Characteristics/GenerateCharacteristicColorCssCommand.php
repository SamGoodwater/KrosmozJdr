<?php

declare(strict_types=1);

namespace App\Console\Commands\Characteristics;

use App\Console\ArtisanExitCode;
use App\Services\Characteristic\CharacteristicColorCssGenerator;
use Illuminate\Console\Command;

class GenerateCharacteristicColorCssCommand extends Command
{
    protected $signature = 'characteristics:generate-color-css';

    protected $description = 'Génère le fichier CSS des couleurs de caractéristiques (classes .color-{key} avec --color hex depuis la BDD)';

    public function handle(CharacteristicColorCssGenerator $generator): int
    {
        if ($generator->generate()) {
            $this->info('Fichier '.CharacteristicColorCssGenerator::OUTPUT_PATH.' généré.');

            return ArtisanExitCode::SUCCESS;
        }

        $this->error('Échec de la génération du fichier CSS.');

        return ArtisanExitCode::FAILURE;
    }
}
