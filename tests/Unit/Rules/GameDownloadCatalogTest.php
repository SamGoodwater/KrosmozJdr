<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Models\User;
use App\Services\Rules\GameDownloadCatalog;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GameDownloadCatalogTest extends TestCase
{
    public function test_restricted_items_use_private_disk(): void
    {
        $catalog = app(GameDownloadCatalog::class);
        $mj = $catalog->configItem('mj-pdf');
        $player = $catalog->configItem('rules-pdf');
        $this->assertNotNull($mj);
        $this->assertNotNull($player);

        $this->assertTrue($catalog->isRestricted($mj));
        $this->assertFalse($catalog->isRestricted($player));
        $this->assertSame('local', $catalog->diskName($mj));
        $this->assertSame('public', $catalog->diskName($player));
    }

    public function test_purge_moves_public_leftover_to_private_disk(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $catalog = app(GameDownloadCatalog::class);
        $mj = $catalog->configItem('mj-pdf');
        $this->assertNotNull($mj);
        Storage::disk('public')->put('downloads/generated/krosmoz-jdr-atelier-mj.pdf', '%PDF-leftover');

        $catalog->purgePublicCopyIfRestricted($mj);

        Storage::disk('public')->assertMissing('downloads/generated/krosmoz-jdr-atelier-mj.pdf');
        Storage::disk('local')->assertExists('downloads/generated/krosmoz-jdr-atelier-mj.pdf');
        $this->assertSame(
            '%PDF-leftover',
            Storage::disk('local')->get('downloads/generated/krosmoz-jdr-atelier-mj.pdf')
        );
    }

    public function test_guest_cannot_access_mj_item(): void
    {
        $catalog = app(GameDownloadCatalog::class);
        $mj = $catalog->configItem('mj-pdf');
        $this->assertNotNull($mj);
        $this->assertFalse($catalog->userCanAccess($mj, null));

        $gm = User::factory()->make(['role' => User::ROLE_GAME_MASTER]);
        $this->assertTrue($catalog->userCanAccess($mj, $gm));
    }
}
