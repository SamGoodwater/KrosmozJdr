<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/tinker/src/Console/TinkerCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Laravel\Tinker\Console\TinkerCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-549df449c62574a109938be6be0061e767de5ecced6004a249702c7fe4b02826-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/tinker/src/Console/TinkerCommand.php',
      ),
    ),
    'namespace' => 'Laravel\\Tinker\\Console',
    'name' => 'Laravel\\Tinker\\Console\\TinkerCommand',
    'shortName' => 'TinkerCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 171,
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
      'commandWhitelist' => 
      array (
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'name' => 'commandWhitelist',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'clear-compiled\', \'down\', \'env\', \'inspire\', \'migrate\', \'migrate:install\', \'optimize\', \'up\']',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 23,
            'startTokenPos' => 65,
            'startFilePos' => 483,
            'endTokenPos' => 91,
            'endFilePos' => 589,
          ),
        ),
        'docComment' => '/**
 * Artisan commands to include in the tinker shell.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'name' => 
      array (
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'name' => 'name',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'tinker\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 102,
            'startFilePos' => 690,
            'endTokenPos' => 102,
            'endFilePos' => 697,
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
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Interact with your application\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 113,
            'startFilePos' => 812,
            'endTokenPos' => 113,
            'endFilePos' => 843,
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
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 62,
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
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute the console command.
 *
 * @return int
 */',
        'startLine' => 44,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Laravel\\Tinker\\Console',
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'currentClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'aliasName' => NULL,
      ),
      'getCommands' => 
      array (
        'name' => 'getCommands',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get artisan commands to pass through to PsySH.
 *
 * @return array
 */',
        'startLine' => 97,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Tinker\\Console',
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'currentClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'aliasName' => NULL,
      ),
      'getCasters' => 
      array (
        'name' => 'getCasters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get an array of Laravel tailored casters.
 *
 * @return array
 */',
        'startLine' => 123,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Tinker\\Console',
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'currentClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'aliasName' => NULL,
      ),
      'getArguments' => 
      array (
        'name' => 'getArguments',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the console command arguments.
 *
 * @return array
 */',
        'startLine' => 153,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Tinker\\Console',
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'currentClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'aliasName' => NULL,
      ),
      'getOptions' => 
      array (
        'name' => 'getOptions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the console command options.
 *
 * @return array
 */',
        'startLine' => 165,
        'endLine' => 170,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Laravel\\Tinker\\Console',
        'declaringClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'implementingClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
        'currentClassName' => 'Laravel\\Tinker\\Console\\TinkerCommand',
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