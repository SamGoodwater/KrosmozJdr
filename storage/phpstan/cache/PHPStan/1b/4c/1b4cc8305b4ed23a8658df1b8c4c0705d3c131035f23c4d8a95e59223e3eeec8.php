<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel/src/DefersProps.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Inertia\DefersProps
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-8861935f9d1ed234f29affea6112de183f385441d3c702c7e74e2d1e7c23be69-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Inertia\\DefersProps',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../inertiajs/inertia-laravel/src/DefersProps.php',
      ),
    ),
    'namespace' => 'Inertia',
    'name' => 'Inertia\\DefersProps',
    'shortName' => 'DefersProps',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 5,
    'endLine' => 47,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
      'deferred' => 
      array (
        'declaringClassName' => 'Inertia\\DefersProps',
        'implementingClassName' => 'Inertia\\DefersProps',
        'name' => 'deferred',
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
            'startLine' => 10,
            'endLine' => 10,
            'startTokenPos' => 23,
            'startFilePos' => 147,
            'endTokenPos' => 23,
            'endFilePos' => 151,
          ),
        ),
        'docComment' => '/**
 * Indicates if the property should be deferred.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 10,
        'endLine' => 10,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'deferGroup' => 
      array (
        'declaringClassName' => 'Inertia\\DefersProps',
        'implementingClassName' => 'Inertia\\DefersProps',
        'name' => 'deferGroup',
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
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 15,
            'startTokenPos' => 37,
            'startFilePos' => 231,
            'endTokenPos' => 37,
            'endFilePos' => 234,
          ),
        ),
        'docComment' => '/**
 * The defer group.
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 41,
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
      'defer' => 
      array (
        'name' => 'defer',
        'parameters' => 
        array (
          'group' => 
          array (
            'name' => 'group',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 22,
                'endLine' => 22,
                'startTokenPos' => 55,
                'startFilePos' => 497,
                'endTokenPos' => 55,
                'endFilePos' => 500,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 27,
            'endColumn' => 47,
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
 * Mark this property as deferred. Deferred properties are excluded
 * from the initial page load and only evaluated when requested by the
 * frontend, improving initial page performance.
 */',
        'startLine' => 22,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\DefersProps',
        'implementingClassName' => 'Inertia\\DefersProps',
        'currentClassName' => 'Inertia\\DefersProps',
        'aliasName' => NULL,
      ),
      'shouldDefer' => 
      array (
        'name' => 'shouldDefer',
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
        'docComment' => '/**
 * Determine if this property should be deferred.
 */',
        'startLine' => 33,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\DefersProps',
        'implementingClassName' => 'Inertia\\DefersProps',
        'currentClassName' => 'Inertia\\DefersProps',
        'aliasName' => NULL,
      ),
      'group' => 
      array (
        'name' => 'group',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the defer group for this property.
 *
 * @return string
 */',
        'startLine' => 43,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Inertia',
        'declaringClassName' => 'Inertia\\DefersProps',
        'implementingClassName' => 'Inertia\\DefersProps',
        'currentClassName' => 'Inertia\\DefersProps',
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