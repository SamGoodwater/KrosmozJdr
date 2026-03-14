<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite;

/**
 * Base pour les tests unitaires qui n'ont pas besoin de base de données.
 *
 * Contrairement à TestCase, n'utilise pas RefreshDatabase.
 * Utile pour les tests de Mailables, services purs, etc.
 *
 * @see Tests\TestCase (avec RefreshDatabase)
 */
abstract class TestCaseNoDatabase extends BaseTestCase
{
    public function createApplication(): \Illuminate\Foundation\Application
    {
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $hotFile = storage_path('framework/vite.hot');
        if (! is_dir(dirname($hotFile))) {
            @mkdir(dirname($hotFile), 0775, true);
        }
        if (! is_file($hotFile)) {
            @file_put_contents($hotFile, "http://localhost:5173\n");
        }
        Vite::useHotFile($hotFile);

        config(['session.driver' => 'array']);
        app('config')->set('app.debug', true);
    }
}
