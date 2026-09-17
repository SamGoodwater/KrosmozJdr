<?php

declare(strict_types=1);

namespace App\Console\Commands\Rules;

use App\Console\ArtisanExitCode;
use App\Services\Rules\RulesBookAssembler;
use App\Services\Rules\RulesDownloadCompiler;
use Illuminate\Console\Command;

/**
 * Compile le livre de règles (Markdown) en PDF et ODT sur le disque public.
 *
 * À lancer après une mise à jour des chapitres, ou depuis la gestion du contenu.
 *
 * @example php artisan rules:compile-downloads
 * @example php artisan rules:compile-downloads --pdf
 */
class RulesCompileDownloadsCommand extends Command
{
    protected $signature = 'rules:compile-downloads
        {--pdf : Générer uniquement le PDF}
        {--odt : Générer uniquement l’OpenDocument}
        {--dry-run : Assemble les chapitres sans écrire les fichiers}';

    protected $description = 'Compile le livre de règles en PDF et ODT (storage public).';

    public function handle(RulesDownloadCompiler $compiler): int
    {
        $pdfOnly = (bool) $this->option('pdf');
        $odtOnly = (bool) $this->option('odt');
        $pdf = $pdfOnly || ! $odtOnly;
        $odt = $odtOnly || ! $pdfOnly;

        if ((bool) $this->option('dry-run')) {
            $this->info('Dry-run : assemblage uniquement.');
            $assembler = app(RulesBookAssembler::class);
            $files = $assembler->chapterFiles();
            $this->line(count($files).' chapitres trouvés.');
            foreach (array_slice($files, 0, 8) as $file) {
                $this->line('  · '.$file['number'].' — '.basename($file['path']));
            }
            if (count($files) > 8) {
                $this->line('  …');
            }

            return ArtisanExitCode::SUCCESS;
        }

        set_time_limit(0);
        $previousMemory = ini_get('memory_limit');
        ini_set('memory_limit', '512M');

        $this->info('Compilation du livre de règles…');

        try {
            $written = $compiler->compile($pdf, $odt, function (string $message, int $percent): void {
                $this->line(sprintf('[%3d %%] %s', $percent, $message));
            });
        } finally {
            if (is_string($previousMemory) && $previousMemory !== '') {
                ini_set('memory_limit', $previousMemory);
            }
        }

        foreach ($written as $file) {
            $this->line(sprintf(
                '  → %s (%s, %s)',
                $file['key'],
                $file['path'],
                $this->humanBytes($file['bytes'])
            ));
        }

        $this->info('Terminé.');

        return ArtisanExitCode::SUCCESS;
    }

    private function humanBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' o';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' Ko';
        }

        return round($bytes / (1024 * 1024), 1).' Mo';
    }
}
