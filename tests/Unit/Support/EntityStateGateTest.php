<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Enums\EntityState;
use App\Http\Middleware\PreventRequestForgery;
use App\Support\Entity\EntityStateGate;
use Illuminate\Auth\Access\AuthorizationException;
use Tests\TestCase;

class EntityStateGateTest extends TestCase
{
    public function test_requires_publish_only_when_entering_playable(): void
    {
        $this->assertTrue(EntityStateGate::requiresPublish('draft', EntityState::Playable->value));
        $this->assertTrue(EntityStateGate::requiresPublish('auto', EntityState::Playable->value));
        $this->assertTrue(EntityStateGate::requiresPublish(null, EntityState::Playable->value));
        $this->assertFalse(EntityStateGate::requiresPublish('playable', EntityState::Playable->value));
        $this->assertFalse(EntityStateGate::requiresPublish('raw', EntityState::Auto->value));
    }

    public function test_automated_writer_may_only_set_auto(): void
    {
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Auto->value);

        $this->expectException(AuthorizationException::class);
        EntityStateGate::assertAutomatedWriterMaySet(EntityState::Playable->value);
    }

    public function test_csrf_middleware_has_no_scrapping_exception(): void
    {
        $ref = new \ReflectionClass(PreventRequestForgery::class);
        $except = $ref->getProperty('except');
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame([], $except->getValue($instance));
        $this->assertStringNotContainsString(
            "'api/scrapping'",
            (string) file_get_contents(base_path('bootstrap/app.php'))
        );
    }
}
