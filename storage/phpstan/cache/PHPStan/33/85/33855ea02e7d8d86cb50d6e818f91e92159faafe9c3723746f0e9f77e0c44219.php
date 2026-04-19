<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/database/seeders/CharacteristicGroupSeeder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Seeders\CharacteristicGroupSeeder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-71127b3e1d38e1fa9f5d32bb2f99cb5479f46cf4d61295476b06a672069fb151-8.4.17-6.70.0.0',
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
    'startLine' => 18,
    'endLine' => 193,
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
        'startLine' => 23,
        'endLine' => 23,
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
        'startLine' => 28,
        'endLine' => 31,
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
            'startLine' => 39,
            'endLine' => 39,
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
        'startLine' => 39,
        'endLine' => 59,
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
        'startLine' => 64,
        'endLine' => 64,
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
        'startLine' => 71,
        'endLine' => 87,
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
        'startLine' => 94,
        'endLine' => 98,
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
            'startLine' => 106,
            'endLine' => 106,
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
        'startLine' => 106,
        'endLine' => 121,
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
        'startLine' => 126,
        'endLine' => 173,
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
        'startLine' => 175,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
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
            'startLine' => 192,
            'endLine' => 192,
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
        'startLine' => 192,
        'endLine' => 192,
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