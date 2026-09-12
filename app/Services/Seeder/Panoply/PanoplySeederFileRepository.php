<?php

declare(strict_types=1);

namespace App\Services\Seeder\Panoply;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Accès aux fichiers JSON de panoplies versionnées (un fichier par set).
 *
 * @example
 * $repository = new PanoplySeederFileRepository();
 * foreach ($repository->all() as $file) { … }
 */
final class PanoplySeederFileRepository
{
    public const RELATIVE_ROOT = 'database/seeders/data/entities/panoplies';

    private const SUFFIX = '-panoply.json';

    public function __construct(private readonly ?string $absoluteRoot = null) {}

    public function root(): string
    {
        return $this->absoluteRoot ?? base_path(self::RELATIVE_ROOT);
    }

    /**
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
     * @param  array<string, mixed>  $payload
     */
    public function fileNameFor(array $payload): string
    {
        $row = is_array($payload['panoply'] ?? null) ? $payload['panoply'] : [];
        $slug = Str::slug((string) ($row['name'] ?? '')) ?: 'panoply';

        return $slug.self::SUFFIX;
    }

    public function relative(string $absolutePath): string
    {
        $base = base_path().DIRECTORY_SEPARATOR;

        return str_starts_with($absolutePath, $base)
            ? substr($absolutePath, strlen($base))
            : $absolutePath;
    }
}
