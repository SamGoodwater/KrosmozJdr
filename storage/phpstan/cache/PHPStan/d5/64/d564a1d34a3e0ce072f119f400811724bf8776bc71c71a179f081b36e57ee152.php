<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Language.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Language
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0419dfee01057c395519475cd5336204b33fb6d8a40290582ae0c80be75d5060-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Language',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Language.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Language',
    'shortName' => 'Language',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Langue (référentiel) — associable aux classes, monstres, spécialisations, etc.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color Hex #RRGGBB
 *
 * @method static LanguageFactory factory($count = null, $state = [])
 *
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereColor($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Language whereUpdatedAt($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 41,
    'endLine' => 67,
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
        'declaringClassName' => 'App\\Models\\Entity\\Language',
        'implementingClassName' => 'App\\Models\\Entity\\Language',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'color\']',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 50,
            'startTokenPos' => 62,
            'startFilePos' => 1836,
            'endTokenPos' => 73,
            'endFilePos' => 1898,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 50,
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
      'breeds' => 
      array (
        'name' => 'breeds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 52,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Language',
        'implementingClassName' => 'App\\Models\\Entity\\Language',
        'currentClassName' => 'App\\Models\\Entity\\Language',
        'aliasName' => NULL,
      ),
      'monsters' => 
      array (
        'name' => 'monsters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 60,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Language',
        'implementingClassName' => 'App\\Models\\Entity\\Language',
        'currentClassName' => 'App\\Models\\Entity\\Language',
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