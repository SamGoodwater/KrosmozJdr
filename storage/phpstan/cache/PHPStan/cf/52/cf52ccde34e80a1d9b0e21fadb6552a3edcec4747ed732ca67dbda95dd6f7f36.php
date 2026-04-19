<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/CreatureCharacteristicSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\CreatureCharacteristicSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-7e250317a79d680cb1f3d0a9154892d57748963afb445718b0eb13ebaff77300-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/CreatureCharacteristicSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
    'shortName' => 'CreatureCharacteristicSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed characteristic_creature (groupe creature : monster, class, npc).
 * Enrichit les lignes avec les samples depuis storage/app/characteristics_creature_samples.json si présent.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 166,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'DOFUS_REFERENCE_LEVELS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'name' => 'DOFUS_REFERENCE_LEVELS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[1, 40, 80, 120, 160, 200]',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 42,
            'startFilePos' => 471,
            'endTokenPos' => 59,
            'endFilePos' => 496,
          ),
        ),
        'docComment' => '/** Niveaux Dofus de référence (alignés sur l\'admin). */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 70,
      ),
      'KROSMOZ_REFERENCE_LEVELS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'name' => 'KROSMOZ_REFERENCE_LEVELS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[1, 4, 8, 12, 16, 20]',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 72,
            'startFilePos' => 611,
            'endTokenPos' => 89,
            'endFilePos' => 631,
          ),
        ),
        'docComment' => '/** Niveaux Krosmoz de référence (alignés sur l\'admin). */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 67,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'dataPath' => 
      array (
        'name' => 'dataPath',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 21,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'normsDataPath' => 
      array (
        'name' => 'normsDataPath',
        'parameters' => 
        array (
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
        'startLine' => 26,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'modelClass' => 
      array (
        'name' => 'modelClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return class-string<CharacteristicCreature>
 */',
        'startLine' => 34,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'jsonGroupSubdirectory' => 
      array (
        'name' => 'jsonGroupSubdirectory',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 39,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'mapRowToAttributes' => 
      array (
        'name' => 'mapRowToAttributes',
        'parameters' => 
        array (
          'row' => 
          array (
            'name' => 'row',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 43,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>
 */',
        'startLine' => 48,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'loadCreatureSamples' => 
      array (
        'name' => 'loadCreatureSamples',
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
 * Charge les samples depuis le JSON d\'extraction creature (optionnel).
 *
 * @return array<string, array<string, mixed>>
 */',
        'startLine' => 61,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'buildConversionSampleRows' => 
      array (
        'name' => 'buildConversionSampleRows',
        'parameters' => 
        array (
          'dofusSample' => 
          array (
            'name' => 'dofusSample',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 50,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'krosmozSample' => 
          array (
            'name' => 'krosmozSample',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 70,
            'endColumn' => 89,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
 * Construit conversion_sample_rows à partir des deux échantillons (paires dofus_level / krosmoz_level).
 *
 * @param  array<string, int|float>  $dofusSample
 * @param  array<string, int|float>  $krosmozSample
 * @return list<array{dofus_level: int, dofus_value: int|float|null, krosmoz_level: int, krosmoz_value: int|float|null}>
 */',
        'startLine' => 83,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'run' => 
      array (
        'name' => 'run',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 101,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\CreatureCharacteristicSeeder',
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