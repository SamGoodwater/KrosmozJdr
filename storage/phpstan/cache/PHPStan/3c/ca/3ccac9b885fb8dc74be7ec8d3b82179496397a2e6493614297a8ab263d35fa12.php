<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectDataCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-fdd8b35dc1e2e6c082b2343797874909697a86c060e70488825103e4003145e1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
    'shortName' => 'ProjectDataCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Point d’entrée unique pour les flux « données DofusDB » (sync / init catalogue / complétion).
 *
 * Délègue aux commandes existantes pour rester DRY.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 306,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:data
        {action : sync (maj auto_update), init (équivalent project:init données), fill (guide complétion)}
        {--fresh : (init) migrate:fresh avant le pipeline}
        {--noimage : (init|sync) pas de téléchargement d’images}
        {--skip-cache : (init|sync|catalogue) ignorer le cache HTTP scrapping}
        {--simulate : (init) ne pas écrire en base}
        {--entity= : (sync) Entités (virgules) : breed|class, spell, monster, panoply, resource, item, consumable — sans --type/--races : sync seul ; avec catalogue : exige ce filtre pour lancer aussi le sync entités}
        {--type= : (sync) L\\\'Essentiels catalogue (virgules) : all | monster (races) | resource | consumable | item | equipment | spell (types de sorts en BDD)}
        {--races : (sync) Raccourci pour --type=monster (races monstres DofusDB)}
        {--lang=fr : (sync catalogue) langue DofusDB pour types/races}
        {--skip-scrapping : (init)}
        {--skip-seeders : (init)}
        {--skip-types : (init)}
        {--skip-capabilities : (init)}
        {--skip-super-admin-prompt : (init)}
        {--max-items=0 : (init)}
        {--update-mode=ignore : (init)}
        {--dry-run : (sync)}
        {--skip-clear-queue : (sync|init)}
        {--skip-notify : (sync|init)}\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 40,
            'startTokenPos' => 62,
            'startFilePos' => 548,
            'endTokenPos' => 62,
            'endFilePos' => 1836,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Données DofusDB : sync (auto_update), init (pipeline complet), fill (guide — non automatisé)\'',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 71,
            'startFilePos' => 1869,
            'endTokenPos' => 71,
            'endFilePos' => 1966,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 128,
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
        'startLine' => 44,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'runSync' => 
      array (
        'name' => 'runSync',
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
        'startLine' => 56,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'buildEntitySyncParams' => 
      array (
        'name' => 'buildEntitySyncParams',
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
 * @return array<string, mixed>
 */',
        'startLine' => 75,
        'endLine' => 99,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'shouldRunEntitySyncAfterCatalog' => 
      array (
        'name' => 'shouldRunEntitySyncAfterCatalog',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Si un catalogue (--type / --races) est demandé sans --entity, on n’exécute pas le sync entités.
 * Sinon (pas de catalogue, ou catalogue + --entity, ou sync seul) : sync entités.
 */',
        'startLine' => 105,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'hasCatalogOptions' => 
      array (
        'name' => 'hasCatalogOptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 117,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'runCatalogSync' => 
      array (
        'name' => 'runCatalogSync',
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
        'startLine' => 126,
        'endLine' => 215,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'collectCatalogTokens' => 
      array (
        'name' => 'collectCatalogTokens',
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
 * @return list<string>
 */',
        'startLine' => 220,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'runInit' => 
      array (
        'name' => 'runInit',
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
        'startLine' => 239,
        'endLine' => 287,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'runFill' => 
      array (
        'name' => 'runFill',
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
        'startLine' => 289,
        'endLine' => 298,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'aliasName' => NULL,
      ),
      'invalidAction' => 
      array (
        'name' => 'invalidAction',
        'parameters' => 
        array (
          'action' => 
          array (
            'name' => 'action',
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
            'startLine' => 300,
            'endLine' => 300,
            'startColumn' => 36,
            'endColumn' => 49,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 300,
        'endLine' => 305,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataCommand',
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