<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\SpecializationImagePaths;
use Tests\TestCase;

final class SpecializationImagePathsTest extends TestCase
{
    public function test_gendered_names_map_to_the_same_file(): void
    {
        $this->assertSame('milicien', SpecializationImagePaths::slugFromName('Milicien·ne'));
        $this->assertSame('negociant', SpecializationImagePaths::slugFromName('Négociant·e'));
        $this->assertSame('explorateur', SpecializationImagePaths::slugFromName('Explorateur·rice'));
        $this->assertSame('explorateurrice', SpecializationImagePaths::asciiKey('Explorateur·rice'));
        $this->assertSame('voleur', SpecializationImagePaths::slugFromName('Voleur·euse'));
        $this->assertSame('artiste', SpecializationImagePaths::slugFromName('Artiste'));
    }

    public function test_playable_icons_exist(): void
    {
        foreach (['erudit', 'milicien', 'voleur', 'devot', 'artiste', 'explorateur', 'artisan', 'negociant', 'sylvain', 'marin', 'courtisan'] as $slug) {
            $this->assertFileExists(SpecializationImagePaths::absolutePath($slug), $slug);
        }

        $this->assertSame(
            '/storage/images/specializations/artiste.png',
            SpecializationImagePaths::urlForName('Artiste')
        );
    }
}
