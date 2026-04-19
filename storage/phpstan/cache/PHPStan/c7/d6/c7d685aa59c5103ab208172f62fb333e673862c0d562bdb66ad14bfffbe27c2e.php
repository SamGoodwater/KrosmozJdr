<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/BelongsToMany.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Relations\BelongsToMany
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-f7027b108c4bcce1ec3b0d3954ec13455fc0a86e353c4e5f0c0ce605272fd273-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'filename' => '/var/www/KrosmozJdr/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Relations/BelongsToMany.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
    'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
    'shortName' => 'BelongsToMany',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @template TRelatedModel of \\Illuminate\\Database\\Eloquent\\Model
 * @template TDeclaringModel of \\Illuminate\\Database\\Eloquent\\Model
 * @template TPivotModel of \\Illuminate\\Database\\Eloquent\\Relations\\Pivot = \\Illuminate\\Database\\Eloquent\\Relations\\Pivot
 * @template TAccessor of string = \'pivot\'
 *
 * @extends \\Illuminate\\Database\\Eloquent\\Relations\\Relation<TRelatedModel, TDeclaringModel, \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>>
 *
 * @todo use TAccessor when PHPStan bug is fixed: https://github.com/phpstan/phpstan/issues/12756
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 1710,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Relation',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithDictionary',
      1 => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\InteractsWithPivotTable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The intermediate table for the relation.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'foreignPivotKey' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'foreignPivotKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The foreign key of the parent model.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'relatedPivotKey' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'relatedPivotKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The associated key of the relation.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'parentKey' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'parentKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The key name of the parent model.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'relatedKey' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'relatedKey',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The key name of the related model.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 68,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 26,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'relationName' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'relationName',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The "name" of the relationship.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 75,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 28,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotColumns' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotColumns',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 82,
            'endLine' => 82,
            'startTokenPos' => 160,
            'startFilePos' => 2334,
            'endTokenPos' => 161,
            'endFilePos' => 2335,
          ),
        ),
        'docComment' => '/**
 * The pivot table columns to retrieve.
 *
 * @var array<string|\\Illuminate\\Contracts\\Database\\Query\\Expression>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 82,
        'endLine' => 82,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotWheres' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotWheres',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 89,
            'endLine' => 89,
            'startTokenPos' => 172,
            'startFilePos' => 2464,
            'endTokenPos' => 173,
            'endFilePos' => 2465,
          ),
        ),
        'docComment' => '/**
 * Any pivot table restrictions for where clauses.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 89,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotWhereIns' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotWhereIns',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 96,
            'endLine' => 96,
            'startTokenPos' => 184,
            'startFilePos' => 2598,
            'endTokenPos' => 185,
            'endFilePos' => 2599,
          ),
        ),
        'docComment' => '/**
 * Any pivot table restrictions for whereIn clauses.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 96,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotWhereNulls' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotWhereNulls',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 103,
            'endLine' => 103,
            'startTokenPos' => 196,
            'startFilePos' => 2736,
            'endTokenPos' => 197,
            'endFilePos' => 2737,
          ),
        ),
        'docComment' => '/**
 * Any pivot table restrictions for whereNull clauses.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 103,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotValues' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotValues',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 208,
            'startFilePos' => 2860,
            'endTokenPos' => 209,
            'endFilePos' => 2861,
          ),
        ),
        'docComment' => '/**
 * The default values for the pivot columns.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'withTimestamps' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'withTimestamps',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 117,
            'endLine' => 117,
            'startTokenPos' => 220,
            'startFilePos' => 2999,
            'endTokenPos' => 220,
            'endFilePos' => 3003,
          ),
        ),
        'docComment' => '/**
 * Indicates if timestamps are available on the pivot table.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 117,
        'endLine' => 117,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotCreatedAt' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotCreatedAt',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The custom pivot table column for the created_at timestamp.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 124,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pivotUpdatedAt' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'pivotUpdatedAt',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The custom pivot table column for the updated_at timestamp.
 *
 * @var string|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 131,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'using' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'using',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The class name of the custom pivot model to use for the relationship.
 *
 * @var class-string<TPivotModel>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 138,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'accessor' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'name' => 'accessor',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pivot\'',
          'attributes' => 
          array (
            'startLine' => 145,
            'endLine' => 145,
            'startTokenPos' => 252,
            'startFilePos' => 3600,
            'endTokenPos' => 252,
            'endFilePos' => 3606,
          ),
        ),
        'docComment' => '/**
 * The name of the accessor to use for the "pivot" relationship.
 *
 * @var TAccessor
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 145,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 34,
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
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 160,
            'endLine' => 160,
            'startColumn' => 9,
            'endColumn' => 22,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parent' => 
          array (
            'name' => 'parent',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 161,
            'endLine' => 161,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 162,
            'endLine' => 162,
            'startColumn' => 9,
            'endColumn' => 14,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'foreignPivotKey' => 
          array (
            'name' => 'foreignPivotKey',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'relatedPivotKey' => 
          array (
            'name' => 'relatedPivotKey',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 164,
            'endLine' => 164,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 4,
            'isOptional' => false,
          ),
          'parentKey' => 
          array (
            'name' => 'parentKey',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 165,
            'endLine' => 165,
            'startColumn' => 9,
            'endColumn' => 18,
            'parameterIndex' => 5,
            'isOptional' => false,
          ),
          'relatedKey' => 
          array (
            'name' => 'relatedKey',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 166,
            'endLine' => 166,
            'startColumn' => 9,
            'endColumn' => 19,
            'parameterIndex' => 6,
            'isOptional' => false,
          ),
          'relationName' => 
          array (
            'name' => 'relationName',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 167,
                'endLine' => 167,
                'startTokenPos' => 293,
                'startFilePos' => 4270,
                'endTokenPos' => 293,
                'endFilePos' => 4273,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 167,
            'endLine' => 167,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 7,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new belongs to many relationship instance.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>  $query
 * @param  TDeclaringModel  $parent
 * @param  string|class-string<TRelatedModel>  $table
 * @param  string  $foreignPivotKey
 * @param  string  $relatedPivotKey
 * @param  string  $parentKey
 * @param  string  $relatedKey
 * @param  string|null  $relationName
 */',
        'startLine' => 159,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'resolveTableName' => 
      array (
        'name' => 'resolveTableName',
        'parameters' => 
        array (
          'table' => 
          array (
            'name' => 'table',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 185,
            'endLine' => 185,
            'startColumn' => 41,
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
 * Attempt to resolve the intermediate table name from the given string.
 *
 * @param  string  $table
 * @return string
 */',
        'startLine' => 185,
        'endLine' => 202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'addConstraints' => 
      array (
        'name' => 'addConstraints',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the base constraints on the relation query.
 *
 * @return void
 */',
        'startLine' => 209,
        'endLine' => 216,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'performJoin' => 
      array (
        'name' => 'performJoin',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 224,
                'endLine' => 224,
                'startTokenPos' => 539,
                'startFilePos' => 5688,
                'endTokenPos' => 539,
                'endFilePos' => 5691,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 36,
            'endColumn' => 48,
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
 * Set the join clause for the relation query.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>|null  $query
 * @return $this
 */',
        'startLine' => 224,
        'endLine' => 239,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'addWhereConstraints' => 
      array (
        'name' => 'addWhereConstraints',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the where clause for the relation query.
 *
 * @return $this
 */',
        'startLine' => 246,
        'endLine' => 253,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'addEagerConstraints' => 
      array (
        'name' => 'addEagerConstraints',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
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
            'startLine' => 256,
            'endLine' => 256,
            'startColumn' => 41,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 256,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'initRelation' => 
      array (
        'name' => 'initRelation',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
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
            'startColumn' => 34,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'relation' => 
          array (
            'name' => 'relation',
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
            'startColumn' => 49,
            'endColumn' => 57,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 268,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'match' => 
      array (
        'name' => 'match',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
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
            'startLine' => 278,
            'endLine' => 278,
            'startColumn' => 27,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'results' => 
          array (
            'name' => 'results',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 278,
            'endLine' => 278,
            'startColumn' => 42,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'relation' => 
          array (
            'name' => 'relation',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 278,
            'endLine' => 278,
            'startColumn' => 71,
            'endColumn' => 79,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 278,
        'endLine' => 296,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'buildDictionary' => 
      array (
        'name' => 'buildDictionary',
        'parameters' => 
        array (
          'results' => 
          array (
            'name' => 'results',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Collection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 304,
            'endLine' => 304,
            'startColumn' => 40,
            'endColumn' => 66,
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
 * Build model dictionary keyed by the relation\'s foreign key.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel>  $results
 * @return array<array<array-key, TRelatedModel>>
 */',
        'startLine' => 304,
        'endLine' => 328,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getPivotClass' => 
      array (
        'name' => 'getPivotClass',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the class being used for pivot models.
 *
 * @return class-string<TPivotModel>
 */',
        'startLine' => 335,
        'endLine' => 338,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'using' => 
      array (
        'name' => 'using',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 350,
            'endLine' => 350,
            'startColumn' => 27,
            'endColumn' => 32,
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
 * Specify the custom pivot model to use for the relationship.
 *
 * @template TNewPivotModel of \\Illuminate\\Database\\Eloquent\\Relations\\Pivot
 *
 * @param  class-string<TNewPivotModel>  $class
 * @return $this
 *
 * @phpstan-this-out static<TRelatedModel, TDeclaringModel, TNewPivotModel, TAccessor>
 */',
        'startLine' => 350,
        'endLine' => 355,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'as' => 
      array (
        'name' => 'as',
        'parameters' => 
        array (
          'accessor' => 
          array (
            'name' => 'accessor',
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
            'startColumn' => 24,
            'endColumn' => 32,
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
 * Specify the custom pivot accessor to use for the relationship.
 *
 * @template TNewAccessor of string
 *
 * @param  TNewAccessor  $accessor
 * @return $this
 *
 * @phpstan-this-out static<TRelatedModel, TDeclaringModel, TPivotModel, TNewAccessor>
 */',
        'startLine' => 367,
        'endLine' => 372,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivot' => 
      array (
        'name' => 'wherePivot',
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
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 32,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'operator' => 
          array (
            'name' => 'operator',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 383,
                'endLine' => 383,
                'startTokenPos' => 1150,
                'startFilePos' => 10315,
                'endTokenPos' => 1150,
                'endFilePos' => 10318,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 41,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 383,
                'endLine' => 383,
                'startTokenPos' => 1157,
                'startFilePos' => 10330,
                'endTokenPos' => 1157,
                'endFilePos' => 10333,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 59,
            'endColumn' => 71,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 383,
                'endLine' => 383,
                'startTokenPos' => 1164,
                'startFilePos' => 10347,
                'endTokenPos' => 1164,
                'endFilePos' => 10351,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 383,
            'endLine' => 383,
            'startColumn' => 74,
            'endColumn' => 89,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a where clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  mixed  $operator
 * @param  mixed  $value
 * @param  string  $boolean
 * @return $this
 */',
        'startLine' => 383,
        'endLine' => 388,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotBetween' => 
      array (
        'name' => 'wherePivotBetween',
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
            'startLine' => 399,
            'endLine' => 399,
            'startColumn' => 39,
            'endColumn' => 45,
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
            'startLine' => 399,
            'endLine' => 399,
            'startColumn' => 48,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 399,
                'endLine' => 399,
                'startTokenPos' => 1228,
                'startFilePos' => 10856,
                'endTokenPos' => 1228,
                'endFilePos' => 10860,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 399,
            'endLine' => 399,
            'startColumn' => 63,
            'endColumn' => 78,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'not' => 
          array (
            'name' => 'not',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 399,
                'endLine' => 399,
                'startTokenPos' => 1235,
                'startFilePos' => 10870,
                'endTokenPos' => 1235,
                'endFilePos' => 10874,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 399,
            'endLine' => 399,
            'startColumn' => 81,
            'endColumn' => 92,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a "where between" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  array  $values
 * @param  string  $boolean
 * @param  bool  $not
 * @return $this
 */',
        'startLine' => 399,
        'endLine' => 402,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotBetween' => 
      array (
        'name' => 'orWherePivotBetween',
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
            'startLine' => 411,
            'endLine' => 411,
            'startColumn' => 41,
            'endColumn' => 47,
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
            'startLine' => 411,
            'endLine' => 411,
            'startColumn' => 50,
            'endColumn' => 62,
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
 * Set a "or where between" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  array  $values
 * @return $this
 */',
        'startLine' => 411,
        'endLine' => 414,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotNotBetween' => 
      array (
        'name' => 'wherePivotNotBetween',
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 42,
            'endColumn' => 48,
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 51,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 424,
                'endLine' => 424,
                'startTokenPos' => 1322,
                'startFilePos' => 11680,
                'endTokenPos' => 1322,
                'endFilePos' => 11684,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 66,
            'endColumn' => 81,
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
 * Set a "where pivot not between" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  array  $values
 * @param  string  $boolean
 * @return $this
 */',
        'startLine' => 424,
        'endLine' => 427,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotNotBetween' => 
      array (
        'name' => 'orWherePivotNotBetween',
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
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 44,
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
            'startLine' => 436,
            'endLine' => 436,
            'startColumn' => 53,
            'endColumn' => 65,
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
 * Set a "or where not between" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  array  $values
 * @return $this
 */',
        'startLine' => 436,
        'endLine' => 439,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotIn' => 
      array (
        'name' => 'wherePivotIn',
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
            'startLine' => 450,
            'endLine' => 450,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 450,
            'endLine' => 450,
            'startColumn' => 43,
            'endColumn' => 49,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 450,
                'endLine' => 450,
                'startTokenPos' => 1405,
                'startFilePos' => 12478,
                'endTokenPos' => 1405,
                'endFilePos' => 12482,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 450,
            'endLine' => 450,
            'startColumn' => 52,
            'endColumn' => 67,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'not' => 
          array (
            'name' => 'not',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 450,
                'endLine' => 450,
                'startTokenPos' => 1412,
                'startFilePos' => 12492,
                'endTokenPos' => 1412,
                'endFilePos' => 12496,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 450,
            'endLine' => 450,
            'startColumn' => 70,
            'endColumn' => 81,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set a "where in" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  mixed  $values
 * @param  string  $boolean
 * @param  bool  $not
 * @return $this
 */',
        'startLine' => 450,
        'endLine' => 455,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivot' => 
      array (
        'name' => 'orWherePivot',
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
            'startLine' => 465,
            'endLine' => 465,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'operator' => 
          array (
            'name' => 'operator',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 465,
                'endLine' => 465,
                'startTokenPos' => 1471,
                'startFilePos' => 12951,
                'endTokenPos' => 1471,
                'endFilePos' => 12954,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 465,
            'endLine' => 465,
            'startColumn' => 43,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 465,
                'endLine' => 465,
                'startTokenPos' => 1478,
                'startFilePos' => 12966,
                'endTokenPos' => 1478,
                'endFilePos' => 12969,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 465,
            'endLine' => 465,
            'startColumn' => 61,
            'endColumn' => 73,
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
 * Set an "or where" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  mixed  $operator
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 465,
        'endLine' => 468,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'withPivotValue' => 
      array (
        'name' => 'withPivotValue',
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
            'startLine' => 481,
            'endLine' => 481,
            'startColumn' => 36,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 481,
                'endLine' => 481,
                'startTokenPos' => 1519,
                'startFilePos' => 13450,
                'endTokenPos' => 1519,
                'endFilePos' => 13453,
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
            'startColumn' => 45,
            'endColumn' => 57,
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
 * Set a where clause for a pivot table column.
 *
 * In addition, new pivot records will receive this value.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression|array<string, string>  $column
 * @param  mixed  $value
 * @return $this
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 481,
        'endLine' => 498,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotIn' => 
      array (
        'name' => 'orWherePivotIn',
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
            'startLine' => 507,
            'endLine' => 507,
            'startColumn' => 36,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 507,
            'endLine' => 507,
            'startColumn' => 45,
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
 * Set an "or where in" clause for a pivot table column.
 *
 * @param  string  $column
 * @param  mixed  $values
 * @return $this
 */',
        'startLine' => 507,
        'endLine' => 510,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotNotIn' => 
      array (
        'name' => 'wherePivotNotIn',
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
            'startLine' => 520,
            'endLine' => 520,
            'startColumn' => 37,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 520,
            'endLine' => 520,
            'startColumn' => 46,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 520,
                'endLine' => 520,
                'startTokenPos' => 1681,
                'startFilePos' => 14507,
                'endTokenPos' => 1681,
                'endFilePos' => 14511,
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
            'startColumn' => 55,
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
 * Set a "where not in" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  mixed  $values
 * @param  string  $boolean
 * @return $this
 */',
        'startLine' => 520,
        'endLine' => 523,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotNotIn' => 
      array (
        'name' => 'orWherePivotNotIn',
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
            'startLine' => 532,
            'endLine' => 532,
            'startColumn' => 39,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 532,
            'endLine' => 532,
            'startColumn' => 48,
            'endColumn' => 54,
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
 * Set an "or where not in" clause for a pivot table column.
 *
 * @param  string  $column
 * @param  mixed  $values
 * @return $this
 */',
        'startLine' => 532,
        'endLine' => 535,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotNull' => 
      array (
        'name' => 'wherePivotNull',
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
            'startLine' => 545,
            'endLine' => 545,
            'startColumn' => 36,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 545,
                'endLine' => 545,
                'startTokenPos' => 1756,
                'startFilePos' => 15194,
                'endTokenPos' => 1756,
                'endFilePos' => 15198,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 545,
            'endLine' => 545,
            'startColumn' => 45,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'not' => 
          array (
            'name' => 'not',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 545,
                'endLine' => 545,
                'startTokenPos' => 1763,
                'startFilePos' => 15208,
                'endTokenPos' => 1763,
                'endFilePos' => 15212,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 545,
            'endLine' => 545,
            'startColumn' => 63,
            'endColumn' => 74,
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
 * Set a "where null" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  string  $boolean
 * @param  bool  $not
 * @return $this
 */',
        'startLine' => 545,
        'endLine' => 550,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => true,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'wherePivotNotNull' => 
      array (
        'name' => 'wherePivotNotNull',
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
            'startLine' => 559,
            'endLine' => 559,
            'startColumn' => 39,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 559,
                'endLine' => 559,
                'startTokenPos' => 1819,
                'startFilePos' => 15642,
                'endTokenPos' => 1819,
                'endFilePos' => 15646,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 559,
            'endLine' => 559,
            'startColumn' => 48,
            'endColumn' => 63,
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
 * Set a "where not null" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  string  $boolean
 * @return $this
 */',
        'startLine' => 559,
        'endLine' => 562,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotNull' => 
      array (
        'name' => 'orWherePivotNull',
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
            'startLine' => 571,
            'endLine' => 571,
            'startColumn' => 38,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'not' => 
          array (
            'name' => 'not',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 571,
                'endLine' => 571,
                'startTokenPos' => 1857,
                'startFilePos' => 15989,
                'endTokenPos' => 1857,
                'endFilePos' => 15993,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 571,
            'endLine' => 571,
            'startColumn' => 47,
            'endColumn' => 58,
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
 * Set a "or where null" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  bool  $not
 * @return $this
 */',
        'startLine' => 571,
        'endLine' => 574,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orWherePivotNotNull' => 
      array (
        'name' => 'orWherePivotNotNull',
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
            'startLine' => 582,
            'endLine' => 582,
            'startColumn' => 41,
            'endColumn' => 47,
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
 * Set a "or where not null" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @return $this
 */',
        'startLine' => 582,
        'endLine' => 585,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orderByPivot' => 
      array (
        'name' => 'orderByPivot',
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
            'startLine' => 594,
            'endLine' => 594,
            'startColumn' => 34,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'direction' => 
          array (
            'name' => 'direction',
            'default' => 
            array (
              'code' => '\'asc\'',
              'attributes' => 
              array (
                'startLine' => 594,
                'endLine' => 594,
                'startTokenPos' => 1923,
                'startFilePos' => 16644,
                'endTokenPos' => 1923,
                'endFilePos' => 16648,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 594,
            'endLine' => 594,
            'startColumn' => 43,
            'endColumn' => 60,
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
 * Add an "order by" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @param  string  $direction
 * @return $this
 */',
        'startLine' => 594,
        'endLine' => 597,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orderByPivotDesc' => 
      array (
        'name' => 'orderByPivotDesc',
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
            'startLine' => 605,
            'endLine' => 605,
            'startColumn' => 38,
            'endColumn' => 44,
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
 * Add an "order by desc" clause for a pivot table column.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @return $this
 */',
        'startLine' => 605,
        'endLine' => 608,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'findOrNew' => 
      array (
        'name' => 'findOrNew',
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
            'startLine' => 621,
            'endLine' => 621,
            'startColumn' => 31,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 621,
                'endLine' => 621,
                'startTokenPos' => 1996,
                'startFilePos' => 17555,
                'endTokenPos' => 1998,
                'endFilePos' => 17559,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 621,
            'endLine' => 621,
            'startColumn' => 36,
            'endColumn' => 51,
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
 * Find a related model by its primary key or return a new instance of the related model.
 *
 * @param  mixed  $id
 * @param  array  $columns
 * @return (
 *     $id is (\\Illuminate\\Contracts\\Support\\Arrayable<array-key, mixed>|array<mixed>)
 *     ? \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
 *     : TRelatedModel&object{pivot: TPivotModel}
 * )
 */',
        'startLine' => 621,
        'endLine' => 628,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'firstOrNew' => 
      array (
        'name' => 'firstOrNew',
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
                'startLine' => 637,
                'endLine' => 637,
                'startTokenPos' => 2062,
                'startFilePos' => 18009,
                'endTokenPos' => 2063,
                'endFilePos' => 18010,
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
            'startLine' => 637,
            'endLine' => 637,
            'startColumn' => 32,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 637,
                'endLine' => 637,
                'startTokenPos' => 2072,
                'startFilePos' => 18029,
                'endTokenPos' => 2073,
                'endFilePos' => 18030,
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
            'startLine' => 637,
            'endLine' => 637,
            'startColumn' => 56,
            'endColumn' => 73,
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
 * Get the first related model record matching the attributes or instantiate it.
 *
 * @param  array  $attributes
 * @param  array  $values
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 637,
        'endLine' => 644,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'firstOrCreate' => 
      array (
        'name' => 'firstOrCreate',
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
                'startLine' => 655,
                'endLine' => 655,
                'startTokenPos' => 2147,
                'startFilePos' => 18619,
                'endTokenPos' => 2148,
                'endFilePos' => 18620,
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
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 35,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 655,
                'endLine' => 655,
                'startTokenPos' => 2159,
                'startFilePos' => 18647,
                'endTokenPos' => 2160,
                'endFilePos' => 18648,
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
                      'name' => 'array',
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
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 59,
            'endColumn' => 84,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'joining' => 
          array (
            'name' => 'joining',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 655,
                'endLine' => 655,
                'startTokenPos' => 2169,
                'startFilePos' => 18668,
                'endTokenPos' => 2170,
                'endFilePos' => 18669,
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
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 87,
            'endColumn' => 105,
            'parameterIndex' => 2,
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
                'startLine' => 655,
                'endLine' => 655,
                'startTokenPos' => 2177,
                'startFilePos' => 18681,
                'endTokenPos' => 2177,
                'endFilePos' => 18684,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 655,
            'endLine' => 655,
            'startColumn' => 108,
            'endColumn' => 120,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the first record matching the attributes. If the record is not found, create it.
 *
 * @param  array  $attributes
 * @param  (\\Closure(): array)|array  $values
 * @param  array  $joining
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 655,
        'endLine' => 670,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'createOrFirst' => 
      array (
        'name' => 'createOrFirst',
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
                'startLine' => 681,
                'endLine' => 681,
                'startTokenPos' => 2336,
                'startFilePos' => 19706,
                'endTokenPos' => 2337,
                'endFilePos' => 19707,
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
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 35,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 681,
                'endLine' => 681,
                'startTokenPos' => 2348,
                'startFilePos' => 19734,
                'endTokenPos' => 2349,
                'endFilePos' => 19735,
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
                      'name' => 'array',
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
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 59,
            'endColumn' => 84,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'joining' => 
          array (
            'name' => 'joining',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 681,
                'endLine' => 681,
                'startTokenPos' => 2358,
                'startFilePos' => 19755,
                'endTokenPos' => 2359,
                'endFilePos' => 19756,
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
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 87,
            'endColumn' => 105,
            'parameterIndex' => 2,
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
                'startLine' => 681,
                'endLine' => 681,
                'startTokenPos' => 2366,
                'startFilePos' => 19768,
                'endTokenPos' => 2366,
                'endFilePos' => 19771,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 681,
            'endLine' => 681,
            'startColumn' => 108,
            'endColumn' => 120,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Attempt to create the record. If a unique constraint violation occurs, attempt to find the matching record.
 *
 * @param  array  $attributes
 * @param  (\\Closure(): array)|array  $values
 * @param  array  $joining
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 681,
        'endLine' => 696,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'updateOrCreate' => 
      array (
        'name' => 'updateOrCreate',
        'parameters' => 
        array (
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
            'startLine' => 707,
            'endLine' => 707,
            'startColumn' => 36,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'values' => 
          array (
            'name' => 'values',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 707,
                'endLine' => 707,
                'startTokenPos' => 2574,
                'startFilePos' => 20822,
                'endTokenPos' => 2575,
                'endFilePos' => 20823,
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
            'startLine' => 707,
            'endLine' => 707,
            'startColumn' => 55,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'joining' => 
          array (
            'name' => 'joining',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 707,
                'endLine' => 707,
                'startTokenPos' => 2584,
                'startFilePos' => 20843,
                'endTokenPos' => 2585,
                'endFilePos' => 20844,
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
            'startLine' => 707,
            'endLine' => 707,
            'startColumn' => 75,
            'endColumn' => 93,
            'parameterIndex' => 2,
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
                'startLine' => 707,
                'endLine' => 707,
                'startTokenPos' => 2592,
                'startFilePos' => 20856,
                'endTokenPos' => 2592,
                'endFilePos' => 20859,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 707,
            'endLine' => 707,
            'startColumn' => 96,
            'endColumn' => 108,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create or update a related record matching the attributes, and fill it with values.
 *
 * @param  array  $attributes
 * @param  array  $values
 * @param  array  $joining
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 707,
        'endLine' => 716,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'find' => 
      array (
        'name' => 'find',
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
            'startLine' => 729,
            'endLine' => 729,
            'startColumn' => 26,
            'endColumn' => 28,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 729,
                'endLine' => 729,
                'startTokenPos' => 2689,
                'startFilePos' => 21614,
                'endTokenPos' => 2691,
                'endFilePos' => 21618,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 729,
            'endLine' => 729,
            'startColumn' => 31,
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
 * Find a related model by its primary key.
 *
 * @param  mixed  $id
 * @param  array  $columns
 * @return (
 *     $id is (\\Illuminate\\Contracts\\Support\\Arrayable<array-key, mixed>|array<mixed>)
 *     ? \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
 *     : (TRelatedModel&object{pivot: TPivotModel})|null
 * )
 */',
        'startLine' => 729,
        'endLine' => 738,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'findSole' => 
      array (
        'name' => 'findSole',
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
            'startLine' => 750,
            'endLine' => 750,
            'startColumn' => 30,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 750,
                'endLine' => 750,
                'startTokenPos' => 2795,
                'startFilePos' => 22309,
                'endTokenPos' => 2797,
                'endFilePos' => 22313,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 750,
            'endLine' => 750,
            'startColumn' => 35,
            'endColumn' => 50,
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
 * Find a sole related model by its primary key.
 *
 * @param  mixed  $id
 * @param  array  $columns
 * @return TRelatedModel&object{pivot: TPivotModel}
 *
 * @throws \\Illuminate\\Database\\Eloquent\\ModelNotFoundException<TRelatedModel>
 * @throws \\Illuminate\\Database\\MultipleRecordsFoundException
 */',
        'startLine' => 750,
        'endLine' => 755,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'findMany' => 
      array (
        'name' => 'findMany',
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
            'startLine' => 764,
            'endLine' => 764,
            'startColumn' => 30,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 764,
                'endLine' => 764,
                'startTokenPos' => 2855,
                'startFilePos' => 22795,
                'endTokenPos' => 2857,
                'endFilePos' => 22799,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 764,
            'endLine' => 764,
            'startColumn' => 36,
            'endColumn' => 51,
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
 * Find multiple related models by their primary keys.
 *
 * @param  \\Illuminate\\Contracts\\Support\\Arrayable|array  $ids
 * @param  array  $columns
 * @return \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 764,
        'endLine' => 775,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'findOrFail' => 
      array (
        'name' => 'findOrFail',
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
            'startLine' => 790,
            'endLine' => 790,
            'startColumn' => 32,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 790,
                'endLine' => 790,
                'startTokenPos' => 2950,
                'startFilePos' => 23629,
                'endTokenPos' => 2952,
                'endFilePos' => 23633,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 790,
            'endLine' => 790,
            'startColumn' => 37,
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
 * Find a related model by its primary key or throw an exception.
 *
 * @param  mixed  $id
 * @param  array  $columns
 * @return (
 *     $id is (\\Illuminate\\Contracts\\Support\\Arrayable<array-key, mixed>|array<mixed>)
 *     ? \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>
 *     : TRelatedModel&object{pivot: TPivotModel}
 * )
 *
 * @throws \\Illuminate\\Database\\Eloquent\\ModelNotFoundException<TRelatedModel>
 */',
        'startLine' => 790,
        'endLine' => 805,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'findOr' => 
      array (
        'name' => 'findOr',
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
            'startLine' => 821,
            'endLine' => 821,
            'startColumn' => 28,
            'endColumn' => 30,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 821,
                'endLine' => 821,
                'startTokenPos' => 3095,
                'startFilePos' => 24659,
                'endTokenPos' => 3097,
                'endFilePos' => 24663,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 821,
            'endLine' => 821,
            'startColumn' => 33,
            'endColumn' => 48,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 821,
                'endLine' => 821,
                'startTokenPos' => 3107,
                'startFilePos' => 24687,
                'endTokenPos' => 3107,
                'endFilePos' => 24690,
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
            'startLine' => 821,
            'endLine' => 821,
            'startColumn' => 51,
            'endColumn' => 75,
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
 * Find a related model by its primary key or call a callback.
 *
 * @template TValue
 *
 * @param  mixed  $id
 * @param  (\\Closure(): TValue)|list<string>|string  $columns
 * @param  (\\Closure(): TValue)|null  $callback
 * @return (
 *     $id is (\\Illuminate\\Contracts\\Support\\Arrayable<array-key, mixed>|array<mixed>)
 *     ? \\Illuminate\\Database\\Eloquent\\Collection<int, TRelatedModel&object{pivot: TPivotModel}>|TValue
 *     : (TRelatedModel&object{pivot: TPivotModel})|TValue
 * )
 */',
        'startLine' => 821,
        'endLine' => 842,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'firstWhere' => 
      array (
        'name' => 'firstWhere',
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
            'startLine' => 853,
            'endLine' => 853,
            'startColumn' => 32,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'operator' => 
          array (
            'name' => 'operator',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 853,
                'endLine' => 853,
                'startTokenPos' => 3265,
                'startFilePos' => 25533,
                'endTokenPos' => 3265,
                'endFilePos' => 25536,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 853,
            'endLine' => 853,
            'startColumn' => 41,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 853,
                'endLine' => 853,
                'startTokenPos' => 3272,
                'startFilePos' => 25548,
                'endTokenPos' => 3272,
                'endFilePos' => 25551,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 853,
            'endLine' => 853,
            'startColumn' => 59,
            'endColumn' => 71,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'boolean' => 
          array (
            'name' => 'boolean',
            'default' => 
            array (
              'code' => '\'and\'',
              'attributes' => 
              array (
                'startLine' => 853,
                'endLine' => 853,
                'startTokenPos' => 3279,
                'startFilePos' => 25565,
                'endTokenPos' => 3279,
                'endFilePos' => 25569,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 853,
            'endLine' => 853,
            'startColumn' => 74,
            'endColumn' => 89,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Add a basic where clause to the query, and return the first result.
 *
 * @param  \\Closure|string|array  $column
 * @param  mixed  $operator
 * @param  mixed  $value
 * @param  string  $boolean
 * @return (TRelatedModel&object{pivot: TPivotModel})|null
 */',
        'startLine' => 853,
        'endLine' => 856,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'first' => 
      array (
        'name' => 'first',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 864,
                'endLine' => 864,
                'startTokenPos' => 3321,
                'startFilePos' => 25866,
                'endTokenPos' => 3323,
                'endFilePos' => 25870,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 864,
            'endLine' => 864,
            'startColumn' => 27,
            'endColumn' => 42,
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
 * Execute the query and get the first result.
 *
 * @param  array  $columns
 * @return (TRelatedModel&object{pivot: TPivotModel})|null
 */',
        'startLine' => 864,
        'endLine' => 869,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'firstOrFail' => 
      array (
        'name' => 'firstOrFail',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 879,
                'endLine' => 879,
                'startTokenPos' => 3383,
                'startFilePos' => 26317,
                'endTokenPos' => 3385,
                'endFilePos' => 26321,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 879,
            'endLine' => 879,
            'startColumn' => 33,
            'endColumn' => 48,
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
 * Execute the query and get the first result or throw an exception.
 *
 * @param  array  $columns
 * @return TRelatedModel&object{pivot: TPivotModel}
 *
 * @throws \\Illuminate\\Database\\Eloquent\\ModelNotFoundException<TRelatedModel>
 */',
        'startLine' => 879,
        'endLine' => 886,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'firstOr' => 
      array (
        'name' => 'firstOr',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 897,
                'endLine' => 897,
                'startTokenPos' => 3452,
                'startFilePos' => 26853,
                'endTokenPos' => 3454,
                'endFilePos' => 26857,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 897,
            'endLine' => 897,
            'startColumn' => 29,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 897,
                'endLine' => 897,
                'startTokenPos' => 3464,
                'startFilePos' => 26881,
                'endTokenPos' => 3464,
                'endFilePos' => 26884,
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
            'startLine' => 897,
            'endLine' => 897,
            'startColumn' => 47,
            'endColumn' => 71,
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
 * Execute the query and get the first result or call a callback.
 *
 * @template TValue
 *
 * @param  (\\Closure(): TValue)|list<string>  $columns
 * @param  (\\Closure(): TValue)|null  $callback
 * @return (TRelatedModel&object{pivot: TPivotModel})|TValue
 */',
        'startLine' => 897,
        'endLine' => 910,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getResults' => 
      array (
        'name' => 'getResults',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 913,
        'endLine' => 918,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'get' => 
      array (
        'name' => 'get',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 921,
                'endLine' => 921,
                'startTokenPos' => 3599,
                'startFilePos' => 27404,
                'endTokenPos' => 3601,
                'endFilePos' => 27408,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 921,
            'endLine' => 921,
            'startColumn' => 25,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 921,
        'endLine' => 946,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'shouldSelect' => 
      array (
        'name' => 'shouldSelect',
        'parameters' => 
        array (
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 954,
                'endLine' => 954,
                'startTokenPos' => 3750,
                'startFilePos' => 28596,
                'endTokenPos' => 3752,
                'endFilePos' => 28600,
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
            'startLine' => 954,
            'endLine' => 954,
            'startColumn' => 37,
            'endColumn' => 58,
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
 * Get the select columns for the relation query.
 *
 * @param  array  $columns
 * @return array
 */',
        'startLine' => 954,
        'endLine' => 961,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'aliasedPivotColumns' => 
      array (
        'name' => 'aliasedPivotColumns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the pivot columns for the relation.
 *
 * "pivot_" is prefixed at each column for easy removal later.
 *
 * @return array
 */',
        'startLine' => 970,
        'endLine' => 980,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'paginate' => 
      array (
        'name' => 'paginate',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 991,
                'endLine' => 991,
                'startTokenPos' => 3895,
                'startFilePos' => 29653,
                'endTokenPos' => 3895,
                'endFilePos' => 29656,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 30,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 991,
                'endLine' => 991,
                'startTokenPos' => 3902,
                'startFilePos' => 29670,
                'endTokenPos' => 3904,
                'endFilePos' => 29674,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 47,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'pageName' => 
          array (
            'name' => 'pageName',
            'default' => 
            array (
              'code' => '\'page\'',
              'attributes' => 
              array (
                'startLine' => 991,
                'endLine' => 991,
                'startTokenPos' => 3911,
                'startFilePos' => 29689,
                'endTokenPos' => 3911,
                'endFilePos' => 29694,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 65,
            'endColumn' => 82,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'page' => 
          array (
            'name' => 'page',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 991,
                'endLine' => 991,
                'startTokenPos' => 3918,
                'startFilePos' => 29705,
                'endTokenPos' => 3918,
                'endFilePos' => 29708,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 991,
            'endLine' => 991,
            'startColumn' => 85,
            'endColumn' => 96,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a paginator for the "select" statement.
 *
 * @param  int|null  $perPage
 * @param  array  $columns
 * @param  string  $pageName
 * @param  int|null  $page
 * @return \\Illuminate\\Pagination\\LengthAwarePaginator<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 991,
        'endLine' => 998,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'simplePaginate' => 
      array (
        'name' => 'simplePaginate',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1009,
                'endLine' => 1009,
                'startTokenPos' => 3999,
                'startFilePos' => 30328,
                'endTokenPos' => 3999,
                'endFilePos' => 30331,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1009,
            'endLine' => 1009,
            'startColumn' => 36,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 1009,
                'endLine' => 1009,
                'startTokenPos' => 4006,
                'startFilePos' => 30345,
                'endTokenPos' => 4008,
                'endFilePos' => 30349,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1009,
            'endLine' => 1009,
            'startColumn' => 53,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'pageName' => 
          array (
            'name' => 'pageName',
            'default' => 
            array (
              'code' => '\'page\'',
              'attributes' => 
              array (
                'startLine' => 1009,
                'endLine' => 1009,
                'startTokenPos' => 4015,
                'startFilePos' => 30364,
                'endTokenPos' => 4015,
                'endFilePos' => 30369,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1009,
            'endLine' => 1009,
            'startColumn' => 71,
            'endColumn' => 88,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'page' => 
          array (
            'name' => 'page',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1009,
                'endLine' => 1009,
                'startTokenPos' => 4022,
                'startFilePos' => 30380,
                'endTokenPos' => 4022,
                'endFilePos' => 30383,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1009,
            'endLine' => 1009,
            'startColumn' => 91,
            'endColumn' => 102,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Paginate the given query into a simple paginator.
 *
 * @param  int|null  $perPage
 * @param  array  $columns
 * @param  string  $pageName
 * @param  int|null  $page
 * @return \\Illuminate\\Contracts\\Pagination\\Paginator<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1009,
        'endLine' => 1016,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'cursorPaginate' => 
      array (
        'name' => 'cursorPaginate',
        'parameters' => 
        array (
          'perPage' => 
          array (
            'name' => 'perPage',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1027,
                'endLine' => 1027,
                'startTokenPos' => 4103,
                'startFilePos' => 31022,
                'endTokenPos' => 4103,
                'endFilePos' => 31025,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1027,
            'endLine' => 1027,
            'startColumn' => 36,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 1027,
                'endLine' => 1027,
                'startTokenPos' => 4110,
                'startFilePos' => 31039,
                'endTokenPos' => 4112,
                'endFilePos' => 31043,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1027,
            'endLine' => 1027,
            'startColumn' => 53,
            'endColumn' => 68,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'cursorName' => 
          array (
            'name' => 'cursorName',
            'default' => 
            array (
              'code' => '\'cursor\'',
              'attributes' => 
              array (
                'startLine' => 1027,
                'endLine' => 1027,
                'startTokenPos' => 4119,
                'startFilePos' => 31060,
                'endTokenPos' => 4119,
                'endFilePos' => 31067,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1027,
            'endLine' => 1027,
            'startColumn' => 71,
            'endColumn' => 92,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'cursor' => 
          array (
            'name' => 'cursor',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1027,
                'endLine' => 1027,
                'startTokenPos' => 4126,
                'startFilePos' => 31080,
                'endTokenPos' => 4126,
                'endFilePos' => 31083,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1027,
            'endLine' => 1027,
            'startColumn' => 95,
            'endColumn' => 108,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Paginate the given query into a cursor paginator.
 *
 * @param  int|null  $perPage
 * @param  array  $columns
 * @param  string  $cursorName
 * @param  string|null  $cursor
 * @return \\Illuminate\\Contracts\\Pagination\\CursorPaginator<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1027,
        'endLine' => 1034,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'chunk' => 
      array (
        'name' => 'chunk',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1043,
            'endLine' => 1043,
            'startColumn' => 27,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1043,
            'endLine' => 1043,
            'startColumn' => 35,
            'endColumn' => 52,
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
 * Chunk the results of the query.
 *
 * @param  int  $count
 * @param  callable  $callback
 * @return bool
 */',
        'startLine' => 1043,
        'endLine' => 1050,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'chunkById' => 
      array (
        'name' => 'chunkById',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1061,
            'endLine' => 1061,
            'startColumn' => 31,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1061,
            'endLine' => 1061,
            'startColumn' => 39,
            'endColumn' => 56,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1061,
                'endLine' => 1061,
                'startTokenPos' => 4292,
                'startFilePos' => 32092,
                'endTokenPos' => 4292,
                'endFilePos' => 32095,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1061,
            'endLine' => 1061,
            'startColumn' => 59,
            'endColumn' => 72,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1061,
                'endLine' => 1061,
                'startTokenPos' => 4299,
                'startFilePos' => 32107,
                'endTokenPos' => 4299,
                'endFilePos' => 32110,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1061,
            'endLine' => 1061,
            'startColumn' => 75,
            'endColumn' => 87,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Chunk the results of a query by comparing numeric IDs.
 *
 * @param  int  $count
 * @param  callable  $callback
 * @param  string|null  $column
 * @param  string|null  $alias
 * @return bool
 */',
        'startLine' => 1061,
        'endLine' => 1064,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'chunkByIdDesc' => 
      array (
        'name' => 'chunkByIdDesc',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1075,
            'endLine' => 1075,
            'startColumn' => 35,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1075,
            'endLine' => 1075,
            'startColumn' => 43,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1075,
                'endLine' => 1075,
                'startTokenPos' => 4345,
                'startFilePos' => 32524,
                'endTokenPos' => 4345,
                'endFilePos' => 32527,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1075,
            'endLine' => 1075,
            'startColumn' => 63,
            'endColumn' => 76,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1075,
                'endLine' => 1075,
                'startTokenPos' => 4352,
                'startFilePos' => 32539,
                'endTokenPos' => 4352,
                'endFilePos' => 32542,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1075,
            'endLine' => 1075,
            'startColumn' => 79,
            'endColumn' => 91,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Chunk the results of a query by comparing IDs in descending order.
 *
 * @param  int  $count
 * @param  callable  $callback
 * @param  string|null  $column
 * @param  string|null  $alias
 * @return bool
 */',
        'startLine' => 1075,
        'endLine' => 1078,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'eachById' => 
      array (
        'name' => 'eachById',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1089,
            'endLine' => 1089,
            'startColumn' => 30,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'count' => 
          array (
            'name' => 'count',
            'default' => 
            array (
              'code' => '1000',
              'attributes' => 
              array (
                'startLine' => 1089,
                'endLine' => 1089,
                'startTokenPos' => 4401,
                'startFilePos' => 32949,
                'endTokenPos' => 4401,
                'endFilePos' => 32952,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1089,
            'endLine' => 1089,
            'startColumn' => 50,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1089,
                'endLine' => 1089,
                'startTokenPos' => 4408,
                'startFilePos' => 32965,
                'endTokenPos' => 4408,
                'endFilePos' => 32968,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1089,
            'endLine' => 1089,
            'startColumn' => 65,
            'endColumn' => 78,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1089,
                'endLine' => 1089,
                'startTokenPos' => 4415,
                'startFilePos' => 32980,
                'endTokenPos' => 4415,
                'endFilePos' => 32983,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1089,
            'endLine' => 1089,
            'startColumn' => 81,
            'endColumn' => 93,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Execute a callback over each item while chunking by ID.
 *
 * @param  callable  $callback
 * @param  int  $count
 * @param  string|null  $column
 * @param  string|null  $alias
 * @return bool
 */',
        'startLine' => 1089,
        'endLine' => 1098,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'orderedChunkById' => 
      array (
        'name' => 'orderedChunkById',
        'parameters' => 
        array (
          'count' => 
          array (
            'name' => 'count',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1110,
            'endLine' => 1110,
            'startColumn' => 38,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1110,
            'endLine' => 1110,
            'startColumn' => 46,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1110,
                'endLine' => 1110,
                'startTokenPos' => 4540,
                'startFilePos' => 33675,
                'endTokenPos' => 4540,
                'endFilePos' => 33678,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1110,
            'endLine' => 1110,
            'startColumn' => 66,
            'endColumn' => 79,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1110,
                'endLine' => 1110,
                'startTokenPos' => 4547,
                'startFilePos' => 33690,
                'endTokenPos' => 4547,
                'endFilePos' => 33693,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1110,
            'endLine' => 1110,
            'startColumn' => 82,
            'endColumn' => 94,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'descending' => 
          array (
            'name' => 'descending',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 1110,
                'endLine' => 1110,
                'startTokenPos' => 4554,
                'startFilePos' => 33710,
                'endTokenPos' => 4554,
                'endFilePos' => 33714,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1110,
            'endLine' => 1110,
            'startColumn' => 97,
            'endColumn' => 115,
            'parameterIndex' => 4,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Chunk the results of a query by comparing IDs in a given order.
 *
 * @param  int  $count
 * @param  callable  $callback
 * @param  string|null  $column
 * @param  string|null  $alias
 * @param  bool  $descending
 * @return bool
 */',
        'startLine' => 1110,
        'endLine' => 1123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'each' => 
      array (
        'name' => 'each',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1132,
            'endLine' => 1132,
            'startColumn' => 26,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'count' => 
          array (
            'name' => 'count',
            'default' => 
            array (
              'code' => '1000',
              'attributes' => 
              array (
                'startLine' => 1132,
                'endLine' => 1132,
                'startTokenPos' => 4677,
                'startFilePos' => 34364,
                'endTokenPos' => 4677,
                'endFilePos' => 34367,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1132,
            'endLine' => 1132,
            'startColumn' => 46,
            'endColumn' => 58,
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
 * Execute a callback over each item while chunking.
 *
 * @param  callable  $callback
 * @param  int  $count
 * @return bool
 */',
        'startLine' => 1132,
        'endLine' => 1141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'lazy' => 
      array (
        'name' => 'lazy',
        'parameters' => 
        array (
          'chunkSize' => 
          array (
            'name' => 'chunkSize',
            'default' => 
            array (
              'code' => '1000',
              'attributes' => 
              array (
                'startLine' => 1149,
                'endLine' => 1149,
                'startTokenPos' => 4766,
                'startFilePos' => 34882,
                'endTokenPos' => 4766,
                'endFilePos' => 34885,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1149,
            'endLine' => 1149,
            'startColumn' => 26,
            'endColumn' => 42,
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
 * Query lazily, by chunks of the given size.
 *
 * @param  int  $chunkSize
 * @return \\Illuminate\\Support\\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1149,
        'endLine' => 1156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'lazyById' => 
      array (
        'name' => 'lazyById',
        'parameters' => 
        array (
          'chunkSize' => 
          array (
            'name' => 'chunkSize',
            'default' => 
            array (
              'code' => '1000',
              'attributes' => 
              array (
                'startLine' => 1166,
                'endLine' => 1166,
                'startTokenPos' => 4827,
                'startFilePos' => 35417,
                'endTokenPos' => 4827,
                'endFilePos' => 35420,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1166,
            'endLine' => 1166,
            'startColumn' => 30,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1166,
                'endLine' => 1166,
                'startTokenPos' => 4834,
                'startFilePos' => 35433,
                'endTokenPos' => 4834,
                'endFilePos' => 35436,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1166,
            'endLine' => 1166,
            'startColumn' => 49,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1166,
                'endLine' => 1166,
                'startTokenPos' => 4841,
                'startFilePos' => 35448,
                'endTokenPos' => 4841,
                'endFilePos' => 35451,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1166,
            'endLine' => 1166,
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
 * Query lazily, by chunking the results of a query by comparing IDs.
 *
 * @param  int  $chunkSize
 * @param  string|null  $column
 * @param  string|null  $alias
 * @return \\Illuminate\\Support\\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1166,
        'endLine' => 1179,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'lazyByIdDesc' => 
      array (
        'name' => 'lazyByIdDesc',
        'parameters' => 
        array (
          'chunkSize' => 
          array (
            'name' => 'chunkSize',
            'default' => 
            array (
              'code' => '1000',
              'attributes' => 
              array (
                'startLine' => 1189,
                'endLine' => 1189,
                'startTokenPos' => 4941,
                'startFilePos' => 36183,
                'endTokenPos' => 4941,
                'endFilePos' => 36186,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1189,
            'endLine' => 1189,
            'startColumn' => 34,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'column' => 
          array (
            'name' => 'column',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1189,
                'endLine' => 1189,
                'startTokenPos' => 4948,
                'startFilePos' => 36199,
                'endTokenPos' => 4948,
                'endFilePos' => 36202,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1189,
            'endLine' => 1189,
            'startColumn' => 53,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'alias' => 
          array (
            'name' => 'alias',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1189,
                'endLine' => 1189,
                'startTokenPos' => 4955,
                'startFilePos' => 36214,
                'endTokenPos' => 4955,
                'endFilePos' => 36217,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1189,
            'endLine' => 1189,
            'startColumn' => 69,
            'endColumn' => 81,
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
 * Query lazily, by chunking the results of a query by comparing IDs in descending order.
 *
 * @param  int  $chunkSize
 * @param  string|null  $column
 * @param  string|null  $alias
 * @return \\Illuminate\\Support\\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1189,
        'endLine' => 1202,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'cursor' => 
      array (
        'name' => 'cursor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a lazy collection for the given query.
 *
 * @return \\Illuminate\\Support\\LazyCollection<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1209,
        'endLine' => 1216,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'prepareQueryBuilder' => 
      array (
        'name' => 'prepareQueryBuilder',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the query builder for query execution.
 *
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 1223,
        'endLine' => 1226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'hydratePivotRelation' => 
      array (
        'name' => 'hydratePivotRelation',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
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
            'startLine' => 1234,
            'endLine' => 1234,
            'startColumn' => 45,
            'endColumn' => 57,
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
 * Hydrate the pivot table relationship on the models.
 *
 * @param  array<int, TRelatedModel>  $models
 * @return void
 */',
        'startLine' => 1234,
        'endLine' => 1244,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'migratePivotAttributes' => 
      array (
        'name' => 'migratePivotAttributes',
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
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1252,
            'endLine' => 1252,
            'startColumn' => 47,
            'endColumn' => 58,
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
 * Get the pivot attributes from a model.
 *
 * @param  TRelatedModel  $model
 * @return array
 */',
        'startLine' => 1252,
        'endLine' => 1268,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'touchIfTouching' => 
      array (
        'name' => 'touchIfTouching',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * If we\'re touching the parent model, touch.
 *
 * @return void
 */',
        'startLine' => 1275,
        'endLine' => 1284,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'touchingParent' => 
      array (
        'name' => 'touchingParent',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if we should touch the parent on sync.
 *
 * @return bool
 */',
        'startLine' => 1291,
        'endLine' => 1294,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'guessInverseRelation' => 
      array (
        'name' => 'guessInverseRelation',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Attempt to guess the name of the inverse of the relation.
 *
 * @return string
 */',
        'startLine' => 1301,
        'endLine' => 1304,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'touch' => 
      array (
        'name' => 'touch',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Touch all of the related models for the relationship.
 *
 * E.g.: Touch all roles associated with this user.
 *
 * @return void
 */',
        'startLine' => 1313,
        'endLine' => 1329,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'allRelatedIds' => 
      array (
        'name' => 'allRelatedIds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the IDs for the related models.
 *
 * @return \\Illuminate\\Support\\Collection<int, int|string>
 */',
        'startLine' => 1336,
        'endLine' => 1339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'save' => 
      array (
        'name' => 'save',
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
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1349,
            'endLine' => 1349,
            'startColumn' => 26,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pivotAttributes' => 
          array (
            'name' => 'pivotAttributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1349,
                'endLine' => 1349,
                'startTokenPos' => 5585,
                'startFilePos' => 40856,
                'endTokenPos' => 5586,
                'endFilePos' => 40857,
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
            'startLine' => 1349,
            'endLine' => 1349,
            'startColumn' => 40,
            'endColumn' => 66,
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
                'startLine' => 1349,
                'endLine' => 1349,
                'startTokenPos' => 5593,
                'startFilePos' => 40869,
                'endTokenPos' => 5593,
                'endFilePos' => 40872,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1349,
            'endLine' => 1349,
            'startColumn' => 69,
            'endColumn' => 81,
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
 * Save a new model and attach it to the parent model.
 *
 * @param  TRelatedModel  $model
 * @param  array  $pivotAttributes
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 1349,
        'endLine' => 1356,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'saveQuietly' => 
      array (
        'name' => 'saveQuietly',
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
                'name' => 'Illuminate\\Database\\Eloquent\\Model',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1366,
            'endLine' => 1366,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pivotAttributes' => 
          array (
            'name' => 'pivotAttributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1366,
                'endLine' => 1366,
                'startTokenPos' => 5652,
                'startFilePos' => 41352,
                'endTokenPos' => 5653,
                'endFilePos' => 41353,
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
            'startLine' => 1366,
            'endLine' => 1366,
            'startColumn' => 47,
            'endColumn' => 73,
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
                'startLine' => 1366,
                'endLine' => 1366,
                'startTokenPos' => 5660,
                'startFilePos' => 41365,
                'endTokenPos' => 5660,
                'endFilePos' => 41368,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1366,
            'endLine' => 1366,
            'startColumn' => 76,
            'endColumn' => 88,
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
 * Save a new model without raising any events and attach it to the parent model.
 *
 * @param  TRelatedModel  $model
 * @param  array  $pivotAttributes
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 1366,
        'endLine' => 1371,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'saveMany' => 
      array (
        'name' => 'saveMany',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1382,
            'endLine' => 1382,
            'startColumn' => 30,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pivotAttributes' => 
          array (
            'name' => 'pivotAttributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1382,
                'endLine' => 1382,
                'startTokenPos' => 5729,
                'startFilePos' => 41936,
                'endTokenPos' => 5730,
                'endFilePos' => 41937,
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
            'startLine' => 1382,
            'endLine' => 1382,
            'startColumn' => 39,
            'endColumn' => 65,
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
 * Save an array of new models and attach them to the parent model.
 *
 * @template TContainer of \\Illuminate\\Support\\Collection<array-key, TRelatedModel>|array<array-key, TRelatedModel>
 *
 * @param  TContainer  $models
 * @param  array  $pivotAttributes
 * @return TContainer
 */',
        'startLine' => 1382,
        'endLine' => 1391,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'saveManyQuietly' => 
      array (
        'name' => 'saveManyQuietly',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1402,
            'endLine' => 1402,
            'startColumn' => 37,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'pivotAttributes' => 
          array (
            'name' => 'pivotAttributes',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1402,
                'endLine' => 1402,
                'startTokenPos' => 5810,
                'startFilePos' => 42568,
                'endTokenPos' => 5811,
                'endFilePos' => 42569,
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
            'startLine' => 1402,
            'endLine' => 1402,
            'startColumn' => 46,
            'endColumn' => 72,
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
 * Save an array of new models without raising any events and attach them to the parent model.
 *
 * @template TContainer of \\Illuminate\\Support\\Collection<array-key, TRelatedModel>|array<array-key, TRelatedModel>
 *
 * @param  TContainer  $models
 * @param  array  $pivotAttributes
 * @return TContainer
 */',
        'startLine' => 1402,
        'endLine' => 1407,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'create' => 
      array (
        'name' => 'create',
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
                'startLine' => 1417,
                'endLine' => 1417,
                'startTokenPos' => 5871,
                'startFilePos' => 43012,
                'endTokenPos' => 5872,
                'endFilePos' => 43013,
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
            'startLine' => 1417,
            'endLine' => 1417,
            'startColumn' => 28,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'joining' => 
          array (
            'name' => 'joining',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1417,
                'endLine' => 1417,
                'startTokenPos' => 5881,
                'startFilePos' => 43033,
                'endTokenPos' => 5882,
                'endFilePos' => 43034,
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
            'startLine' => 1417,
            'endLine' => 1417,
            'startColumn' => 52,
            'endColumn' => 70,
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
                'startLine' => 1417,
                'endLine' => 1417,
                'startTokenPos' => 5889,
                'startFilePos' => 43046,
                'endTokenPos' => 5889,
                'endFilePos' => 43049,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1417,
            'endLine' => 1417,
            'startColumn' => 73,
            'endColumn' => 85,
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
 * Create a new instance of the related model.
 *
 * @param  array  $attributes
 * @param  array  $joining
 * @param  bool  $touch
 * @return TRelatedModel&object{pivot: TPivotModel}
 */',
        'startLine' => 1417,
        'endLine' => 1431,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'createMany' => 
      array (
        'name' => 'createMany',
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
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1440,
            'endLine' => 1440,
            'startColumn' => 32,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'joinings' => 
          array (
            'name' => 'joinings',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1440,
                'endLine' => 1440,
                'startTokenPos' => 5987,
                'startFilePos' => 43878,
                'endTokenPos' => 5988,
                'endFilePos' => 43879,
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
            'startLine' => 1440,
            'endLine' => 1440,
            'startColumn' => 51,
            'endColumn' => 70,
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
 * Create an array of new instances of the related models.
 *
 * @param  iterable  $records
 * @param  array  $joinings
 * @return array<int, TRelatedModel&object{pivot: TPivotModel}>
 */',
        'startLine' => 1440,
        'endLine' => 1451,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getRelationExistenceQuery' => 
      array (
        'name' => 'getRelationExistenceQuery',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1454,
            'endLine' => 1454,
            'startColumn' => 47,
            'endColumn' => 60,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parentQuery' => 
          array (
            'name' => 'parentQuery',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1454,
            'endLine' => 1454,
            'startColumn' => 63,
            'endColumn' => 82,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 1454,
                'endLine' => 1454,
                'startTokenPos' => 6087,
                'startFilePos' => 44251,
                'endTokenPos' => 6089,
                'endFilePos' => 44255,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1454,
            'endLine' => 1454,
            'startColumn' => 85,
            'endColumn' => 100,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/** @inheritDoc */',
        'startLine' => 1454,
        'endLine' => 1463,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getRelationExistenceQueryForSelfJoin' => 
      array (
        'name' => 'getRelationExistenceQueryForSelfJoin',
        'parameters' => 
        array (
          'query' => 
          array (
            'name' => 'query',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1473,
            'endLine' => 1473,
            'startColumn' => 58,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parentQuery' => 
          array (
            'name' => 'parentQuery',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Eloquent\\Builder',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1473,
            'endLine' => 1473,
            'startColumn' => 74,
            'endColumn' => 93,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'columns' => 
          array (
            'name' => 'columns',
            'default' => 
            array (
              'code' => '[\'*\']',
              'attributes' => 
              array (
                'startLine' => 1473,
                'endLine' => 1473,
                'startTokenPos' => 6184,
                'startFilePos' => 45030,
                'endTokenPos' => 6186,
                'endFilePos' => 45034,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1473,
            'endLine' => 1473,
            'startColumn' => 96,
            'endColumn' => 111,
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
 * Add the constraints for a relationship query on the same table.
 *
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>  $query
 * @param  \\Illuminate\\Database\\Eloquent\\Builder<TDeclaringModel>  $parentQuery
 * @param  mixed  $columns
 * @return \\Illuminate\\Database\\Eloquent\\Builder<TRelatedModel>
 */',
        'startLine' => 1473,
        'endLine' => 1484,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'take' => 
      array (
        'name' => 'take',
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
            'startLine' => 1492,
            'endLine' => 1492,
            'startColumn' => 26,
            'endColumn' => 31,
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
 * Alias to set the "limit" value of the query.
 *
 * @param  int  $value
 * @return $this
 */',
        'startLine' => 1492,
        'endLine' => 1495,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'limit' => 
      array (
        'name' => 'limit',
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
            'startLine' => 1503,
            'endLine' => 1503,
            'startColumn' => 27,
            'endColumn' => 32,
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
 * Set the "limit" value of the query.
 *
 * @param  int  $value
 * @return $this
 */',
        'startLine' => 1503,
        'endLine' => 1520,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getExistenceCompareKey' => 
      array (
        'name' => 'getExistenceCompareKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the key for comparing against the parent key in "has" query.
 *
 * @return string
 */',
        'startLine' => 1527,
        'endLine' => 1530,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'withTimestamps' => 
      array (
        'name' => 'withTimestamps',
        'parameters' => 
        array (
          'createdAt' => 
          array (
            'name' => 'createdAt',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1539,
                'endLine' => 1539,
                'startTokenPos' => 6459,
                'startFilePos' => 46688,
                'endTokenPos' => 6459,
                'endFilePos' => 46691,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1539,
            'endLine' => 1539,
            'startColumn' => 36,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'updatedAt' => 
          array (
            'name' => 'updatedAt',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1539,
                'endLine' => 1539,
                'startTokenPos' => 6466,
                'startFilePos' => 46707,
                'endTokenPos' => 6466,
                'endFilePos' => 46710,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1539,
            'endLine' => 1539,
            'startColumn' => 55,
            'endColumn' => 71,
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
 * Specify that the pivot table has creation and update timestamps.
 *
 * @param  string|null|false  $createdAt
 * @param  string|null|false  $updatedAt
 * @return $this
 */',
        'startLine' => 1539,
        'endLine' => 1552,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'createdAt' => 
      array (
        'name' => 'createdAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "created at" column.
 *
 * @return string
 */',
        'startLine' => 1559,
        'endLine' => 1562,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'updatedAt' => 
      array (
        'name' => 'updatedAt',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "updated at" column.
 *
 * @return string
 */',
        'startLine' => 1569,
        'endLine' => 1572,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getForeignPivotKeyName' => 
      array (
        'name' => 'getForeignPivotKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the foreign key for the relation.
 *
 * @return string
 */',
        'startLine' => 1579,
        'endLine' => 1582,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getQualifiedForeignPivotKeyName' => 
      array (
        'name' => 'getQualifiedForeignPivotKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified foreign key for the relation.
 *
 * @return string
 */',
        'startLine' => 1589,
        'endLine' => 1592,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getRelatedPivotKeyName' => 
      array (
        'name' => 'getRelatedPivotKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the "related key" for the relation.
 *
 * @return string
 */',
        'startLine' => 1599,
        'endLine' => 1602,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getQualifiedRelatedPivotKeyName' => 
      array (
        'name' => 'getQualifiedRelatedPivotKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified "related key" for the relation.
 *
 * @return string
 */',
        'startLine' => 1609,
        'endLine' => 1612,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getParentKeyName' => 
      array (
        'name' => 'getParentKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the parent key for the relationship.
 *
 * @return string
 */',
        'startLine' => 1619,
        'endLine' => 1622,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getQualifiedParentKeyName' => 
      array (
        'name' => 'getQualifiedParentKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified parent key name for the relation.
 *
 * @return string
 */',
        'startLine' => 1629,
        'endLine' => 1632,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getRelatedKeyName' => 
      array (
        'name' => 'getRelatedKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the related key for the relationship.
 *
 * @return string
 */',
        'startLine' => 1639,
        'endLine' => 1642,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getQualifiedRelatedKeyName' => 
      array (
        'name' => 'getQualifiedRelatedKeyName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified related key name for the relation.
 *
 * @return string
 */',
        'startLine' => 1649,
        'endLine' => 1652,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getTable' => 
      array (
        'name' => 'getTable',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the intermediate table for the relationship.
 *
 * @return string
 */',
        'startLine' => 1659,
        'endLine' => 1662,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getRelationName' => 
      array (
        'name' => 'getRelationName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the relationship name for the relationship.
 *
 * @return string
 */',
        'startLine' => 1669,
        'endLine' => 1672,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getPivotAccessor' => 
      array (
        'name' => 'getPivotAccessor',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the pivot accessor for this relationship.
 *
 * @return TAccessor
 */',
        'startLine' => 1679,
        'endLine' => 1682,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'getPivotColumns' => 
      array (
        'name' => 'getPivotColumns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the pivot columns for this relationship.
 *
 * @return array
 */',
        'startLine' => 1689,
        'endLine' => 1692,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'aliasName' => NULL,
      ),
      'qualifyPivotColumn' => 
      array (
        'name' => 'qualifyPivotColumn',
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
            'startLine' => 1700,
            'endLine' => 1700,
            'startColumn' => 40,
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
 * Qualify the given column name by the pivot table.
 *
 * @param  string|\\Illuminate\\Contracts\\Database\\Query\\Expression  $column
 * @return string|\\Illuminate\\Contracts\\Database\\Query\\Expression
 */',
        'startLine' => 1700,
        'endLine' => 1709,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Relations',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
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