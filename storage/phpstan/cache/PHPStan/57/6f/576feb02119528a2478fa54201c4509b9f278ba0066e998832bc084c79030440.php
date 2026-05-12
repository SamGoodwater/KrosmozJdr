<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/EffectSubEffect.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\EffectSubEffect
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0d32eea68abbb1ba191894835ca60880dfb4d2da7accb0415b3e79e1d1fb3c8f-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\EffectSubEffect',
        'filename' => '/var/www/KrosmozJdr/app/Models/EffectSubEffect.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\EffectSubEffect',
    'shortName' => 'EffectSubEffect',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Pivot degré d’effet / sub_effect (ordre, scope, params).
 *
 * @property int $id
 * @property int $effect_degree_id
 * @property int $sub_effect_id
 * @property int $order
 * @property string $scope
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property array<array-key, mixed>|null $params
 * @property bool $crit_only
 * @property string|null $duration_formula
 * @property string|null $logic_group
 * @property string|null $logic_operator
 * @property string|null $logic_condition
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EffectDegree $effectDegree
 * @property-read SubEffect $subEffect
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereCritOnly($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereDiceNum($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereDiceSide($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereDurationFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereEffectDegreeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereLogicCondition($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereLogicGroup($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereLogicOperator($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereParams($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereScope($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereSubEffectId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereValueMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectSubEffect whereValueMin($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 55,
    'endLine' => 101,
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
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\EffectSubEffect',
        'implementingClassName' => 'App\\Models\\EffectSubEffect',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'effect_sub_effect\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 48,
            'startFilePos' => 3033,
            'endTokenPos' => 48,
            'endFilePos' => 3051,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\EffectSubEffect',
        'implementingClassName' => 'App\\Models\\EffectSubEffect',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'effect_degree_id\', \'sub_effect_id\', \'order\', \'scope\', \'value_min\', \'value_max\', \'dice_num\', \'dice_side\', \'duration_formula\', \'logic_group\', \'logic_operator\', \'logic_condition\', \'params\', \'crit_only\']',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 74,
            'startTokenPos' => 57,
            'startFilePos' => 3081,
            'endTokenPos' => 101,
            'endFilePos' => 3400,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
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
        'declaringClassName' => 'App\\Models\\EffectSubEffect',
        'implementingClassName' => 'App\\Models\\EffectSubEffect',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'effect_degree_id\' => \'integer\', \'sub_effect_id\' => \'integer\', \'order\' => \'integer\', \'value_min\' => \'integer\', \'value_max\' => \'integer\', \'dice_num\' => \'integer\', \'dice_side\' => \'integer\', \'duration_formula\' => \'string\', \'logic_group\' => \'string\', \'logic_operator\' => \'string\', \'logic_condition\' => \'string\', \'params\' => \'array\', \'crit_only\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 90,
            'startTokenPos' => 110,
            'startFilePos' => 3427,
            'endTokenPos' => 203,
            'endFilePos' => 3892,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 90,
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
      'effectDegree' => 
      array (
        'name' => 'effectDegree',
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
        'startLine' => 92,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectSubEffect',
        'implementingClassName' => 'App\\Models\\EffectSubEffect',
        'currentClassName' => 'App\\Models\\EffectSubEffect',
        'aliasName' => NULL,
      ),
      'subEffect' => 
      array (
        'name' => 'subEffect',
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
        'declaringClassName' => 'App\\Models\\EffectSubEffect',
        'implementingClassName' => 'App\\Models\\EffectSubEffect',
        'currentClassName' => 'App\\Models\\EffectSubEffect',
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