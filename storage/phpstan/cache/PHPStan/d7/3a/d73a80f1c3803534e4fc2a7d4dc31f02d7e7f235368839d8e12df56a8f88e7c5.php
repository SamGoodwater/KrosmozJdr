<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/EffectUsage.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\EffectUsage
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-38304057c681715a69153006ccf64a56983118d1c5478fa23d7baf04c284dd8d-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\EffectUsage',
        'filename' => '/var/www/KrosmozJdr/app/Models/EffectUsage.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\EffectUsage',
    'shortName' => 'EffectUsage',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Lien polymorphique entité (item, consumable, resource) → degré d’effet.
 *
 * Les sorts utilisent la table {@see effect_spell} ; le seuil est sur {@see EffectDegree::required_creature_level}.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $effect_degree_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EffectDegree $effectDegree
 * @property-read Model|\\Eloquent $entity
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereEffectDegreeId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereEntityId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereEntityType($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|EffectUsage whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 39,
    'endLine' => 75,
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
      'ENTITY_TYPE_MAP' => 
      array (
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'name' => 'ENTITY_TYPE_MAP',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'item\' => \\App\\Models\\Entity\\Item::class, \'consumable\' => \\App\\Models\\Entity\\Consumable::class, \'resource\' => \\App\\Models\\Entity\\Resource::class]',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 69,
            'startTokenPos' => 177,
            'startFilePos' => 2315,
            'endTokenPos' => 206,
            'endFilePos' => 2441,
          ),
        ),
        'docComment' => '/** Entités supportées (nom court => classe). Les sorts ne passent plus par cette table. */',
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'effect_usages\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 68,
            'startFilePos' => 1750,
            'endTokenPos' => 68,
            'endFilePos' => 1764,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
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
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'entity_type\', \'entity_id\', \'effect_degree_id\']',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 47,
            'startTokenPos' => 77,
            'startFilePos' => 1794,
            'endTokenPos' => 88,
            'endFilePos' => 1872,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 47,
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
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'entity_id\' => \'integer\', \'effect_degree_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 52,
            'startTokenPos' => 97,
            'startFilePos' => 1899,
            'endTokenPos' => 113,
            'endFilePos' => 1980,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 49,
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
      'entity' => 
      array (
        'name' => 'entity',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 54,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'currentClassName' => 'App\\Models\\EffectUsage',
        'aliasName' => NULL,
      ),
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
        'startLine' => 59,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'currentClassName' => 'App\\Models\\EffectUsage',
        'aliasName' => NULL,
      ),
      'entityTypeToClass' => 
      array (
        'name' => 'entityTypeToClass',
        'parameters' => 
        array (
          'shortType' => 
          array (
            'name' => 'shortType',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 46,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 71,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\EffectUsage',
        'implementingClassName' => 'App\\Models\\EffectUsage',
        'currentClassName' => 'App\\Models\\EffectUsage',
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