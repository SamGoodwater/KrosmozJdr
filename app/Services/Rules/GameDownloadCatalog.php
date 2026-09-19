<?php

declare(strict_types=1);

namespace App\Services\Rules;

use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Liste les fichiers du catalogue `config/game_downloads.php` avec taille et URL.
 *
 * Un fichier généré absent n’est pas une erreur : il apparaîtra après
 * `rules:compile-downloads`. Les entrées `read_level` sont filtrées selon le rôle.
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
    public function list(?User $user = null): array
    {
        $user ??= Auth::user();
        $items = [];
        foreach (config('game_downloads.items', []) as $item) {
            if (! is_array($item) || ! isset($item['key'])) {
                continue;
            }
            $this->purgePublicCopyIfRestricted($item);
            if (! $this->userCanAccess($item, $user)) {
                continue;
            }
            $relative = $this->relativePath($item);
            $disk = $this->diskFor($item);
            $available = $relative !== null && $disk->exists($relative);
            $size = $available ? (int) $disk->size($relative) : null;
            $mtime = $available ? $disk->lastModified($relative) : null;

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
     * Entrée brute de `config/game_downloads.php`, sans filtre de rôle.
     *
     * @return array<string, mixed>|null
     */
    public function configItem(string $key): ?array
    {
        foreach (config('game_downloads.items', []) as $item) {
            if (is_array($item) && (string) ($item['key'] ?? '') === $key) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Fichier réservé (MJ+) : hors disque public, sinon `/storage/…` contourne le 403.
     *
     * @param  array<string, mixed>  $item
     */
    public function isRestricted(array $item): bool
    {
        return (int) ($item['read_level'] ?? User::ROLE_GUEST) > User::ROLE_GUEST;
    }

    /**
     * Disque de stockage : `local` (privé) si `read_level` > invité, sinon le disque catalogue.
     *
     * @param  array<string, mixed>  $item
     */
    public function diskName(array $item): string
    {
        return $this->isRestricted($item)
            ? 'local'
            : (string) config('game_downloads.disk', 'public');
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public function diskFor(array $item): Filesystem
    {
        return Storage::disk($this->diskName($item));
    }

    /**
     * Migre une copie publique oubliée vers le disque privé, puis l’efface.
     *
     * Sans ça, un atelier MJ déjà compilé resterait téléchargeable via
     * `/storage/downloads/generated/krosmoz-jdr-atelier-mj.pdf`.
     *
     * @param  array<string, mixed>  $item
     */
    public function purgePublicCopyIfRestricted(array $item): void
    {
        if (! $this->isRestricted($item)) {
            return;
        }

        $relative = $this->relativePath($item);
        if ($relative === null) {
            return;
        }

        $public = Storage::disk('public');
        if (! $public->exists($relative)) {
            return;
        }

        $private = Storage::disk('local');
        if (! $private->exists($relative)) {
            $contents = $public->get($relative);
            if (is_string($contents) && $contents !== '') {
                $private->put($relative, $contents);
            }
        }

        $public->delete($relative);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    public function userCanAccess(array $item, ?User $user = null): bool
    {
        $user ??= Auth::user();
        $required = (int) ($item['read_level'] ?? User::ROLE_GUEST);
        $role = $user !== null ? (int) $user->role : User::ROLE_GUEST;

        return $role >= $required;
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
     * Chemin relatif sur le disque de l’entrée, ou null si elle est mal configurée.
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

}
