<?php

declare(strict_types=1);

namespace App\Support\Console;

/**
 * Estime un pourcentage 0–95 à partir de la sortie console (phases connues, `42 %`, volume).
 */
final class ConsoleProgressEstimator
{
    private int $percent = 5;

    private int $lines = 0;

    private string $label = 'En cours';

    private bool $hasPhaseLabel = false;

    /** @var array<string, int> */
    private const PHASES = [
        'Tests backend' => 20,
        'Tests frontend' => 35,
        'Qualité' => 50,
        'Pint' => 55,
        'PHPStan' => 60,
        'ESLint' => 62,
        'Sécurité' => 70,
        'Documentation' => 80,
        'composer' => 40,
        'pnpm' => 60,
        'Rapport écrit' => 95,
        'Dump' => 40,
        'Archive' => 70,
        'Synchronisation' => 30,
    ];

    public function ingest(string $chunk): void
    {
        if (preg_match_all('/\b(\d{1,3})\s*%/', $chunk, $matches) > 0) {
            foreach ($matches[1] as $raw) {
                $this->percent = max($this->percent, min(95, (int) $raw));
            }
        }

        foreach (self::PHASES as $needle => $pct) {
            if (stripos($chunk, $needle) !== false) {
                $this->percent = max($this->percent, min(95, $pct));
                $this->label = $needle;
                $this->hasPhaseLabel = true;
            }
        }

        $this->lines += substr_count($chunk, "\n");
        $this->percent = max($this->percent, min(90, 8 + intdiv($this->lines, 2)));

        if (! $this->hasPhaseLabel) {
            $trimmed = trim($chunk);
            $lastLine = $trimmed === '' ? '' : trim((string) strrchr("\n".$trimmed, "\n"));
            if ($lastLine !== '' && mb_strlen($lastLine) < 80) {
                $this->label = mb_substr($lastLine, 0, 80);
            }
        }
    }

    public function percent(): int
    {
        return max(0, min(95, $this->percent));
    }

    public function label(): string
    {
        return $this->label;
    }
}
