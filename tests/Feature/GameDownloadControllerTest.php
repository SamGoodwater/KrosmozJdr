<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GameDownloadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_downloads_available_file(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('downloads/generated/krosmoz-jdr-regles.pdf', '%PDF-fake');

        $this->get(route('game-downloads.show', 'rules-pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_missing_generated_file_returns_404(): void
    {
        Storage::fake('public');

        $this->get(route('game-downloads.show', 'rules-odt'))
            ->assertNotFound();
    }

    public function test_unknown_key_returns_404(): void
    {
        $this->get(route('game-downloads.show', 'fichier-inconnu'))
            ->assertNotFound();
    }

    public function test_guest_cannot_download_mj_book(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('downloads/generated/krosmoz-jdr-atelier-mj.pdf', '%PDF-fake');

        $this->get(route('game-downloads.show', 'mj-pdf'))
            ->assertForbidden();
    }

    public function test_game_master_downloads_mj_book(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('local')->put('downloads/generated/krosmoz-jdr-atelier-mj.pdf', '%PDF-fake');
        $gm = User::factory()->create(['role' => User::ROLE_GAME_MASTER]);

        $this->actingAs($gm)
            ->get(route('game-downloads.show', 'mj-pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_guest_request_moves_public_mj_leftover_off_public_disk(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::disk('public')->put('downloads/generated/krosmoz-jdr-atelier-mj.pdf', '%PDF-secret-mj');

        $this->get(route('game-downloads.show', 'mj-pdf'))
            ->assertForbidden();

        Storage::disk('public')->assertMissing('downloads/generated/krosmoz-jdr-atelier-mj.pdf');
        Storage::disk('local')->assertExists('downloads/generated/krosmoz-jdr-atelier-mj.pdf');
        $this->assertSame(
            '%PDF-secret-mj',
            Storage::disk('local')->get('downloads/generated/krosmoz-jdr-atelier-mj.pdf')
        );
    }
}
