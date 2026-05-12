<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSetupCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingSetupCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-14469cd25886e926df9b15fab998956bbdbe5a9f4afb338bcd7f9c826e8d03d5',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSetupCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
    'shortName' => 'ScrappingSetupCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Bootstrap du socle scrapping (migrations + seeders essentiels).
 *
 * Cette commande initialise les données indispensables au pipeline:
 * caractéristiques, mappings DofusDB, mappings scrapping par entité.
 *
 * @example php artisan scrapping:setup
 * @example php artisan scrapping:setup --fresh
 * @example php artisan scrapping:setup --skip-migrate
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 30,
    'endLine' => 90,
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
      'SEEDERS' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'name' => 'SEEDERS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\\Database\\Seeders\\Type\\TypeSeeder::class, \\Database\\Seeders\\CharacteristicSeeder::class, \\Database\\Seeders\\CreatureCharacteristicSeeder::class, \\Database\\Seeders\\ObjectCharacteristicSeeder::class, \\Database\\Seeders\\DofusdbCharacteristicIdSeeder::class, \\Database\\Seeders\\SpellCharacteristicSeeder::class, \\Database\\Seeders\\SpellEffectTypeSeeder::class, \\Database\\Seeders\\DofusdbEffectMappingSeeder::class, \\Database\\Seeders\\ScrappingEntityMappingSeeder::class, \\Database\\Seeders\\ScrappingEntityMappingCharacteristicSeeder::class]',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 52,
            'startTokenPos' => 130,
            'startFilePos' => 1510,
            'endTokenPos' => 182,
            'endFilePos' => 1964,
          ),
        ),
        'docComment' => '/** @var list<class-string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:setup
                            {--fresh : Exécute migrate:fresh --force avant les seeders}
                            {--skip-migrate : Ne lance pas les migrations}\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 34,
            'startTokenPos' => 97,
            'startFilePos' => 1105,
            'endTokenPos' => 97,
            'endFilePos' => 1285,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 76,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Initialise le socle scrapping (migrations + caractéristiques + mappings)\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 106,
            'startFilePos' => 1318,
            'endTokenPos' => 106,
            'endFilePos' => 1392,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 105,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'scrapping:bootstrap\']',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 115,
            'startFilePos' => 1421,
            'endTokenPos' => 117,
            'endFilePos' => 1443,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 49,
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
        'startLine' => 54,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSetupCommand',
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