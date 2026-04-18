<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Project;

use App\Services\Project\ProjectRunService;
use PHPUnit\Framework\TestCase;

class ProjectRunServiceTest extends TestCase
{
    public function test_collect_actions_update_all_inclut_composer_et_pnpm(): void
    {
        $service = new ProjectRunService;
        $actions = $service->collectActionsFromOptionMap(['update:all' => true]);

        $expected = [
            'runSetupInstall',
            'runComposerProjectUpdate',
            'runPnpmProjectUpdate',
        ];

        self::assertSame($expected, $actions);
    }

    public function test_collect_actions_clear_cache_seul(): void
    {
        $service = new ProjectRunService;
        $actions = $service->collectActionsFromOptionMap(['clear:cache' => true]);

        self::assertSame(['clearCache'], $actions);
    }

    public function test_collect_actions_clear_test_inclut_clear_test_artifacts(): void
    {
        $service = new ProjectRunService;
        $actions = $service->collectActionsFromOptionMap(['clear:test' => true]);

        self::assertSame(['clearTestArtifacts'], $actions);
    }
}
