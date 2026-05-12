<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/DofusdbEffectMapping.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\DofusdbEffectMapping
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-8c31403147458d4a1c1747d6111f000f2775f72b6d6a52e598e35dc244456efd-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\DofusdbEffectMapping',
        'filename' => '/var/www/KrosmozJdr/app/Models/DofusdbEffectMapping.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\DofusdbEffectMapping',
    'shortName' => 'DofusdbEffectMapping',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Mapping effectId DofusDB → sous-effet KrosmozJDR (sub_effect_slug + characteristic_source).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 * @property int $id
 * @property int $dofusdb_effect_id
 * @property string $sub_effect_slug
 * @property string $characteristic_source
 * @property string|null $characteristic_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereCharacteristicKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereCharacteristicSource($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereDofusdbEffectId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereSubEffectSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|DofusdbEffectMapping whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 33,
    'endLine' => 53,
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
      'SOURCE_ELEMENT' => 
      array (
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'SOURCE_ELEMENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'element\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 54,
            'startFilePos' => 1789,
            'endTokenPos' => 54,
            'endFilePos' => 1797,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'SOURCE_CHARACTERISTIC' => 
      array (
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'SOURCE_CHARACTERISTIC',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'characteristic\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 65,
            'startFilePos' => 1842,
            'endTokenPos' => 65,
            'endFilePos' => 1857,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'SOURCE_NONE' => 
      array (
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'SOURCE_NONE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'none\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 76,
            'startFilePos' => 1892,
            'endTokenPos' => 76,
            'endFilePos' => 1897,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'dofusdb_effect_mappings\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 43,
            'startFilePos' => 1727,
            'endTokenPos' => 43,
            'endFilePos' => 1751,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_effect_id\', \'sub_effect_slug\', \'characteristic_source\', \'characteristic_key\']',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 48,
            'startTokenPos' => 85,
            'startFilePos' => 1927,
            'endTokenPos' => 99,
            'endFilePos' => 2052,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 48,
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
        'declaringClassName' => 'App\\Models\\DofusdbEffectMapping',
        'implementingClassName' => 'App\\Models\\DofusdbEffectMapping',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_effect_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 52,
            'startTokenPos' => 108,
            'startFilePos' => 2079,
            'endTokenPos' => 117,
            'endFilePos' => 2127,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 52,
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