<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectUpdateCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-db7ae008c5e74b9af75c0fb868ca8f1c279a08007858cca27aaa4fadcf9df7b0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectUpdateCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
    'shortName' => 'ProjectUpdateCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Mise à jour des entités en base dont auto_update = true.
 *
 * Ne crée pas de nouvelles entités ; met à jour uniquement celles déjà présentes
 * et marquées pour mise à jour automatique.
 *
 * Compatible exécution longue (set_time_limit(0), DB::reconnect entre chunks).
 * Vide la queue avant l\'update. Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:data:sync
 * @example php artisan project:update
 * @example php artisan project:data:sync --entity=monster --dry-run
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 36,
    'endLine' => 241,
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
      'ENTITY_CONFIG' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'name' => 'ENTITY_CONFIG',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'class\' => [\'alias\' => \'class\', \'model\' => \\App\\Models\\Entity\\Breed::class, \'idColumn\' => \'dofusdb_id\'], \'spell\' => [\'alias\' => \'spell\', \'model\' => \\App\\Models\\Entity\\Spell::class, \'idColumn\' => \'dofusdb_id\'], \'monster\' => [\'alias\' => \'monster\', \'model\' => \\App\\Models\\Entity\\Monster::class, \'idColumn\' => \'dofusdb_id\'], \'resource\' => [\'alias\' => \'resource\', \'model\' => \\App\\Models\\Entity\\Resource::class, \'idColumn\' => \'dofusdb_id\'], \'consumable\' => [\'alias\' => \'consumable\', \'model\' => \\App\\Models\\Entity\\Consumable::class, \'idColumn\' => \'dofusdb_id\'], \'item\' => [\'alias\' => \'item\', \'model\' => \\App\\Models\\Entity\\Item::class, \'idColumn\' => \'dofusdb_id\'], \'panoply\' => [\'alias\' => \'panoply\', \'model\' => \\App\\Models\\Entity\\Panoply::class, \'idColumn\' => \'dofusdb_id\']]',
          'attributes' => 
          array (
            'startLine' => 53,
            'endLine' => 89,
            'startTokenPos' => 150,
            'startFilePos' => 1930,
            'endTokenPos' => 376,
            'endFilePos' => 2956,
          ),
        ),
        'docComment' => '/** Mapping entité Krosmoz → alias scrapping:run et modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'IDS_CHUNK_SIZE' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'name' => 'IDS_CHUNK_SIZE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '100',
          'attributes' => 
          array (
            'startLine' => 91,
            'endLine' => 91,
            'startTokenPos' => 387,
            'startFilePos' => 2995,
            'endTokenPos' => 387,
            'endFilePos' => 2997,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 91,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
    ),
    'immediateProperties' => 
    array (
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'project:update\']',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 117,
            'startFilePos' => 1262,
            'endTokenPos' => 119,
            'endFilePos' => 1279,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:data:sync
        {--entity= : Entités (virgules) : breed|class, spell, monster, panoply, resource, item, consumable}
        {--noimage : Ne pas télécharger les images}
        {--skip-cache : Ignorer le cache HTTP}
        {--dry-run : Simuler sans écrire}
        {--skip-clear-queue : Ne pas vider la queue avant la mise à jour}
        {--skip-notify : Ne pas notifier les admin à la fin}\'',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 48,
            'startTokenPos' => 128,
            'startFilePos' => 1310,
            'endTokenPos' => 128,
            'endFilePos' => 1718,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 63,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Met à jour les entités en base avec auto_update=true depuis DofusDB\'',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 50,
            'startTokenPos' => 137,
            'startFilePos' => 1751,
            'endTokenPos' => 137,
            'endFilePos' => 1821,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 101,
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
        'startLine' => 93,
        'endLine' => 197,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'aliasName' => NULL,
      ),
      'getAutoUpdateIds' => 
      array (
        'name' => 'getAutoUpdateIds',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
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
            'startLine' => 202,
            'endLine' => 202,
            'startColumn' => 39,
            'endColumn' => 51,
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
 * @return list<int>
 */',
        'startLine' => 202,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'aliasName' => NULL,
      ),
      'clearQueue' => 
      array (
        'name' => 'clearQueue',
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
        'startLine' => 230,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectUpdateCommand',
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