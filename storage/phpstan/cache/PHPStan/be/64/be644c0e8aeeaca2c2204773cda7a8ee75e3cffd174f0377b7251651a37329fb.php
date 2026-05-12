<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Console/KeyGenerateCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Foundation\Console\KeyGenerateCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-025134768a2df75a488819b97aed5d8968f4658eea3370df350ed4e62aca4690-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Console/KeyGenerateCommand.php',
      ),
    ),
    'namespace' => 'Illuminate\\Foundation\\Console',
    'name' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
    'shortName' => 'KeyGenerateCommand',
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
            'code' => '\'key:generate\'',
            'attributes' => 
            array (
              'startLine' => 10,
              'endLine' => 10,
              'startTokenPos' => 33,
              'startFilePos' => 229,
              'endTokenPos' => 33,
              'endFilePos' => 242,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 10,
    'endLine' => 129,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Console\\ConfirmableTrait',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'key:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 22,
            'startTokenPos' => 60,
            'startFilePos' => 439,
            'endTokenPos' => 60,
            'endFilePos' => 604,
          ),
        ),
        'docComment' => '/**
 * The name and signature of the console command.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 79,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Set the application key\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 71,
            'startFilePos' => 719,
            'endTokenPos' => 71,
            'endFilePos' => 743,
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
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 55,
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
        'startLine' => 36,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Console',
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'currentClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'aliasName' => NULL,
      ),
      'generateRandomKey' => 
      array (
        'name' => 'generateRandomKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Generate a random key for the application.
 *
 * @return string
 */',
        'startLine' => 61,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Console',
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'currentClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'aliasName' => NULL,
      ),
      'setKeyInEnvironmentFile' => 
      array (
        'name' => 'setKeyInEnvironmentFile',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 48,
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
 * Set the application key in the environment file.
 *
 * @param  string  $key
 * @return bool
 */',
        'startLine' => 74,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Console',
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'currentClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'aliasName' => NULL,
      ),
      'writeNewEnvironmentFileWith' => 
      array (
        'name' => 'writeNewEnvironmentFileWith',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 95,
            'endLine' => 95,
            'startColumn' => 52,
            'endColumn' => 55,
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
 * Write a new environment file with the given key.
 *
 * @param  string  $key
 * @return bool
 */',
        'startLine' => 95,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Console',
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'currentClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'aliasName' => NULL,
      ),
      'keyReplacementPattern' => 
      array (
        'name' => 'keyReplacementPattern',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a regex pattern that will match env APP_KEY with any random key.
 *
 * @return string
 */',
        'startLine' => 123,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Foundation\\Console',
        'declaringClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'implementingClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
        'currentClassName' => 'Illuminate\\Foundation\\Console\\KeyGenerateCommand',
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