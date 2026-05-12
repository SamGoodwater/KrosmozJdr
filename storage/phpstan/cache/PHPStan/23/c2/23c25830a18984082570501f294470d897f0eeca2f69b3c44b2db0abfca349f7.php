<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/ObjectEffect.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ObjectEffect
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b79effd9b8790f43cb5db380c6b3f88136343dbf5931d3d095c99b471b259e53-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ObjectEffect',
        'filename' => '/var/www/KrosmozJdr/app/Models/ObjectEffect.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ObjectEffect',
    'shortName' => 'ObjectEffect',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Effet simple lié à un objet jeu (item, consommable, ressource) : action + cible optionnelle + valeur optionnelle.
 *
 * @property int $id
 * @property string $object_effectable_type
 * @property int $object_effectable_id
 * @property ObjectEffectAction $action
 * @property int|null $characteristic_id
 * @property int|null $monster_id
 * @property int|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Characteristic|null $characteristic
 * @property-read Monster|null $monster
 * @property-read Model|\\Eloquent $objectEffectable
 * @method static \\Database\\Factories\\ObjectEffectFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereAction($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereCharacteristicId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereMonsterId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereObjectEffectableId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereObjectEffectableType($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ObjectEffect whereValue($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 49,
    'endLine' => 103,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'object_effectable_type\', \'object_effectable_id\', \'action\', \'characteristic_id\', \'monster_id\', \'value\']',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 74,
            'startTokenPos' => 165,
            'startFilePos' => 2913,
            'endTokenPos' => 185,
            'endFilePos' => 3071,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
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
    ),
    'immediateMethods' => 
    array (
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
            'startLine' => 57,
            'endLine' => 57,
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
        'docComment' => '/**
 * Alias court (item, consumable, resource) → classe Eloquent, pour les formulaires / API.
 */',
        'startLine' => 57,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'currentClassName' => 'App\\Models\\ObjectEffect',
        'aliasName' => NULL,
      ),
      'casts' => 
      array (
        'name' => 'casts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<string, string>
 */',
        'startLine' => 79,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'currentClassName' => 'App\\Models\\ObjectEffect',
        'aliasName' => NULL,
      ),
      'objectEffectable' => 
      array (
        'name' => 'objectEffectable',
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
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'currentClassName' => 'App\\Models\\ObjectEffect',
        'aliasName' => NULL,
      ),
      'characteristic' => 
      array (
        'name' => 'characteristic',
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
        'startLine' => 94,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'currentClassName' => 'App\\Models\\ObjectEffect',
        'aliasName' => NULL,
      ),
      'monster' => 
      array (
        'name' => 'monster',
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
        'startLine' => 99,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ObjectEffect',
        'implementingClassName' => 'App\\Models\\ObjectEffect',
        'currentClassName' => 'App\\Models\\ObjectEffect',
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