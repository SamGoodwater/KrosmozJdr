<?php

declare(strict_types=1);

namespace Tests\Unit\Console;

use App\Console\CommandGuide;
use Tests\TestCase;

class CommandGuideTest extends TestCase
{
    public function test_parses_yaml_blocks_and_filters_ui(): void
    {
        $markdown = <<<'MD'
# Titre

## `project:dev`

```yaml
signature: project:dev
domain: development
ui: false
cron: false
```

Lance les serveurs.

## `project:clear`

```yaml
signature: project:clear
domain: cleanup
ui: true
cron: true
```

Nettoie les caches.
MD;

        $all = CommandGuide::parse($markdown);
        self::assertCount(2, $all);
        self::assertSame('project:dev', $all[0]['signature']);
        self::assertFalse($all[0]['ui']);
        self::assertSame('project:clear', $all[1]['signature']);
        self::assertTrue($all[1]['ui']);
        self::assertTrue($all[1]['cron']);

        $ui = array_values(array_filter($all, static fn (array $e): bool => $e['ui']));
        self::assertCount(1, $ui);
        self::assertSame('project:clear', $ui[0]['signature']);
    }

    public function test_live_guide_has_required_commands_and_hides_dev_from_ui(): void
    {
        $all = CommandGuide::all();
        $signatures = array_column($all, 'signature');

        foreach ([
            'project:prepare',
            'project:dev',
            'project:deps',
            'project:review',
            'project:data',
            'project:clear',
            'project:backup',
            'project:init',
        ] as $signature) {
            self::assertContains($signature, $signatures);
        }

        $uiSignatures = array_column(CommandGuide::forUi(), 'signature');
        self::assertNotContains('project:dev', $uiSignatures);
        self::assertNotContains('project:prepare', $uiSignatures);
        self::assertNotContains('project:init', $uiSignatures);
        self::assertContains('project:deps', $uiSignatures);
        self::assertContains('project:data', $uiSignatures);
        self::assertContains('project:clear', $uiSignatures);
        self::assertContains('project:backup', $uiSignatures);
        self::assertContains('project:review', $uiSignatures);
    }
}
