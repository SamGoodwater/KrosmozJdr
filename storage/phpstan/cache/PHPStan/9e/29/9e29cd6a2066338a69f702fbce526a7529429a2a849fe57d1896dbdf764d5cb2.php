<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Commands/ClearCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\MediaLibrary\MediaCollections\Commands\ClearCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-a109bd7aa91b00c0a36b7111082f3bfa169e9a9d7f3b3d2cd8c0b87dd736d656-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Commands/ClearCommand.php',
      ),
    ),
    'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
    'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
    'shortName' => 'ClearCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 67,
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
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'media-library:clear {modelType?} {collectionName?}
    {-- force : Force the operation to run when in production}\'',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 16,
            'startTokenPos' => 53,
            'startFilePos' => 382,
            'endTokenPos' => 53,
            'endFilePos' => 496,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 64,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Delete all items in a media collection.\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 62,
            'startFilePos' => 529,
            'endTokenPos' => 62,
            'endFilePos' => 569,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 71,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'mediaRepository' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
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
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 47,
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 28,
            'endColumn' => 59,
            'parameterIndex' => 0,
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
        'startLine' => 22,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'aliasName' => NULL,
      ),
      'getMediaItems' => 
      array (
        'name' => 'getMediaItems',
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
        'docComment' => '/** @return LazyCollection<int, Media> */',
        'startLine' => 45,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\ClearCommand',
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