<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsAutreAuditCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingEffectsAutreAuditCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-a0b6599c89803a5b6ac88f61a7f738a6690b42cd361321cd05318ba917de84fe',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsAutreAuditCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
    'shortName' => 'ScrappingEffectsAutreAuditCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Audit spécifique des sous-effets "autre" sur les sorts importés.
 *
 * Objectif:
 * - Mesurer la part de "autre" dans les sous-effets de sorts.
 * - Identifier les "autre" probablement convertibles (retrait/placement/soin/dégâts/etc.).
 * - Donner un top actionnable (effectId DofusDB quand disponible, textes normalisés).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 322,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
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
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:effects:audit-autre
                            {--json : Sortie JSON}
                            {--top=20 : Nombre de lignes max pour les tops}
                            {--sample-limit=20 : Nombre max d\\\'exemples par catégorie}\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 24,
            'startTokenPos' => 54,
            'startFilePos' => 639,
            'endTokenPos' => 54,
            'endFilePos' => 884,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 89,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Audit des sous-effets "autre" pour améliorer la qualité de conversion des sorts\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 63,
            'startFilePos' => 917,
            'endTokenPos' => 63,
            'endFilePos' => 999,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 113,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'decodeParams' => 
      array (
        'name' => 'decodeParams',
        'parameters' => 
        array (
          'paramsRaw' => 
          array (
            'name' => 'paramsRaw',
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
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 35,
            'endColumn' => 50,
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
 * @return array<string, mixed>
 */',
        'startLine' => 195,
        'endLine' => 207,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'normalizeText' => 
      array (
        'name' => 'normalizeText',
        'parameters' => 
        array (
          'text' => 
          array (
            'name' => 'text',
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
            'startLine' => 209,
            'endLine' => 209,
            'startColumn' => 36,
            'endColumn' => 47,
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
        'startLine' => 209,
        'endLine' => 226,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'classifyAutreText' => 
      array (
        'name' => 'classifyAutreText',
        'parameters' => 
        array (
          'normalized' => 
          array (
            'name' => 'normalized',
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
            'startLine' => 231,
            'endLine' => 231,
            'startColumn' => 40,
            'endColumn' => 57,
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
 * @return \'damage_like\'|\'removal_like\'|\'movement_like\'|\'support_like\'|\'unknown\'
 */',
        'startLine' => 231,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'formatTopNumericMap' => 
      array (
        'name' => 'formatTopNumericMap',
        'parameters' => 
        array (
          'map' => 
          array (
            'name' => 'map',
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
            'startLine' => 263,
            'endLine' => 263,
            'startColumn' => 42,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'top' => 
          array (
            'name' => 'top',
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
            'startLine' => 263,
            'endLine' => 263,
            'startColumn' => 54,
            'endColumn' => 61,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<int,int>  $map
 * @return list<array{key:int,count:int}>
 */',
        'startLine' => 263,
        'endLine' => 276,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'formatTopStringMap' => 
      array (
        'name' => 'formatTopStringMap',
        'parameters' => 
        array (
          'map' => 
          array (
            'name' => 'map',
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
            'startLine' => 282,
            'endLine' => 282,
            'startColumn' => 41,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'top' => 
          array (
            'name' => 'top',
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
            'startLine' => 282,
            'endLine' => 282,
            'startColumn' => 53,
            'endColumn' => 60,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string,int>  $map
 * @return list<array{key:string,count:int}>
 */',
        'startLine' => 282,
        'endLine' => 295,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'aliasName' => NULL,
      ),
      'buildWarnings' => 
      array (
        'name' => 'buildWarnings',
        'parameters' => 
        array (
          'autreRate' => 
          array (
            'name' => 'autreRate',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 300,
            'endLine' => 300,
            'startColumn' => 36,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'convertibleRate' => 
          array (
            'name' => 'convertibleRate',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 300,
            'endLine' => 300,
            'startColumn' => 54,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'withDofusEffectId' => 
          array (
            'name' => 'withDofusEffectId',
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
            'startLine' => 300,
            'endLine' => 300,
            'startColumn' => 78,
            'endColumn' => 99,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'autreRows' => 
          array (
            'name' => 'autreRows',
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
            'startLine' => 300,
            'endLine' => 300,
            'startColumn' => 102,
            'endColumn' => 115,
            'parameterIndex' => 3,
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
        'startLine' => 300,
        'endLine' => 321,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsAutreAuditCommand',
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