<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\KrosmozGameTerms;
use PHPUnit\Framework\TestCase;

final class KrosmozGameTermsTest extends TestCase
{
    public function test_replaces_adjective_preserving_case(): void
    {
        $this->assertSame(
            'PAS DISSIPABLE.',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('PAS DÉSENVOÛTABLE.')
        );
        $this->assertSame(
            'Non dissipable',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('Non désenvoutable')
        );
        $this->assertSame(
            'Dissipable',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('Désenvoûtable')
        );
        $this->assertSame(
            'effet dissipable',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('effet désenvoûtable')
        );
        $this->assertSame(
            'boucliers dissipables',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('boucliers désenvoutables')
        );
    }

    public function test_leaves_desenvoutement_and_verbs_unchanged(): void
    {
        $this->assertSame(
            'Désenvoûtement Handicapant',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('Désenvoûtement Handicapant')
        );
        $this->assertSame(
            'Désenvoûte intégralement la cible.',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('Désenvoûte intégralement la cible.')
        );
        $this->assertSame(
            'pour se désenvoûter',
            KrosmozGameTerms::replaceDesenvoutableWithDissipable('pour se désenvoûter')
        );
    }

    public function test_replace_in_mixed_walks_arrays(): void
    {
        $this->assertSame(
            ['value' => 'Pas dissipable'],
            KrosmozGameTerms::replaceInMixed(['value' => 'Pas désenvoutable'])
        );
    }
}
