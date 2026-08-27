<?php

declare(strict_types=1);

namespace App\Console;

use RuntimeException;

/**
 * Lit {@see COMMANDS.md} : un bloc YAML par commande (signature, domain, ui, cron).
 *
 * Ce n’est pas une seconde documentation : le Markdown reste la source, ce lecteur
 * sert au filtrage UI (`ui: true`) et aux tests.
 *
 * @phpstan-type CommandEntry array{
 *     signature: string,
 *     domain: string,
 *     ui: bool,
 *     cron: bool,
 *     heading: string,
 *     body: string
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

            $entries[] = [
                'signature' => $signature,
                'domain' => isset($meta['domain']) ? trim($meta['domain']) : '',
                'ui' => self::toBool($meta['ui'] ?? 'false'),
                'cron' => self::toBool($meta['cron'] ?? 'false'),
                'heading' => $heading,
                'body' => trim($match[3]),
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
}
