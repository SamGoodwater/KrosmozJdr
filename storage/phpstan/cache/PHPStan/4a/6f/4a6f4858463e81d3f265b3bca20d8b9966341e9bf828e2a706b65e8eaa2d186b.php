<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Commands/CleanCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\MediaLibrary\MediaCollections\Commands\CleanCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b03fec7539c947f673570b52730d6067be2e14274ba860995d9d3efedbaf2245-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Commands/CleanCommand.php',
      ),
    ),
    'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
    'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
    'shortName' => 'CleanCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 263,
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
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'media-library:clean {modelType?} {collectionName?} {disk?}
    {--dry-run : List files that will be removed without removing them},
    {--force : Force the operation to run when in production},
    {--rate-limit= : Limit the number of requests per second},
    {--delete-orphaned : Delete orphaned media items},
    {--skip-conversions : Do not remove deprecated conversions}\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 31,
            'startTokenPos' => 108,
            'startFilePos' => 936,
            'endTokenPos' => 108,
            'endFilePos' => 1313,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 31,
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
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Clean deprecated conversions and files without related model.\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 117,
            'startFilePos' => 1346,
            'endTokenPos' => 117,
            'endFilePos' => 1408,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 93,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'mediaRepository' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
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
        'startLine' => 35,
        'endLine' => 35,
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
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
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
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fileSystem' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'name' => 'fileSystem',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Contracts\\Filesystem\\Factory',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'isDryRun' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'name' => 'isDryRun',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 149,
            'startFilePos' => 1577,
            'endTokenPos' => 149,
            'endFilePos' => 1581,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'rateLimit' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'name' => 'rateLimit',
        'modifiers' => 2,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 43,
            'startTokenPos' => 160,
            'startFilePos' => 1616,
            'endTokenPos' => 160,
            'endFilePos' => 1616,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
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
            'startLine' => 46,
            'endLine' => 46,
            'startColumn' => 9,
            'endColumn' => 40,
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
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 9,
            'endColumn' => 40,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'fileSystem' => 
          array (
            'name' => 'fileSystem',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Contracts\\Filesystem\\Factory',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 2,
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
        'startLine' => 45,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
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
        'startLine' => 75,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'deleteOrphanedMediaItems' => 
      array (
        'name' => 'deleteOrphanedMediaItems',
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
        'startLine' => 98,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'getOrphanedMediaItems' => 
      array (
        'name' => 'getOrphanedMediaItems',
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
        'startLine' => 118,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'deleteFilesGeneratedForDeprecatedConversions' => 
      array (
        'name' => 'deleteFilesGeneratedForDeprecatedConversions',
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
        'startLine' => 129,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'deleteConversionFilesForDeprecatedConversions' => 
      array (
        'name' => 'deleteConversionFilesForDeprecatedConversions',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 144,
            'endLine' => 144,
            'startColumn' => 70,
            'endColumn' => 81,
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
        'startLine' => 144,
        'endLine' => 163,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'deleteDeprecatedResponsiveImages' => 
      array (
        'name' => 'deleteDeprecatedResponsiveImages',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 165,
            'endLine' => 165,
            'startColumn' => 57,
            'endColumn' => 68,
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
        'startLine' => 165,
        'endLine' => 186,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'shouldGenerateResponsiveImagesForOriginal' => 
      array (
        'name' => 'shouldGenerateResponsiveImagesForOriginal',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 188,
            'endLine' => 188,
            'startColumn' => 66,
            'endColumn' => 77,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 188,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'deleteOrphanedDirectories' => 
      array (
        'name' => 'deleteOrphanedDirectories',
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
        'startLine' => 208,
        'endLine' => 244,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'aliasName' => NULL,
      ),
      'markConversionAsRemoved' => 
      array (
        'name' => 'markConversionAsRemoved',
        'parameters' => 
        array (
          'media' => 
          array (
            'name' => 'media',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 48,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversionPath' => 
          array (
            'name' => 'conversionPath',
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
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 62,
            'endColumn' => 83,
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
        'startLine' => 246,
        'endLine' => 262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Commands\\CleanCommand',
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