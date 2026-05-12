<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/Conversions/Commands/RegenerateCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\MediaLibrary\Conversions\Commands\RegenerateCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-045917c05e56246b6e506e687da03d98d5ce1ef6ee6a0856180711b05862691d-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/Conversions/Commands/RegenerateCommand.php',
      ),
    ),
    'namespace' => 'Spatie\\MediaLibrary\\Conversions\\Commands',
    'name' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
    'shortName' => 'RegenerateCommand',
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
    'endLine' => 123,
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
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'media-library:regenerate {modelType?} {--ids=*}
    {--only=* : Regenerate specific conversions}
    {--starting-from-id= : Regenerate media with an id equal to or higher than the provided value}
    {--X|exclude-starting-id : Exclude the provided id when regenerating from a specific id}
    {--only-missing : Regenerate only missing conversions}
    {--with-responsive-images : Regenerate responsive images}
    {--force : Force the operation to run when in production}
    {--queue-all : Queue all conversions, even non-queued ones}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 26,
            'startTokenPos' => 73,
            'startFilePos' => 506,
            'endTokenPos' => 73,
            'endFilePos' => 1042,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 65,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Regenerate the derived images of media\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 82,
            'startFilePos' => 1075,
            'endTokenPos' => 82,
            'endFilePos' => 1114,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 70,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'mediaRepository' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'name' => 'mediaRepository',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fileManipulator' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'name' => 'fileManipulator',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\Conversions\\FileManipulator',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'errorMessages' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'name' => 'errorMessages',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 107,
            'startFilePos' => 1253,
            'endTokenPos' => 108,
            'endFilePos' => 1254,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 40,
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
          'mediaRepository' => 
          array (
            'name' => 'mediaRepository',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\MediaCollections\\MediaRepository',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 28,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'fileManipulator' => 
          array (
            'name' => 'fileManipulator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\Conversions\\FileManipulator',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 36,
            'endLine' => 36,
            'startColumn' => 62,
            'endColumn' => 93,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
        'startLine' => 36,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\Conversions\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'aliasName' => NULL,
      ),
      'getMediaToBeRegenerated' => 
      array (
        'name' => 'getMediaToBeRegenerated',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Support\\LazyCollection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 85,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\Conversions\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'aliasName' => NULL,
      ),
      'getMediaIds' => 
      array (
        'name' => 'getMediaIds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 109,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\Conversions\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\Conversions\\Commands\\RegenerateCommand',
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