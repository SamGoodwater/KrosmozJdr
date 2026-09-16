<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\BreedImagePaths;
use Tests\TestCase;

final class BreedImagePathsTest extends TestCase
{
    public function test_ascii_slug_strips_accents(): void
    {
        $this->assertSame('cra', BreedImagePaths::asciiSlug('Crâ'));
        $this->assertSame('feca', BreedImagePaths::asciiSlug('Féca'));
        $this->assertSame('xelor', BreedImagePaths::asciiSlug('Xélor'));
        $this->assertSame('steamer', BreedImagePaths::slugFromName('Foggernaut'));
    }

    public function test_iop_local_files_are_complete(): void
    {
        $values = BreedImagePaths::columnValuesForSlug('iop');

        $this->assertSame('/storage/images/breeds/iop/symbol-full.png', $values['symbol_full']);
        $this->assertSame('/storage/images/breeds/iop/symbol-bw.png', $values['symbol_bw']);
        $this->assertSame('/storage/images/breeds/iop/logo_m.png', $values['logo_male']);
        $this->assertSame('/storage/images/breeds/iop/logo_f.png', $values['logo_female']);
        $this->assertSame('/storage/images/breeds/iop/full_m.png', $values['image_full_male']);
        $this->assertSame('/storage/images/breeds/iop/full_f.png', $values['image_full_female']);
        $this->assertSame($values['image_full_male'], $values['image']);
        $this->assertSame($values['symbol_bw'], $values['icon']);
    }

    public function test_osamodas_falls_back_to_color_symbol_without_bw(): void
    {
        $values = BreedImagePaths::columnValuesForSlug('osamodas');

        $this->assertNull($values['symbol_bw']);
        $this->assertNotNull($values['symbol_full']);
        $this->assertSame($values['symbol_full'], $values['icon']);
    }
}
