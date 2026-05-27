<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Cms;

use App\Support\Cms\RulesMarkdownInclusiveAlly;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RulesMarkdownInclusiveAllyTest extends TestCase
{
    #[Test]
    public function it_converts_plural_and_singular_ally_forms(): void
    {
        $input = 'Un allié aide tes alliés. Une alliée parle aux alliées.';

        $output = RulesMarkdownInclusiveAlly::apply($input);

        $this->assertSame(
            'Un·e allié·e aide tes allié·e·s. Un·e allié·e parle aux allié·e·s.',
            $output
        );
    }

    #[Test]
    public function it_does_not_double_apply_inclusive_markers(): void
    {
        $input = 'Tu aides un·e allié·e.';

        $this->assertSame($input, RulesMarkdownInclusiveAlly::apply($input));
    }
}
