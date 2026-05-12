<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectBackupCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-23371e0936af8af2f350774e063d558657602858dade0e88a49867b7a3b53ca7',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectBackupCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
    'shortName' => 'ProjectBackupCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Sauvegarde MySQL/MariaDB/SQLite + archive compressée de storage/app (hors backups), rotation ~1 mois.
 *
 * @example php artisan project:backup
 * @example php artisan project:backup --no-storage
 * @example php artisan project:backup --retention-days=14
 * @example php artisan project:backup --prune-only --dry-run
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 98,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:backup
        {--no-database : Exclure le dump SQL (gzip)}
        {--no-storage : Exclure l’archive storage/app}
        {--path= : Répertoire des sauvegardes (défaut : config ou storage/app/backups)}
        {--retention-days= : Jours de conservation des fichiers (défaut : config, 30)}
        {--no-prune : Ne pas supprimer les sauvegardes plus anciennes que la rétention}
        {--prune-only : Exécuter uniquement la purge (pas de nouvelle sauvegarde)}
        {--dry-run : Avec --prune-only ou --no-prune absent : afficher les fichiers qui seraient supprimés}\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 28,
            'startTokenPos' => 52,
            'startFilePos' => 620,
            'endTokenPos' => 52,
            'endFilePos' => 1205,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
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
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Sauvegarde BDD + storage/app compressés, purge des fichiers > rétention (défaut 30 j)\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 61,
            'startFilePos' => 1238,
            'endTokenPos' => 61,
            'endFilePos' => 1327,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 120,
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
        'startLine' => 32,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'aliasName' => NULL,
      ),
      'makeService' => 
      array (
        'name' => 'makeService',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Project\\ProjectBackupService',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 80,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectBackupCommand',
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