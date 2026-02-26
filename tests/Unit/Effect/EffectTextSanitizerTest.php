<?php

declare(strict_types=1);

namespace Tests\Unit\Effect;

use App\Services\Effect\EffectTextSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour EffectTextSanitizer (sans bootstrap Laravel/DB).
 *
 * @see App\Services\Effect\EffectTextSanitizer
 */
class EffectTextSanitizerTest extends TestCase
{
    private EffectTextSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new EffectTextSanitizer();
    }

    public function test_sanitize_plain_text_unchanged(): void
    {
        $input = 'Inflige des dégâts Terre.';
        $this->assertSame($input, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_preserves_variable_placeholder(): void
    {
        $input = 'Bonus : [agi] ou [value] selon le niveau [level].';
        $this->assertSame($input, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_preserves_dice_notation(): void
    {
        $input = 'Jet 2d6 + 1d20 dégâts';
        $this->assertSame($input, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_strips_html_tags(): void
    {
        $input = 'Texte <b>gras</b> et <script>alert(1)</script> fin';
        $expected = 'Texte gras et alert(1) fin';
        $this->assertSame($expected, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_strips_img_and_onerror(): void
    {
        $input = 'OK <img src="x" onerror="alert(2)" /> suite';
        $result = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringNotContainsString('onerror', $result);
        $this->assertStringContainsString('OK', $result);
        $this->assertStringContainsString('suite', $result);
    }

    public function test_sanitize_removes_angle_brackets_content(): void
    {
        $input = 'A < B et C > D';
        $result = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
        $this->assertSame('A B et C D', $result);
    }

    public function test_sanitize_removes_javascript_protocol(): void
    {
        $input = 'Lien javascript:alert(1) ici';
        $result = $this->sanitizer->sanitize($input);
        $this->assertStringNotContainsString('javascript:', $result);
    }

    public function test_sanitize_normalizes_whitespace(): void
    {
        $input = "Un   texte\tavec\nespaces";
        $expected = 'Un texte avec espaces';
        $this->assertSame($expected, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_trims(): void
    {
        $input = '  [value] dégâts  ';
        $expected = '[value] dégâts';
        $this->assertSame($expected, $this->sanitizer->sanitize($input));
    }

    public function test_sanitize_empty_string_returns_empty(): void
    {
        $this->assertSame('', $this->sanitizer->sanitize(''));
    }

    public function test_sanitize_formula_like_text_preserved(): void
    {
        $input = '[level] * 2 + floor([agi] / 2)';
        $this->assertSame($input, $this->sanitizer->sanitize($input));
    }
}
