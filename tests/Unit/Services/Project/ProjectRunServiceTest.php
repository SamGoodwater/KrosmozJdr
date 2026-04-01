<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Project;

use App\Services\Project\ProjectRunService;
use PHPUnit\Framework\TestCase;

class ProjectRunServiceTest extends TestCase
{
    public function test_collect_actions_prepare_chaîne_attendue(): void
    {
        $service = new ProjectRunService;
        $actions = $service->collectActionsFromOptionMap(['prepare' => true]);

        $expected = [
            'killServers',
            'clearCss',
            'clearCache',
            'clearConfig',
            'clearRoute',
            'clearView',
            'clearDebugbar',
            'clearQueue',
            'clearSchedule',
            'clearEvent',
            'clearOptimize',
            'runSetupInstall',
            'updateCss',
            'updateDocs',
            'dumpAutoload',
            'optimiseIde',
            'optimiseLaravel',
            'runSetupDb',
        ];

        self::assertSame($expected, $actions);
    }

    public function test_collect_actions_clear_cache_seul(): void
    {
        $service = new ProjectRunService;
        $actions = $service->collectActionsFromOptionMap(['clear:cache' => true]);

        self::assertSame(['clearCache'], $actions);
    }
}
