<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Assemble et enregistre le journal public (fichiers Markdown, pas la base).
 *
 * @example Fichiers sous `storage/app/public/changelog/` : `intro.md`, `roadmap.md`, `1.3.2.md`.
 */
final class ChangelogMarkdownService
{
    private const VERSION_PATTERN = '/^\d+\.\d+\.\d+$/';

    private const MAX_CHARS = 80000;

    /**
     * @return list<string>
     */
    public function listSortedVersions(): array
    {
        $disk = Storage::disk('public');
        $versions = [];
        foreach ($disk->files('changelog') as $path) {
            $base = basename((string) $path, '.md');
            if (! preg_match(self::VERSION_PATTERN, $base)) {
                continue;
            }
            $versions[] = $base;
        }

        usort($versions, static fn (string $a, string $b): int => version_compare($a, $b));

        /** @var list<string> $versions */
        return $versions;
    }

    public function isValidVersionSlug(string $version): bool
    {
        return (bool) preg_match(self::VERSION_PATTERN, $version);
    }

    /**
     * Nom de fichier éditable : `intro`, `roadmap`, ou une version `X.Y.Z`.
     */
    public function isEditableName(string $name): bool
    {
        return $name === 'intro' || $name === 'roadmap' || $this->isValidVersionSlug($name);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function composeFeedMarkdown(string $version): string
    {
        if (! $this->isValidVersionSlug($version) || ! $this->exists($version)) {
            abort(404);
        }

        $chunks = [];
        $intro = $this->read('intro');
        if ($intro !== null && trim($intro) !== '') {
            $chunks[] = rtrim($intro)."\n\n";
        }

        $chunks[] = $this->navigationMarkdown($version, $this->listSortedVersions());
        $chunks[] = rtrim((string) $this->read($version))."\n";

        return implode('', $chunks);
    }

    public function read(string $name): ?string
    {
        if (! $this->isEditableName($name) || ! $this->exists($name)) {
            return null;
        }

        $contents = Storage::disk('public')->get($this->relativePath($name));

        return is_string($contents) ? $contents : null;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function write(string $name, string $markdown): void
    {
        if (! $this->isEditableName($name)) {
            abort(404);
        }

        $markdown = str_replace(["\r\n", "\r", "\0"], ["\n", "\n", ''], $markdown);
        if (mb_strlen($markdown) > self::MAX_CHARS) {
            abort(422, 'Le texte est trop long.');
        }

        Storage::disk('public')->put($this->relativePath($name), $markdown);
    }

    /**
     * Fichiers que l’admin peut ouvrir depuis la page Journal.
     *
     * @return list<array{name: string, label: string, markdown: string}>
     */
    public function editableFiles(): array
    {
        $files = [
            $this->describe('roadmap', 'La suite'),
            $this->describe('intro', 'Introduction'),
        ];

        foreach ($this->listSortedVersions() as $version) {
            $files[] = $this->describe($version, 'Version '.$version);
        }

        return array_values(array_filter($files));
    }

    /**
     * Découpe `roadmap.md` en étapes de frise.
     *
     * Chaque étape est un titre `## 1.4 · Prochaine version` suivi d’un paragraphe.
     * Le libellé fixe l’état : « Déjà là », « Prochaine version », sinon « Ensuite ».
     *
     * @return list<array{version: string, label: string, state: string, text: string}>
     */
    public function parseRoadmap(string $markdown): array
    {
        $steps = [];
        $current = null;
        $buffer = [];

        $push = function () use (&$steps, &$current, &$buffer): void {
            if ($current === null) {
                return;
            }
            $text = trim(preg_replace("/\n{2,}/", "\n\n", implode("\n", $buffer)) ?? '');
            $steps[] = [
                'version' => $current['version'],
                'label' => $current['label'],
                'state' => $this->roadmapState($current['label']),
                'text' => $text,
            ];
        };

        foreach (preg_split("/\r\n|\n|\r/", $markdown) ?: [] as $line) {
            if (preg_match('/^##\s+(.+?)\s+[·•|\-]\s+(.+?)\s*$/u', (string) $line, $matches) === 1) {
                $push();
                $current = [
                    'version' => trim($matches[1]),
                    'label' => trim($matches[2]),
                ];
                $buffer = [];

                continue;
            }
            if ($current !== null) {
                $buffer[] = (string) $line;
            }
        }
        $push();

        /** @var list<array{version: string, label: string, state: string, text: string}> $steps */
        return $steps;
    }

    private function exists(string $name): bool
    {
        return Storage::disk('public')->exists($this->relativePath($name));
    }

    private function relativePath(string $name): string
    {
        return 'changelog/'.$name.'.md';
    }

    /**
     * @return array{name: string, label: string, markdown: string}|null
     */
    private function describe(string $name, string $label): ?array
    {
        $markdown = $this->read($name);
        if ($markdown === null && $name !== 'roadmap' && $name !== 'intro') {
            return null;
        }

        return [
            'name' => $name,
            'label' => $label,
            'markdown' => $markdown ?? '',
        ];
    }

    /**
     * @param  list<string>  $sortedVersions
     */
    private function navigationMarkdown(string $current, array $sortedVersions): string
    {
        if ($sortedVersions === []) {
            return '';
        }

        $parts = [];
        foreach ($sortedVersions as $version) {
            if ($version === $current) {
                $parts[] = "**{$version}**";
            } else {
                $parts[] = sprintf('[*%s*](/changelog/feed/%s)', $version, $version);
            }
        }

        return "### Versions\n\n"
            .implode(' · ', $parts)
            ."\n\n---\n\n";
    }

    private function roadmapState(string $label): string
    {
        $normalized = mb_strtolower($label);
        if (str_contains($normalized, 'déjà') || str_contains($normalized, 'deja')) {
            return 'done';
        }
        if (str_contains($normalized, 'prochaine')) {
            return 'next';
        }

        return 'later';
    }
}
