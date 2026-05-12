<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRacesSeedCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingRacesSeedCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-7cf858df6ce20fcf191b3ccde1d8fc8336d0523c5d1f6aa88e19d8c7a77b5a6b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRacesSeedCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
    'shortName' => 'ScrappingRacesSeedCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Récupère les races de monstres depuis l\'API DofusDB et les synchronise en base.
 *
 * Crée ou met à jour les entrées monster_races avec dofusdb_race_id et name.
 * Les races existantes ne sont pas réinitialisées (state, read_level, write_level préservés).
 *
 * @example php artisan scrapping:races:seed
 * @example php artisan scrapping:races:seed --lang=fr --skip-cache
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 95,
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
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:races:seed
                            {--lang=fr : Langue du catalogue DofusDB}
                            {--skip-cache : Ignorer le cache du catalogue}\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 26,
            'startTokenPos' => 62,
            'startFilePos' => 767,
            'endTokenPos' => 62,
            'endFilePos' => 933,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 26,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Récupère les races monstres depuis l\\\'API DofusDB et les synchronise en base\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 71,
            'startFilePos' => 966,
            'endTokenPos' => 71,
            'endFilePos' => 1045,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 110,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'catalogService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'name' => 'catalogService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 9,
        'endColumn' => 74,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'catalogService' => 
          array (
            'name' => 'catalogService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 9,
            'endColumn' => 74,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 30,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'aliasName' => NULL,
      ),
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
        'startLine' => 36,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRacesSeedCommand',
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