<?php

declare(strict_types=1);

namespace App\Services\Rules;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Liste les fichiers du catalogue `config/game_downloads.php` avec taille et URL.
 *
 * Un fichier généré absent n’est pas une erreur : il apparaîtra après
 * `rules:compile-downloads`.
 *
 * @example
 * $files = app(GameDownloadCatalog::class)->list();
 */
class GameDownloadCatalog
{
    /**
     * @return list<array{
     *   key: string,
     *   label: string,
     *   description: string,
     *   group: string,
     *   group_label: string,
     *   icon: string,
     *   mime: string,
     *   generated: bool,
     *   available: bool,
     *   size: int|null,
     *   updated_at: string|null,
     *   download_url: string
     * }>
     */
    public function list(): array
    {
        $items = [];
        foreach (config('game_downloads.items', []) as $item) {
            if (! is_array($item) || ! isset($item['key'])) {
                continue;
            }
            $relative = $this->relativePath($item);
            $available = $relative !== null && $this->disk()->exists($relative);
            $size = $available ? (int) $this->disk()->size($relative) : null;
            $mtime = $available ? $this->disk()->lastModified($relative) : null;

            $items[] = [
                'key' => (string) $item['key'],
                'label' => (string) ($item['label'] ?? $item['key']),
                'description' => (string) ($item['description'] ?? ''),
                'group' => (string) ($item['group'] ?? 'autres'),
                'group_label' => (string) ($item['group_label'] ?? 'Autres'),
                'icon' => (string) ($item['icon'] ?? 'fa-file'),
                'mime' => (string) ($item['mime'] ?? 'application/octet-stream'),
                'generated' => (bool) ($item['generated'] ?? false),
                'available' => $available,
                'size' => $size,
                'updated_at' => $mtime !== null ? date('c', $mtime) : null,
                'download_url' => route('game-downloads.show', ['key' => $item['key']]),
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(string $key): ?array
    {
        foreach ($this->list() as $item) {
            if ($item['key'] === $key) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Chemin relatif sur le disque public, ou null si l’entrée est mal configurée.
     *
     * @param  array<string, mixed>  $item
     */
    public function relativePath(array $item): ?string
    {
        if (! empty($item['generated'])) {
            $filename = (string) ($item['filename'] ?? '');
            if ($filename === '') {
                return null;
            }

            return trim((string) config('game_downloads.generated_directory', 'downloads/generated'), '/').'/'.$filename;
        }

        $path = (string) ($item['path'] ?? '');

        return $path !== '' ? $path : null;
    }

    /**
     * @return array{generated_at: string|null, available: int, missing: int}
     */
    public function generatedStatus(): array
    {
        $generated = array_values(array_filter(
            $this->list(),
            static fn (array $item): bool => $item['generated']
        ));
        $available = count(array_filter($generated, static fn (array $item): bool => $item['available']));
        $dates = array_values(array_filter(array_column($generated, 'updated_at')));

        return [
            'generated_at' => $dates === [] ? null : max($dates),
            'available' => $available,
            'missing' => count($generated) - $available,
        ];
    }

    private function disk(): Filesystem
    {
        return Storage::disk((string) config('game_downloads.disk', 'public'));
    }
}
