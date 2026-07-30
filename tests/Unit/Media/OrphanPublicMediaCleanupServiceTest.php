<?php

declare(strict_types=1);

namespace Tests\Unit\Media;

use App\Services\Media\OrphanPublicMediaCleanupService;
use PHPUnit\Framework\TestCase;

/**
 * Filtrage dry-run des fichiers publics MediaLibrary sans référence.
 *
 * @example php artisan test --filter=OrphanPublicMediaCleanupServiceTest
 */
class OrphanPublicMediaCleanupServiceTest extends TestCase
{
    public function test_filter_orphan_files_keeps_referenced_media_and_protected_paths(): void
    {
        $service = new OrphanPublicMediaCleanupService;

        $orphans = $service->filterOrphanFiles(
            [
                'images/entity/spells/10/fire.png',
                'images/entity/spells/11/old.png',
                'sections/4/44/document.pdf',
                'legal/cgu.md',
                'changelog/1.3.4.md',
                'images/calendar/javian.png',
            ],
            [
                'images/entity/spells/10',
                'sections/4/44',
            ],
        );

        $this->assertSame(['images/entity/spells/11/old.png'], $orphans);
    }
}
