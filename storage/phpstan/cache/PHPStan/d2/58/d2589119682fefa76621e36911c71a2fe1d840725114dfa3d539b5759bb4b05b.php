<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Characteristics/CharacteristicDefinitionReader.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Characteristics\CharacteristicDefinitionReader
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-250f3c1858c31e820d19e9d3b54539f240effa3e2d5bde23ed4ad56ef9830521-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'filename' => '/var/www/KrosmozJdr/app/Services/Characteristics/CharacteristicDefinitionReader.php',
      ),
    ),
    'namespace' => 'App\\Services\\Characteristics',
    'name' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
    'shortName' => 'CharacteristicDefinitionReader',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Parcourt les fichiers `stem-groupe-definition.json` et charge le contenu utile au seed.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 86,
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
      'allDefinitionAbsolutePaths' => 
      array (
        'name' => 'allDefinitionAbsolutePaths',
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
 * @return list<string> chemins absolus triés
 */',
        'startLine' => 18,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Characteristics',
        'declaringClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'implementingClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'currentClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'aliasName' => NULL,
      ),
      'assertPathUnderDefinitionsRoot' => 
      array (
        'name' => 'assertPathUnderDefinitionsRoot',
        'parameters' => 
        array (
          'absolutePath' => 
          array (
            'name' => 'absolutePath',
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
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 59,
            'endColumn' => 78,
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
        'docComment' => '/**
 * Vérifie que le fichier est sous {@see CharacteristicDefinitionNaming::RELATIVE_ROOT} (contre les chemins arbitraires).
 */',
        'startLine' => 45,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Characteristics',
        'declaringClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'implementingClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'currentClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'aliasName' => NULL,
      ),
      'load' => 
      array (
        'name' => 'load',
        'parameters' => 
        array (
          'absolutePath' => 
          array (
            'name' => 'absolutePath',
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
            'startLine' => 63,
            'endLine' => 63,
            'startColumn' => 33,
            'endColumn' => 52,
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
 * @return array{characteristic: array<string, mixed>, entities: array<string, array<string, mixed>>, relations: mixed}
 */',
        'startLine' => 63,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Characteristics',
        'declaringClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'implementingClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
        'currentClassName' => 'App\\Services\\Characteristics\\CharacteristicDefinitionReader',
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