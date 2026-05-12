<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectPrepareCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectPrepareCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-8b6fe8fb95fc98555433f81bfcbc1acda7ad0662821edad4dddb56f658c0e327',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectPrepareCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
    'shortName' => 'ProjectPrepareCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Prépare l’environnement de dev : rebuild CSS, caches vues, documentation, migrations.
 *
 * @example php artisan project:prepare
 * @example php artisan project:prepare --clear
 * @example php artisan project:prepare --dev
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 61,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:prepare
        {--clear : Supprimer les artefacts de tests (PHPUnit, coverage, storage/framework/testing) avant la préparation}
        {--dev : Après la préparation, enchaîner project:optimize puis les serveurs (comme project:dev sans double préparation)}\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 31,
            'startTokenPos' => 90,
            'startFilePos' => 759,
            'endTokenPos' => 90,
            'endFilePos' => 1030,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 134,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Rebuild CSS, vide caches applicatifs/vues, régénère la doc, exécute les migrations.\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 99,
            'startFilePos' => 1063,
            'endTokenPos' => 99,
            'endFilePos' => 1151,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 119,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'projectRunService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'name' => 'projectRunService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Project\\ProjectRunService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 9,
        'endColumn' => 61,
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
          'projectRunService' => 
          array (
            'name' => 'projectRunService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Project\\ProjectRunService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 9,
            'endColumn' => 61,
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
        'startLine' => 23,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
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
        'startLine' => 35,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectPrepareCommand',
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