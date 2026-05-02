<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/BreedElementOrientation.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\BreedElementOrientation
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-e9ee7b359594b97c37037bb7d6522a306cde5aac28b804b94a0621f3a3d14bd9-8.4.17-6.70.0.0',
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
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 50,
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
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 42,
            'startFilePos' => 610,
            'endTokenPos' => 42,
            'endFilePos' => 614,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
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
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 53,
            'startFilePos' => 651,
            'endTokenPos' => 53,
            'endFilePos' => 657,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
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
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 64,
            'startFilePos' => 693,
            'endTokenPos' => 64,
            'endFilePos' => 698,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
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
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 75,
            'startFilePos' => 735,
            'endTokenPos' => 75,
            'endFilePos' => 741,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
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
            'startLine' => 31,
            'endLine' => 36,
            'startTokenPos' => 88,
            'startFilePos' => 802,
            'endTokenPos' => 110,
            'endFilePos' => 921,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 36,
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
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 119,
            'startFilePos' => 948,
            'endTokenPos' => 119,
            'endFilePos' => 975,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
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
            'startLine' => 40,
            'endLine' => 44,
            'startTokenPos' => 128,
            'startFilePos' => 1005,
            'endTokenPos' => 139,
            'endFilePos' => 1077,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 44,
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
        'startLine' => 46,
        'endLine' => 49,
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