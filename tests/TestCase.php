<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Configuration des tests d'authentification.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Désactiver complètement le CSRF pour tous les tests
        // En Laravel 11, le middleware CSRF est automatiquement inclus dans le groupe 'web'
        // On doit le désactiver explicitement pour les tests
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
        ]);
        
        // Désactiver d'autres middlewares non essentiels pour l'auth
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Désactiver complètement le CSRF au niveau de l'application
        config(['session.driver' => 'array']);
        config(['session.verify_csrf_token' => false]);
        
        // Désactiver le CSRF dans la configuration de l'application
        app('config')->set('session.verify_csrf_token', false);
        app('config')->set('app.debug', true);
        
        // Forcer l'environnement de test
        app()->detectEnvironment(function () {
            return 'testing';
        });
    }
}
