<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Auth/Access/AuthorizationException.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Auth\Access\AuthorizationException
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-df3f1333fd7419cacf361642e175dc3c2c55281ae9364f614fbd6a9a2680024b-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Auth/Access/AuthorizationException.php',
      ),
    ),
    'namespace' => 'Illuminate\\Auth\\Access',
    'name' => 'Illuminate\\Auth\\Access\\AuthorizationException',
    'shortName' => 'AuthorizationException',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 113,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Exception',
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
      'response' => 
      array (
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'name' => 'response',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The response from the gate.
 *
 * @var \\Illuminate\\Auth\\Access\\Response
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 24,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'status' => 
      array (
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'name' => 'status',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The HTTP response status code.
 *
 * @var int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 22,
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
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 53,
                'startFilePos' => 584,
                'endTokenPos' => 53,
                'endFilePos' => 587,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 33,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'code' => 
          array (
            'name' => 'code',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 60,
                'startFilePos' => 598,
                'endTokenPos' => 60,
                'endFilePos' => 601,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 50,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'previous' => 
          array (
            'name' => 'previous',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 31,
                'endLine' => 31,
                'startTokenPos' => 70,
                'startFilePos' => 627,
                'endTokenPos' => 70,
                'endFilePos' => 630,
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
                      'name' => 'Throwable',
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
            'startLine' => 31,
            'endLine' => 31,
            'startColumn' => 64,
            'endColumn' => 90,
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
 * Create a new authorization exception instance.
 *
 * @param  string|null  $message
 * @param  mixed  $code
 * @param  \\Throwable|null  $previous
 */',
        'startLine' => 31,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'response' => 
      array (
        'name' => 'response',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the response from the gate.
 *
 * @return \\Illuminate\\Auth\\Access\\Response
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
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'setResponse' => 
      array (
        'name' => 'setResponse',
        'parameters' => 
        array (
          'response' => 
          array (
            'name' => 'response',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 54,
            'endLine' => 54,
            'startColumn' => 33,
            'endColumn' => 41,
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
 * Set the response from the gate.
 *
 * @param  \\Illuminate\\Auth\\Access\\Response  $response
 * @return $this
 */',
        'startLine' => 54,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'withStatus' => 
      array (
        'name' => 'withStatus',
        'parameters' => 
        array (
          'status' => 
          array (
            'name' => 'status',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 67,
            'endLine' => 67,
            'startColumn' => 32,
            'endColumn' => 38,
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
 * Set the HTTP response status code.
 *
 * @param  int|null  $status
 * @return $this
 */',
        'startLine' => 67,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'asNotFound' => 
      array (
        'name' => 'asNotFound',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the HTTP response status code to 404.
 *
 * @return $this
 */',
        'startLine' => 79,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'hasStatus' => 
      array (
        'name' => 'hasStatus',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the HTTP status code has been set.
 *
 * @return bool
 */',
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'status' => 
      array (
        'name' => 'status',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the HTTP status code.
 *
 * @return int|null
 */',
        'startLine' => 99,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'aliasName' => NULL,
      ),
      'toResponse' => 
      array (
        'name' => 'toResponse',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a deny response object from this exception.
 *
 * @return \\Illuminate\\Auth\\Access\\Response
 */',
        'startLine' => 109,
        'endLine' => 112,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Auth\\Access',
        'declaringClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'implementingClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
        'currentClassName' => 'Illuminate\\Auth\\Access\\AuthorizationException',
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