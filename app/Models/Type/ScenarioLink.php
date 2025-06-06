<?php

namespace App\Models\Type;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity\Scenario;

/**
 * 
 *
 * @property int $id
 * @property int $scenario_id
 * @property int $next_scenario_id
 * @property string|null $condition
 * @property-read Scenario $nextScenario
 * @property-read Scenario $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereNextScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereScenarioId($value)
 * @mixin \Eloquent
 */
class ScenarioLink extends Model
{
    protected $table = 'scenario_link';
    public $timestamps = false;

    protected $fillable = [
        'scenario_id',
        'next_scenario_id',
        'condition',
    ];

    /**
     * Le scénario source du lien.
     */
    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id');
    }

    /**
     * Le scénario cible du lien.
     */
    public function nextScenario()
    {
        return $this->belongsTo(Scenario::class, 'next_scenario_id');
    }
}
