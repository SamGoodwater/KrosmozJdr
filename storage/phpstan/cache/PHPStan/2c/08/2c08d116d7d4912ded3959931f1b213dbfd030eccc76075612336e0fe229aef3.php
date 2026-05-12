<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/ScrappingEntityMappingSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\ScrappingEntityMappingSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-72995e364899a11b58ff664ff2f3dc6c9f6db311161687e5df03af90b219537f-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/ScrappingEntityMappingSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
    'shortName' => 'ScrappingEntityMappingSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Seed des règles de mapping scrapping depuis database/seeders/data/scrapping_entity_mappings.php.
 *
 * Fichier généré par : php artisan scrapping:seeders:export --scrapping-mappings
 * (après modification des règles via l\'UI admin « Mapping scrapping »).
 *
 * @see docs/50-Fonctionnalités/VISION_UI_ADMIN_MAPPING_ET_CARACTERISTIQUES.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 185,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Seeder',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Database\\Seeders\\Concerns\\LoadsSeederDataFile',
    ),
    'immediateConstants' => 
    array (
      'DATA_FILE' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'name' => 'DATA_FILE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'database/seeders/data/scrapping_entity_mappings.php\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 65,
            'startFilePos' => 750,
            'endTokenPos' => 65,
            'endFilePos' => 802,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 84,
      ),
      'SOURCE_CONFIG_BASE' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'name' => 'SOURCE_CONFIG_BASE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'resources/scrapping/config/sources\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 76,
            'startFilePos' => 844,
            'endTokenPos' => 76,
            'endFilePos' => 879,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 76,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
        'startLine' => 28,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'currentClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'aliasName' => NULL,
      ),
      'loadRowsFromEntityJson' => 
      array (
        'name' => 'loadRowsFromEntityJson',
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
 * Bootstrap de secours: convertit les mappings JSON des entités en lignes seeder.
 *
 * @return list<array<string, mixed>>
 */',
        'startLine' => 91,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'currentClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'aliasName' => NULL,
      ),
      'readJsonFile' => 
      array (
        'name' => 'readJsonFile',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 172,
            'endLine' => 172,
            'startColumn' => 35,
            'endColumn' => 46,
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
                  'name' => 'array',
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
 * @return array<string, mixed>|null
 */',
        'startLine' => 172,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
        'currentClassName' => 'Database\\Seeders\\ScrappingEntityMappingSeeder',
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