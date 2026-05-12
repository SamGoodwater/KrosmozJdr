<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Queue/Console/ListFailedCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Queue\Console\ListFailedCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-64a9b3842e8a79c45a6b1a2063225d95f13ad61290f83a2f56a0e9575e6b82a0-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Queue/Console/ListFailedCommand.php',
      ),
    ),
    'namespace' => 'Illuminate\\Queue\\Console',
    'name' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
    'shortName' => 'ListFailedCommand',
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
            'code' => '\'queue:failed\'',
            'attributes' => 
            array (
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 33,
              'startFilePos' => 209,
              'endTokenPos' => 33,
              'endFilePos' => 222,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 10,
    'endLine' => 125,
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
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'name' => 'name',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'queue:failed\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 55,
            'startFilePos' => 365,
            'endTokenPos' => 55,
            'endFilePos' => 378,
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
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'List all of the failed queue jobs\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 66,
            'startFilePos' => 493,
            'endTokenPos' => 66,
            'endFilePos' => 527,
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
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 65,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'headers' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'name' => 'headers',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'ID\', \'Connection\', \'Queue\', \'Class\', \'Failed At\']',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 77,
            'startFilePos' => 642,
            'endTokenPos' => 91,
            'endFilePos' => 692,
          ),
        ),
        'docComment' => '/**
 * The table headers for the command.
 *
 * @var string[]
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 77,
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
 * @return void
 */',
        'startLine' => 39,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'aliasName' => NULL,
      ),
      'getFailedJobs' => 
      array (
        'name' => 'getFailedJobs',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Compile the failed jobs into a displayable format.
 *
 * @return array
 */',
        'startLine' => 55,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'aliasName' => NULL,
      ),
      'parseFailedJob' => 
      array (
        'name' => 'parseFailedJob',
        'parameters' => 
        array (
          'failed' => 
          array (
            'name' => 'failed',
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
            'startLine' => 71,
            'endLine' => 71,
            'startColumn' => 39,
            'endColumn' => 51,
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
 * Parse the failed job row.
 *
 * @param  array  $failed
 * @return array
 */',
        'startLine' => 71,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'aliasName' => NULL,
      ),
      'extractJobName' => 
      array (
        'name' => 'extractJobName',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 37,
            'endColumn' => 44,
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
 * Extract the failed job name from payload.
 *
 * @param  string  $payload
 * @return string|null
 */',
        'startLine' => 86,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'aliasName' => NULL,
      ),
      'matchJobName' => 
      array (
        'name' => 'matchJobName',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 103,
            'endLine' => 103,
            'startColumn' => 37,
            'endColumn' => 44,
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
 * Match the job name from the payload.
 *
 * @param  array  $payload
 * @return string|null
 */',
        'startLine' => 103,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'aliasName' => NULL,
      ),
      'displayFailedJobs' => 
      array (
        'name' => 'displayFailedJobs',
        'parameters' => 
        array (
          'jobs' => 
          array (
            'name' => 'jobs',
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
            'startLine' => 116,
            'endLine' => 116,
            'startColumn' => 42,
            'endColumn' => 52,
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
 * Display the failed jobs in the console.
 *
 * @param  array  $jobs
 * @return void
 */',
        'startLine' => 116,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Console',
        'declaringClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'implementingClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
        'currentClassName' => 'Illuminate\\Queue\\Console\\ListFailedCommand',
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