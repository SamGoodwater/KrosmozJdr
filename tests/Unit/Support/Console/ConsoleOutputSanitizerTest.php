<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Console;

use App\Support\Console\ConsoleOutputSanitizer;
use PHPUnit\Framework\TestCase;

/**
 * @see ConsoleOutputSanitizer
 */
class ConsoleOutputSanitizerTest extends TestCase
{
    public function test_strips_ansi_and_redacts_secrets(): void
    {
        $sanitizer = new ConsoleOutputSanitizer;
        $raw = "\e[32mOK\e[0m DB_PASSWORD=hunter2 Bearer abcdefghijklmnop url=mysql://user:secret@localhost/db";

        $clean = $sanitizer->sanitize($raw);

        $this->assertStringNotContainsString('hunter2', $clean);
        $this->assertStringNotContainsString('abcdefghijklmnop', $clean);
        $this->assertStringNotContainsString('user:secret@', $clean);
        $this->assertStringContainsString('OK', $clean);
        $this->assertStringContainsString('DB_PASSWORD=***', $clean);
        $this->assertStringContainsString('Bearer ***', $clean);
    }

    public function test_caps_oversized_output(): void
    {
        $sanitizer = new ConsoleOutputSanitizer;
        $huge = str_repeat('a', ConsoleOutputSanitizer::MAX_OUTPUT_CHARS + 50);
        $capped = $sanitizer->cap($huge);

        $this->assertStringContainsString('sortie tronquée', $capped);
        $this->assertLessThanOrEqual(
            ConsoleOutputSanitizer::MAX_OUTPUT_CHARS + 40,
            strlen($capped)
        );
    }
}
