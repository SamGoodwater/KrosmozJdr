<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // La route `home` renvoie 302 vers la page CMS « accueil » si elle existe et est visible, sinon 200 (Inertia).
        $this->assertContains(
            $response->getStatusCode(),
            [200, 302],
            'La page d\'accueil doit répondre par une page (200) ou une redirection vers le CMS (302).'
        );
    }
}
