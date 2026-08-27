<?php

declare(strict_types=1);

namespace App\Console;

use RuntimeException;

/**
 * Lit {@see COMMANDS.md} : un bloc YAML par commande (signature, domain, ui, cron, admin).
 *
 * Ce n’est pas une seconde documentation : le Markdown reste la source, ce lecteur
 * sert au filtrage UI (`ui: true`) et aux tests.
 *
 * @phpstan-type CommandEntry array{
 *     signature: string,
 *     domain: string,
 *     ui: bool,
 *     cron: bool,
 *     admin: string,
 *     heading: string,
 *     body: string,
 *     summary: string
 * }
 */
final class CommandGuide
{
    public const MARKDOWN_RELATIVE_PATH = 'app/Console/COMMANDS.md';

    /**
     * @return list<CommandEntry>
     */
    public static function all(?string $markdownAbsolutePath = null): array
    {
        $path = $markdownAbsolutePath ?? base_path(self::MARKDOWN_RELATIVE_PATH);
        if (! is_file($path)) {
            throw new RuntimeException('Guide commandes introuvable : '.$path);
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new RuntimeException('Impossible de lire '.$path);
        }

        return self::parse($raw);
    }

    /**
     * Commandes exposables dans l’admin (jamais `project:dev` / prepare / init).
     *
     * @return list<CommandEntry>
     */
    public static function forUi(?string $markdownAbsolutePath = null): array
    {
        return array_values(array_filter(
            self::all($markdownAbsolutePath),
            static fn (array $entry): bool => $entry['ui'] === true
        ));
    }

    /**
     * Cartes UI : signature, domaine, lien admin, résumé.
     *
     * @return list<array{signature: string, domain: string, admin: string, summary: string}>
     */
    public static function forUiCards(?string $markdownAbsolutePath = null): array
    {
        return array_map(
            static fn (array $entry): array => [
                'signature' => $entry['signature'],
                'domain' => $entry['domain'],
                'admin' => $entry['admin'],
                'summary' => $entry['summary'],
            ],
            self::forUi($markdownAbsolutePath)
        );
    }

    /**
     * @return list<CommandEntry>
     */
    public static function parse(string $markdown): array
    {
        $entries = [];
        if (! preg_match_all(
            '/^## `([^`]+)`\s*\n+```yaml\n(.*?)```\n*(.*?)(?=^## |\z)/ms',
            $markdown,
            $matches,
            PREG_SET_ORDER
        )) {
            return $entries;
        }

        foreach ($matches as $match) {
            $heading = trim($match[1]);
            $meta = self::parseYamlBlock($match[2]);
            $signature = isset($meta['signature']) ? trim($meta['signature']) : $heading;
            $body = trim($match[3]);

            $entries[] = [
                'signature' => $signature,
                'domain' => isset($meta['domain']) ? trim($meta['domain']) : '',
                'ui' => self::toBool($meta['ui'] ?? 'false'),
                'cron' => self::toBool($meta['cron'] ?? 'false'),
                'admin' => self::sanitizeAdminPath($meta['admin'] ?? ''),
                'heading' => $heading,
                'body' => $body,
                'summary' => self::summaryFromBody($body),
            ];
        }

        return $entries;
    }

    /**
     * @return array<string, string>
     */
    private static function parseYamlBlock(string $block): array
    {
        $out = [];
        foreach (preg_split('/\R/', $block) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || ! str_contains($line, ':')) {
                continue;
            }
            [$key, $value] = explode(':', $line, 2);
            $out[trim($key)] = trim($value);
        }

        return $out;
    }

    private static function toBool(string $value): bool
    {
        return in_array(strtolower(trim($value)), ['1', 'true', 'yes'], true);
    }

    /**
     * Lien interne admin uniquement (`/admin/...`).
     */
    private static function sanitizeAdminPath(string $value): string
    {
        $value = trim($value);
        if ($value === '' || ! str_starts_with($value, '/admin/')) {
            return '';
        }
        if (str_contains($value, '..') || str_contains($value, '://') || str_contains($value, "\n")) {
            return '';
        }

        return $value;
    }

    private static function summaryFromBody(string $body): string
    {
        $text = preg_replace('/```.*?```/s', '', $body) ?? $body;
        $text = trim($text);
        if ($text === '') {
            return '';
        }
        $first = explode("\n\n", $text)[0];
        $first = preg_replace('/\s+/', ' ', $first) ?? $first;
        $first = trim($first);

        if (function_exists('mb_substr')) {
            return mb_substr($first, 0, 220);
        }

        return substr($first, 0, 220);
    }
}
