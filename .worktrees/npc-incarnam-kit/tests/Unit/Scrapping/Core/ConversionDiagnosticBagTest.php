<?php

declare(strict_types=1);

namespace Tests\Unit\Scrapping\Core;

use App\Services\Scrapping\Core\Conversion\ConversionDiagnosticBag;
use PHPUnit\Framework\TestCase;

class ConversionDiagnosticBagTest extends TestCase
{
    public function test_it_keeps_manual_review_context(): void
    {
        $bag = new ConversionDiagnosticBag;
        $bag->manualReview('unmapped_effect', 'Effet à revoir.', ['effect_id' => 42]);

        $this->assertTrue($bag->hasEntries());
        $this->assertTrue($bag->requiresManualReview());
        $this->assertSame('manual_review', $bag->all()[0]['level']);
        $this->assertSame(42, $bag->all()[0]['context']['effect_id']);
    }
}
