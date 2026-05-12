<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/DofusdbCharacteristicIdSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\DofusdbCharacteristicIdSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-08182d420dbc6987fabe02b905857474f5a8982e8baefccf4424add0187ecef7-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/DofusdbCharacteristicIdSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
    'shortName' => 'DofusdbCharacteristicIdSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Remplit dofusdb_characteristic_id sur characteristic_object à partir du mapping DofusDB → Krosmoz.
 *
 * Source : resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json
 * (mapping id GET /characteristics → characteristic_key groupe object).
 * Phase 1.1 — Permet la résolution id → caractéristique côté service (M2).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_SCRAPPING.md
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 69,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Seeder',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'JSON_PATH' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
        'name' => 'JSON_PATH',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 55,
            'startFilePos' => 846,
            'endTokenPos' => 55,
            'endFilePos' => 928,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 114,
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
        'startLine' => 26,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
        'implementingClassName' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
        'currentClassName' => 'Database\\Seeders\\DofusdbCharacteristicIdSeeder',
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