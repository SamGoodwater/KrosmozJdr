<?php

declare(strict_types=1);

namespace Tests\Unit\Project;

use App\Jobs\ProcessScrappingJob;
use App\Jobs\RunProjectBackupJob;
use App\Notifications\LastConnectionNotification;
use App\Support\Queue\ProjectQueues;
use Tests\TestCase;

class ProjectQueuesTest extends TestCase
{
    public function test_backup_and_scrapping_jobs_use_dedicated_queues(): void
    {
        $backup = new RunProjectBackupJob(1, [], null);
        $scrapping = new ProcessScrappingJob('00000000-0000-0000-0000-000000000000');

        $this->assertSame(ProjectQueues::BACKUP, $backup->queue);
        $this->assertSame(ProjectQueues::SCRAPPING, $scrapping->queue);
        $this->assertStringContainsString(ProjectQueues::IA, ProjectQueues::listenList());
        $this->assertStringContainsString('default', ProjectQueues::listenList());
    }

    public function test_instant_notifications_target_the_notifications_queue(): void
    {
        $notification = new LastConnectionNotification('03/10/2026 à 08:00');

        $this->assertSame(ProjectQueues::NOTIFICATIONS, $notification->viaQueues()['database']);
        $this->assertSame(ProjectQueues::NOTIFICATIONS, $notification->viaQueues()['mail']);
    }
}
