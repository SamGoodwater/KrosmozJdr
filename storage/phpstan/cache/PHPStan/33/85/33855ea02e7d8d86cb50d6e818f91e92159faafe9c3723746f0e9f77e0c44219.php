<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/CharacteristicGroupSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\CharacteristicGroupSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-3217ef4458a784e50c94a21b17ac595881d58aed7a739f87a7e351a61421da25-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'filename' => '/var/www/KrosmozJdr/database/seeders/CharacteristicGroupSeeder.php',
      ),
    ),
    'namespace' => 'Database\\Seeders',
    'name' => 'Database\\Seeders\\CharacteristicGroupSeeder',
    'shortName' => 'CharacteristicGroupSeeder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 64,
    'docComment' => '/**
 * Base pour les seeders des tables characteristic_creature, characteristic_object, characteristic_spell.
 * Source : fichiers `stem-groupe-definition.json` sous {@see CharacteristicDefinitionNaming::RELATIVE_ROOT}.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 258,
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
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
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
 * @return class-string<Model>
 */',
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 53,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 66,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'defaultEntity' => 
      array (
        'name' => 'defaultEntity',
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
 * Clé entity par défaut si absente du row (ex. \'*\' ou \'spell\').
 */',
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'commonAttributes' => 
      array (
        'name' => 'commonAttributes',
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
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 41,
            'endColumn' => 50,
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
 * Attributs communs à creature, object et spell (limites, formules, conversion).
 *
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>
 */',
        'startLine' => 40,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
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
        'docComment' => '/**
 * Sous-dossier de {@see CharacteristicDefinitionNaming::RELATIVE_ROOT} (creature|object|spell).
 */',
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 64,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 66,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'jsonDefinitionPaths' => 
      array (
        'name' => 'jsonDefinitionPaths',
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
 * Chemins absolus des fichiers `*-definition.json` pour ce groupe (triés).
 *
 * @return list<string>
 */',
        'startLine' => 72,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'characteristicIdsByKey' => 
      array (
        'name' => 'characteristicIdsByKey',
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
 * Ids des caractéristiques indexées par clé métier (une requête).
 *
 * @return array<string, int>
 */',
        'startLine' => 95,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'normalizeItemTypeIdsForSync' => 
      array (
        'name' => 'normalizeItemTypeIdsForSync',
        'parameters' => 
        array (
          'raw' => 
          array (
            'name' => 'raw',
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
            'startLine' => 107,
            'endLine' => 107,
            'startColumn' => 52,
            'endColumn' => 61,
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
 * Normalise les ids `item_types` pour la table pivot (entiers strictement positifs, uniques).
 *
 * @param  array<mixed>  $raw
 * @return list<int>
 */',
        'startLine' => 107,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'resolveCharacteristicObjectItemTypeIdsForSync' => 
      array (
        'name' => 'resolveCharacteristicObjectItemTypeIdsForSync',
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
            'startLine' => 133,
            'endLine' => 133,
            'startColumn' => 70,
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
 * Résout les ids pivot `item_types.id` pour une ligne entité objet.
 * Si `item_type_dofus_ids` est renseigné : résolution par `dofusdb_type_id` uniquement (pas de repli
 * sur `item_type_ids`, souvent des ids BDD d’un autre environnement).
 * Sinon : `item_type_ids` filtrés pour ne garder que des ids présents en base.
 *
 * @param  array<string, mixed>  $row
 * @return list<int>
 */',
        'startLine' => 133,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'aliasName' => NULL,
      ),
      'seedPivotsFromJsonDefinitions' => 
      array (
        'name' => 'seedPivotsFromJsonDefinitions',
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
        'docComment' => '/**
 * Seed depuis les fichiers `stem-groupe-definition.json`.
 */',
        'startLine' => 188,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
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
        'startLine' => 240,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
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
            'startLine' => 257,
            'endLine' => 257,
            'startColumn' => 52,
            'endColumn' => 61,
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
 * Mappe une ligne du fichier vers les attributs à passer à updateOrCreate.
 *
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>
 */',
        'startLine' => 257,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 70,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 66,
        'namespace' => 'Database\\Seeders',
        'declaringClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'implementingClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
        'currentClassName' => 'Database\\Seeders\\CharacteristicGroupSeeder',
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