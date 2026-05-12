<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectRefreshCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectRefreshCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-2df43ed9e28452f8b27a65c90d9c0b0b30f21d1f6ec774fe31902ae32b81b1a4',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectRefreshCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
    'shortName' => 'ProjectRefreshCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Réinstallation lourde : dépendances, base vide, caches. À utiliser en local uniquement.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 74,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:refresh
        {--hard : Exécuter setup --refresh (vendor + node_modules) avant migrate:fresh}
        {--without-seed : Ne pas passer --seed à migrate:fresh}
        {--force : Ne pas demander confirmation (scripts/CI)}\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 25,
            'startTokenPos' => 80,
            'startFilePos' => 532,
            'endTokenPos' => 80,
            'endFilePos' => 764,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 25,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Repartir sur une base propre : optionnellement réinstaller les libs, migrate:fresh, caches\'',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 89,
            'startFilePos' => 797,
            'endTokenPos' => 89,
            'endFilePos' => 889,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 123,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'projectRunService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
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
        'startLine' => 17,
        'endLine' => 17,
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
            'startLine' => 17,
            'endLine' => 17,
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
        'startLine' => 16,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
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
        'startLine' => 29,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectRefreshCommand',
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