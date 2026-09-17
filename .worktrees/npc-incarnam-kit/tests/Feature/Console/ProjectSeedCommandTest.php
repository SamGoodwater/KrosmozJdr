<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProjectSeedCommandTest extends TestCase
{
    public function test_project_seed_is_registered(): void
    {
        $this->assertArrayHasKey('project:seed', Artisan::all());
    }

    public function test_project_seed_rejects_orphan_flags_in_production_guard(): void
    {
        $this->app['env'] = 'production';

        $code = Artisan::call('project:seed', []);

        $this->assertNotSame(0, $code);
    }
}
