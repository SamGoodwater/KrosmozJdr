<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/Concerns/InteractsWithPivotTable.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Relations\Concerns\InteractsWithPivotTable
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-84cf0bba87211e84d3e4e44370cebc3d38e6060daf30769cc188770fc48157c4-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/Concerns/InteractsWithPivotTable.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
    'name' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
    'shortName' => 'InteractsWithPivotTable',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 803,
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
    ),
    'immediateMethods' => 
    array (
      'toggle' => 
      array (
        'name' => 'toggle',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 28,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 22,
                'endLine' => 22,
                'startTokenPos' => 61,
                'startFilePos' => 602,
                'endTokenPos' => 61,
                'endFilePos' => 605,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 34,
            'endColumn' => 46,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Toggles a model (or models) from the parent.
 *
 * Each existing model is detached, and non existing ones are attached.
 *
 * @param  mixed  $ids
 * @param  bool  $touch
 * @return array
 */',
        'startLine' => 22,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'toggleOrFail' => 
      array (
        'name' => 'toggleOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 34,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 75,
                'endLine' => 75,
                'startTokenPos' => 327,
                'startFilePos' => 2499,
                'endTokenPos' => 327,
                'endFilePos' => 2502,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 40,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Toggles a model (or models) from the parent within a transaction.
 *
 * @param  mixed  $ids
 * @param  bool  $touch
 * @return array
 *
 * @throws \\Throwable
 */',
        'startLine' => 75,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'syncWithoutDetaching' => 
      array (
        'name' => 'syncWithoutDetaching',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 42,
            'endColumn' => 45,
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
 * Sync the intermediate tables with a list of IDs without detaching.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array|int|string  $ids
 * @return array{attached: array, detached: array, updated: array}
 */',
        'startLine' => 86,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'sync' => 
      array (
        'name' => 'sync',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 26,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'detaching' => 
          array (
            'name' => 'detaching',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 98,
                'endLine' => 98,
                'startTokenPos' => 408,
                'startFilePos' => 3345,
                'endTokenPos' => 408,
                'endFilePos' => 3348,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 32,
            'endColumn' => 48,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sync the intermediate tables with a list of IDs or collection of models.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array|int|string  $ids
 * @param  bool  $detaching
 * @return array{attached: array, detached: array, updated: array}
 */',
        'startLine' => 98,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'syncOrFail' => 
      array (
        'name' => 'syncOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 32,
            'endColumn' => 35,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'detaching' => 
          array (
            'name' => 'detaching',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 157,
                'endLine' => 157,
                'startTokenPos' => 694,
                'startFilePos' => 5723,
                'endTokenPos' => 694,
                'endFilePos' => 5726,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 157,
            'endLine' => 157,
            'startColumn' => 38,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sync the intermediate tables with a list of IDs or collection of models within a transaction.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array  $ids
 * @param  bool  $detaching
 * @return array{attached: array, detached: array, updated: array}
 *
 * @throws \\Throwable
 */',
        'startLine' => 157,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'syncWithoutDetachingOrFail' => 
      array (
        'name' => 'syncWithoutDetachingOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 170,
            'endLine' => 170,
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
 * Sync the intermediate tables with a list of IDs without detaching within a transaction.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array  $ids
 * @return array{attached: array, detached: array, updated: array}
 *
 * @throws \\Throwable
 */',
        'startLine' => 170,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'syncWithPivotValues' => 
      array (
        'name' => 'syncWithPivotValues',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 183,
            'endLine' => 183,
            'startColumn' => 41,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
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
            'startLine' => 183,
            'endLine' => 183,
            'startColumn' => 47,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'detaching' => 
          array (
            'name' => 'detaching',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 183,
                'endLine' => 183,
                'startTokenPos' => 782,
                'startFilePos' => 6719,
                'endTokenPos' => 782,
                'endFilePos' => 6722,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 183,
            'endLine' => 183,
            'startColumn' => 62,
            'endColumn' => 83,
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
 * Sync the intermediate tables with a list of IDs or collection of models with the given pivot values.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array|int|string  $ids
 * @param  array  $values
 * @param  bool  $detaching
 * @return array{attached: array, detached: array, updated: array}
 */',
        'startLine' => 183,
        'endLine' => 188,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'syncWithPivotValuesOrFail' => 
      array (
        'name' => 'syncWithPivotValuesOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 200,
            'endLine' => 200,
            'startColumn' => 47,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
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
            'startLine' => 200,
            'endLine' => 200,
            'startColumn' => 53,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'detaching' => 
          array (
            'name' => 'detaching',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 200,
                'endLine' => 200,
                'startTokenPos' => 866,
                'startFilePos' => 7399,
                'endTokenPos' => 866,
                'endFilePos' => 7402,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 200,
            'endLine' => 200,
            'startColumn' => 68,
            'endColumn' => 89,
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
 * Sync the intermediate tables with a list of IDs with the given pivot values within a transaction.
 *
 * @param  \\Illuminate\\Support\\Collection|\\Illuminate\\Database\\Eloquent\\Model|array|int|string  $ids
 * @param  array  $values
 * @param  bool  $detaching
 * @return array{attached: array, detached: array, updated: array}
 *
 * @throws \\Throwable
 */',
        'startLine' => 200,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'formatRecordsList' => 
      array (
        'name' => 'formatRecordsList',
        'parameters' => 
        array (
          'records' => 
          array (
            'name' => 'records',
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
            'startLine' => 211,
            'endLine' => 211,
            'startColumn' => 42,
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
 * Format the sync / toggle record list so that it is keyed by ID.
 *
 * @param  array  $records
 * @return array
 */',
        'startLine' => 211,
        'endLine' => 224,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'attachNew' => 
      array (
        'name' => 'attachNew',
        'parameters' => 
        array (
          'records' => 
          array (
            'name' => 'records',
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
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 34,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'current' => 
          array (
            'name' => 'current',
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
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 50,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 234,
                'endLine' => 234,
                'startTokenPos' => 1045,
                'startFilePos' => 8398,
                'endTokenPos' => 1045,
                'endFilePos' => 8401,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 234,
            'endLine' => 234,
            'startColumn' => 66,
            'endColumn' => 78,
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
 * Attach all of the records that aren\'t in the given current records.
 *
 * @param  array  $records
 * @param  array  $current
 * @param  bool  $touch
 * @return array
 */',
        'startLine' => 234,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'updateExistingPivot' => 
      array (
        'name' => 'updateExistingPivot',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 41,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 46,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 268,
                'endLine' => 268,
                'startTokenPos' => 1227,
                'startFilePos' => 9738,
                'endTokenPos' => 1227,
                'endFilePos' => 9741,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 65,
            'endColumn' => 77,
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
 * Update an existing pivot record on the table.
 *
 * @param  mixed  $id
 * @param  array  $attributes
 * @param  bool  $touch
 * @return int
 */',
        'startLine' => 268,
        'endLine' => 287,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'updateExistingPivotOrFail' => 
      array (
        'name' => 'updateExistingPivotOrFail',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 47,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 52,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 299,
                'endLine' => 299,
                'startTokenPos' => 1362,
                'startFilePos' => 10566,
                'endTokenPos' => 1362,
                'endFilePos' => 10569,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 299,
            'endLine' => 299,
            'startColumn' => 71,
            'endColumn' => 83,
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
 * Update an existing pivot record on the table within a transaction.
 *
 * @param  mixed  $id
 * @param  array  $attributes
 * @param  bool  $touch
 * @return int
 *
 * @throws \\Throwable
 */',
        'startLine' => 299,
        'endLine' => 302,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'updateExistingPivotUsingCustomClass' => 
      array (
        'name' => 'updateExistingPivotUsingCustomClass',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 312,
            'endLine' => 312,
            'startColumn' => 60,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 312,
            'endLine' => 312,
            'startColumn' => 65,
            'endColumn' => 81,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 312,
            'endLine' => 312,
            'startColumn' => 84,
            'endColumn' => 89,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Update an existing pivot record on the table via a custom class.
 *
 * @param  mixed  $id
 * @param  array  $attributes
 * @param  bool  $touch
 * @return int
 */',
        'startLine' => 312,
        'endLine' => 327,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'attach' => 
      array (
        'name' => 'attach',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 337,
            'endLine' => 337,
            'startColumn' => 28,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 337,
                'endLine' => 337,
                'startTokenPos' => 1524,
                'startFilePos' => 11549,
                'endTokenPos' => 1525,
                'endFilePos' => 11550,
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
            'startLine' => 337,
            'endLine' => 337,
            'startColumn' => 34,
            'endColumn' => 55,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 337,
                'endLine' => 337,
                'startTokenPos' => 1532,
                'startFilePos' => 11562,
                'endTokenPos' => 1532,
                'endFilePos' => 11565,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 337,
            'endLine' => 337,
            'startColumn' => 58,
            'endColumn' => 70,
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
 * Attach a model to the parent.
 *
 * @param  mixed  $ids
 * @param  array  $attributes
 * @param  bool  $touch
 * @return void
 */',
        'startLine' => 337,
        'endLine' => 353,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'attachOrFail' => 
      array (
        'name' => 'attachOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 34,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 365,
                'endLine' => 365,
                'startTokenPos' => 1635,
                'startFilePos' => 12464,
                'endTokenPos' => 1636,
                'endFilePos' => 12465,
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
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 40,
            'endColumn' => 61,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 365,
                'endLine' => 365,
                'startTokenPos' => 1643,
                'startFilePos' => 12477,
                'endTokenPos' => 1643,
                'endFilePos' => 12480,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 365,
            'endLine' => 365,
            'startColumn' => 64,
            'endColumn' => 76,
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
 * Attach a model to the parent within a transaction.
 *
 * @param  mixed  $ids
 * @param  array  $attributes
 * @param  bool  $touch
 * @return void
 *
 * @throws \\Throwable
 */',
        'startLine' => 365,
        'endLine' => 368,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'attachUsingCustomClass' => 
      array (
        'name' => 'attachUsingCustomClass',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
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
            'startColumn' => 47,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startColumn' => 53,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Attach a model to the parent using a custom class.
 *
 * @param  mixed  $ids
 * @param  array  $attributes
 * @return void
 */',
        'startLine' => 377,
        'endLine' => 386,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'formatAttachRecords' => 
      array (
        'name' => 'formatAttachRecords',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 44,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 50,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create an array of records to insert into the pivot table.
 *
 * @param  array  $ids
 * @param  array  $attributes
 * @return array
 */',
        'startLine' => 395,
        'endLine' => 412,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'formatAttachRecord' => 
      array (
        'name' => 'formatAttachRecord',
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
            'startLine' => 423,
            'endLine' => 423,
            'startColumn' => 43,
            'endColumn' => 46,
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
            'startLine' => 423,
            'endLine' => 423,
            'startColumn' => 49,
            'endColumn' => 54,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 423,
            'endLine' => 423,
            'startColumn' => 57,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'hasTimestamps' => 
          array (
            'name' => 'hasTimestamps',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 423,
            'endLine' => 423,
            'startColumn' => 70,
            'endColumn' => 83,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a full attachment record payload.
 *
 * @param  int  $key
 * @param  mixed  $value
 * @param  array  $attributes
 * @param  bool  $hasTimestamps
 * @return array
 */',
        'startLine' => 423,
        'endLine' => 430,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'extractAttachIdAndAttributes' => 
      array (
        'name' => 'extractAttachIdAndAttributes',
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
            'startLine' => 440,
            'endLine' => 440,
            'startColumn' => 53,
            'endColumn' => 56,
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
            'startLine' => 440,
            'endLine' => 440,
            'startColumn' => 59,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'attributes' => 
          array (
            'name' => 'attributes',
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
            'startLine' => 440,
            'endLine' => 440,
            'startColumn' => 67,
            'endColumn' => 83,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the attach record ID and extra attributes.
 *
 * @param  mixed  $key
 * @param  mixed  $value
 * @param  array  $attributes
 * @return array
 */',
        'startLine' => 440,
        'endLine' => 445,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'baseAttachRecord' => 
      array (
        'name' => 'baseAttachRecord',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 454,
            'endLine' => 454,
            'startColumn' => 41,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'timed' => 
          array (
            'name' => 'timed',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 454,
            'endLine' => 454,
            'startColumn' => 46,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new pivot attachment record.
 *
 * @param  int  $id
 * @param  bool  $timed
 * @return array
 */',
        'startLine' => 454,
        'endLine' => 472,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'addTimestampsToAttachment' => 
      array (
        'name' => 'addTimestampsToAttachment',
        'parameters' => 
        array (
          'record' => 
          array (
            'name' => 'record',
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
            'startLine' => 481,
            'endLine' => 481,
            'startColumn' => 50,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'exists' => 
          array (
            'name' => 'exists',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 481,
                'endLine' => 481,
                'startTokenPos' => 2126,
                'startFilePos' => 15932,
                'endTokenPos' => 2126,
                'endFilePos' => 15936,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 481,
            'endLine' => 481,
            'startColumn' => 65,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the creation and update timestamps on an attach record.
 *
 * @param  array  $record
 * @param  bool  $exists
 * @return array
 */',
        'startLine' => 481,
        'endLine' => 500,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'hasPivotColumn' => 
      array (
        'name' => 'hasPivotColumn',
        'parameters' => 
        array (
          'column' => 
          array (
            'name' => 'column',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 508,
            'endLine' => 508,
            'startColumn' => 36,
            'endColumn' => 42,
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
 * Determine whether the given column is defined as a pivot column.
 *
 * @param  string  $column
 * @return bool
 */',
        'startLine' => 508,
        'endLine' => 511,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'detach' => 
      array (
        'name' => 'detach',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 520,
                'endLine' => 520,
                'startTokenPos' => 2298,
                'startFilePos' => 16849,
                'endTokenPos' => 2298,
                'endFilePos' => 16852,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 520,
            'endLine' => 520,
            'startColumn' => 28,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 520,
                'endLine' => 520,
                'startTokenPos' => 2305,
                'startFilePos' => 16864,
                'endTokenPos' => 2305,
                'endFilePos' => 16867,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 520,
            'endLine' => 520,
            'startColumn' => 41,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Detach models from the relationship.
 *
 * @param  mixed  $ids
 * @param  bool  $touch
 * @return int
 */',
        'startLine' => 520,
        'endLine' => 551,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'detachOrFail' => 
      array (
        'name' => 'detachOrFail',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 562,
                'endLine' => 562,
                'startTokenPos' => 2472,
                'startFilePos' => 18182,
                'endTokenPos' => 2472,
                'endFilePos' => 18185,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 562,
            'endLine' => 562,
            'startColumn' => 34,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'touch' => 
          array (
            'name' => 'touch',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 562,
                'endLine' => 562,
                'startTokenPos' => 2479,
                'startFilePos' => 18197,
                'endTokenPos' => 2479,
                'endFilePos' => 18200,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 562,
            'endLine' => 562,
            'startColumn' => 47,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Detach models from the relationship within a transaction.
 *
 * @param  mixed  $ids
 * @param  bool  $touch
 * @return int
 *
 * @throws \\Throwable
 */',
        'startLine' => 562,
        'endLine' => 565,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'detachUsingCustomClass' => 
      array (
        'name' => 'detachUsingCustomClass',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 573,
            'endLine' => 573,
            'startColumn' => 47,
            'endColumn' => 50,
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
 * Detach models from the relationship using a custom class.
 *
 * @param  mixed  $ids
 * @return int
 */',
        'startLine' => 573,
        'endLine' => 584,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'getCurrentlyAttachedPivots' => 
      array (
        'name' => 'getCurrentlyAttachedPivots',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the pivot models that are currently attached.
 *
 * @return \\Illuminate\\Support\\Collection
 */',
        'startLine' => 591,
        'endLine' => 594,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'getCurrentlyAttachedPivotsForIds' => 
      array (
        'name' => 'getCurrentlyAttachedPivotsForIds',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 602,
                'endLine' => 602,
                'startTokenPos' => 2616,
                'startFilePos' => 19219,
                'endTokenPos' => 2616,
                'endFilePos' => 19222,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 602,
            'endLine' => 602,
            'startColumn' => 57,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the pivot models that are currently attached, filtered by related model keys.
 *
 * @param  mixed  $ids
 * @return \\Illuminate\\Support\\Collection
 */',
        'startLine' => 602,
        'endLine' => 618,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'newPivot' => 
      array (
        'name' => 'newPivot',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 627,
                'endLine' => 627,
                'startTokenPos' => 2777,
                'startFilePos' => 20086,
                'endTokenPos' => 2778,
                'endFilePos' => 20087,
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
            'startLine' => 627,
            'endLine' => 627,
            'startColumn' => 30,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'exists' => 
          array (
            'name' => 'exists',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 627,
                'endLine' => 627,
                'startTokenPos' => 2785,
                'startFilePos' => 20100,
                'endTokenPos' => 2785,
                'endFilePos' => 20104,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 627,
            'endLine' => 627,
            'startColumn' => 54,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new pivot model instance.
 *
 * @param  array  $attributes
 * @param  bool  $exists
 * @return \\Illuminate\\Database\\Eloquent\\Relations\\Pivot
 */',
        'startLine' => 627,
        'endLine' => 638,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'newExistingPivot' => 
      array (
        'name' => 'newExistingPivot',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 646,
                'endLine' => 646,
                'startTokenPos' => 2890,
                'startFilePos' => 20723,
                'endTokenPos' => 2891,
                'endFilePos' => 20724,
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
            'startLine' => 646,
            'endLine' => 646,
            'startColumn' => 38,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new existing pivot model instance.
 *
 * @param  array  $attributes
 * @return \\Illuminate\\Database\\Eloquent\\Relations\\Pivot
 */',
        'startLine' => 646,
        'endLine' => 649,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'newPivotStatement' => 
      array (
        'name' => 'newPivotStatement',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a new plain query builder for the pivot table.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 656,
        'endLine' => 659,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'newPivotStatementForId' => 
      array (
        'name' => 'newPivotStatementForId',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 667,
            'endLine' => 667,
            'startColumn' => 44,
            'endColumn' => 46,
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
 * Get a new pivot statement for a given "other" ID.
 *
 * @param  mixed  $id
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 667,
        'endLine' => 670,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'newPivotQuery' => 
      array (
        'name' => 'newPivotQuery',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new query builder for the pivot table.
 *
 * @return \\Illuminate\\Database\\Query\\Builder
 */',
        'startLine' => 677,
        'endLine' => 694,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'withPivot' => 
      array (
        'name' => 'withPivot',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 702,
            'endLine' => 702,
            'startColumn' => 31,
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
 * Set the columns on the pivot table to retrieve.
 *
 * @param  mixed  $columns
 * @return $this
 */',
        'startLine' => 702,
        'endLine' => 709,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'parseIds' => 
      array (
        'name' => 'parseIds',
        'parameters' => 
        array (
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
            'startLine' => 717,
            'endLine' => 717,
            'startColumn' => 33,
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
 * Get all of the IDs from the given mixed value.
 *
 * @param  mixed  $value
 * @return array
 */',
        'startLine' => 717,
        'endLine' => 734,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'parseId' => 
      array (
        'name' => 'parseId',
        'parameters' => 
        array (
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
            'startLine' => 742,
            'endLine' => 742,
            'startColumn' => 32,
            'endColumn' => 37,
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
 * Get the ID from the given mixed value.
 *
 * @param  mixed  $value
 * @return mixed
 */',
        'startLine' => 742,
        'endLine' => 745,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'castKeys' => 
      array (
        'name' => 'castKeys',
        'parameters' => 
        array (
          'keys' => 
          array (
            'name' => 'keys',
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
            'startLine' => 753,
            'endLine' => 753,
            'startColumn' => 33,
            'endColumn' => 43,
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
 * Cast the given keys to integers if they are numeric and string otherwise.
 *
 * @param  array  $keys
 * @return array
 */',
        'startLine' => 753,
        'endLine' => 758,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'castKey' => 
      array (
        'name' => 'castKey',
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
            'startLine' => 766,
            'endLine' => 766,
            'startColumn' => 32,
            'endColumn' => 35,
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
 * Cast the given key to convert to primary key type.
 *
 * @param  mixed  $key
 * @return mixed
 */',
        'startLine' => 766,
        'endLine' => 772,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'castAttributes' => 
      array (
        'name' => 'castAttributes',
        'parameters' => 
        array (
          'attributes' => 
          array (
            'name' => 'attributes',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 780,
            'endLine' => 780,
            'startColumn' => 39,
            'endColumn' => 49,
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
 * Cast the given pivot attributes.
 *
 * @param  array  $attributes
 * @return array
 */',
        'startLine' => 780,
        'endLine' => 785,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'aliasName' => NULL,
      ),
      'getTypeSwapValue' => 
      array (
        'name' => 'getTypeSwapValue',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 794,
            'endLine' => 794,
            'startColumn' => 41,
            'endColumn' => 45,
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
            'startLine' => 794,
            'endLine' => 794,
            'startColumn' => 48,
            'endColumn' => 53,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Converts a given value to a given type value.
 *
 * @param  string  $type
 * @param  mixed  $value
 * @return mixed
 */',
        'startLine' => 794,
        'endLine' => 802,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
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