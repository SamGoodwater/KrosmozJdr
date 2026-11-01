<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Services\Rules\GuideBookAssembler;
use Tests\TestCase;

class GuideBookAssemblerTest extends TestCase
{
    public function test_assembles_guide_from_universe_chapters(): void
    {
        $root = sys_get_temp_dir().'/krosmoz-guide-'.uniqid('', true);
        $dir = $root.'/4-Le-monde-des-douze/4.1-l-univers';
        mkdir($dir, 0775, true);
        file_put_contents(
            $dir.'/4.1.1-presentation-du-krosmoz.md',
            "# Le Monde des Douze\n\n**Description** : Accroche.\n\nEn **an 646**, Sufokia refait surface.\n"
        );
        file_put_contents(
            $dir.'/4.1.2-temporalite.md',
            "# L'année\n\nJavian est le premier mois.\n"
        );

        try {
            $html = (new GuideBookAssembler($root))->toHtml();
            $this->assertStringContainsString('guide des aventurier', $html);
            $this->assertStringContainsString('646', $html);
            $this->assertStringContainsString('Javian', $html);
            $this->assertStringNotContainsString('Description', $html);
        } finally {
            @unlink($dir.'/4.1.1-presentation-du-krosmoz.md');
            @unlink($dir.'/4.1.2-temporalite.md');
            @rmdir($dir);
            @rmdir($root.'/4-Le-monde-des-douze/4.1-l-univers');
            @rmdir($root.'/4-Le-monde-des-douze');
            @rmdir($root);
        }
    }

    public function test_returns_empty_when_chapters_missing(): void
    {
        $root = sys_get_temp_dir().'/krosmoz-guide-empty-'.uniqid('', true);
        mkdir($root, 0775, true);

        try {
            $this->assertSame('', (new GuideBookAssembler($root))->toHtml());
        } finally {
            @rmdir($root);
        }
    }
}
