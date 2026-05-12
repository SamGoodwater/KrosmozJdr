<?php

declare(strict_types=1);

namespace App\Console\Commands\Dev;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Throwable;

/**
 * Lance des vérifications locales (tests, analyse statique, audit, doc) et produit un rapport Markdown
 * + des prompts prêts à coller dans Cursor pour analyse / correctifs « agent ».
 *
 * Deux modes :
 * - **Profil** (argument positionnel) : `tests`, `quality`, `security`, `docs`, `all` — inchangé si aucune option d’action explicite.
 * - **Actions** (options `--pint`, `--tests`, `--test-back`, `--test-front`, `--phpstan`, `--eslint`, `--security`, `--docs`, `--all`) :
 *   combinaison libre ; sans argument ni option d’action → équivalent **`--all`** (tout le périmètre).
 *
 * @example php artisan dev:review tests
 * @example php artisan dev:review quality
 * @example php artisan dev:review all --fix-pint
 * @example php artisan dev:review --pint
 * @example php artisan dev:review --test-back --phpstan
 * @example php artisan dev:review --tests
 * @example php artisan dev:review --all
 *
 * @see docs/10-BestPractices/SECURITY_PRACTICES.md
 * @see docs/10-BestPractices/TESTING_PRACTICES.md
 */
class DevReviewCommand extends Command
{
    use GuardsProductionEnvironment;

    protected $signature = 'dev:review
        {profile? : Profil (si aucune option d’action explicite) : tests, quality, security, docs ou all (défaut : all)}
        {--report-path= : Chemin absolu ou relatif au rapport Markdown (défaut : storage/app/dev-reports/review-<timestamp>.md)}
        {--no-cursor-prompts : N’affiche pas le rappel terminal sur les prompts (les prompts restent en fin de rapport Markdown)}
        {--fix-pint : Après Pint (--test), appliquer Laravel Pint sans mode test (modifie les fichiers)}
        {--cursor-agent : Après le rapport, enchaîne des Agent.prompt locaux (@cursor/sdk) pour chaque bloc « Prompts Cursor » ; requiert CURSOR_API_KEY, Node et pnpm install}
        {--all : Toutes les étapes (tests back+front, PHPStan, Pint, ESLint, audit Composer, doc)}
        {--pint : Laravel Pint en mode --test uniquement}
        {--tests : Tests PHPUnit + Vitest (`pnpm run test:run`)}
        {--test-back : Tests PHPUnit (`php artisan test`) uniquement}
        {--test-front : Tests Vitest (`pnpm run test:run`) uniquement}
        {--phpstan : PHPStan (Larastan)}
        {--eslint : ESLint (`pnpm run lint`)}
        {--security : `composer audit`}
        {--docs : Contrôles légers sur la documentation}';

    protected $description = 'Rapport dev local (tests, qualité, sécurité, doc) + prompts Cursor ; options par action ou profil legacy.';

    /** @var list<string> */
    private array $failures = [];

    /**
     * Plan d’exécution courant (clés : test_back, test_front, pint, phpstan, eslint, security, docs).
     *
     * @var array<string, bool>
     */
    private array $actionPlan = [];

    public function handle(): int
    {
        if (! $this->guardNotProduction('dev:review est interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        $profileArg = $this->argument('profile');
        $profileRaw = is_string($profileArg) ? trim($profileArg) : '';

        if ($this->hasExplicitActionOptions()) {
            if ($profileRaw !== '') {
                $this->warn('Le profil positionnel « '.$profileRaw.' » est ignoré : les options `--pint`, `--tests`, `--all`, etc. définissent seules le périmètre.');
            }
            $this->actionPlan = $this->resolveActionPlanFromOptions();
            $modeLabel = 'composition';
        } else {
            $profile = strtolower($profileRaw !== '' ? $profileRaw : 'all');
            if (! in_array($profile, ['tests', 'quality', 'security', 'docs', 'all'], true)) {
                $this->error('Profil inconnu : '.$profile.' (attendu : tests, quality, security, docs, all). Utilisez les options --pint, --tests, --all, etc. pour le mode par actions.');

                return ArtisanExitCode::FAILURE;
            }
            $this->actionPlan = $this->legacyProfileToActionPlan($profile);
            $modeLabel = 'profil `'.$profile.'`';
        }

        $reportPath = $this->resolveReportPath();
        File::ensureDirectoryExists(dirname($reportPath));

        $buffer = $this->headerMarkdown($modeLabel);
        $buffer .= $this->runActionPlan();
        $buffer .= $this->cursorPromptsMarkdownAppendix();
        $buffer .= $this->footerMarkdown();

        File::put($reportPath, $buffer);
        $this->info('Rapport écrit : '.$reportPath);

        if (! $this->option('no-cursor-prompts')) {
            $this->newLine();
            $this->writeCursorPromptsConsoleHint($reportPath);
        }

        if ($this->option('cursor-agent')) {
            $this->newLine();
            if ($this->runCursorSdkAgents($reportPath) !== ArtisanExitCode::SUCCESS) {
                $this->failures[] = 'cursor-sdk';
            }
        }

        if ($this->failures !== []) {
            $this->newLine();
            $this->warn('Étapes en échec : '.implode(', ', $this->failures));
            $this->comment('Détails : sorties brutes des outils dans le rapport Markdown.');

            return ArtisanExitCode::FAILURE;
        }

        return ArtisanExitCode::SUCCESS;
    }

    /**
     * @return array<string, bool>
     */
    private function legacyProfileToActionPlan(string $profile): array
    {
        $all = [
            'test_back' => false,
            'test_front' => false,
            'pint' => false,
            'phpstan' => false,
            'eslint' => false,
            'security' => false,
            'docs' => false,
        ];

        return match ($profile) {
            'tests' => array_merge($all, ['test_back' => true, 'test_front' => true]),
            'quality' => array_merge($all, ['pint' => true, 'phpstan' => true, 'eslint' => true]),
            'security' => array_merge($all, ['security' => true]),
            'docs' => array_merge($all, ['docs' => true]),
            'all' => array_merge($all, [
                'test_back' => true,
                'test_front' => true,
                'pint' => true,
                'phpstan' => true,
                'eslint' => true,
                'security' => true,
                'docs' => true,
            ]),
            default => $all,
        };
    }

    private function hasExplicitActionOptions(): bool
    {
        return $this->option('all')
            || $this->option('pint')
            || $this->option('tests')
            || $this->option('test-back')
            || $this->option('test-front')
            || $this->option('phpstan')
            || $this->option('eslint')
            || $this->option('security')
            || $this->option('docs');
    }

    /**
     * @return array<string, bool>
     */
    private function resolveActionPlanFromOptions(): array
    {
        $empty = [
            'test_back' => false,
            'test_front' => false,
            'pint' => false,
            'phpstan' => false,
            'eslint' => false,
            'security' => false,
            'docs' => false,
        ];

        if ($this->option('all')) {
            return array_merge($empty, [
                'test_back' => true,
                'test_front' => true,
                'pint' => true,
                'phpstan' => true,
                'eslint' => true,
                'security' => true,
                'docs' => true,
            ]);
        }

        $plan = $empty;
        if ($this->option('tests')) {
            $plan['test_back'] = true;
            $plan['test_front'] = true;
        }
        if ($this->option('test-back')) {
            $plan['test_back'] = true;
        }
        if ($this->option('test-front')) {
            $plan['test_front'] = true;
        }
        if ($this->option('pint')) {
            $plan['pint'] = true;
        }
        if ($this->option('phpstan')) {
            $plan['phpstan'] = true;
        }
        if ($this->option('eslint')) {
            $plan['eslint'] = true;
        }
        if ($this->option('security')) {
            $plan['security'] = true;
        }
        if ($this->option('docs')) {
            $plan['docs'] = true;
        }

        return $plan;
    }

    private function runActionPlan(): string
    {
        $out = '';
        if ($this->actionPlan['test_back']) {
            $out .= $this->sectionTestBack();
        }
        if ($this->actionPlan['test_front']) {
            $out .= $this->sectionTestFront();
        }

        $qualityAny = $this->actionPlan['phpstan'] || $this->actionPlan['pint'] || $this->actionPlan['eslint'];
        if ($qualityAny) {
            $out .= $this->sectionQualityPartial(
                $this->actionPlan['phpstan'],
                $this->actionPlan['pint'],
                $this->actionPlan['eslint']
            );
        }

        if ($this->actionPlan['pint'] && $this->option('fix-pint')) {
            $out .= $this->sectionPintFix();
        }

        if ($this->actionPlan['security']) {
            $out .= $this->sectionSecurity();
        }
        if ($this->actionPlan['docs']) {
            $out .= $this->sectionDocs();
        }

        return $out;
    }

    /**
     * Enchaîne le script Node `@cursor/sdk` (un Agent.prompt par bloc du rapport).
     */
    private function runCursorSdkAgents(string $reportPath): int
    {
        $script = base_path('scripts/cursor-dev-review-agents.mjs');
        if (! is_file($script)) {
            $this->error('Script SDK absent : '.$script);

            return ArtisanExitCode::FAILURE;
        }

        if (trim((string) (config('services.cursor.api_key') ?? '')) === '') {
            $this->error('CURSOR_API_KEY manquant. Ajoutez-le au `.env` (voir `.env.example`, intégrations Cursor).');

            return ArtisanExitCode::FAILURE;
        }

        $this->info('Lancement des agents Cursor (SDK local, un run par section du rapport)…');

        $result = Process::timeout(7200)
            ->path(base_path())
            ->run([
                'node',
                $script,
                '--report',
                $reportPath,
            ]);

        $out = $result->output();
        $err = $result->errorOutput();
        if ($out !== '') {
            $this->output->write($out);
        }
        if ($err !== '') {
            $this->output->write($err);
        }

        return $result->successful() ? ArtisanExitCode::SUCCESS : ArtisanExitCode::FAILURE;
    }

    private function resolveReportPath(): string
    {
        $custom = $this->option('report-path');
        if (is_string($custom) && $custom !== '') {
            return str_starts_with($custom, DIRECTORY_SEPARATOR)
                ? $custom
                : base_path($custom);
        }

        $name = 'review-'.date('Y-m-d-His').'.md';

        return storage_path('app/dev-reports/'.$name);
    }

    private function headerMarkdown(string $modeLabel): string
    {
        $user = get_current_user();
        $date = now()->toIso8601String();
        $env = (string) config('app.env');
        $actionsList = $this->formatActionPlanForMarkdown();

        return <<<MD
# Rapport dev — Krosmoz-JDR

- **Mode** : {$modeLabel}
- **Actions** : {$actionsList}
- **Commande** : `dev:review` / `project:review`
- **Date** : {$date}
- **Utilisateur OS** : {$user}
- **APP_ENV** : {$env}

MD;
    }

    private function formatActionPlanForMarkdown(): string
    {
        $labels = [];
        if ($this->actionPlan['test_back']) {
            $labels[] = 'tests backend (PHPUnit)';
        }
        if ($this->actionPlan['test_front']) {
            $labels[] = 'tests frontend (Vitest)';
        }
        if ($this->actionPlan['phpstan']) {
            $labels[] = 'PHPStan';
        }
        if ($this->actionPlan['pint']) {
            $labels[] = 'Pint (`--test`)';
        }
        if ($this->actionPlan['eslint']) {
            $labels[] = 'ESLint';
        }
        if ($this->actionPlan['security']) {
            $labels[] = 'audit Composer';
        }
        if ($this->actionPlan['docs']) {
            $labels[] = 'documentation';
        }

        return $labels !== [] ? '`'.implode('`, `', $labels).'`' : '*(aucune)*';
    }

    private function footerMarkdown(): string
    {
        return "\n---\n*Généré par `php artisan dev:review` ou `php artisan project:review`.*\n";
    }

    private function sectionTestBack(): string
    {
        $this->info('Tests backend : php artisan test…');
        $md = "\n## Tests backend (PHPUnit)\n\n";
        $result = $this->runProcess(
            [PHP_BINARY, 'artisan', 'test'],
            7200
        );
        $md .= $this->markdownProcessResult($result);
        if ($result['exit'] !== 0) {
            $this->failures[] = 'test-back';
        }

        return $md;
    }

    private function sectionTestFront(): string
    {
        $this->info('Tests frontend : pnpm run test:run…');
        $md = "\n## Tests frontend (Vitest)\n\n";
        $result = $this->runProcess(['pnpm', 'run', 'test:run'], 3600);
        $md .= $this->markdownProcessResult($result);
        if ($result['exit'] !== 0) {
            $this->failures[] = 'test-front';
        }

        return $md;
    }

    private function sectionQualityPartial(bool $phpstan, bool $pint, bool $eslint): string
    {
        $this->info('Qualité (sous-ensemble) : '.implode(', ', array_filter([
            $phpstan ? 'PHPStan' : null,
            $pint ? 'Pint' : null,
            $eslint ? 'ESLint' : null,
        ])).'…');

        $md = "\n## Qualité (analyse statique & style)\n\n";

        if ($phpstan) {
            $md .= "### PHPStan (Larastan)\n\n";
            $stan = $this->runProcess(
                [PHP_BINARY, base_path('vendor/bin/phpstan'), 'analyse', '--configuration='.base_path('phpstan.neon'), '--no-progress'],
                900
            );
            $md .= $this->markdownProcessResult($stan);
            if ($stan['exit'] !== 0) {
                $this->failures[] = 'phpstan';
            }
        }

        if ($pint) {
            $md .= "\n### Laravel Pint (--test)\n\n";
            $pintResult = $this->runProcess([PHP_BINARY, base_path('vendor/bin/pint'), '--test'], 300);
            $md .= $this->markdownProcessResult($pintResult);
            if ($pintResult['exit'] !== 0) {
                $this->failures[] = 'pint';
            }
        }

        if ($eslint) {
            $md .= "\n### ESLint (pnpm run lint)\n\n";
            $lint = $this->runProcess(['pnpm', 'run', 'lint'], 300);
            $md .= $this->markdownProcessResult($lint);
            if ($lint['exit'] !== 0) {
                $this->failures[] = 'eslint';
            }
        }

        return $md;
    }

    private function sectionSecurity(): string
    {
        $this->info('Sécurité : composer audit…');
        $md = "\n## Sécurité (dépendances)\n\n";
        $md .= "> Revue applicative (validation, XSS, auth, etc.) : voir `docs/10-BestPractices/SECURITY_PRACTICES.md` et le prompt Cursor ci-dessous.\n\n";

        $audit = $this->runProcess(['composer', 'audit', '--no-interaction'], 120);
        $md .= $this->markdownProcessResult($audit);
        if ($audit['exit'] !== 0) {
            $this->failures[] = 'composer-audit';
        }

        return $md;
    }

    private function sectionDocs(): string
    {
        $this->info('Documentation : contrôles légers…');
        $md = "\n## Documentation\n\n";
        $indexPath = base_path('docs/docs.index.json');
        if (! is_file($indexPath)) {
            $md .= "**Erreur** : fichier absent `docs/docs.index.json`.\n\n";
            $this->failures[] = 'docs-index-missing';

            return $md;
        }

        try {
            $raw = File::get($indexPath);
            json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $md .= "- `docs/docs.index.json` : JSON valide.\n";
        } catch (Throwable $e) {
            $md .= '- **JSON invalide** : '.$e->getMessage()."\n\n";
            $this->failures[] = 'docs-index-json';
        }

        $readme = base_path('docs/README.md');
        $md .= is_file($readme)
            ? "- `docs/README.md` : présent.\n"
            : "- `docs/README.md` : absent (optionnel si un autre index couvre l’entrée doc).\n";

        $guide = base_path('docs/DOCUMENTATION_GUIDE.md');
        $md .= is_file($guide)
            ? "- `docs/DOCUMENTATION_GUIDE.md` : présent.\n\n"
            : "- **Manquant** : `docs/DOCUMENTATION_GUIDE.md`.\n\n";
        if (! is_file($guide)) {
            $this->failures[] = 'documentation-guide-missing';
        }

        $md .= "> Pour régénérer l’index : `pnpm run update:docs` (modifie des fichiers).\n\n";

        return $md;
    }

    private function sectionPintFix(): string
    {
        $this->warn('Application de Pint (écriture des fichiers)…');
        $md = "\n## Laravel Pint (apply)\n\n";
        $pint = $this->runProcess([PHP_BINARY, base_path('vendor/bin/pint')], 300);
        $md .= $this->markdownProcessResult($pint);
        if ($pint['exit'] !== 0) {
            $this->failures[] = 'pint-apply';
        }

        return $md;
    }

    /**
     * @param  list<string>  $command
     * @return array{exit: int, output: string, combined: string}
     */
    private function runProcess(array $command, int $timeoutSeconds): array
    {
        try {
            $result = Process::timeout($timeoutSeconds)
                ->path(base_path())
                ->run($command);
            $out = $result->output();
            $err = $result->errorOutput();
            $combined = trim($out."\n".$err);

            return [
                'exit' => $result->exitCode() ?? 1,
                'output' => $out,
                'combined' => $combined !== '' ? $combined : '(sortie vide)',
            ];
        } catch (Throwable $e) {
            return [
                'exit' => 1,
                'output' => '',
                'combined' => '**Exception** : '.$e->getMessage(),
            ];
        }
    }

    /**
     * @param  array{exit: int, output: string, combined: string}  $result
     */
    private function markdownProcessResult(array $result): string
    {
        $status = $result['exit'] === 0 ? 'OK' : 'ÉCHEC (code '.$result['exit'].')';
        $body = $result['combined'];
        $escaped = str_replace('```', '`\`\`', $body);

        return '- **Statut** : '.$status."\n\n```text\n".$escaped."\n```\n\n";
    }

    /**
     * @return list<array{title: string, prompt: string}>
     */
    private function cursorPromptItems(): array
    {
        $items = [];

        if ($this->actionPlan['test_back'] || $this->actionPlan['test_front']) {
            $parts = [];
            if ($this->actionPlan['test_back']) {
                $parts[] = 'PHPUnit (`php artisan test`)';
            }
            if ($this->actionPlan['test_front']) {
                $parts[] = 'Vitest (`pnpm run test:run`)';
            }
            $items[] = [
                'title' => 'Tests + correctifs',
                'prompt' => 'Analyse la sortie des tests du rapport ('.implode(' ; ', $parts).'). Pour chaque échec : cause probable, fichier(s) concernés, patch minimal proposé. Ne modifie que ce qui est nécessaire pour faire passer les tests.',
            ];
        }

        if ($this->actionPlan['phpstan'] || $this->actionPlan['pint'] || $this->actionPlan['eslint']) {
            $items[] = [
                'title' => 'Simplification / optimisation',
                'prompt' => 'À partir du rapport (PHPStan, Pint, ESLint selon les sections présentes), propose des simplifications et micro-optimisations pertinentes (lisibilité, perf, duplication). Indique la priorité et si un correctif peut être appliqué automatiquement (Pint) ou nécessite une revue.',
            ];
        }

        if ($this->actionPlan['security']) {
            $items[] = [
                'title' => 'Sécurité & robustesse',
                'prompt' => 'En t’appuyant sur le rapport et sur docs/10-BestPractices/SECURITY_PRACTICES.md, liste les risques (validation, XSS, auth, fuites de données, requêtes N+1 sensibles). Propose des correctifs concrets par fichier ou par zone.',
            ];
        }

        if ($this->actionPlan['docs']) {
            $items[] = [
                'title' => 'Documentation',
                'prompt' => 'Vérifie la cohérence de la doc avec le code (docs/, docs.index.json, DOCUMENTATION_GUIDE.md). Signale les écarts, sections obsolètes et ce qu’il faudrait ajouter ou retirer.',
            ];
        }

        return $items;
    }

    private function cursorPromptsMarkdownAppendix(): string
    {
        $items = $this->cursorPromptItems();
        if ($items === []) {
            return '';
        }

        $md = "\n## Prompts Cursor (copier-coller dans un agent)\n\n";
        $md .= "> Joindre ce fichier Markdown en contexte dans Cursor pour interpréter les sorties des outils ci-dessus.\n\n";

        foreach ($items as $item) {
            $escaped = str_replace('```', '`\`\`', $item['prompt']);
            $md .= '### '.$item['title']."\n\n```text\n".$escaped."\n```\n\n";
        }

        return $md;
    }

    private function writeCursorPromptsConsoleHint(string $reportPath): void
    {
        $this->comment('Prompts Cursor : copier les blocs « text » en fin de fichier rapport (lisibles sans coupure).');
        $this->line('  '.$reportPath);
        $this->newLine();

        $titles = array_map(static fn (array $i): string => $i['title'], $this->cursorPromptItems());
        if ($titles !== []) {
            $this->line('Sections prompts : '.implode(' · ', $titles));
        }
    }
}
