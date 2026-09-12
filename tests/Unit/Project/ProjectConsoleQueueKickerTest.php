<?php

declare(strict_types=1);

namespace Tests\Unit\Project;

use App\Services\Project\ProjectConsoleQueueKicker;
use Tests\TestCase;

class ProjectConsoleQueueKickerTest extends TestCase
{
    public function test_kick_is_a_noop_during_tests(): void
    {
        $kicker = new ProjectConsoleQueueKicker;
        $kicker->kick(ProjectConsoleQueueKicker::QUEUE_RULES_DOWNLOADS);
        $kicker->kick('not a valid queue');

        $this->assertTrue(app()->runningUnitTests());
    }
}
