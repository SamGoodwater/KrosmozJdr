<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Console/Scheduling/ScheduleClearCacheCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Console\Scheduling\ScheduleClearCacheCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d6cd43e6c4c8334f8477b3ddf04565c9b7ae80c9946eb4b8d971fbbcb772d50c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Console/Scheduling/ScheduleClearCacheCommand.php',
      ),
    ),
    'namespace' => 'Illuminate\\Console\\Scheduling',
    'name' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
    'shortName' => 'ScheduleClearCacheCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'Symfony\\Component\\Console\\Attribute\\AsCommand',
        'isRepeated' => false,
        'arguments' => 
        array (
          'name' => 
          array (
            'code' => '\'schedule:clear-cache\'',
            'attributes' => 
            array (
              'startLine' => 8,
              'endLine' => 8,
              'startTokenPos' => 23,
              'startFilePos' => 151,
              'endTokenPos' => 23,
              'endFilePos' => 172,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 8,
    'endLine' => 49,
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
      'name' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'implementingClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'name' => 'name',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'schedule:clear-cache\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 45,
            'startFilePos' => 323,
            'endTokenPos' => 45,
            'endFilePos' => 344,
          ),
        ),
        'docComment' => '/**
 * The console command name.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'implementingClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Delete the cached mutex files created by scheduler\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 56,
            'startFilePos' => 459,
            'endTokenPos' => 56,
            'endFilePos' => 510,
          ),
        ),
        'docComment' => '/**
 * The console command description.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 82,
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
          'schedule' => 
          array (
            'name' => 'schedule',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Console\\Scheduling\\Schedule',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 28,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the console command.
 *
 * @param  \\Illuminate\\Console\\Scheduling\\Schedule  $schedule
 * @return void
 */',
        'startLine' => 31,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Console\\Scheduling',
        'declaringClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'implementingClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
        'currentClassName' => 'Illuminate\\Console\\Scheduling\\ScheduleClearCacheCommand',
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