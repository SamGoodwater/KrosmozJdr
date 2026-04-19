<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Models/Media.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\MediaLibrary\MediaCollections\Models\Media
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-cc30235b1154b4b8f017a599e93adbeacd51c9edf7b20f784e7ee7c78a750f8c-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Models/Media.php',
      ),
    ),
    'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
    'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
    'shortName' => 'Media',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property string $uuid
 * @property string $model_type
 * @property string|int $model_id
 * @property string $collection_name
 * @property string $name
 * @property string $file_name
 * @property string $mime_type
 * @property string $disk
 * @property string $conversions_disk
 * @property string $type
 * @property string $extension
 * @property-read string $human_readable_size
 * @property-read string $preview_url
 * @property-read string $original_url
 * @property int $size
 * @property ?int $order_column
 * @property array $manipulations
 * @property array $custom_properties
 * @property array $generated_conversions
 * @property array $responsive_images
 * @property-read ?\\Illuminate\\Support\\Carbon $created_at
 * @property-read ?\\Illuminate\\Support\\Carbon $updated_at
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 66,
    'endLine' => 595,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Mail\\Attachable',
      1 => 'Illuminate\\Contracts\\Support\\Htmlable',
      2 => 'Illuminate\\Contracts\\Support\\Responsable',
    ),
    'traitClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Concerns\\CustomMediaProperties',
      1 => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Concerns\\HasUuid',
      2 => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Concerns\\IsSorted',
    ),
    'immediateConstants' => 
    array (
      'TYPE_OTHER' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'TYPE_OTHER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'other\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 245,
            'startFilePos' => 2870,
            'endTokenPos' => 245,
            'endFilePos' => 2876,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'media\'',
          'attributes' => 
          array (
            'startLine' => 72,
            'endLine' => 72,
            'startTokenPos' => 234,
            'startFilePos' => 2830,
            'endTokenPos' => 234,
            'endFilePos' => 2836,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 72,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'guarded' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'guarded',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 76,
            'endLine' => 76,
            'startTokenPos' => 254,
            'startFilePos' => 2905,
            'endTokenPos' => 255,
            'endFilePos' => 2906,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 76,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'appends' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'appends',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'original_url\', \'preview_url\']',
          'attributes' => 
          array (
            'startLine' => 78,
            'endLine' => 78,
            'startTokenPos' => 264,
            'startFilePos' => 2935,
            'endTokenPos' => 269,
            'endFilePos' => 2965,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 78,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 57,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'manipulations\' => \'array\', \'custom_properties\' => \'array\', \'generated_conversions\' => \'array\', \'responsive_images\' => \'array\']',
          'attributes' => 
          array (
            'startLine' => 80,
            'endLine' => 85,
            'startTokenPos' => 278,
            'startFilePos' => 2992,
            'endTokenPos' => 308,
            'endFilePos' => 3158,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 80,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'streamChunkSize' => 
      array (
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'name' => 'streamChunkSize',
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
          'code' => '1024 * 1024',
          'attributes' => 
          array (
            'startLine' => 87,
            'endLine' => 87,
            'startTokenPos' => 320,
            'startFilePos' => 3200,
            'endTokenPos' => 324,
            'endFilePos' => 3210,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 87,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 51,
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
      'newCollection' => 
      array (
        'name' => 'newCollection',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 90,
                'endLine' => 90,
                'startTokenPos' => 344,
                'startFilePos' => 3341,
                'endTokenPos' => 345,
                'endFilePos' => 3342,
              ),
            ),
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
            'startLine' => 90,
            'endLine' => 90,
            'startColumn' => 35,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Collections\\MediaCollection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @phpstan-ignore method.childReturnType */',
        'startLine' => 90,
        'endLine' => 93,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'model' => 
      array (
        'name' => 'model',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\MorphTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 95,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getFullUrl' => 
      array (
        'name' => 'getFullUrl',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 100,
                'endLine' => 100,
                'startTokenPos' => 403,
                'startFilePos' => 3592,
                'endTokenPos' => 403,
                'endFilePos' => 3593,
              ),
            ),
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
            'startLine' => 100,
            'endLine' => 100,
            'startColumn' => 32,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 100,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getUrl' => 
      array (
        'name' => 'getUrl',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 105,
                'endLine' => 105,
                'startTokenPos' => 438,
                'startFilePos' => 3721,
                'endTokenPos' => 438,
                'endFilePos' => 3722,
              ),
            ),
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
            'startLine' => 105,
            'endLine' => 105,
            'startColumn' => 28,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 105,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getTemporaryUrl' => 
      array (
        'name' => 'getTemporaryUrl',
        'parameters' => 
        array (
          'expiration' => 
          array (
            'name' => 'expiration',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 112,
                'endLine' => 112,
                'startTokenPos' => 485,
                'startFilePos' => 3941,
                'endTokenPos' => 485,
                'endFilePos' => 3944,
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
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
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
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 37,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 112,
                'endLine' => 112,
                'startTokenPos' => 494,
                'startFilePos' => 3972,
                'endTokenPos' => 494,
                'endFilePos' => 3973,
              ),
            ),
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
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 76,
            'endColumn' => 102,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 112,
                'endLine' => 112,
                'startTokenPos' => 503,
                'startFilePos' => 3993,
                'endTokenPos' => 504,
                'endFilePos' => 3994,
              ),
            ),
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
            'startLine' => 112,
            'endLine' => 112,
            'startColumn' => 105,
            'endColumn' => 123,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 112,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getPath' => 
      array (
        'name' => 'getPath',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 120,
                'endLine' => 120,
                'startTokenPos' => 573,
                'startFilePos' => 4319,
                'endTokenPos' => 573,
                'endFilePos' => 4320,
              ),
            ),
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
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 29,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 120,
        'endLine' => 125,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getPathRelativeToRoot' => 
      array (
        'name' => 'getPathRelativeToRoot',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 127,
                'endLine' => 127,
                'startTokenPos' => 616,
                'startFilePos' => 4518,
                'endTokenPos' => 616,
                'endFilePos' => 4519,
              ),
            ),
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
            'startLine' => 127,
            'endLine' => 127,
            'startColumn' => 43,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 127,
        'endLine' => 130,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getUrlGenerator' => 
      array (
        'name' => 'getUrlGenerator',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
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
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 37,
            'endColumn' => 58,
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
            'name' => 'Spatie\\MediaLibrary\\Support\\UrlGenerator\\UrlGenerator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 132,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getAvailableUrl' => 
      array (
        'name' => 'getAvailableUrl',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 137,
            'endLine' => 137,
            'startColumn' => 37,
            'endColumn' => 58,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 137,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getAvailableTemporaryUrl' => 
      array (
        'name' => 'getAvailableTemporaryUrl',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 46,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'expiration' => 
          array (
            'name' => 'expiration',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 150,
                'endLine' => 150,
                'startTokenPos' => 760,
                'startFilePos' => 5229,
                'endTokenPos' => 760,
                'endFilePos' => 5232,
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
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
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
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 70,
            'endColumn' => 106,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 150,
                'endLine' => 150,
                'startTokenPos' => 769,
                'startFilePos' => 5252,
                'endTokenPos' => 770,
                'endFilePos' => 5253,
              ),
            ),
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
            'startLine' => 150,
            'endLine' => 150,
            'startColumn' => 109,
            'endColumn' => 127,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 150,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getDownloadFilename' => 
      array (
        'name' => 'getDownloadFilename',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 163,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getAvailableFullUrl' => 
      array (
        'name' => 'getAvailableFullUrl',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 168,
            'endLine' => 168,
            'startColumn' => 41,
            'endColumn' => 62,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 168,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getAvailablePath' => 
      array (
        'name' => 'getAvailablePath',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 181,
            'endLine' => 181,
            'startColumn' => 38,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 181,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getAvailablePathRelativeToRoot' => 
      array (
        'name' => 'getAvailablePathRelativeToRoot',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 194,
            'endLine' => 194,
            'startColumn' => 52,
            'endColumn' => 73,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 194,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'type' => 
      array (
        'name' => 'type',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 207,
        'endLine' => 220,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getTypeFromExtension' => 
      array (
        'name' => 'getTypeFromExtension',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 222,
        'endLine' => 229,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getTypeFromMime' => 
      array (
        'name' => 'getTypeFromMime',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 231,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'extension' => 
      array (
        'name' => 'extension',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 240,
        'endLine' => 243,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'humanReadableSize' => 
      array (
        'name' => 'humanReadableSize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 245,
        'endLine' => 248,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getDiskDriverName' => 
      array (
        'name' => 'getDiskDriverName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 250,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getConversionsDiskDriverName' => 
      array (
        'name' => 'getConversionsDiskDriverName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 255,
        'endLine' => 260,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'hasCustomProperty' => 
      array (
        'name' => 'hasCustomProperty',
        'parameters' => 
        array (
          'propertyName' => 
          array (
            'name' => 'propertyName',
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
            'startLine' => 262,
            'endLine' => 262,
            'startColumn' => 39,
            'endColumn' => 58,
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
        'startLine' => 262,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getCustomProperty' => 
      array (
        'name' => 'getCustomProperty',
        'parameters' => 
        array (
          'propertyName' => 
          array (
            'name' => 'propertyName',
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
            'startLine' => 272,
            'endLine' => 272,
            'startColumn' => 39,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 272,
                'endLine' => 272,
                'startTokenPos' => 1462,
                'startFilePos' => 8592,
                'endTokenPos' => 1462,
                'endFilePos' => 8595,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 272,
            'endLine' => 272,
            'startColumn' => 61,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the value of custom property with the given name.
 *
 * @param  mixed  $default
 */',
        'startLine' => 272,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'setCustomProperty' => 
      array (
        'name' => 'setCustomProperty',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 281,
            'endLine' => 281,
            'startColumn' => 39,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 281,
            'endLine' => 281,
            'startColumn' => 53,
            'endColumn' => 58,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 281,
        'endLine' => 290,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'forgetCustomProperty' => 
      array (
        'name' => 'forgetCustomProperty',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 295,
            'endLine' => 295,
            'startColumn' => 42,
            'endColumn' => 53,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 295,
        'endLine' => 304,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getMediaConversionNames' => 
      array (
        'name' => 'getMediaConversionNames',
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
        'startLine' => 306,
        'endLine' => 311,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getGeneratedConversions' => 
      array (
        'name' => 'getGeneratedConversions',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Support\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 313,
        'endLine' => 316,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'markAsConversionGenerated' => 
      array (
        'name' => 'markAsConversionGenerated',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
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
            'startLine' => 321,
            'endLine' => 321,
            'startColumn' => 47,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 321,
        'endLine' => 332,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'markAsConversionNotGenerated' => 
      array (
        'name' => 'markAsConversionNotGenerated',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
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
            'startLine' => 337,
            'endLine' => 337,
            'startColumn' => 50,
            'endColumn' => 71,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 337,
        'endLine' => 348,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'hasGeneratedConversion' => 
      array (
        'name' => 'hasGeneratedConversion',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
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
            'startLine' => 350,
            'endLine' => 350,
            'startColumn' => 44,
            'endColumn' => 65,
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
        'startLine' => 350,
        'endLine' => 355,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'setStreamChunkSize' => 
      array (
        'name' => 'setStreamChunkSize',
        'parameters' => 
        array (
          'chunkSize' => 
          array (
            'name' => 'chunkSize',
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
            'startLine' => 360,
            'endLine' => 360,
            'startColumn' => 40,
            'endColumn' => 53,
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
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return $this
 */',
        'startLine' => 360,
        'endLine' => 365,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toResponse' => 
      array (
        'name' => 'toResponse',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 367,
            'endLine' => 367,
            'startColumn' => 32,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversion' => 
          array (
            'name' => 'conversion',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 367,
                'endLine' => 367,
                'startTokenPos' => 1909,
                'startFilePos' => 10887,
                'endTokenPos' => 1909,
                'endFilePos' => 10888,
              ),
            ),
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
            'startLine' => 367,
            'endLine' => 367,
            'startColumn' => 42,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Symfony\\Component\\HttpFoundation\\StreamedResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 367,
        'endLine' => 370,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toInlineResponse' => 
      array (
        'name' => 'toInlineResponse',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 372,
            'endLine' => 372,
            'startColumn' => 38,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversion' => 
          array (
            'name' => 'conversion',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 372,
                'endLine' => 372,
                'startTokenPos' => 1950,
                'startFilePos' => 11064,
                'endTokenPos' => 1950,
                'endFilePos' => 11065,
              ),
            ),
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
            'startLine' => 372,
            'endLine' => 372,
            'startColumn' => 48,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Symfony\\Component\\HttpFoundation\\StreamedResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 372,
        'endLine' => 375,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toAvailableResponse' => 
      array (
        'name' => 'toAvailableResponse',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 377,
            'endLine' => 377,
            'startColumn' => 41,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 377,
            'endLine' => 377,
            'startColumn' => 51,
            'endColumn' => 72,
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
            'name' => 'Symfony\\Component\\HttpFoundation\\StreamedResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 377,
        'endLine' => 380,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toAvailableInlineResponse' => 
      array (
        'name' => 'toAvailableInlineResponse',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 382,
            'endLine' => 382,
            'startColumn' => 47,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 382,
            'endLine' => 382,
            'startColumn' => 57,
            'endColumn' => 78,
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
            'name' => 'Symfony\\Component\\HttpFoundation\\StreamedResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 382,
        'endLine' => 385,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'findFirstAvailableConversion' => 
      array (
        'name' => 'findFirstAvailableConversion',
        'parameters' => 
        array (
          'conversionNames' => 
          array (
            'name' => 'conversionNames',
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
            'startLine' => 387,
            'endLine' => 387,
            'startColumn' => 51,
            'endColumn' => 72,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 387,
        'endLine' => 396,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'buildResponse' => 
      array (
        'name' => 'buildResponse',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 398,
            'endLine' => 398,
            'startColumn' => 36,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'contentDispositionType' => 
          array (
            'name' => 'contentDispositionType',
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
            'startLine' => 398,
            'endLine' => 398,
            'startColumn' => 46,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'conversion' => 
          array (
            'name' => 'conversion',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 398,
                'endLine' => 398,
                'startTokenPos' => 2131,
                'startFilePos' => 11988,
                'endTokenPos' => 2131,
                'endFilePos' => 11989,
              ),
            ),
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
            'startLine' => 398,
            'endLine' => 398,
            'startColumn' => 78,
            'endColumn' => 100,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Symfony\\Component\\HttpFoundation\\StreamedResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 398,
        'endLine' => 426,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getResponsiveImageUrls' => 
      array (
        'name' => 'getResponsiveImageUrls',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 428,
                'endLine' => 428,
                'startTokenPos' => 2363,
                'startFilePos' => 13051,
                'endTokenPos' => 2363,
                'endFilePos' => 13052,
              ),
            ),
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
            'startLine' => 428,
            'endLine' => 428,
            'startColumn' => 44,
            'endColumn' => 70,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
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
        'startLine' => 428,
        'endLine' => 431,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'hasResponsiveImages' => 
      array (
        'name' => 'hasResponsiveImages',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 433,
                'endLine' => 433,
                'startTokenPos' => 2399,
                'startFilePos' => 13208,
                'endTokenPos' => 2399,
                'endFilePos' => 13209,
              ),
            ),
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
            'startLine' => 433,
            'endLine' => 433,
            'startColumn' => 41,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => true,
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
        'startLine' => 433,
        'endLine' => 436,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'getSrcset' => 
      array (
        'name' => 'getSrcset',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 438,
                'endLine' => 438,
                'startTokenPos' => 2438,
                'startFilePos' => 13360,
                'endTokenPos' => 2438,
                'endFilePos' => 13361,
              ),
            ),
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
            'startLine' => 438,
            'endLine' => 438,
            'startColumn' => 31,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 438,
        'endLine' => 441,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'previewUrl' => 
      array (
        'name' => 'previewUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 443,
        'endLine' => 448,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'originalUrl' => 
      array (
        'name' => 'originalUrl',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 450,
        'endLine' => 453,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'move' => 
      array (
        'name' => 'move',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\HasMedia',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 26,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'collectionName' => 
          array (
            'name' => 'collectionName',
            'default' => 
            array (
              'code' => '\'default\'',
              'attributes' => 
              array (
                'startLine' => 456,
                'endLine' => 456,
                'startTokenPos' => 2568,
                'startFilePos' => 13873,
                'endTokenPos' => 2568,
                'endFilePos' => 13881,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 43,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'diskName' => 
          array (
            'name' => 'diskName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 456,
                'endLine' => 456,
                'startTokenPos' => 2577,
                'startFilePos' => 13903,
                'endTokenPos' => 2577,
                'endFilePos' => 13904,
              ),
            ),
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
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 72,
            'endColumn' => 92,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'fileName' => 
          array (
            'name' => 'fileName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 456,
                'endLine' => 456,
                'startTokenPos' => 2586,
                'startFilePos' => 13926,
                'endTokenPos' => 2586,
                'endFilePos' => 13927,
              ),
            ),
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
            'startLine' => 456,
            'endLine' => 456,
            'startColumn' => 95,
            'endColumn' => 115,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/** @param  string  $collectionName */',
        'startLine' => 456,
        'endLine' => 463,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'copy' => 
      array (
        'name' => 'copy',
        'parameters' => 
        array (
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Spatie\\MediaLibrary\\HasMedia',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 469,
            'endLine' => 469,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'collectionName' => 
          array (
            'name' => 'collectionName',
            'default' => 
            array (
              'code' => '\'default\'',
              'attributes' => 
              array (
                'startLine' => 470,
                'endLine' => 470,
                'startTokenPos' => 2649,
                'startFilePos' => 14257,
                'endTokenPos' => 2649,
                'endFilePos' => 14265,
              ),
            ),
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
            'startLine' => 470,
            'endLine' => 470,
            'startColumn' => 9,
            'endColumn' => 42,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'diskName' => 
          array (
            'name' => 'diskName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 471,
                'endLine' => 471,
                'startTokenPos' => 2658,
                'startFilePos' => 14295,
                'endTokenPos' => 2658,
                'endFilePos' => 14296,
              ),
            ),
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
            'startLine' => 471,
            'endLine' => 471,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'fileName' => 
          array (
            'name' => 'fileName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 472,
                'endLine' => 472,
                'startTokenPos' => 2667,
                'startFilePos' => 14326,
                'endTokenPos' => 2667,
                'endFilePos' => 14327,
              ),
            ),
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
            'startLine' => 472,
            'endLine' => 472,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'fileAdderCallback' => 
          array (
            'name' => 'fileAdderCallback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 473,
                'endLine' => 473,
                'startTokenPos' => 2677,
                'startFilePos' => 14368,
                'endTokenPos' => 2677,
                'endFilePos' => 14371,
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
                      'name' => 'Closure',
                      'isIdentifier' => false,
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
            'startLine' => 473,
            'endLine' => 473,
            'startColumn' => 9,
            'endColumn' => 42,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'self',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  null|Closure(FileAdder): FileAdder  $fileAdderCallback
 */',
        'startLine' => 468,
        'endLine' => 504,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'responsiveImages' => 
      array (
        'name' => 'responsiveImages',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 506,
                'endLine' => 506,
                'startTokenPos' => 2872,
                'startFilePos' => 15394,
                'endTokenPos' => 2872,
                'endFilePos' => 15395,
              ),
            ),
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
            'startLine' => 506,
            'endLine' => 506,
            'startColumn' => 38,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\ResponsiveImages\\RegisteredResponsiveImages',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 506,
        'endLine' => 509,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'stream' => 
      array (
        'name' => 'stream',
        'parameters' => 
        array (
          'conversion' => 
          array (
            'name' => 'conversion',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 511,
                'endLine' => 511,
                'startTokenPos' => 2907,
                'startFilePos' => 15558,
                'endTokenPos' => 2907,
                'endFilePos' => 15559,
              ),
            ),
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
            'startLine' => 511,
            'endLine' => 511,
            'startColumn' => 28,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 511,
        'endLine' => 525,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toHtml' => 
      array (
        'name' => 'toHtml',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 527,
        'endLine' => 530,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'img' => 
      array (
        'name' => 'img',
        'parameters' => 
        array (
          'conversionName' => 
          array (
            'name' => 'conversionName',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 532,
                'endLine' => 532,
                'startTokenPos' => 3032,
                'startFilePos' => 16101,
                'endTokenPos' => 3032,
                'endFilePos' => 16102,
              ),
            ),
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
            'startLine' => 532,
            'endLine' => 532,
            'startColumn' => 25,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'extraAttributes' => 
          array (
            'name' => 'extraAttributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 532,
                'endLine' => 532,
                'startTokenPos' => 3039,
                'startFilePos' => 16124,
                'endTokenPos' => 3040,
                'endFilePos' => 16125,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 532,
            'endLine' => 532,
            'startColumn' => 54,
            'endColumn' => 74,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\MediaCollections\\HtmlableMedia',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 532,
        'endLine' => 537,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'arguments' => 
          array (
            'name' => 'arguments',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => true,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 539,
            'endLine' => 539,
            'startColumn' => 30,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Spatie\\MediaLibrary\\MediaCollections\\HtmlableMedia',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 539,
        'endLine' => 542,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'temporaryUpload' => 
      array (
        'name' => 'temporaryUpload',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 544,
        'endLine' => 552,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'findWithTemporaryUploadInCurrentSession' => 
      array (
        'name' => 'findWithTemporaryUploadInCurrentSession',
        'parameters' => 
        array (
          'uuids' => 
          array (
            'name' => 'uuids',
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
            'startLine' => 554,
            'endLine' => 554,
            'startColumn' => 68,
            'endColumn' => 79,
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
            'name' => 'Illuminate\\Database\\Eloquent\\Collection',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 554,
        'endLine' => 569,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'mailAttachment' => 
      array (
        'name' => 'mailAttachment',
        'parameters' => 
        array (
          'conversion' => 
          array (
            'name' => 'conversion',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 571,
                'endLine' => 571,
                'startTokenPos' => 3260,
                'startFilePos' => 17402,
                'endTokenPos' => 3260,
                'endFilePos' => 17403,
              ),
            ),
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
            'startLine' => 571,
            'endLine' => 571,
            'startColumn' => 36,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Mail\\Attachment',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 571,
        'endLine' => 580,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'toMailAttachment' => 
      array (
        'name' => 'toMailAttachment',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Mail\\Attachment',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 582,
        'endLine' => 585,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'aliasName' => NULL,
      ),
      'saveOrTouch' => 
      array (
        'name' => 'saveOrTouch',
        'parameters' => 
        array (
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
        'startLine' => 587,
        'endLine' => 594,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Spatie\\MediaLibrary\\MediaCollections\\Models',
        'declaringClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'implementingClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
        'currentClassName' => 'Spatie\\MediaLibrary\\MediaCollections\\Models\\Media',
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