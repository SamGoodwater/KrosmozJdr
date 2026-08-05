<?php

declare(strict_types=1);

namespace Tests\Feature\Scrapping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeedsScrappingPipeline;
use Tests\TestCase;

class ScrappingAuditCommandTest extends TestCase
{
    use RefreshDatabase, SeedsScrappingPipeline;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedScrappingPipeline();
    }

    public function test_audit_returns_machine_readable_report(): void
    {
        $this->artisan('scrapping:audit', ['--entity' => 'monster', '--json' => true])
            ->expectsOutputToContain('"structural_errors"')
            ->assertSuccessful();
    }
}
