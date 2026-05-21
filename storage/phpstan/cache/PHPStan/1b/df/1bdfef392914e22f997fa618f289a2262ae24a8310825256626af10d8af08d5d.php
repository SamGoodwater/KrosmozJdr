<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../spatie/laravel-medialibrary/src/MediaCollections/Models/Media.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Spatie\MediaLibrary\MediaCollections\Models\Media
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-102831bc68d5ce2287d99d8118e680aff8f0f2faf8dc660b82535f916ec6701f-8.4.17-6.70.0.1',
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
 * @property-read ?Carbon $created_at
 * @property-read ?Carbon $updated_at
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 67,
    'endLine' => 596,
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
            'startLine' => 75,
            'endLine' => 75,
            'startTokenPos' => 250,
            'startFilePos' => 2861,
            'endTokenPos' => 250,
            'endFilePos' => 2867,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
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
            'startLine' => 73,
            'endLine' => 73,
            'startTokenPos' => 239,
            'startFilePos' => 2821,
            'endTokenPos' => 239,
            'endFilePos' => 2827,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 73,
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
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 259,
            'startFilePos' => 2896,
            'endTokenPos' => 260,
            'endFilePos' => 2897,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
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
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 269,
            'startFilePos' => 2926,
            'endTokenPos' => 274,
            'endFilePos' => 2956,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
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
            'startLine' => 81,
            'endLine' => 86,
            'startTokenPos' => 283,
            'startFilePos' => 2983,
            'endTokenPos' => 313,
            'endFilePos' => 3149,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 81,
        'endLine' => 86,
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
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 325,
            'startFilePos' => 3191,
            'endTokenPos' => 329,
            'endFilePos' => 3201,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
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
                'startLine' => 91,
                'endLine' => 91,
                'startTokenPos' => 349,
                'startFilePos' => 3332,
                'endTokenPos' => 350,
                'endFilePos' => 3333,
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
            'startLine' => 91,
            'endLine' => 91,
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
        'startLine' => 91,
        'endLine' => 94,
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
        'startLine' => 96,
        'endLine' => 99,
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
                'startLine' => 101,
                'endLine' => 101,
                'startTokenPos' => 408,
                'startFilePos' => 3583,
                'endTokenPos' => 408,
                'endFilePos' => 3584,
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
            'startLine' => 101,
            'endLine' => 101,
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
        'startLine' => 101,
        'endLine' => 104,
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
                'startLine' => 106,
                'endLine' => 106,
                'startTokenPos' => 443,
                'startFilePos' => 3712,
                'endTokenPos' => 443,
                'endFilePos' => 3713,
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
            'startLine' => 106,
            'endLine' => 106,
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
        'startLine' => 106,
        'endLine' => 111,
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
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 490,
                'startFilePos' => 3932,
                'endTokenPos' => 490,
                'endFilePos' => 3935,
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
            'startLine' => 113,
            'endLine' => 113,
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
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 499,
                'startFilePos' => 3963,
                'endTokenPos' => 499,
                'endFilePos' => 3964,
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
            'startLine' => 113,
            'endLine' => 113,
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
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 508,
                'startFilePos' => 3984,
                'endTokenPos' => 509,
                'endFilePos' => 3985,
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
            'startLine' => 113,
            'endLine' => 113,
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
        'startLine' => 113,
        'endLine' => 119,
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
                'startLine' => 121,
                'endLine' => 121,
                'startTokenPos' => 578,
                'startFilePos' => 4310,
                'endTokenPos' => 578,
                'endFilePos' => 4311,
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
            'startLine' => 121,
            'endLine' => 121,
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
        'startLine' => 121,
        'endLine' => 126,
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
                'startLine' => 128,
                'endLine' => 128,
                'startTokenPos' => 621,
                'startFilePos' => 4509,
                'endTokenPos' => 621,
                'endFilePos' => 4510,
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
            'startLine' => 128,
            'endLine' => 128,
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
        'startLine' => 128,
        'endLine' => 131,
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
            'startLine' => 133,
            'endLine' => 133,
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
        'startLine' => 133,
        'endLine' => 136,
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
            'startLine' => 138,
            'endLine' => 138,
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
        'startLine' => 138,
        'endLine' => 149,
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
            'startLine' => 151,
            'endLine' => 151,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 765,
                'startFilePos' => 5220,
                'endTokenPos' => 765,
                'endFilePos' => 5223,
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
            'startLine' => 151,
            'endLine' => 151,
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
                'startLine' => 151,
                'endLine' => 151,
                'startTokenPos' => 774,
                'startFilePos' => 5243,
                'endTokenPos' => 775,
                'endFilePos' => 5244,
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
            'startLine' => 151,
            'endLine' => 151,
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
        'startLine' => 151,
        'endLine' => 162,
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
        'startLine' => 164,
        'endLine' => 167,
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
            'startLine' => 169,
            'endLine' => 169,
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
        'startLine' => 169,
        'endLine' => 180,
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
            'startLine' => 182,
            'endLine' => 182,
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
        'startLine' => 182,
        'endLine' => 193,
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
            'startLine' => 195,
            'endLine' => 195,
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
        'startLine' => 195,
        'endLine' => 206,
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
        'startLine' => 208,
        'endLine' => 221,
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
        'startLine' => 223,
        'endLine' => 230,
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
        'startLine' => 232,
        'endLine' => 239,
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
        'startLine' => 241,
        'endLine' => 244,
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
        'startLine' => 246,
        'endLine' => 249,
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
        'startLine' => 251,
        'endLine' => 254,
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
        'startLine' => 256,
        'endLine' => 261,
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
            'startLine' => 263,
            'endLine' => 263,
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
        'startLine' => 263,
        'endLine' => 266,
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
            'startLine' => 273,
            'endLine' => 273,
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
                'startLine' => 273,
                'endLine' => 273,
                'startTokenPos' => 1467,
                'startFilePos' => 8583,
                'endTokenPos' => 1467,
                'endFilePos' => 8586,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 273,
            'endLine' => 273,
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
        'startLine' => 273,
        'endLine' => 276,
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
            'startLine' => 282,
            'endLine' => 282,
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
            'startLine' => 282,
            'endLine' => 282,
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
        'startLine' => 282,
        'endLine' => 291,
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
            'startLine' => 296,
            'endLine' => 296,
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
        'startLine' => 296,
        'endLine' => 305,
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
        'startLine' => 307,
        'endLine' => 312,
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
        'startLine' => 314,
        'endLine' => 317,
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
            'startLine' => 322,
            'endLine' => 322,
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
        'startLine' => 322,
        'endLine' => 333,
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
            'startLine' => 338,
            'endLine' => 338,
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
        'startLine' => 338,
        'endLine' => 349,
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
            'startLine' => 351,
            'endLine' => 351,
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
        'startLine' => 351,
        'endLine' => 356,
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
            'startLine' => 361,
            'endLine' => 361,
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
        'startLine' => 361,
        'endLine' => 366,
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
            'startLine' => 368,
            'endLine' => 368,
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
                'startLine' => 368,
                'endLine' => 368,
                'startTokenPos' => 1914,
                'startFilePos' => 10878,
                'endTokenPos' => 1914,
                'endFilePos' => 10879,
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
            'startLine' => 368,
            'endLine' => 368,
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
        'startLine' => 368,
        'endLine' => 371,
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
            'startLine' => 373,
            'endLine' => 373,
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
                'startLine' => 373,
                'endLine' => 373,
                'startTokenPos' => 1955,
                'startFilePos' => 11055,
                'endTokenPos' => 1955,
                'endFilePos' => 11056,
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
            'startLine' => 373,
            'endLine' => 373,
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
        'startLine' => 373,
        'endLine' => 376,
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
            'startLine' => 378,
            'endLine' => 378,
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
            'startLine' => 378,
            'endLine' => 378,
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
        'startLine' => 378,
        'endLine' => 381,
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
            'startLine' => 383,
            'endLine' => 383,
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
            'startLine' => 383,
            'endLine' => 383,
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
        'startLine' => 383,
        'endLine' => 386,
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
            'startLine' => 388,
            'endLine' => 388,
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
        'startLine' => 388,
        'endLine' => 397,
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
            'startLine' => 399,
            'endLine' => 399,
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
            'startLine' => 399,
            'endLine' => 399,
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
                'startLine' => 399,
                'endLine' => 399,
                'startTokenPos' => 2136,
                'startFilePos' => 11979,
                'endTokenPos' => 2136,
                'endFilePos' => 11980,
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
            'startLine' => 399,
            'endLine' => 399,
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
        'startLine' => 399,
        'endLine' => 427,
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
                'startLine' => 429,
                'endLine' => 429,
                'startTokenPos' => 2368,
                'startFilePos' => 13042,
                'endTokenPos' => 2368,
                'endFilePos' => 13043,
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
            'startLine' => 429,
            'endLine' => 429,
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
        'startLine' => 429,
        'endLine' => 432,
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
                'startLine' => 434,
                'endLine' => 434,
                'startTokenPos' => 2404,
                'startFilePos' => 13199,
                'endTokenPos' => 2404,
                'endFilePos' => 13200,
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
            'startLine' => 434,
            'endLine' => 434,
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
        'startLine' => 434,
        'endLine' => 437,
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
                'startLine' => 439,
                'endLine' => 439,
                'startTokenPos' => 2443,
                'startFilePos' => 13351,
                'endTokenPos' => 2443,
                'endFilePos' => 13352,
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
            'startLine' => 439,
            'endLine' => 439,
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
        'startLine' => 439,
        'endLine' => 442,
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
        'startLine' => 444,
        'endLine' => 449,
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
        'startLine' => 451,
        'endLine' => 454,
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
            'startLine' => 457,
            'endLine' => 457,
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
                'startLine' => 457,
                'endLine' => 457,
                'startTokenPos' => 2573,
                'startFilePos' => 13864,
                'endTokenPos' => 2573,
                'endFilePos' => 13872,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 457,
            'endLine' => 457,
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
                'startLine' => 457,
                'endLine' => 457,
                'startTokenPos' => 2582,
                'startFilePos' => 13894,
                'endTokenPos' => 2582,
                'endFilePos' => 13895,
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
            'startLine' => 457,
            'endLine' => 457,
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
                'startLine' => 457,
                'endLine' => 457,
                'startTokenPos' => 2591,
                'startFilePos' => 13917,
                'endTokenPos' => 2591,
                'endFilePos' => 13918,
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
            'startLine' => 457,
            'endLine' => 457,
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
        'startLine' => 457,
        'endLine' => 464,
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
            'startLine' => 470,
            'endLine' => 470,
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
                'startLine' => 471,
                'endLine' => 471,
                'startTokenPos' => 2654,
                'startFilePos' => 14248,
                'endTokenPos' => 2654,
                'endFilePos' => 14256,
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
                'startLine' => 472,
                'endLine' => 472,
                'startTokenPos' => 2663,
                'startFilePos' => 14286,
                'endTokenPos' => 2663,
                'endFilePos' => 14287,
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
                'startLine' => 473,
                'endLine' => 473,
                'startTokenPos' => 2672,
                'startFilePos' => 14317,
                'endTokenPos' => 2672,
                'endFilePos' => 14318,
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
            'startLine' => 473,
            'endLine' => 473,
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
                'startLine' => 474,
                'endLine' => 474,
                'startTokenPos' => 2682,
                'startFilePos' => 14359,
                'endTokenPos' => 2682,
                'endFilePos' => 14362,
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
            'startLine' => 474,
            'endLine' => 474,
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
        'startLine' => 469,
        'endLine' => 505,
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
                'startLine' => 507,
                'endLine' => 507,
                'startTokenPos' => 2877,
                'startFilePos' => 15385,
                'endTokenPos' => 2877,
                'endFilePos' => 15386,
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
            'startLine' => 507,
            'endLine' => 507,
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
        'startLine' => 507,
        'endLine' => 510,
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
                'startLine' => 512,
                'endLine' => 512,
                'startTokenPos' => 2912,
                'startFilePos' => 15549,
                'endTokenPos' => 2912,
                'endFilePos' => 15550,
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
            'startLine' => 512,
            'endLine' => 512,
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
        'startLine' => 512,
        'endLine' => 526,
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
        'startLine' => 528,
        'endLine' => 531,
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
                'startLine' => 533,
                'endLine' => 533,
                'startTokenPos' => 3037,
                'startFilePos' => 16092,
                'endTokenPos' => 3037,
                'endFilePos' => 16093,
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
            'startLine' => 533,
            'endLine' => 533,
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
                'startLine' => 533,
                'endLine' => 533,
                'startTokenPos' => 3044,
                'startFilePos' => 16115,
                'endTokenPos' => 3045,
                'endFilePos' => 16116,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 533,
            'endLine' => 533,
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
        'startLine' => 533,
        'endLine' => 538,
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
            'startLine' => 540,
            'endLine' => 540,
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
        'startLine' => 540,
        'endLine' => 543,
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
        'startLine' => 545,
        'endLine' => 553,
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
            'startLine' => 555,
            'endLine' => 555,
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
        'startLine' => 555,
        'endLine' => 570,
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
                'startLine' => 572,
                'endLine' => 572,
                'startTokenPos' => 3265,
                'startFilePos' => 17393,
                'endTokenPos' => 3265,
                'endFilePos' => 17394,
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
            'startLine' => 572,
            'endLine' => 572,
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
        'startLine' => 572,
        'endLine' => 581,
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
        'startLine' => 583,
        'endLine' => 586,
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
        'startLine' => 588,
        'endLine' => 595,
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