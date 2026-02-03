<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Formule de conversion DofusDB → KrosmozJDR pour une (entity, characteristic_key).
 *
 * @property int $id
 * @property string $characteristic_key
 * @property string $entity
 * @property string $formula_type
 * @property string|null $conversion_formula Formule simple ([d], [level]) ou table JSON
 * @property string|null $handler_name Nom d'un handler PHP (ex. resistance_dofus_to_krosmoz)
 * @property array|null $parameters
 * @property string|null $formula_display
 */
class DofusdbConversionFormula extends Model
{
    protected $table = 'dofusdb_conversion_formulas';

    /** @var list<string> */
    protected $fillable = [
        'characteristic_key',
        'entity',
        'formula_type',
        'conversion_formula',
        'handler_name',
        'parameters',
        'formula_display',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'parameters' => 'array',
    ];
}
