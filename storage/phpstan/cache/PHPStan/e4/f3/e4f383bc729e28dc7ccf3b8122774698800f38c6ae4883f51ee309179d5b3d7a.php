<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Effect.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Effect
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-ee80ec296a1318c807f707743d97f6b47a5d6419b3dec1f12371146f69088c86-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Effect',
        'filename' => '/var/www/KrosmozJdr/app/Models/Effect.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Effect',
    'shortName' => 'Effect',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Définition d’effet (généralités). Les degrés (zone, seuil, sous-effets) : {@see EffectDegree}.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $target_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, EffectDegree> $degrees
 * @property-read int|null $degrees_count
 * @property-read Collection<int, EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereSlug($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereTargetType($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Effect whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 43,
    'endLine' => 84,
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
      'TARGET_DIRECT' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'TARGET_DIRECT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'direct\'',
          'attributes' => 
          array (
            'startLine' => 55,
            'endLine' => 55,
            'startTokenPos' => 104,
            'startFilePos' => 2199,
            'endTokenPos' => 104,
            'endFilePos' => 2206,
          ),
        ),
        'docComment' => '/** Cible : application directe sur la cible (défaut). */',
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'TARGET_TRAP' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'TARGET_TRAP',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'trap\'',
          'attributes' => 
          array (
            'startLine' => 57,
            'endLine' => 57,
            'startTokenPos' => 115,
            'startFilePos' => 2241,
            'endTokenPos' => 115,
            'endFilePos' => 2246,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 57,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TARGET_GLYPH' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'TARGET_GLYPH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'glyph\'',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 59,
            'startTokenPos' => 126,
            'startFilePos' => 2282,
            'endTokenPos' => 126,
            'endFilePos' => 2288,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'SCOPE_GENERAL' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'SCOPE_GENERAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'general\'',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 137,
            'startFilePos' => 2325,
            'endTokenPos' => 137,
            'endFilePos' => 2333,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'SCOPE_COMBAT' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'SCOPE_COMBAT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'combat\'',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 148,
            'startFilePos' => 2369,
            'endTokenPos' => 148,
            'endFilePos' => 2376,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'SCOPE_OUT_OF_COMBAT' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'SCOPE_OUT_OF_COMBAT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'out_of_combat\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 159,
            'startFilePos' => 2419,
            'endTokenPos' => 159,
            'endFilePos' => 2433,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'effects\'',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 45,
            'startTokenPos' => 68,
            'startFilePos' => 1977,
            'endTokenPos' => 68,
            'endFilePos' => 1985,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'slug\', \'description\', \'target_type\']',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 52,
            'startTokenPos' => 77,
            'startFilePos' => 2015,
            'endTokenPos' => 91,
            'endFilePos' => 2099,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
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
      'degrees' => 
      array (
        'name' => 'degrees',
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
        'startLine' => 67,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'currentClassName' => 'App\\Models\\Effect',
        'aliasName' => NULL,
      ),
      'spells' => 
      array (
        'name' => 'spells',
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
        'startLine' => 72,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'currentClassName' => 'App\\Models\\Effect',
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
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasManyThrough',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Usages polymorphiques (items, consommables…) pointant vers un degré de cet effet.
 */',
        'startLine' => 80,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Effect',
        'implementingClassName' => 'App\\Models\\Effect',
        'currentClassName' => 'App\\Models\\Effect',
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