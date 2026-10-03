<?php

declare(strict_types=1);

namespace Tests\Unit\Characteristic;

use App\Support\Characteristics\CharacteristicDefinitionNaming;
use PHPUnit\Framework\TestCase;

/**
 * Chaque icône référencée dans characteristic-definitions doit exister sous storage/app/public/images.
 */
final class CharacteristicDefinitionIconAssetsTest extends TestCase
{
    public function test_characteristic_definition_icon_files_exist_and_are_non_empty(): void
    {
        $root = dirname(__DIR__, 3);
        $definitionsRoot = $root.'/'.CharacteristicDefinitionNaming::RELATIVE_ROOT;
        $imagesRoot = $root.'/storage/app/public/images';

        $missing = [];
        $empty = [];

        foreach (glob($definitionsRoot.'/*/*.json') ?: [] as $path) {
            $json = json_decode((string) file_get_contents($path), true);
            if (! is_array($json)) {
                continue;
            }
            $char = $json['characteristic'] ?? null;
            if (! is_array($char)) {
                continue;
            }

            foreach ($this->collectIconRefs($char) as $iconRef) {
                $resolved = $this->resolveIconFilesystemPath($iconRef, $imagesRoot);
                if ($resolved === null) {
                    continue;
                }
                if (! is_file($resolved)) {
                    $missing[] = basename($path).': '.$iconRef.' → '.$resolved;

                    continue;
                }
                if (filesize($resolved) === 0) {
                    $empty[] = basename($path).': '.$iconRef;
                }
            }
        }

        $this->assertSame([], $missing, "Icônes manquantes:\n".implode("\n", $missing));
        $this->assertSame([], $empty, "Icônes vides:\n".implode("\n", $empty));
    }

    /**
     * @param  array<string, mixed>  $characteristic
     * @return list<string>
     */
    private function collectIconRefs(array $characteristic): array
    {
        $refs = [];
        foreach (['icon', 'icon_false'] as $field) {
            $icon = $characteristic[$field] ?? null;
            if (is_string($icon) && trim($icon) !== '') {
                $refs[] = trim($icon);
            }
        }
        $overrides = $characteristic['value_overrides'] ?? null;
        if (is_array($overrides)) {
            foreach ($overrides as $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                $icon = $entry['icon'] ?? null;
                if (is_string($icon) && trim($icon) !== '') {
                    $refs[] = trim($icon);
                }
            }
        }

        return array_values(array_unique($refs));
    }

    private function resolveIconFilesystemPath(string $icon, string $imagesRoot): ?string
    {
        if (str_starts_with($icon, 'fa-') || str_contains($icon, 'fa-solid')) {
            return null;
        }
        if (str_starts_with($icon, 'http://') || str_starts_with($icon, 'https://')) {
            return null;
        }

        $normalized = str_replace('\\', '/', $icon);
        $normalized = preg_replace('#^icons/characteristics/#i', 'icons/caracteristics/', $normalized) ?? $normalized;
        $normalized = preg_replace('#^icons/caracteristiques/#i', 'icons/caracteristics/', $normalized) ?? $normalized;

        if (! str_contains($normalized, '/')) {
            $normalized = 'icons/caracteristics/'.$normalized;
        }

        if (str_starts_with($normalized, '/')) {
            $normalized = ltrim($normalized, '/');
        }
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = preg_replace('#^storage/images/#', '', $normalized) ?? $normalized;
        }
        if (str_starts_with($normalized, 'images/')) {
            $normalized = substr($normalized, strlen('images/'));
        }

        return $imagesRoot.'/'.ltrim($normalized, '/');
    }
}
