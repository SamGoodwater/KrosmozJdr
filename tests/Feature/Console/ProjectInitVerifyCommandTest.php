<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\CharacteristicSeeder;
use Database\Seeders\CreatureCharacteristicSeeder;
use Database\Seeders\CriticalPagesSeeder;
use Database\Seeders\DofusdbEffectMappingSeeder;
use Database\Seeders\NavMenuSeeder;
use Database\Seeders\ObjectCharacteristicSeeder;
use Database\Seeders\SpellCharacteristicSeeder;
use Database\Seeders\Type\TypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Tests de la commande project:init:verify.
 */
class ProjectInitVerifyCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_is_registered(): void
    {
        $this->assertArrayHasKey('project:init:verify', Artisan::all());
    }

    public function test_verify_fails_on_empty_database(): void
    {
        $code = Artisan::call('project:init:verify');

        $this->assertSame(1, $code);
        $this->assertStringContainsString('échec', Artisan::output());
    }

    public function test_verify_ok_after_minimal_socle_seed(): void
    {
        $this->seedMinimalSocle();

        $code = Artisan::call('project:init:verify', ['--json' => true]);
        $payload = json_decode(Artisan::output(), true);

        $this->assertSame(0, $code);
        $this->assertIsArray($payload);
        $this->assertTrue($payload['ok']);
        $this->assertSame([], $payload['failures']);
        $this->assertTrue(Page::query()->where('slug', 'accueil')->where('in_menu', true)->exists());
    }

    public function test_verify_with_rules_fails_without_toc_import(): void
    {
        $this->seedMinimalSocle();

        $code = Artisan::call('project:init:verify', ['--with-rules' => true]);

        $this->assertSame(1, $code);
        $this->assertStringContainsString('règles', Artisan::output());
    }

    public function test_verify_warns_without_super_admin(): void
    {
        $this->seedMinimalSocle();

        User::factory()->create(['role' => User::ROLE_ADMIN]);

        $code = Artisan::call('project:init:verify');

        $this->assertSame(0, $code);
        $this->assertStringContainsString('super_admin', Artisan::output());
    }

    private function seedMinimalSocle(): void
    {
        $this->seed([
            TypeSeeder::class,
            CharacteristicSeeder::class,
            CreatureCharacteristicSeeder::class,
            ObjectCharacteristicSeeder::class,
            SpellCharacteristicSeeder::class,
            DofusdbEffectMappingSeeder::class,
            CriticalPagesSeeder::class,
            NavMenuSeeder::class,
        ]);
    }
}
