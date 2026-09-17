<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Console\Concerns\GuardsProductionEnvironment;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Les commandes destructrices ou réservées au bootstrap ne doivent pas s’exécuter avec APP_ENV=production.
 *
 * @see GuardsProductionEnvironment
 */
class ArtisanProductionGuardsTest extends TestCase
{
    /**
     * @param  array<string, bool|string>  $parameters
     */
    private function assertCommandBlockedWithMessage(string $command, array $parameters, string $needle): void
    {
        $app = $this->app;
        $originalEnv = $app->environment();
        $app->instance('env', 'production');
        try {
            $code = Artisan::call($command, $parameters);
            $this->assertSame(1, $code);
            $this->assertStringContainsStringIgnoringCase($needle, Artisan::output());
        } finally {
            $app->instance('env', $originalEnv);
        }
    }

    public function test_setup_clean_is_blocked_in_production(): void
    {
        $this->assertCommandBlockedWithMessage('setup', ['--clean' => true], 'interdit');
    }

    public function test_project_init_is_blocked_in_production(): void
    {
        $this->assertCommandBlockedWithMessage('project:init', [], 'interdit');
    }
}
