<?php

declare(strict_types=1);

namespace App\Console\Commands\Project;

use App\Console\ArtisanExitCode;
use App\Console\Concerns\GuardsProductionEnvironment;
use App\Services\Project\ProjectRunService;
use Illuminate\Console\Command;

/**
 * Quality gate et pipeline effets de sorts (via {@see ProjectRunService}).
 */
class ProjectEffectsCommand extends Command
{
    use GuardsProductionEnvironment;

    public function __construct(
        private readonly ProjectRunService $projectRunService
    ) {
        parent::__construct();
    }

    protected $signature = 'project:effects
        {--quality : Quality gate effets (strict)}
        {--quality-dev : Quality gate effets (dev, allow-empty)}
        {--pipeline : Import sorts + pipeline (strict)}
        {--pipeline-dev : Import sorts + pipeline (dev, allow-empty)}
        {--simulate : (pipeline) Sans écriture BDD}
        {--skip-cache : (pipeline) Ignorer le cache HTTP}
        {--ids= : (pipeline) IDs DofusDB des sorts (virgules)}
        {--levelMin= : (pipeline)}
        {--levelMax= : (pipeline)}
        {--limit=100 : (pipeline)}
        {--max-pages=0 : (pipeline)}
        {--max-items=300 : (pipeline)}
        {--include-relations=1 : (pipeline)}';

    protected $description = 'Qualité / pipeline effets de sorts (entrée dédiée pour scrapping effets).';

    public function handle(): int
    {
        if (! $this->guardNotProduction('Interdit en production.')) {
            return ArtisanExitCode::FAILURE;
        }

        $map = [];
        $pairs = [
            'quality' => 'check:effects-quality',
            'quality-dev' => 'check:effects-quality:dev',
            'pipeline' => 'pipeline:effects-quality',
            'pipeline-dev' => 'pipeline:effects-quality:dev',
        ];

        foreach ($pairs as $cliFlag => $runKey) {
            if ($this->option($cliFlag)) {
                if ($map !== []) {
                    $this->error('Une seule option parmi --quality, --quality-dev, --pipeline, --pipeline-dev.');

                    return ArtisanExitCode::FAILURE;
                }
                $map[$runKey] = true;
            }
        }

        if ($map === []) {
            $this->warn('Choisissez un mode : --quality, --quality-dev, --pipeline ou --pipeline-dev.');

            return ArtisanExitCode::FAILURE;
        }

        return $this->projectRunService->runOptionMap($map, $this);
    }
}
