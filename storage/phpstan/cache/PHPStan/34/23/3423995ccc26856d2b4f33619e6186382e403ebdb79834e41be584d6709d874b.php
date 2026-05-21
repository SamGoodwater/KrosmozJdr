<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel/src/ScrollProp.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Inertia\ScrollProp
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d285f8f8319e094c4d70f3f7fd561f96bdf58fa1a92e121107b57274481b5e81-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Inertia\\ScrollProp',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel/src/ScrollProp.php',
      ),
    ),
    'namespace' => 'Inertia',
    'name' => 'Inertia\\ScrollProp',
    'shortName' => 'ScrollProp',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Represents a paginated property that can be merged during partial reloads.
 *
 * This class provides functionality for handling pagination data with merge capabilities,
 * allowing paginated content to be appended or prepended during client-side navigation.
 *
 * @template T
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 129,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Inertia\\Deferrable',
      1 => 'Inertia\\Mergeable',
    ),
    'traitClassNames' => 
    array (
      0 => 'Inertia\\DefersProps',
      1 => 'Inertia\\MergesProps',
      2 => 'Inertia\\ResolvesCallables',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'value' => 
      array (
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'name' => 'value',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The property value.
 *
 * Merged with existing client-side data during partial reloads.
 *
 * @var T
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resolved' => 
      array (
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'name' => 'resolved',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The resolved property value.
 *
 * @var T
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'wrapper' => 
      array (
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'name' => 'wrapper',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The wrapper key for the data array.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'metadata' => 
      array (
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'name' => 'metadata',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The scroll metadata provider.
 *
 * @var ProvidesScrollMetadata|callable(T): ProvidesScrollMetadata|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 48,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 24,
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
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'mixed',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'wrapper' => 
          array (
            'name' => 'wrapper',
            'default' => 
            array (
              'code' => '\'data\'',
              'attributes' => 
              array (
                'startLine' => 58,
                'endLine' => 58,
                'startTokenPos' => 90,
                'startFilePos' => 1409,
                'endTokenPos' => 90,
                'endFilePos' => 1414,
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
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 47,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'metadata' => 
          array (
            'name' => 'metadata',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 58,
                'endLine' => 58,
                'startTokenPos' => 103,
                'startFilePos' => 1466,
                'endTokenPos' => 103,
                'endFilePos' => 1469,
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
                      'name' => 'Inertia\\ProvidesScrollMetadata',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'callable',
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
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 73,
            'endColumn' => 125,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new merge property instance. Merge properties are combined
 * with existing client-side data during partial reloads instead of
 * completely replacing the property value.
 *
 * @param  T  $value
 * @param  ProvidesScrollMetadata|callable(T): ProvidesScrollMetadata|null  $metadata
 */',
        'startLine' => 58,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'currentClassName' => 'Inertia\\ScrollProp',
        'aliasName' => NULL,
      ),
      'configureMergeIntent' => 
      array (
        'name' => 'configureMergeIntent',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 72,
                'endLine' => 72,
                'startTokenPos' => 161,
                'startFilePos' => 1935,
                'endTokenPos' => 161,
                'endFilePos' => 1938,
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
                      'name' => 'Illuminate\\Http\\Request',
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
            'startLine' => 72,
            'endLine' => 72,
            'startColumn' => 42,
            'endColumn' => 65,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Configure the merge strategy based on the infinite scroll merge intent header.
 *
 * The frontend InfiniteScroll component sends its merge intent directly,
 * eliminating the need for direction-based logic on the backend.
 */',
        'startLine' => 72,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'currentClassName' => 'Inertia\\ScrollProp',
        'aliasName' => NULL,
      ),
      'resolveMetadataProvider' => 
      array (
        'name' => 'resolveMetadataProvider',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Inertia\\ProvidesScrollMetadata',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the scroll metadata provider.
 */',
        'startLine' => 84,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'currentClassName' => 'Inertia\\ScrollProp',
        'aliasName' => NULL,
      ),
      'metadata' => 
      array (
        'name' => 'metadata',
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
        'docComment' => '/**
 * Get the pagination meta information.
 *
 * @return array{pageName: string, previousPage: int|string|null, nextPage: int|string|null, currentPage: int|string|null}
 */',
        'startLine' => 104,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'currentClassName' => 'Inertia\\ScrollProp',
        'aliasName' => NULL,
      ),
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the property value.
 *
 * @return T
 */',
        'startLine' => 121,
        'endLine' => 128,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\ScrollProp',
        'implementingClassName' => 'Inertia\\ScrollProp',
        'currentClassName' => 'Inertia\\ScrollProp',
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