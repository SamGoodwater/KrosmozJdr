<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectOptimizeCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectOptimizeCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-9a6dac75d09474aaf57233eaa439428676796a85780909133ebe28b5dfcd48ae',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectOptimizeCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
    'shortName' => 'ProjectOptimizeCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Optimisation locale : optimize:clear, IDE Helper, dump-autoload, optimize.
 *
 * @example php artisan project:optimize
 * @example php artisan project:optimize --ide-only
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 59,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:optimize
        {--clear-only : optimize:clear Laravel uniquement}
        {--ide-only : IDE Helper + dump-autoload uniquement}\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 30,
            'startTokenPos' => 90,
            'startFilePos' => 705,
            'endTokenPos' => 90,
            'endFilePos' => 842,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 62,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'optimize:clear → ide-helper → dump-autoload → optimize (pipeline dev).\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 99,
            'startFilePos' => 875,
            'endTokenPos' => 99,
            'endFilePos' => 952,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 108,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'projectRunService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
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
        'startLine' => 23,
        'endLine' => 23,
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
            'startLine' => 23,
            'endLine' => 23,
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
        'startLine' => 22,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
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
        'startLine' => 34,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectOptimizeCommand',
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