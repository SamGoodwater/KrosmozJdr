<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Support/Cms/RulesCharacteristicKrefReplacementCatalog.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Support\Cms\RulesCharacteristicKrefReplacementCatalog
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-60d1ff04b6de212a39de3e569d5dda4484f6f460c6f5066902a3217cc5c699ef-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'filename' => '/var/www/KrosmozJdr/app/Support/Cms/RulesCharacteristicKrefReplacementCatalog.php',
      ),
    ),
    'namespace' => 'App\\Support\\Cms',
    'name' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
    'shortName' => 'RulesCharacteristicKrefReplacementCatalog',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Catalogue des remplacements texte → shortcode {@code [[kref:characteristic:clé|libellé]]}
 * pour les fichiers Markdown des règles (hors blocs masqués).
 *
 * @see docs/400- Jeu/420- Règles/REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 283,
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
      'orderedPairs' => 
      array (
        'name' => 'orderedPairs',
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
 * @return list<array{0: string, 1: string}> Paires [libellé à matcher, clé BDD]
 */',
        'startLine' => 16,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'abbreviationPatterns' => 
      array (
        'name' => 'abbreviationPatterns',
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
 * Abréviations courantes (contexte JDR) → shortcode avec libellé court.
 *
 * @return list<array{0: string, 1: string, 2: string}> [regex sans délimiteurs, clé, libellé affiché]
 */',
        'startLine' => 154,
        'endLine' => 163,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'applyToMarkdown' => 
      array (
        'name' => 'applyToMarkdown',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startLine' => 165,
            'endLine' => 165,
            'startColumn' => 44,
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
        'startLine' => 165,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'maskProtectedRegions' => 
      array (
        'name' => 'maskProtectedRegions',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startLine' => 178,
            'endLine' => 178,
            'startColumn' => 50,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'placeholders' => 
          array (
            'name' => 'placeholders',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 178,
            'endLine' => 178,
            'startColumn' => 68,
            'endColumn' => 87,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, string>  $placeholders
 */',
        'startLine' => 178,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'unmask' => 
      array (
        'name' => 'unmask',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 36,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'placeholders' => 
          array (
            'name' => 'placeholders',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 54,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, string>  $placeholders
 */',
        'startLine' => 213,
        'endLine' => 220,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'applyWordPairs' => 
      array (
        'name' => 'applyWordPairs',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startLine' => 225,
            'endLine' => 225,
            'startColumn' => 44,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'placeholders' => 
          array (
            'name' => 'placeholders',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 225,
            'endLine' => 225,
            'startColumn' => 62,
            'endColumn' => 81,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, string>  $placeholders
 */',
        'startLine' => 225,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'maskInlineKrefShortcodes' => 
      array (
        'name' => 'maskInlineKrefShortcodes',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startLine' => 248,
            'endLine' => 248,
            'startColumn' => 54,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'placeholders' => 
          array (
            'name' => 'placeholders',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 248,
            'endLine' => 248,
            'startColumn' => 72,
            'endColumn' => 91,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Remplace chaque shortcode {@code [[kref:…]]} par un jeton masqué pour les passes suivantes.
 *
 * @param  array<string, string>  $placeholders
 */',
        'startLine' => 248,
        'endLine' => 270,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'aliasName' => NULL,
      ),
      'applyAbbreviationPatterns' => 
      array (
        'name' => 'applyAbbreviationPatterns',
        'parameters' => 
        array (
          'markdown' => 
          array (
            'name' => 'markdown',
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
            'startColumn' => 55,
            'endColumn' => 70,
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
        'startLine' => 272,
        'endLine' => 282,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Support\\Cms',
        'declaringClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'implementingClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
        'currentClassName' => 'App\\Support\\Cms\\RulesCharacteristicKrefReplacementCatalog',
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