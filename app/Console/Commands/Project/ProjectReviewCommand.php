<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use Illuminate\Console\Command;

/**
 * Alias « projet » de {@see \App\Console\Commands\Dev\DevReviewCommand} : même rapport Markdown
 * (tests, qualité, sécurité, doc) pour le fournir à un agent Cursor.
 *
 * **Mode profil** (sans options d’action) : argument `tests`, `quality`, `security`, `docs`, `all` (défaut `all`).
 * **Mode actions** : `--pint`, `--tests`, `--test-back`, `--test-front`, `--phpstan`, `--eslint`, `--security`, `--docs`, `--all` (combinables). Sans argument ni option d’action → équivalent **`--all`**.
 *
 * @example php artisan project:review
 * @example php artisan project:review --pint
 * @example php artisan project:review --test-back --phpstan
 * @example php artisan project:review --tests
 * @example php artisan project:review --all
 * @example php artisan project:review tests --report-path=storage/app/dev-reports/last-review.md
 */
class ProjectReviewCommand extends Command
{
    protected $signature = 'project:review|review
        {profile? : Profil (sans options d’action) : tests, quality, security, docs ou all (défaut : all)}
        {--report-path= : Chemin absolu ou relatif au rapport Markdown (défaut : storage/app/dev-reports/review-<timestamp>.md)}
        {--no-cursor-prompts : N’affiche pas le rappel terminal sur les prompts (les prompts restent en fin de rapport Markdown)}
        {--fix-pint : Après Pint (--test), appliquer Laravel Pint sans mode test (modifie les fichiers)}
        {--cursor-agent : Enchaîne des Agent.prompt locaux (@cursor/sdk) ; requiert CURSOR_API_KEY, Node et pnpm install}
        {--all : Toutes les étapes (tests back+front, PHPStan, Pint, ESLint, audit Composer, doc)}
        {--pint : Laravel Pint en mode --test uniquement}
        {--tests : Tests PHPUnit + Vitest (`pnpm run test:run`)}
        {--test-back : Tests PHPUnit (`php artisan test`) uniquement}
        {--test-front : Tests Vitest (`pnpm run test:run`) uniquement}
        {--phpstan : PHPStan (Larastan)}
        {--eslint : ESLint (`pnpm run lint`)}
        {--security : `composer audit`}
        {--docs : Contrôles légers sur la documentation}';

    protected $description = 'Rapport dev Markdown (alias de dev:review) — profil ou options par action';

    public function handle(): int
    {
        $params = [];
        $profile = $this->argument('profile');
        if ($profile !== null && trim((string) $profile) !== '') {
            $params['profile'] = (string) $profile;
        }
        $reportPath = $this->option('report-path');
        if (is_string($reportPath) && trim($reportPath) !== '') {
            $params['--report-path'] = $reportPath;
        }
        if ($this->option('no-cursor-prompts')) {
            $params['--no-cursor-prompts'] = true;
        }
        if ($this->option('fix-pint')) {
            $params['--fix-pint'] = true;
        }
        if ($this->option('cursor-agent')) {
            $params['--cursor-agent'] = true;
        }
        foreach ([
            'all',
            'pint',
            'tests',
            'test-back',
            'test-front',
            'phpstan',
            'eslint',
            'security',
            'docs',
        ] as $flag) {
            if ($this->option($flag)) {
                $params['--'.$flag] = true;
            }
        }

        return $this->call('dev:review', $params);
    }
}
