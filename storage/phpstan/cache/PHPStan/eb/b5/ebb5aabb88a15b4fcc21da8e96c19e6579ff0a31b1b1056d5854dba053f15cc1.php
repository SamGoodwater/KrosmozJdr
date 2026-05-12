<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Process/PendingProcess.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Process\PendingProcess
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-554955d1fe402491b6921e48d4b11ef362a1ebef6f6c36b70a77142204ba967c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Process\\PendingProcess',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Process/PendingProcess.php',
      ),
    ),
    'namespace' => 'Illuminate\\Process',
    'name' => 'Illuminate\\Process\\PendingProcess',
    'shortName' => 'PendingProcess',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 439,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Conditionable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'factory' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'factory',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The process factory instance.
 *
 * @var \\Illuminate\\Process\\Factory
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'command' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'command',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The command to invoke the process.
 *
 * @var array<array-key, string>|string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 20,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'path' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'path',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The working directory of the process.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 17,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'timeout' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'timeout',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '60',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 45,
            'startTokenPos' => 96,
            'startFilePos' => 937,
            'endTokenPos' => 96,
            'endFilePos' => 938,
          ),
        ),
        'docComment' => '/**
 * The maximum number of seconds the process may run.
 *
 * @var int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'idleTimeout' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'idleTimeout',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The maximum number of seconds the process may go without returning output.
 *
 * @var int
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'environment' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'environment',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 59,
            'endLine' => 59,
            'startTokenPos' => 114,
            'startFilePos' => 1217,
            'endTokenPos' => 115,
            'endFilePos' => 1218,
          ),
        ),
        'docComment' => '/**
 * The additional environment variables for the process.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 59,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'input' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'input',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The standard input data that should be piped into the command.
 *
 * @var string|int|float|bool|resource|\\Traversable|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 66,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'quietly' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'quietly',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 133,
            'startFilePos' => 1526,
            'endTokenPos' => 133,
            'endFilePos' => 1530,
          ),
        ),
        'docComment' => '/**
 * Indicates whether output should be disabled for the process.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'tty' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'tty',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 80,
            'startTokenPos' => 144,
            'startFilePos' => 1640,
            'endTokenPos' => 144,
            'endFilePos' => 1644,
          ),
        ),
        'docComment' => '/**
 * Indicates if TTY mode should be enabled.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'options' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'options',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 155,
            'startFilePos' => 1766,
            'endTokenPos' => 156,
            'endFilePos' => 1767,
          ),
        ),
        'docComment' => '/**
 * The options that will be passed to "proc_open".
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fakeHandlers' => 
      array (
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'name' => 'fakeHandlers',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 94,
            'endLine' => 94,
            'startTokenPos' => 167,
            'startFilePos' => 1888,
            'endTokenPos' => 168,
            'endFilePos' => 1889,
          ),
        ),
        'docComment' => '/**
 * The registered fake handler callbacks.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 94,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 33,
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
          'factory' => 
          array (
            'name' => 'factory',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Process\\Factory',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 101,
            'endLine' => 101,
            'startColumn' => 33,
            'endColumn' => 48,
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
 * Create a new pending process instance.
 *
 * @param  \\Illuminate\\Process\\Factory  $factory
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'command' => 
      array (
        'name' => 'command',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 29,
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
 * Specify the command that will invoke the process.
 *
 * @param  array<array-key, string>|string  $command
 * @return $this
 */',
        'startLine' => 112,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'path' => 
      array (
        'name' => 'path',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 125,
            'endLine' => 125,
            'startColumn' => 26,
            'endColumn' => 37,
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
 * Specify the working directory of the process.
 *
 * @param  string  $path
 * @return $this
 */',
        'startLine' => 125,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'timeout' => 
      array (
        'name' => 'timeout',
        'parameters' => 
        array (
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 29,
            'endColumn' => 40,
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
 * Specify the maximum number of seconds the process may run.
 *
 * @param  int  $timeout
 * @return $this
 */',
        'startLine' => 138,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'idleTimeout' => 
      array (
        'name' => 'idleTimeout',
        'parameters' => 
        array (
          'timeout' => 
          array (
            'name' => 'timeout',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 151,
            'endLine' => 151,
            'startColumn' => 33,
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
 * Specify the maximum number of seconds a process may go without returning output.
 *
 * @param  int  $timeout
 * @return $this
 */',
        'startLine' => 151,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'forever' => 
      array (
        'name' => 'forever',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the process may run forever without timing out.
 *
 * @return $this
 */',
        'startLine' => 163,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'env' => 
      array (
        'name' => 'env',
        'parameters' => 
        array (
          'environment' => 
          array (
            'name' => 'environment',
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
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 25,
            'endColumn' => 42,
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
 * Set the additional environment variables for the process.
 *
 * @param  array  $environment
 * @return $this
 */',
        'startLine' => 176,
        'endLine' => 181,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'input' => 
      array (
        'name' => 'input',
        'parameters' => 
        array (
          'input' => 
          array (
            'name' => 'input',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 189,
            'endLine' => 189,
            'startColumn' => 27,
            'endColumn' => 32,
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
 * Set the standard input that should be provided when invoking the process.
 *
 * @param  \\Traversable|resource|string|int|float|bool|null  $input
 * @return $this
 */',
        'startLine' => 189,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'quietly' => 
      array (
        'name' => 'quietly',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Disable output for the process.
 *
 * @return $this
 */',
        'startLine' => 201,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'tty' => 
      array (
        'name' => 'tty',
        'parameters' => 
        array (
          'tty' => 
          array (
            'name' => 'tty',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 214,
                'endLine' => 214,
                'startTokenPos' => 453,
                'startFilePos' => 4258,
                'endTokenPos' => 453,
                'endFilePos' => 4261,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 214,
            'endLine' => 214,
            'startColumn' => 25,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enable TTY mode for the process.
 *
 * @param  bool  $tty
 * @return $this
 */',
        'startLine' => 214,
        'endLine' => 219,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'options' => 
      array (
        'name' => 'options',
        'parameters' => 
        array (
          'options' => 
          array (
            'name' => 'options',
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
            'startLine' => 227,
            'endLine' => 227,
            'startColumn' => 29,
            'endColumn' => 42,
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
 * Set the "proc_open" options that should be used when invoking the process.
 *
 * @param  array  $options
 * @return $this
 */',
        'startLine' => 227,
        'endLine' => 232,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'run' => 
      array (
        'name' => 'run',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 244,
                'endLine' => 244,
                'startTokenPos' => 523,
                'startFilePos' => 4969,
                'endTokenPos' => 523,
                'endFilePos' => 4972,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 25,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'output' => 
          array (
            'name' => 'output',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 244,
                'endLine' => 244,
                'startTokenPos' => 533,
                'startFilePos' => 4995,
                'endTokenPos' => 533,
                'endFilePos' => 4998,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 244,
            'endLine' => 244,
            'startColumn' => 60,
            'endColumn' => 83,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Run the process.
 *
 * @param  array<array-key, string>|string|null  $command
 * @param  callable|null  $output
 * @return \\Illuminate\\Contracts\\Process\\ProcessResult
 *
 * @throws \\Illuminate\\Process\\Exceptions\\ProcessTimedOutException
 * @throws \\RuntimeException
 */',
        'startLine' => 244,
        'endLine' => 263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'start' => 
      array (
        'name' => 'start',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 274,
                'endLine' => 274,
                'startTokenPos' => 747,
                'startFilePos' => 6143,
                'endTokenPos' => 747,
                'endFilePos' => 6146,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 274,
            'endLine' => 274,
            'startColumn' => 27,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'output' => 
          array (
            'name' => 'output',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 274,
                'endLine' => 274,
                'startTokenPos' => 757,
                'startFilePos' => 6169,
                'endTokenPos' => 757,
                'endFilePos' => 6172,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 274,
            'endLine' => 274,
            'startColumn' => 62,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Start the process in the background.
 *
 * @param  array<array-key, string>|string|null  $command
 * @param  callable|null  $output
 * @return \\Illuminate\\Process\\InvokedProcess
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 274,
        'endLine' => 289,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'toSymfonyProcess' => 
      array (
        'name' => 'toSymfonyProcess',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'array',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'string',
                      'isIdentifier' => true,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 297,
            'endLine' => 297,
            'startColumn' => 41,
            'endColumn' => 66,
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
 * Get a Symfony Process instance from the current pending command.
 *
 * @param  array<array-key, string>|string|null  $command
 * @return \\Symfony\\Component\\Process\\Process
 */',
        'startLine' => 297,
        'endLine' => 329,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'supportsTty' => 
      array (
        'name' => 'supportsTty',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine whether TTY is supported on the current operating system.
 *
 * @return bool
 */',
        'startLine' => 336,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'withFakeHandlers' => 
      array (
        'name' => 'withFakeHandlers',
        'parameters' => 
        array (
          'fakeHandlers' => 
          array (
            'name' => 'fakeHandlers',
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
            'startLine' => 347,
            'endLine' => 347,
            'startColumn' => 38,
            'endColumn' => 56,
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
 * Specify the fake process result handlers for the pending process.
 *
 * @param  array  $fakeHandlers
 * @return $this
 */',
        'startLine' => 347,
        'endLine' => 352,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'fakeFor' => 
      array (
        'name' => 'fakeFor',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
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
            'startLine' => 360,
            'endLine' => 360,
            'startColumn' => 32,
            'endColumn' => 46,
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
 * Get the fake handler for the given command, if applicable.
 *
 * @param  string  $command
 * @return \\Closure|null
 */',
        'startLine' => 360,
        'endLine' => 364,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'resolveSynchronousFake' => 
      array (
        'name' => 'resolveSynchronousFake',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
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
            'startLine' => 373,
            'endLine' => 373,
            'startColumn' => 47,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fake' => 
          array (
            'name' => 'fake',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 373,
            'endLine' => 373,
            'startColumn' => 64,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the given fake handler for a synchronous process.
 *
 * @param  string  $command
 * @param  \\Closure  $fake
 * @return mixed
 */',
        'startLine' => 373,
        'endLine' => 393,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
        'aliasName' => NULL,
      ),
      'resolveAsynchronousFake' => 
      array (
        'name' => 'resolveAsynchronousFake',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
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
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 48,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'output' => 
          array (
            'name' => 'output',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
                      'isIdentifier' => true,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 65,
            'endColumn' => 81,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fake' => 
          array (
            'name' => 'fake',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 84,
            'endColumn' => 96,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the given fake handler for an asynchronous process.
 *
 * @param  string  $command
 * @param  callable|null  $output
 * @param  \\Closure  $fake
 * @return \\Illuminate\\Process\\FakeInvokedProcess
 *
 * @throws \\LogicException
 */',
        'startLine' => 405,
        'endLine' => 438,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Process',
        'declaringClassName' => 'Illuminate\\Process\\PendingProcess',
        'implementingClassName' => 'Illuminate\\Process\\PendingProcess',
        'currentClassName' => 'Illuminate\\Process\\PendingProcess',
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