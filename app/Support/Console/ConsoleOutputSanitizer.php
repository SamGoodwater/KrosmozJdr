<?php

declare(strict_types=1);

namespace App\Support\Console;

/**
 * Filtre la sortie Artisan avant affichage web : ANSI, secrets, lignes trop longues.
 *
 * @example
 * $clean = (new ConsoleOutputSanitizer)->sanitize("DB_PASSWORD=secret\nOK");
 */
final class ConsoleOutputSanitizer
{
    public const MAX_LINE_LENGTH = 500;

    public const MAX_OUTPUT_CHARS = 100_000;

    /**
     * Nettoie un fragment de sortie console.
     */
    public function sanitize(string $chunk): string
    {
        $text = preg_replace('/\e\[[\d;]*[A-Za-z]/', '', $chunk) ?? $chunk;
        $text = str_replace("\r", '', $text);

        $replacements = [
            '/(password|passwd|secret|token|api[_-]?key|app_key|authorization)\s*[:=]\s*\S+/i' => '$1=***',
            '/\b(APP_KEY|DB_PASSWORD|MAIL_PASSWORD|REDIS_PASSWORD|AWS_SECRET_ACCESS_KEY|CURSOR_API_KEY)\s*=\s*\S+/i' => '$1=***',
            '/Bearer\s+[A-Za-z0-9\-._~+\/]+=*/i' => 'Bearer ***',
            '#://[^:\s/]+:[^@\s/]+@#' => '://***:***@',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        $lines = explode("\n", $text);
        $lines = array_map(function (string $line): string {
            if (mb_strlen($line) > self::MAX_LINE_LENGTH) {
                return mb_substr($line, 0, self::MAX_LINE_LENGTH).'…';
            }

            return $line;
        }, $lines);

        return implode("\n", $lines);
    }

    /**
     * Conserve la fin de la sortie si elle dépasse le plafond.
     */
    public function cap(string $output): string
    {
        if (strlen($output) <= self::MAX_OUTPUT_CHARS) {
            return $output;
        }

        return "[… sortie tronquée …]\n".substr($output, -self::MAX_OUTPUT_CHARS);
    }
}
