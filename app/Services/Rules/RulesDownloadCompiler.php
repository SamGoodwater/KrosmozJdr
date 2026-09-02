<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Illuminate\Support\Facades\Storage;

/**
 * Compile le livre de règles en PDF et ODT sur le disque public.
 *
 * N’est lancé que par la commande Artisan (import / init) ou le bouton admin :
 * jamais à la volée sur une page publique.
 *
 * @example
 * $written = app(RulesDownloadCompiler::class)->compile();
 */
class RulesDownloadCompiler
{
    public function __construct(
        private readonly RulesBookAssembler $assembler,
        private readonly RulesPdfWriter $pdfWriter,
        private readonly RulesOdtWriter $odtWriter,
    ) {}

    /**
     * @param  callable(string, int): void|null  $onProgress  message, pourcentage
     * @return list<array{key: string, path: string, bytes: int}>
     */
    public function compile(bool $pdf = true, bool $odt = true, ?callable $onProgress = null): array
    {
        $report = static function (string $message, int $percent) use ($onProgress): void {
            if ($onProgress !== null) {
                $onProgress($message, $percent);
            }
        };

        $report('Assemblage des chapitres Markdown…', 5);
        $html = $this->assembler->toHtml();
        if ($html === '') {
            throw new \RuntimeException('Aucun chapitre de règles à compiler.');
        }

        $directory = trim((string) config('game_downloads.generated_directory', 'downloads/generated'), '/');
        $disk = Storage::disk((string) config('game_downloads.disk', 'public'));
        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $written = [];
        $items = collect(config('game_downloads.items', []))
            ->filter(fn (array $item): bool => (bool) ($item['generated'] ?? false))
            ->keyBy('key');

        if ($pdf && $items->has('rules-pdf')) {
            $report('Génération du PDF…', 40);
            $relative = $directory.'/'.(string) $items['rules-pdf']['filename'];
            $this->pdfWriter->write($html, $relative);
            $written[] = $this->describe('rules-pdf', $relative);
        }

        if ($odt && $items->has('rules-odt')) {
            $report('Génération de l’OpenDocument…', 75);
            $relative = $directory.'/'.(string) $items['rules-odt']['filename'];
            $this->odtWriter->write($html, $relative);
            $written[] = $this->describe('rules-odt', $relative);
        }

        $report('Compilation terminée.', 100);

        return $written;
    }

    /**
     * @return array{key: string, path: string, bytes: int}
     */
    private function describe(string $key, string $relativePath): array
    {
        $disk = Storage::disk((string) config('game_downloads.disk', 'public'));

        return [
            'key' => $key,
            'path' => $relativePath,
            'bytes' => (int) $disk->size($relativePath),
        ];
    }
}
