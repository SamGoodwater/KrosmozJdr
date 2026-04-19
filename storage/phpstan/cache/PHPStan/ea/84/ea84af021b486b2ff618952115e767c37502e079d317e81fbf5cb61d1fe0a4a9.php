<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/ObjectCharacteristicSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\ObjectCharacteristicSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5d585f8cfa6353e37c313f10c2b4f099de089444385c9906ec8b947d1453406c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/ObjectCharacteristicSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
    'shortName' => 'ObjectCharacteristicSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed characteristic_object (groupe object : item, consumable, resource, panoply).
 * Enrichit les lignes avec les samples depuis storage/app/characteristics_object_samples.json si présent.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 201,
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
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'name' => 'DOFUS_REFERENCE_LEVELS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[1, 40, 80, 120, 160, 200]',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 17,
            'startTokenPos' => 47,
            'startFilePos' => 507,
            'endTokenPos' => 64,
            'endFilePos' => 532,
          ),
        ),
        'docComment' => '/** Niveaux Dofus de référence (alignés sur l\'admin). */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 70,
      ),
      'KROSMOZ_REFERENCE_LEVELS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'name' => 'KROSMOZ_REFERENCE_LEVELS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[1, 4, 8, 12, 16, 20]',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 77,
            'startFilePos' => 647,
            'endTokenPos' => 94,
            'endFilePos' => 667,
          ),
        ),
        'docComment' => '/** Niveaux Krosmoz de référence (alignés sur l\'admin). */',
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
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
        'startLine' => 22,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
        'startLine' => 27,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
 * @return class-string<CharacteristicObject>
 */',
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
            'startLine' => 49,
            'endLine' => 49,
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
        'startLine' => 49,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'resolveItemTypeIdsFromDofusTypeIds' => 
      array (
        'name' => 'resolveItemTypeIdsFromDofusTypeIds',
        'parameters' => 
        array (
          'dofusdbTypeIds' => 
          array (
            'name' => 'dofusdbTypeIds',
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
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 59,
            'endColumn' => 79,
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
 * Résout les ids pivot `item_types.id` à partir des id DofusDB (`item_types.dofusdb_type_id`).
 *
 * @param  list<int>  $dofusdbTypeIds
 * @return list<int>
 */',
        'startLine' => 65,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'aliasName' => NULL,
      ),
      'loadObjectSamples' => 
      array (
        'name' => 'loadObjectSamples',
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
 * Charge les samples depuis le JSON d\'extraction (optionnel).
 *
 * @return array<string, array<string, mixed>>
 */',
        'startLine' => 84,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
            'startLine' => 106,
            'endLine' => 106,
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
            'startLine' => 106,
            'endLine' => 106,
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
        'startLine' => 106,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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
        'startLine' => 124,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ObjectCharacteristicSeeder',
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