<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Console/Concerns/NormalizesProjectSyncEntities.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Concerns\NormalizesProjectSyncEntities
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5ca7f35490a27e3a0d9bdd6b7d513f9b43f015a65faf67d4b32dbdbe387830e5-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'filename' => '/var/www/KrosmozJdr/app/Console/Concerns/NormalizesProjectSyncEntities.php',
      ),
    ),
    'namespace' => 'App\\Console\\Concerns',
    'name' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
    'shortName' => 'NormalizesProjectSyncEntities',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Normalise les noms d’entités passés en CLI (alias utilisateur → clés internes / scrapping).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 45,
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
      'normalizeEntityToken' => 
      array (
        'name' => 'normalizeEntityToken',
        'parameters' => 
        array (
          'raw' => 
          array (
            'name' => 'raw',
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
            'startLine' => 15,
            'endLine' => 15,
            'startColumn' => 45,
            'endColumn' => 55,
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
        'docComment' => '/**
 * Alias utilisateur → entité interne (scrapping / project:data:sync).
 */',
        'startLine' => 15,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'implementingClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'currentClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'aliasName' => NULL,
      ),
      'normalizeEntityCsvToList' => 
      array (
        'name' => 'normalizeEntityCsvToList',
        'parameters' => 
        array (
          'csv' => 
          array (
            'name' => 'csv',
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 49,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return list<string>
 */',
        'startLine' => 28,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'implementingClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'currentClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'aliasName' => NULL,
      ),
      'normalizeEntityCsvToOptionString' => 
      array (
        'name' => 'normalizeEntityCsvToOptionString',
        'parameters' => 
        array (
          'csv' => 
          array (
            'name' => 'csv',
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 57,
            'endColumn' => 67,
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
        'startLine' => 41,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'implementingClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
        'currentClassName' => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
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