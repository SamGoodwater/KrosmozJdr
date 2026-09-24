<?php

declare(strict_types=1);

namespace Tests\Unit\GenerativeAi;

use App\Services\GenerativeAi\ManualJsonPayloadSanitizer;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ManualJsonPayloadSanitizerTest extends TestCase
{
    #[Test]
    public function it_parses_object_and_strips_tags(): void
    {
        $sanitizer = new ManualJsonPayloadSanitizer;
        $out = $sanitizer->parseAndSanitize([
            'effect' => '<b>1d6</b> Terre',
            'nested' => ['note' => '<img src=x onerror=1>ok'],
        ]);

        $this->assertSame('1d6 Terre', $out['effect']);
        $this->assertSame('ok', $out['nested']['note']);
    }

    #[Test]
    public function it_rejects_root_array(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ManualJsonPayloadSanitizer)->parseAndSanitize('[1,2]');
    }

    #[Test]
    public function it_rejects_invalid_json_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ManualJsonPayloadSanitizer)->parseAndSanitize('{bad');
    }
}
