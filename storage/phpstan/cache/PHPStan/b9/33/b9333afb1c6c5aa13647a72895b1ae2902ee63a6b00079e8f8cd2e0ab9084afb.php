<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesSeedCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingTypesSeedCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-1f42c8b2a0e7d648c8b0355062781fe9d5ada2bfef32283e35e5cf9407440686',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesSeedCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
    'shortName' => 'ScrappingTypesSeedCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Remplit les tables resource_types, consumable_types, item_types depuis l’API DofusDB.
 *
 * Une seule commande : récupère tous les item-types via l’API (superTypeId → Ressource / Consommable / Équipement),
 * écrit les fichiers database/seeders/data/*.php puis exécute les 3 seeders pour synchroniser la BDD.
 * Aucun type n’est oublié : la classification repose sur l’API (https://api.dofusdb.fr/item-types).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_TYPES_ITEM_BDD_SEEDER.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 107,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ITEM_TYPE_SEEDERS' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'name' => 'ITEM_TYPE_SEEDERS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'resource\' => \\Database\\Seeders\\Type\\ResourceTypeSeeder::class, \'consumable\' => \\Database\\Seeders\\Type\\ConsumableTypeSeeder::class, \'item\' => \\Database\\Seeders\\Type\\ItemTypeSeeder::class]',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 37,
            'startTokenPos' => 85,
            'startFilePos' => 1554,
            'endTokenPos' => 114,
            'endFilePos' => 1772,
          ),
        ),
        'docComment' => '/** @var array<string, class-string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:types:seed
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}
                            {--no-files : Ne pas écrire les fichiers data (seulement exécuter les seeders sur les fichiers existants)}
                            {--only= : Sous-ensemble des seeders item-types (virgules) : resource, consumable, item ou equipment (syn. item) ; all ou vide = les trois}\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 26,
            'startTokenPos' => 52,
            'startFilePos' => 804,
            'endTokenPos' => 52,
            'endFilePos' => 1275,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 169,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Remplit les types item (ressource / consommable / équipement) depuis l’API DofusDB puis les seeders\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 61,
            'startFilePos' => 1308,
            'endTokenPos' => 61,
            'endFilePos' => 1411,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 134,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'scrapping:seed-item-types\']',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 70,
            'startFilePos' => 1440,
            'endTokenPos' => 72,
            'endFilePos' => 1468,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 55,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 39,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'aliasName' => NULL,
      ),
      'resolveItemTypeSeederKeys' => 
      array (
        'name' => 'resolveItemTypeSeederKeys',
        'parameters' => 
        array (
          'onlyOption' => 
          array (
            'name' => 'onlyOption',
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 48,
            'endColumn' => 65,
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
 * @return list<string> clés resource|consumable|item
 */',
        'startLine' => 87,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesSeedCommand',
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