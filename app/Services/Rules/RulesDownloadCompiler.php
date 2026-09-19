<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Compile les livres de règles en PDF et ODT.
 *
 * Livre joueur (ch. 1–4, disque public) et atelier MJ (ch. 5, disque privé) :
 * un `read_level` MJ ne doit pas atterrir sous `/storage/…`.
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
        private readonly GameDownloadCatalog $catalog,
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

        $directory = trim((string) config('game_downloads.generated_directory', 'downloads/generated'), '/');

        $items = collect(config('game_downloads.items', []))
            ->filter(fn (array $item): bool => (bool) ($item['generated'] ?? false))
            ->filter(function (array $item) use ($pdf, $odt): bool {
                $mime = (string) ($item['mime'] ?? '');
                if ($mime === 'application/pdf') {
                    return $pdf;
                }
                if (str_contains($mime, 'opendocument')) {
                    return $odt;
                }

                return true;
            })
            ->values();

        $report('Assemblage des chapitres Markdown…', 5);
        $htmlByAudience = [];
        foreach ($items as $item) {
            $audience = (string) ($item['audience'] ?? RulesBookAssembler::AUDIENCE_PLAYER);
            if (! array_key_exists($audience, $htmlByAudience)) {
                $htmlByAudience[$audience] = $this->assembler->forAudience($audience)->toHtml();
            }
        }

        if (trim(implode('', $htmlByAudience)) === '') {
            throw new RuntimeException('Aucun chapitre de règles à compiler.');
        }

        $written = [];
        $total = max(1, $items->count());
        foreach ($items as $index => $item) {
            $audience = (string) ($item['audience'] ?? RulesBookAssembler::AUDIENCE_PLAYER);
            $html = trim($htmlByAudience[$audience] ?? '');
            if ($html === '') {
                continue;
            }

            $filename = (string) ($item['filename'] ?? '');
            if ($filename === '') {
                continue;
            }

            $percent = 20 + (int) floor((($index + 1) / $total) * 75);
            $report('Génération de '.$filename.'…', $percent);

            $relative = $directory.'/'.$filename;
            $diskName = $this->catalog->diskName($item);
            $disk = Storage::disk($diskName);
            if (! $disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }
            $title = $audience === RulesBookAssembler::AUDIENCE_MJ
                ? 'Krosmoz JDR — Atelier MJ'
                : 'Krosmoz JDR — Livre de règles';
            $mime = (string) ($item['mime'] ?? '');
            if ($mime === 'application/pdf') {
                $this->pdfWriter->write($html, $relative, $title, $diskName);
            } else {
                $this->odtWriter->write($html, $relative, $title, $diskName);
            }
            $this->catalog->purgePublicCopyIfRestricted($item);

            $written[] = $this->describe((string) $item['key'], $relative, $diskName);
        }

        if ($written === []) {
            throw new RuntimeException('Aucun chapitre de règles à compiler.');
        }

        foreach (config('game_downloads.items', []) as $item) {
            if (is_array($item)) {
                $this->catalog->purgePublicCopyIfRestricted($item);
            }
        }

        $report('Compilation terminée.', 100);

        return $written;
    }

    /**
     * @return array{key: string, path: string, bytes: int}
     */
    private function describe(string $key, string $relativePath, string $diskName): array
    {
        $disk = Storage::disk($diskName);

        return [
            'key' => $key,
            'path' => $relativePath,
            'bytes' => (int) $disk->size($relativePath),
        ];
    }
}
