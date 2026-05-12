<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Characteristic/CharacteristicColorCssGenerator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Characteristic\CharacteristicColorCssGenerator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5b02b0d865bd5e0450d8efe182c5b901437a4bb7472e82f3c325a7ec67345276-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'filename' => '/var/www/KrosmozJdr/app/Services/Characteristic/CharacteristicColorCssGenerator.php',
      ),
    ),
    'namespace' => 'App\\Services\\Characteristic',
    'name' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
    'shortName' => 'CharacteristicColorCssGenerator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Génère le fichier CSS des couleurs de caractéristiques (palette Tailwind ou hex legacy → .color-{key} avec --color).
 * Source unique : colonne {@see Characteristic::$color} ; pas de duplication dans les thèmes SCSS.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 110,
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
      'OUTPUT_PATH' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'implementingClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'name' => 'OUTPUT_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'public/css/characteristic-colors.css\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 48,
            'startFilePos' => 572,
            'endTokenPos' => 48,
            'endFilePos' => 609,
          ),
        ),
        'docComment' => '/** Chemin du fichier généré (public, servi en statique). */',
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 70,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'generate' => 
      array (
        'name' => 'generate',
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
 * Génère le fichier CSS à partir des caractéristiques ayant une couleur renseignée en base.
 */',
        'startLine' => 23,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic',
        'declaringClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'implementingClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'currentClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'aliasName' => NULL,
      ),
      'buildCss' => 
      array (
        'name' => 'buildCss',
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
        'docComment' => '/**
 * Construit le contenu CSS (classes .color-{key} avec --color en var(--color-{palette}-nuance)).
 */',
        'startLine' => 39,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic',
        'declaringClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'implementingClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'currentClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'aliasName' => NULL,
      ),
      'resolveColorCssValue' => 
      array (
        'name' => 'resolveColorCssValue',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
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
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 43,
            'endColumn' => 56,
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
                  'name' => 'array',
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
        'docComment' => '/**
 * @return array{main: string, bg: string}|null
 */',
        'startLine' => 73,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic',
        'declaringClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'implementingClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'currentClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'aliasName' => NULL,
      ),
      'sanitizeClassKey' => 
      array (
        'name' => 'sanitizeClassKey',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
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
            'startLine' => 106,
            'endLine' => 106,
            'startColumn' => 39,
            'endColumn' => 49,
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
        'docComment' => '/** Clé safe pour une classe CSS (lettres, chiffres, tirets, underscores). */',
        'startLine' => 106,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic',
        'declaringClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'implementingClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
        'currentClassName' => 'App\\Services\\Characteristic\\CharacteristicColorCssGenerator',
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