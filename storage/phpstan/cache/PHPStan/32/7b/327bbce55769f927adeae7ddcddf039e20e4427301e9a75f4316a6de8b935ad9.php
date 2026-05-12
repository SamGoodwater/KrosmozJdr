<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Characteristic/Getter/CharacteristicGetterService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Characteristic\Getter\CharacteristicGetterService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-dfc379779703d49e260c222173c742ae7e03eb16b68b1cc75e6c12a05305a509-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Characteristic/Getter/CharacteristicGetterService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Characteristic\\Getter',
    'name' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
    'shortName' => 'CharacteristicGetterService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Getter généraliste : fournit les définitions d’une caractéristique par clé et entité.
 * Résout entity → groupe (creature, object, spell) et fusionne table générale + table de groupe.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 567,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'App\\Contracts\\Characteristic\\CharacteristicDefinitionLookup',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'ENTITY_ALL' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'ENTITY_ALL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'*\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 84,
            'startFilePos' => 877,
            'endTokenPos' => 84,
            'endFilePos' => 879,
          ),
        ),
        'docComment' => '/** Valeur entity = « s\'applique à toutes les entités du groupe ». */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
      'FIELD_MAP_CACHE_TTL' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'FIELD_MAP_CACHE_TTL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3600',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 97,
            'startFilePos' => 999,
            'endTokenPos' => 97,
            'endFilePos' => 1002,
          ),
        ),
        'docComment' => '/** TTL cache index champ → clé (aligné sur le mapping DofusDB). */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'GROUP_CREATURE' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'GROUP_CREATURE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'monster\', \'class\', \'npc\']',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 130,
            'startFilePos' => 1192,
            'endTokenPos' => 138,
            'endFilePos' => 1218,
          ),
        ),
        'docComment' => '/** Entités du groupe creature */',
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 63,
      ),
      'GROUP_OBJECT' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'GROUP_OBJECT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'item\', \'consumable\', \'resource\', \'panoply\']',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 151,
            'startFilePos' => 1292,
            'endTokenPos' => 162,
            'endFilePos' => 1336,
          ),
        ),
        'docComment' => '/** Entités du groupe object */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 79,
      ),
      'GROUP_SPELL' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'GROUP_SPELL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'spell\']',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 175,
            'startFilePos' => 1408,
            'endTokenPos' => 177,
            'endFilePos' => 1416,
          ),
        ),
        'docComment' => '/** Entités du groupe spell */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'FIELD_MAP_ENTITIES' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'FIELD_MAP_ENTITIES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[...self::GROUP_CREATURE, ...self::GROUP_OBJECT, ...self::GROUP_SPELL]',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 54,
            'startTokenPos' => 204,
            'startFilePos' => 1722,
            'endTokenPos' => 224,
            'endFilePos' => 1822,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'DOFUSDB_TO_KEY_CACHE_TTL' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'DOFUSDB_TO_KEY_CACHE_TTL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3600',
          'attributes' => 
          array (
            'startLine' => 306,
            'endLine' => 306,
            'startTokenPos' => 1851,
            'startFilePos' => 10999,
            'endTokenPos' => 1851,
            'endFilePos' => 11002,
          ),
        ),
        'docComment' => '/** Cache TTL pour le mapping dofusdb_id → characteristic_key par groupe (secondes). */',
        'attributes' => 
        array (
        ),
        'startLine' => 306,
        'endLine' => 306,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
    ),
    'immediateProperties' => 
    array (
      'definitionMemo' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'definitionMemo',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 190,
            'startFilePos' => 1649,
            'endTokenPos' => 191,
            'endFilePos' => 1650,
          ),
        ),
        'docComment' => '/**
 * Résultats mémoïsés de getDefinition (évite requêtes répétées dans une même requête HTTP / worker).
 *
 * @var array<string, array<string, mixed>|null>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'formulaResolution' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'name' => 'formulaResolution',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 9,
        'endColumn' => 68,
        'isPromoted' => true,
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
          'formulaResolution' => 
          array (
            'name' => 'formulaResolution',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 9,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 29,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getDefinition' => 
      array (
        'name' => 'getDefinition',
        'parameters' => 
        array (
          'characteristicKey' => 
          array (
            'name' => 'characteristicKey',
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 35,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 62,
            'endColumn' => 75,
            'parameterIndex' => 1,
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
 * Retourne la définition complète d’une caractéristique pour une entité (nom, limites, formules, conversion, etc.).
 *
 * @return array<string, mixed>|null
 */',
        'startLine' => 61,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getLimits' => 
      array (
        'name' => 'getLimits',
        'parameters' => 
        array (
          'characteristicKey' => 
          array (
            'name' => 'characteristicKey',
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
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 31,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 58,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'variables' => 
          array (
            'name' => 'variables',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 108,
                'endLine' => 108,
                'startTokenPos' => 569,
                'startFilePos' => 4318,
                'endTokenPos' => 570,
                'endFilePos' => 4319,
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
            'startLine' => 108,
            'endLine' => 108,
            'startColumn' => 74,
            'endColumn' => 94,
            'parameterIndex' => 2,
            'isOptional' => true,
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
 * Retourne les limites min/max pour une caractéristique et une entité.
 * Min/max peuvent être une valeur fixe, une formule ([level]*2) ou une table par caractéristique ;
 * ils sont évalués avec les variables fournies (ex. level, vitality). Sans variables, formules/tables
 * sont évaluées avec 0 pour les variables manquantes.
 *
 * @param  array<string, int|float>  $variables  Contexte pour l\'évaluation (ex. [\'level\' => 5, \'vitality\' => 10])
 * @return array{min: int, max: int}|null
 */',
        'startLine' => 108,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getLimitsByField' => 
      array (
        'name' => 'getLimitsByField',
        'parameters' => 
        array (
          'field' => 
          array (
            'name' => 'field',
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
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 38,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 53,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'variables' => 
          array (
            'name' => 'variables',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 132,
                'endLine' => 132,
                'startTokenPos' => 735,
                'startFilePos' => 5168,
                'endTokenPos' => 736,
                'endFilePos' => 5169,
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
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 69,
            'endColumn' => 89,
            'parameterIndex' => 2,
            'isOptional' => true,
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
 * Retourne les limites pour un champ de données (nom de colonne ou clé) et une entité.
 *
 * @param  array<string, int|float>  $variables  Contexte pour l\'évaluation des formules min/max
 * @return array{min: int, max: int}|null
 */',
        'startLine' => 132,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getDefinitionByField' => 
      array (
        'name' => 'getDefinitionByField',
        'parameters' => 
        array (
          'field' => 
          array (
            'name' => 'field',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 42,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 57,
            'endColumn' => 70,
            'parameterIndex' => 1,
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
 * Retourne la définition complète d\'une caractéristique à partir d\'un nom de champ (colonne ou clé).
 * Utilisé par le service Limit pour valider selon le type (boolean, list, string).
 *
 * @return array<string, mixed>|null
 */',
        'startLine' => 145,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getGroupForEntity' => 
      array (
        'name' => 'getGroupForEntity',
        'parameters' => 
        array (
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 39,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le groupe (creature, object, spell) pour une entité.
 */',
        'startLine' => 155,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'resolveFieldToKey' => 
      array (
        'name' => 'resolveFieldToKey',
        'parameters' => 
        array (
          'field' => 
          array (
            'name' => 'field',
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
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 40,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 176,
            'endLine' => 176,
            'startColumn' => 55,
            'endColumn' => 68,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * Résout un nom de champ (ex. level, life) ou un nom court (ex. level → level_creature) en clé BDD pour une entité.
 * Accepte la clé complète, le db_column, ou le nom court sans suffixe (_creature, _object, _spell).
 *
 * Une requête indexée par entité (cache applicatif) remplace le chargement complet des pivots à chaque résolution.
 */',
        'startLine' => 176,
        'endLine' => 191,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getCachedFieldToKeyMap' => 
      array (
        'name' => 'getCachedFieldToKeyMap',
        'parameters' => 
        array (
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 199,
            'endLine' => 199,
            'startColumn' => 45,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Index : clé complète, db_column, alias — pour une entité donnée.
 * Lignes `entity = *` puis surcharge entité (la surcharge écrase les alias en doublon).
 *
 * @return array<string, string>
 */',
        'startLine' => 199,
        'endLine' => 210,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'buildFieldToKeyMap' => 
      array (
        'name' => 'buildFieldToKeyMap',
        'parameters' => 
        array (
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 215,
            'endLine' => 215,
            'startColumn' => 41,
            'endColumn' => 54,
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
 * @return array<string, string>
 */',
        'startLine' => 215,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'accumulateFieldAliases' => 
      array (
        'name' => 'accumulateFieldAliases',
        'parameters' => 
        array (
          'rows' => 
          array (
            'name' => 'rows',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 246,
            'endLine' => 246,
            'startColumn' => 45,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  Collection<int, CharacteristicCreature|CharacteristicObject|CharacteristicSpell>  $rows
 * @return array<string, string>
 */',
        'startLine' => 246,
        'endLine' => 266,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'fieldKeySuffixForEntity' => 
      array (
        'name' => 'fieldKeySuffixForEntity',
        'parameters' => 
        array (
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 46,
            'endColumn' => 59,
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
        'startLine' => 268,
        'endLine' => 281,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getConversionFormula' => 
      array (
        'name' => 'getConversionFormula',
        'parameters' => 
        array (
          'characteristicKey' => 
          array (
            'name' => 'characteristicKey',
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
            'startLine' => 286,
            'endLine' => 286,
            'startColumn' => 42,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 286,
            'endLine' => 286,
            'startColumn' => 69,
            'endColumn' => 82,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * Retourne la formule de conversion Dofus → Krosmoz pour une caractéristique et une entité.
 */',
        'startLine' => 286,
        'endLine' => 292,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getConversionFunctionId' => 
      array (
        'name' => 'getConversionFunctionId',
        'parameters' => 
        array (
          'characteristicKey' => 
          array (
            'name' => 'characteristicKey',
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
            'startLine' => 297,
            'endLine' => 297,
            'startColumn' => 45,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 297,
            'endLine' => 297,
            'startColumn' => 72,
            'endColumn' => 85,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * Retourne l\'identifiant de la fonction de conversion optionnelle pour une caractéristique et une entité.
 */',
        'startLine' => 297,
        'endLine' => 303,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getDofusdbToCharacteristicKeyMap' => 
      array (
        'name' => 'getDofusdbToCharacteristicKeyMap',
        'parameters' => 
        array (
          'group' => 
          array (
            'name' => 'group',
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
            'startLine' => 315,
            'endLine' => 315,
            'startColumn' => 54,
            'endColumn' => 66,
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
 * Retourne le mapping complet dofusdb_characteristic_id → characteristic_key pour un groupe (une requête, mis en cache).
 * À utiliser en batch pour éviter N+1 (ex. itemEffectsToKrosmozBonus avec plusieurs effets par item).
 *
 * @param  \'object\'|\'creature\'|\'spell\'  $group
 * @return array<int, string> dofusdb_characteristic_id => characteristic key
 */',
        'startLine' => 315,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'getCharacteristicKeyByDofusdbCharacteristicId' => 
      array (
        'name' => 'getCharacteristicKeyByDofusdbCharacteristicId',
        'parameters' => 
        array (
          'dofusdbCharacteristicId' => 
          array (
            'name' => 'dofusdbCharacteristicId',
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
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 67,
            'endColumn' => 94,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'group' => 
          array (
            'name' => 'group',
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
            'startLine' => 349,
            'endLine' => 349,
            'startColumn' => 97,
            'endColumn' => 109,
            'parameterIndex' => 1,
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
        'docComment' => '/**
 * Résout un id DofusDB (GET /characteristics) vers la clé Krosmoz de la caractéristique (M2).
 * Pour plusieurs résolutions (ex. boucle effets), préférer getDofusdbToCharacteristicKeyMap() pour éviter N+1.
 *
 * @param  \'object\'|\'creature\'|\'spell\'  $group  Groupe de caractéristiques (table de groupe)
 *
 * @example getCharacteristicKeyByDofusdbCharacteristicId(10, \'object\') === \'strength_object\'
 */',
        'startLine' => 349,
        'endLine' => 354,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'clearCache' => 
      array (
        'name' => 'clearCache',
        'parameters' => 
        array (
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
        'docComment' => '/** Invalide les caches du getter (dofusdb_id → key par groupe). À appeler après mise à jour des tables de groupe. */',
        'startLine' => 357,
        'endLine' => 366,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'findGroupRows' => 
      array (
        'name' => 'findGroupRows',
        'parameters' => 
        array (
          'characteristicId' => 
          array (
            'name' => 'characteristicId',
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
            'startLine' => 374,
            'endLine' => 374,
            'startColumn' => 36,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 374,
            'endLine' => 374,
            'startColumn' => 59,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Trouve la ligne de base (entity=\'*\') et la surcharge (entity spécifique) pour characteristic_id + entity.
 * Permet d\'affiner les propriétés du groupe pour une entité précise (ex. formule PV pour monster uniquement).
 *
 * @return array{0: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null, 1: CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null}
 */',
        'startLine' => 374,
        'endLine' => 408,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'pickGroupValue' => 
      array (
        'name' => 'pickGroupValue',
        'parameters' => 
        array (
          'base' => 
          array (
            'name' => 'base',
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
                      'name' => 'App\\Models\\CharacteristicCreature',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicObject',
                      'isIdentifier' => false,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicSpell',
                      'isIdentifier' => false,
                    ),
                  ),
                  3 => 
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
            'startLine' => 414,
            'endLine' => 414,
            'startColumn' => 9,
            'endColumn' => 82,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'overlay' => 
          array (
            'name' => 'overlay',
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
                      'name' => 'App\\Models\\CharacteristicCreature',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicObject',
                      'isIdentifier' => false,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicSpell',
                      'isIdentifier' => false,
                    ),
                  ),
                  3 => 
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
            'startLine' => 415,
            'endLine' => 415,
            'startColumn' => 9,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'attribute' => 
          array (
            'name' => 'attribute',
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
            'startLine' => 416,
            'endLine' => 416,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Fusionne base et overlay : pour chaque propriété du groupe, la valeur non nulle de l\'overlay l\'emporte.
 */',
        'startLine' => 413,
        'endLine' => 424,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'resolveLimitValue' => 
      array (
        'name' => 'resolveLimitValue',
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
            'startLine' => 432,
            'endLine' => 432,
            'startColumn' => 40,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'variables' => 
          array (
            'name' => 'variables',
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
            'startLine' => 432,
            'endLine' => 432,
            'startColumn' => 54,
            'endColumn' => 69,
            'parameterIndex' => 1,
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
                  'name' => 'int',
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
 * Résout une limite (min ou max) : valeur fixe, formule ou table → entier.
 *
 * @param  mixed  $value  Valeur en BDD (string numérique, formule ou JSON table)
 * @param  array<string, int|float>  $variables  Contexte pour l\'évaluation
 */',
        'startLine' => 432,
        'endLine' => 450,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'mergeDefinition' => 
      array (
        'name' => 'mergeDefinition',
        'parameters' => 
        array (
          'characteristic' => 
          array (
            'name' => 'characteristic',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Characteristic',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 461,
            'endLine' => 461,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'base' => 
          array (
            'name' => 'base',
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
                      'name' => 'App\\Models\\CharacteristicCreature',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicObject',
                      'isIdentifier' => false,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicSpell',
                      'isIdentifier' => false,
                    ),
                  ),
                  3 => 
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
            'startLine' => 462,
            'endLine' => 462,
            'startColumn' => 9,
            'endColumn' => 82,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'overlay' => 
          array (
            'name' => 'overlay',
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
                      'name' => 'App\\Models\\CharacteristicCreature',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicObject',
                      'isIdentifier' => false,
                    ),
                  ),
                  2 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'App\\Models\\CharacteristicSpell',
                      'isIdentifier' => false,
                    ),
                  ),
                  3 => 
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
            'startLine' => 463,
            'endLine' => 463,
            'startColumn' => 9,
            'endColumn' => 85,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 464,
            'endLine' => 464,
            'startColumn' => 9,
            'endColumn' => 22,
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
 * Fusionne la caractéristique générale et les lignes de groupe (base + surcharge entité) en un seul tableau.
 * Les propriétés non généralistes (min, max, formula, etc.) sont prises sur la surcharge si non vides, sinon sur la base.
 *
 * @param  CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null  $base  Ligne entity=\'*\'
 * @param  CharacteristicCreature|CharacteristicObject|CharacteristicSpell|null  $overlay  Ligne entity précise (ex. monster)
 * @return array<string, mixed>
 */',
        'startLine' => 460,
        'endLine' => 522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'aliasName' => NULL,
      ),
      'findMasterBaseRow' => 
      array (
        'name' => 'findMasterBaseRow',
        'parameters' => 
        array (
          'characteristic' => 
          array (
            'name' => 'characteristic',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Characteristic',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 528,
            'endLine' => 528,
            'startColumn' => 40,
            'endColumn' => 69,
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
                  'name' => 'App\\Models\\CharacteristicCreature',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'App\\Models\\CharacteristicObject',
                  'isIdentifier' => false,
                ),
              ),
              2 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'App\\Models\\CharacteristicSpell',
                  'isIdentifier' => false,
                ),
              ),
              3 => 
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
 * Retourne la ligne de base (entity=\'*\' ou équivalent) pour une caractéristique maître,
 * en cherchant dans les trois tables de groupe. Utilisé pour les caractéristiques liées.
 */',
        'startLine' => 528,
        'endLine' => 566,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Getter',
        'declaringClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'implementingClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
        'currentClassName' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
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