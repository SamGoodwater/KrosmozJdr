<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GameDownloadControllerTest extends TestCase
{
    public function test_guest_downloads_available_file(): void
    {
        Storage::fake('public');
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
}
