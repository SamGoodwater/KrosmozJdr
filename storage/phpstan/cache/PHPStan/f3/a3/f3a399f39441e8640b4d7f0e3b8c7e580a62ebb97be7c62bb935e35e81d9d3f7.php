<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/BreedElementOrientation.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\BreedElementOrientation
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6cd0353b39c033301e974f51c708c4e00408c4b3bf0b8c6aef1f24bd8f72cc21-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\BreedElementOrientation',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/BreedElementOrientation.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\BreedElementOrientation',
    'shortName' => 'BreedElementOrientation',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Association voix élémentaire → orientation de classe (icône breed_orientations).
 *
 * @property int $id
 * @property int $breed_id
 * @property string $element air|earth|fire|water
 * @property string $orientation_key clef fichier (sans extension)
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Breed $breed
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereBreedId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereElement($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereOrientationKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedElementOrientation whereUpdatedAt($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 30,
    'endLine' => 60,
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
      'ELEMENT_AIR' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'ELEMENT_AIR',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'air\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 42,
            'startFilePos' => 1589,
            'endTokenPos' => 42,
            'endFilePos' => 1593,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 37,
      ),
      'ELEMENT_EARTH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'ELEMENT_EARTH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'earth\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 53,
            'startFilePos' => 1630,
            'endTokenPos' => 53,
            'endFilePos' => 1636,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ELEMENT_FIRE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'ELEMENT_FIRE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'fire\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 64,
            'startFilePos' => 1672,
            'endTokenPos' => 64,
            'endFilePos' => 1677,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'ELEMENT_WATER' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'ELEMENT_WATER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'water\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 75,
            'startFilePos' => 1714,
            'endTokenPos' => 75,
            'endFilePos' => 1720,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'ELEMENTS' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'ELEMENTS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[self::ELEMENT_AIR, self::ELEMENT_EARTH, self::ELEMENT_FIRE, self::ELEMENT_WATER]',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 46,
            'startTokenPos' => 88,
            'startFilePos' => 1781,
            'endTokenPos' => 110,
            'endFilePos' => 1900,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'breed_element_orientations\'',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 48,
            'startTokenPos' => 119,
            'startFilePos' => 1927,
            'endTokenPos' => 119,
            'endFilePos' => 1954,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 52,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'breed_id\', \'element\', \'orientation_key\']',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 54,
            'startTokenPos' => 128,
            'startFilePos' => 1984,
            'endTokenPos' => 139,
            'endFilePos' => 2056,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 54,
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
      'breed' => 
      array (
        'name' => 'breed',
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
        'startLine' => 56,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'implementingClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
        'currentClassName' => 'App\\Models\\Entity\\BreedElementOrientation',
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