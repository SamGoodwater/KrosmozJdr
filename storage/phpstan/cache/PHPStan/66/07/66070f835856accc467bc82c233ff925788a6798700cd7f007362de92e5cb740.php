<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/EffectDegree.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\EffectDegree
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-3e7833a79966290fe7ac131adfbd86f2ae45e78e16ff13182b0e5400982059fe-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\EffectDegree',
        'filename' => '/var/www/KrosmozJdr/app/Models/EffectDegree.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\EffectDegree',
    'shortName' => 'EffectDegree',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Degré d’un effet : zone, slug, seuil de niveau requis, sous-effets.
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 * @property int $id
 * @property int $effect_id
 * @property int $degree
 * @property int|null $required_creature_level
 * @property string|null $area
 * @property string|null $slug
 * @property string|null $config_signature
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Effect $effect
 * @property-read Collection<int, EffectSubEffect> $effectSubEffects
 * @property-read int|null $effect_sub_effects_count
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereArea($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereConfigSignature($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereDegree($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereEffectId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereRequiredCreatureLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectDegree whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 45,
    'endLine' => 78,
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
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'effect_degrees\'',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 58,
            'startFilePos' => 2233,
            'endTokenPos' => 58,
            'endFilePos' => 2248,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'effect_id\', \'degree\', \'required_creature_level\', \'area\', \'slug\', \'config_signature\']',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 56,
            'startTokenPos' => 67,
            'startFilePos' => 2278,
            'endTokenPos' => 87,
            'endFilePos' => 2418,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 56,
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
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'effect_id\' => \'integer\', \'degree\' => \'integer\', \'required_creature_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 62,
            'startTokenPos' => 96,
            'startFilePos' => 2445,
            'endTokenPos' => 119,
            'endFilePos' => 2564,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 62,
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
      'effect' => 
      array (
        'name' => 'effect',
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
        'startLine' => 64,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'currentClassName' => 'App\\Models\\EffectDegree',
        'aliasName' => NULL,
      ),
      'effectSubEffects' => 
      array (
        'name' => 'effectSubEffects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 69,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'currentClassName' => 'App\\Models\\EffectDegree',
        'aliasName' => NULL,
      ),
      'effectUsages' => 
      array (
        'name' => 'effectUsages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 74,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectDegree',
        'implementingClassName' => 'App\\Models\\EffectDegree',
        'currentClassName' => 'App\\Models\\EffectDegree',
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