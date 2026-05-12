<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/SpellEffect.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\SpellEffect
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9f97c8e651b839fb277611bff401e08825287cafa7ce6d6e124ced10b27af532-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\SpellEffect',
        'filename' => '/var/www/KrosmozJdr/app/Models/SpellEffect.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\SpellEffect',
    'shortName' => 'SpellEffect',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Effet appliqué à un sort (instance).
 *
 * @property int $id
 * @property int $spell_id
 * @property int $spell_effect_type_id
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property int|null $duration
 * @property string $target_scope
 * @property string|null $zone_shape
 * @property bool $dispellable
 * @property int $order
 * @property string|null $raw_description
 * @property int|null $summon_monster_id
 * @property-read Spell $spell
 * @property-read SpellEffectType $spellEffectType
 * @property-read Monster|null $summonMonster
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereDiceNum($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereDiceSide($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereDispellable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereDuration($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereRawDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereSpellEffectTypeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereSpellId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereSummonMonsterId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereTargetScope($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereValueMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereValueMin($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellEffect whereZoneShape($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 56,
    'endLine' => 111,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'TARGET_SELF' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'TARGET_SELF',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'self\'',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 186,
            'startFilePos' => 3575,
            'endTokenPos' => 186,
            'endFilePos' => 3580,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TARGET_ALLY' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'TARGET_ALLY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'ally\'',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 197,
            'startFilePos' => 3615,
            'endTokenPos' => 197,
            'endFilePos' => 3620,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TARGET_ENEMY' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'TARGET_ENEMY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'enemy\'',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 208,
            'startFilePos' => 3656,
            'endTokenPos' => 208,
            'endFilePos' => 3662,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'TARGET_CELL' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'TARGET_CELL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cell\'',
          'attributes' => 
          array (
            'startLine' => 93,
            'endLine' => 93,
            'startTokenPos' => 219,
            'startFilePos' => 3697,
            'endTokenPos' => 219,
            'endFilePos' => 3702,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 93,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TARGET_ZONE' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'TARGET_ZONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'zone\'',
          'attributes' => 
          array (
            'startLine' => 95,
            'endLine' => 95,
            'startTokenPos' => 230,
            'startFilePos' => 3737,
            'endTokenPos' => 230,
            'endFilePos' => 3742,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 95,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'spell_effects\'',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 58,
            'startFilePos' => 2884,
            'endTokenPos' => 58,
            'endFilePos' => 2898,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'spell_id\', \'spell_effect_type_id\', \'value_min\', \'value_max\', \'dice_num\', \'dice_side\', \'duration\', \'target_scope\', \'zone_shape\', \'dispellable\', \'order\', \'raw_description\', \'summon_monster_id\']',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 74,
            'startTokenPos' => 67,
            'startFilePos' => 2928,
            'endTokenPos' => 108,
            'endFilePos' => 3231,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'value_min\' => \'integer\', \'value_max\' => \'integer\', \'dice_num\' => \'integer\', \'dice_side\' => \'integer\', \'duration\' => \'integer\', \'dispellable\' => \'boolean\', \'order\' => \'integer\', \'summon_monster_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 85,
            'startTokenPos' => 117,
            'startFilePos' => 3258,
            'endTokenPos' => 175,
            'endFilePos' => 3540,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'spell' => 
      array (
        'name' => 'spell',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 97,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'currentClassName' => 'App\\Models\\SpellEffect',
        'aliasName' => NULL,
      ),
      'spellEffectType' => 
      array (
        'name' => 'spellEffectType',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 102,
        'endLine' => 105,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'currentClassName' => 'App\\Models\\SpellEffect',
        'aliasName' => NULL,
      ),
      'summonMonster' => 
      array (
        'name' => 'summonMonster',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 107,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\SpellEffect',
        'implementingClassName' => 'App\\Models\\SpellEffect',
        'currentClassName' => 'App\\Models\\SpellEffect',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));