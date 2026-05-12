<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-ide-helper/src/Console/EloquentCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Barryvdh\LaravelIdeHelper\Console\EloquentCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-718126f517e8239d46b415364f1586e12d52040b93e299296d260161b513fb94-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../barryvdh/laravel-ide-helper/src/Console/EloquentCommand.php',
      ),
    ),
    'namespace' => 'Barryvdh\\LaravelIdeHelper\\Console',
    'name' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
    'shortName' => 'EloquentCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A command to add \\Eloquent mixin to Eloquent\\Model
 *
 * @author Charles A. Peterson <artistan@gmail.com>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 62,
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
        'declaringClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'implementingClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'name' => 'name',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'ide-helper:eloquent\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 44,
            'startFilePos' => 760,
            'endTokenPos' => 44,
            'endFilePos' => 780,
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
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 44,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'files' => 
      array (
        'declaringClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'implementingClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'name' => 'files',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * @var Filesystem $files
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'implementingClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Add \\Eloquent helper to \\Eloquent\\Model\'',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 62,
            'startFilePos' => 964,
            'endTokenPos' => 62,
            'endFilePos' => 1004,
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
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 71,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'files' => 
          array (
            'name' => 'files',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Filesystem\\Filesystem',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 33,
            'endColumn' => 49,
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
 * @param Filesystem $files
 */',
        'startLine' => 47,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Barryvdh\\LaravelIdeHelper\\Console',
        'declaringClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'implementingClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'currentClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the console command.
 *
 * @return void
 */',
        'startLine' => 58,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Barryvdh\\LaravelIdeHelper\\Console',
        'declaringClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'implementingClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
        'currentClassName' => 'Barryvdh\\LaravelIdeHelper\\Console\\EloquentCommand',
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