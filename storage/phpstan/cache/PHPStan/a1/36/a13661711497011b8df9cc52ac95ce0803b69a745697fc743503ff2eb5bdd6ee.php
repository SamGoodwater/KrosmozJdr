<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/ScrappingEntityMappingCharacteristicSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\ScrappingEntityMappingCharacteristicSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-4d01c81fe37116ccb13febf001b139b2868b07509a3ccfa2dc0862ce38b031b6-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/ScrappingEntityMappingCharacteristicSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
    'shortName' => 'ScrappingEntityMappingCharacteristicSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Lie les caractéristiques du groupe object aux règles « bonus » item et panoply
 * (table pivot scrapping_entity_mapping_characteristic).
 *
 * Exclut : CA, recharge wakfu, bonus de sauvegarde, compétences, bonus de touche.
 *
 * @see docs/50-Fonctionnalités/Characteristics-DB/DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 73,
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
      'EXCLUDED_OBJECT_KEYS' => 
      array (
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
        'name' => 'EXCLUDED_OBJECT_KEYS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'ca_object\', \'wakfu_recharge_object\', \'save_vit_sag_object\', \'save_force_int_cha_agi_object\', \'competences_object\', \'competences_passives_object\', \'touch_object\']',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 30,
            'startTokenPos' => 52,
            'startFilePos' => 729,
            'endTokenPos' => 75,
            'endFilePos' => 954,
          ),
        ),
        'docComment' => '/** Clés de caractéristiques object à ne pas lier à bonus (pas d\'équivalent DofusDB ou non mappées). */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 6,
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
        'startLine' => 32,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
        'implementingClassName' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
        'currentClassName' => 'Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder',
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