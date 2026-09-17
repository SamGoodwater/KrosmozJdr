<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Services\Rules\RulesBookAssembler;
use App\Services\Rules\RulesDownloadCompiler;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class RulesDownloadCompilerTest extends TestCase
{
    public function test_writes_pdf_and_odt_on_public_disk(): void
    {
        Storage::fake('public');
        $root = sys_get_temp_dir().'/krosmoz-rules-'.uniqid('', true);
        mkdir($root, 0775, true);
        file_put_contents($root.'/1.1.1-intro.md', "# 1.1.1 Intro\n\nUn **paragraphe** de test.\n");

        try {
            $this->app->instance(RulesBookAssembler::class, new RulesBookAssembler($root));
            $written = app(RulesDownloadCompiler::class)->compile();

            $this->assertCount(2, $written);
            Storage::disk('public')->assertExists('downloads/generated/krosmoz-jdr-regles.pdf');
            Storage::disk('public')->assertExists('downloads/generated/krosmoz-jdr-regles.odt');

            $pdf = Storage::disk('public')->get('downloads/generated/krosmoz-jdr-regles.pdf');
            $this->assertStringStartsWith('%PDF', $pdf);

            $odtAbsolute = Storage::disk('public')->path('downloads/generated/krosmoz-jdr-regles.odt');
            $zip = new ZipArchive;
            $this->assertTrue($zip->open($odtAbsolute) === true);
            $this->assertNotFalse($zip->locateName('content.xml'));
            $content = (string) $zip->getFromName('content.xml');
            $zip->close();
            $this->assertStringContainsString('Intro', $content);
            $this->assertStringContainsString('paragraphe', $content);
        } finally {
            @unlink($root.'/1.1.1-intro.md');
            @rmdir($root);
        }
    }
}
