<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel-lang/publisher/src/Console/Reset.php-PHPStan\BetterReflection\Reflection\ReflectionClass-LaravelLang\Publisher\Console\Reset
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-c637c09ccd337aae0a770f5620e3c16cd0be0b00f98b8cce2ee23584b6cfd692-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'LaravelLang\\Publisher\\Console\\Reset',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel-lang/publisher/src/Console/Reset.php',
      ),
    ),
    'namespace' => 'LaravelLang\\Publisher\\Console',
    'name' => 'LaravelLang\\Publisher\\Console\\Reset',
    'shortName' => 'Reset',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 39,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'LaravelLang\\Publisher\\Console\\Base',
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
        'declaringClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'implementingClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'lang:reset\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 47,
            'startFilePos' => 585,
            'endTokenPos' => 47,
            'endFilePos' => 596,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'implementingClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Resets installed localizations.\'',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 56,
            'startFilePos' => 629,
            'endTokenPos' => 56,
            'endFilePos' => 661,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 63,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'question' => 
      array (
        'declaringClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'implementingClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'name' => 'question',
        'modifiers' => 2,
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
                  'name' => 'string',
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
        'default' => 
        array (
          'code' => '\'Are you sure you want to reset localization files?\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 68,
            'startFilePos' => 699,
            'endTokenPos' => 68,
            'endFilePos' => 750,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 87,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'processor' => 
      array (
        'declaringClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'implementingClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'name' => 'processor',
        'modifiers' => 2,
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
                  'name' => 'LaravelLang\\Publisher\\Processors\\Processor',
                  'isIdentifier' => false,
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
        'default' => 
        array (
          'code' => '\\LaravelLang\\Publisher\\Processors\\Reset::class',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 81,
            'startFilePos' => 798,
            'endTokenPos' => 83,
            'endFilePos' => 818,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 66,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 33,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'LaravelLang\\Publisher\\Console',
        'declaringClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'implementingClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
        'currentClassName' => 'LaravelLang\\Publisher\\Console\\Reset',
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