<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\KrefShortcodeReplacer;
use FilesystemIterator;
use PHPUnit\Framework\Attributes\Test;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class KrefShortcodeReplacerTest extends TestCase
{
    #[Test]
    public function it_converts_characteristic_shortcode_to_kref_span(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Coût en [[kref:characteristic:action_points_creature|PA]].'
        );

        $this->assertStringContainsString('<span class="kref"', $html);
        $this->assertStringContainsString('>PA</span>', $html);
        $this->assertStringNotContainsString('[[kref:', $html);
    }

    #[Test]
    public function it_converts_page_section_shortcode_with_at_separator(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            'Voir [[kref:pageSection:regles-3-2-combat@regle-3-2-2-tour-de-jeu-et-actions|Tour de jeu]].'
        );

        $this->assertStringContainsString('class="kref kref--nav"', $html);
        $this->assertStringContainsString('>Tour de jeu</span>', $html);
    }

    #[Test]
    public function it_flattens_a_shortcode_nested_in_a_label_before_converting(): void
    {
        $html = (new KrefShortcodeReplacer)->replace(
            '[[kref:characteristic:dodge_action_points_creature|Esquive [[kref:characteristic:action_points_creature|PA]]]]'
        );

        $this->assertStringNotContainsString('[[kref:', $html);
        $this->assertStringContainsString('>Esquive PA</span>', $html);
        $this->assertSame(1, substr_count($html, '<span class="kref"'));
    }

    #[Test]
    public function rules_markdown_does_not_nest_a_kref_inside_a_label(): void
    {
        $root = base_path('private/game/rules');
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'md') {
                continue;
            }
            $raw = (string) file_get_contents($file->getPathname());
            $this->assertSame(
                $raw,
                KrefShortcodeReplacer::flattenNestedShortcodes($raw),
                $file->getPathname()
            );
        }
    }

    #[Test]
    public function it_leaves_unknown_shortcodes_unchanged(): void
    {
        $input = '[[kref:unknown:foo|Bar]]';

        $this->assertSame($input, (new KrefShortcodeReplacer)->replace($input));
    }

    #[Test]
    public function it_stores_entity_view_minimal_in_payload(): void
    {
        $html = (new KrefShortcodeReplacer(static fn (): int => 42))->replace(
            '[[kref:entity:spells:Boule de feu|Boule de feu|view=minimal]]'
        );

        $this->assertStringContainsString('kref--view-minimal', $html);
        $this->assertSame(1, preg_match('/title="([^"]+)"/', $html, $m));
        $b64 = strtr($m[1], '-_', '+/');
        $pad = strlen($b64) % 4 === 0 ? '' : str_repeat('=', 4 - (strlen($b64) % 4));
        $json = base64_decode($b64.$pad, true);
        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('entity', $decoded['t'] ?? null);
        $this->assertSame('spells', $decoded['p']['entityType'] ?? null);
        $this->assertSame(42, $decoded['p']['id'] ?? null);
        $this->assertSame('minimal', $decoded['p']['view'] ?? null);
    }
}
