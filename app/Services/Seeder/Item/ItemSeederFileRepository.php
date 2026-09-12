<?php

declare(strict_types=1);

namespace App\Services\Seeder\Item;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Accès aux fichiers JSON d'équipements versionnés (un fichier par item).
 *
 * @example
 * $repository = new ItemSeederFileRepository();
 * foreach ($repository->all() as $payload) { … }
 */
final class ItemSeederFileRepository
{
    public const RELATIVE_ROOT = 'database/seeders/data/entities/items';

    private const SUFFIX = '-item.json';

    public function __construct(private readonly ?string $absoluteRoot = null) {}

    public function root(): string
    {
        return $this->absoluteRoot ?? base_path(self::RELATIVE_ROOT);
    }

    /**
     * Chemins absolus des fichiers d'items, triés pour un ordre de seed reproductible.
     *
     * @return list<string>
     */
    public function paths(): array
    {
        $root = $this->root();
        if (! is_dir($root)) {
            return [];
        }
        $paths = glob($root.DIRECTORY_SEPARATOR.'*'.self::SUFFIX) ?: [];
        sort($paths, SORT_NATURAL);

        return array_values($paths);
    }

    /**
     * Contenu décodé de tous les fichiers. Les fichiers illisibles sont ignorés.
     *
     * @return list<array{path: string, payload: array<string, mixed>}>
     */
    public function all(): array
    {
        $out = [];
        foreach ($this->paths() as $path) {
            $payload = $this->read($path);
            if ($payload !== null) {
                $out[] = ['path' => $path, 'payload' => $payload];
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function read(string $absolutePath): ?array
    {
        if (! is_file($absolutePath)) {
            return null;
        }
        $decoded = json_decode((string) file_get_contents($absolutePath), true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Écrit un item et retourne le chemin absolu du fichier.
     *
     * @param  array<string, mixed>  $payload
     */
    public function write(array $payload, string $fileName): string
    {
        $root = $this->root();
        if (! is_dir($root)) {
            File::makeDirectory($root, 0755, true);
        }
        $path = $root.DIRECTORY_SEPARATOR.$fileName;
        File::put($path, json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        )."\n");

        return $path;
    }

    /**
     * Nom de fichier déterministe : slug du nom, suffixé par la clé en cas de collision.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $takenByIdentity  slug déjà attribué => identité de l'item
     */
    public function fileNameFor(array $payload, array $takenByIdentity = []): string
    {
        $item = is_array($payload['item'] ?? null) ? $payload['item'] : [];
        $slug = Str::slug((string) ($item['name'] ?? '')) ?: 'item';
        $identity = ItemSeederPayload::identity($payload);

        $taken = $takenByIdentity[$slug] ?? null;
        if ($taken !== null && $taken !== $identity) {
            $suffix = Str::slug(str_replace(':', '-', $identity)) ?: 'x';
            $slug .= '-'.$suffix;
        }

        return $slug.self::SUFFIX;
    }

    /**
     * Supprime les fichiers absents de la liste fournie. Retourne les chemins supprimés.
     *
     * @param  list<string>  $keptAbsolutePaths
     * @return list<string>
     */
    public function prune(array $keptAbsolutePaths): array
    {
        $kept = array_flip($keptAbsolutePaths);
        $removed = [];
        foreach ($this->paths() as $path) {
            if (! isset($kept[$path])) {
                File::delete($path);
                $removed[] = $path;
            }
        }

        return $removed;
    }

    public function relative(string $absolutePath): string
    {
        $base = base_path().DIRECTORY_SEPARATOR;

        return str_starts_with($absolutePath, $base)
            ? substr($absolutePath, strlen($base))
            : $absolutePath;
    }
}
