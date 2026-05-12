<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Support/ElementConstants.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Support\ElementConstants
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-77afb3e01d9abaf1d57e3c7ff34c7c1f12240720f262b1bd2b762830746aa4a4-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Support\\ElementConstants',
        'filename' => '/var/www/KrosmozJdr/app/Support/ElementConstants.php',
      ),
    ),
    'namespace' => 'App\\Support',
    'name' => 'App\\Support\\ElementConstants',
    'shortName' => 'ElementConstants',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Constantes partagées pour les éléments (Spell, Capability).
 *
 * Stockage : masque 7 bits (voir {@see ElementBitmask}).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 81,
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
      'ELEMENT' => 
      array (
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'name' => 'ELEMENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 15,
            'startTokenPos' => 35,
            'startFilePos' => 364,
            'endTokenPos' => 36,
            'endFilePos' => 365,
          ),
        ),
        'docComment' => '/** @var array<int, string> @deprecated Utiliser ElementBitmask::PRIMARY_LABELS ou ElementBitmask::label() */',
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 30,
      ),
      'PRIMARIES' => 
      array (
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'name' => 'PRIMARIES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[0, 1, 2, 3, 4, 5, 6]',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 49,
            'startFilePos' => 455,
            'endTokenPos' => 69,
            'endFilePos' => 475,
          ),
        ),
        'docComment' => '/** Index des primaires sélectionnables (0–6). */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 51,
      ),
      'LEGACY_STRING_TO_INT' => 
      array (
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'name' => 'LEGACY_STRING_TO_INT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'0\' => 0, \'1\' => 1, \'2\' => 2, \'3\' => 3, \'4\' => 4, \'5\' => 5, \'6\' => 0, \'neutral\' => 0, \'earth\' => 1, \'terre\' => 1, \'fire\' => 2, \'feu\' => 2, \'air\' => 3, \'water\' => 4, \'eau\' => 4]',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 41,
            'startTokenPos' => 82,
            'startFilePos' => 734,
            'endTokenPos' => 189,
            'endFilePos' => 1037,
          ),
        ),
        'docComment' => '/**
 * Mapping legacy capability element (string) → valeur int (masque ou ancien code ≤29 normalisé à la volée).
 *
 * @deprecated Étendre au besoin pour sagesse/vitalité textuelles
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'getLabel' => 
      array (
        'name' => 'getLabel',
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
            'startLine' => 43,
            'endLine' => 43,
            'startColumn' => 37,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
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
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 43,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'currentClassName' => 'App\\Support\\ElementConstants',
        'aliasName' => NULL,
      ),
      'isValid' => 
      array (
        'name' => 'isValid',
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
            'startLine' => 53,
            'endLine' => 53,
            'startColumn' => 36,
            'endColumn' => 45,
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
        'startLine' => 53,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'currentClassName' => 'App\\Support\\ElementConstants',
        'aliasName' => NULL,
      ),
      'getColorToken' => 
      array (
        'name' => 'getColorToken',
        'parameters' => 
        array (
          'primaryIndex' => 
          array (
            'name' => 'primaryIndex',
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 42,
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
        'docComment' => '/**
 * Token couleur Tailwind pour un primaire seul (0–6).
 */',
        'startLine' => 61,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'currentClassName' => 'App\\Support\\ElementConstants',
        'aliasName' => NULL,
      ),
      'getDaisyColor' => 
      array (
        'name' => 'getDaisyColor',
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
            'startLine' => 77,
            'endLine' => 77,
            'startColumn' => 42,
            'endColumn' => 51,
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
        'docComment' => '/** @deprecated Utiliser getColorToken */',
        'startLine' => 77,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support',
        'declaringClassName' => 'App\\Support\\ElementConstants',
        'implementingClassName' => 'App\\Support\\ElementConstants',
        'currentClassName' => 'App\\Support\\ElementConstants',
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