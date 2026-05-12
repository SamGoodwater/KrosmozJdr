<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingRepairItemRoutingCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-e57e6907e16d1566776f91c040b2f30d6dbcc7cf8cac5e4d1b437d1b798244ba',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRepairItemRoutingCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
    'shortName' => 'ScrappingRepairItemRoutingCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Corrige les items mal routés (items -> resources/consumables) à partir du type DofusDB.
 *
 * Cette commande sert de rattrapage des données historiques :
 * - détecte les lignes de `items` dont le type cible attendu n\'est pas `items`
 * - migre vers `resources` ou `consumables`
 * - transfère les pivots principaux (drops + recettes)
 * - réaffecte media/effect_usages puis supprime la ligne source
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 416,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:items:repair-routing
                            {--apply : Appliquer les corrections (sinon dry-run)}
                            {--limit=0 : Limiter le nombre de lignes sources traitées (0 = toutes)}\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 30,
            'startTokenPos' => 84,
            'startFilePos' => 911,
            'endTokenPos' => 84,
            'endFilePos' => 1125,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 102,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Rattrape les items mal classés vers resources/consumables\'',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 93,
            'startFilePos' => 1158,
            'endTokenPos' => 93,
            'endFilePos' => 1217,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 90,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'itemTypeIdsByDofusType' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'name' => 'itemTypeIdsByDofusType',
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
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 106,
            'startFilePos' => 1296,
            'endTokenPos' => 107,
            'endFilePos' => 1297,
          ),
        ),
        'docComment' => '/** @var array<int,int> */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 47,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'resourceTypeIdsByDofusType' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'name' => 'resourceTypeIdsByDofusType',
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
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 120,
            'startFilePos' => 1380,
            'endTokenPos' => 121,
            'endFilePos' => 1381,
          ),
        ),
        'docComment' => '/** @var array<int,int> */',
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'consumableTypeIdsByDofusType' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'name' => 'consumableTypeIdsByDofusType',
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
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 134,
            'startFilePos' => 1466,
            'endTokenPos' => 135,
            'endFilePos' => 1467,
          ),
        ),
        'docComment' => '/** @var array<int,int> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 53,
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
        'startLine' => 43,
        'endLine' => 193,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'resolveTargetTable' => 
      array (
        'name' => 'resolveTargetTable',
        'parameters' => 
        array (
          'dofusTypeId' => 
          array (
            'name' => 'dofusTypeId',
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
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 41,
            'endColumn' => 56,
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
        'startLine' => 195,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'buildResourcePayloadFromItem' => 
      array (
        'name' => 'buildResourcePayloadFromItem',
        'parameters' => 
        array (
          'source' => 
          array (
            'name' => 'source',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Entity\\Item',
                'isIdentifier' => false,
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
            'startColumn' => 51,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'resourceTypeId' => 
          array (
            'name' => 'resourceTypeId',
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 65,
            'endColumn' => 83,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'forCreate' => 
          array (
            'name' => 'forCreate',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 213,
                'endLine' => 213,
                'startTokenPos' => 1574,
                'startFilePos' => 8594,
                'endTokenPos' => 1574,
                'endFilePos' => 8597,
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
            'startLine' => 213,
            'endLine' => 213,
            'startColumn' => 86,
            'endColumn' => 107,
            'parameterIndex' => 2,
            'isOptional' => true,
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
 * @return array<string,mixed>
 */',
        'startLine' => 213,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'buildConsumablePayloadFromItem' => 
      array (
        'name' => 'buildConsumablePayloadFromItem',
        'parameters' => 
        array (
          'source' => 
          array (
            'name' => 'source',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Entity\\Item',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 53,
            'endColumn' => 64,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'consumableTypeId' => 
          array (
            'name' => 'consumableTypeId',
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 67,
            'endColumn' => 87,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'forCreate' => 
          array (
            'name' => 'forCreate',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 243,
                'endLine' => 243,
                'startTokenPos' => 1871,
                'startFilePos' => 9843,
                'endTokenPos' => 1871,
                'endFilePos' => 9846,
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
            'startLine' => 243,
            'endLine' => 243,
            'startColumn' => 90,
            'endColumn' => 111,
            'parameterIndex' => 2,
            'isOptional' => true,
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
 * @return array<string,mixed>
 */',
        'startLine' => 243,
        'endLine' => 269,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveCreatureItemToResource' => 
      array (
        'name' => 'moveCreatureItemToResource',
        'parameters' => 
        array (
          'sourceItemId' => 
          array (
            'name' => 'sourceItemId',
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
            'startLine' => 271,
            'endLine' => 271,
            'startColumn' => 49,
            'endColumn' => 65,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetResourceId' => 
          array (
            'name' => 'targetResourceId',
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
            'startLine' => 271,
            'endLine' => 271,
            'startColumn' => 68,
            'endColumn' => 88,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 271,
        'endLine' => 300,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveCreatureItemToConsumable' => 
      array (
        'name' => 'moveCreatureItemToConsumable',
        'parameters' => 
        array (
          'sourceItemId' => 
          array (
            'name' => 'sourceItemId',
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
            'startLine' => 302,
            'endLine' => 302,
            'startColumn' => 51,
            'endColumn' => 67,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetConsumableId' => 
          array (
            'name' => 'targetConsumableId',
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
            'startLine' => 302,
            'endLine' => 302,
            'startColumn' => 70,
            'endColumn' => 92,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 302,
        'endLine' => 331,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveItemRecipeToResourceRecipe' => 
      array (
        'name' => 'moveItemRecipeToResourceRecipe',
        'parameters' => 
        array (
          'sourceItemId' => 
          array (
            'name' => 'sourceItemId',
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
            'startLine' => 333,
            'endLine' => 333,
            'startColumn' => 53,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetResourceId' => 
          array (
            'name' => 'targetResourceId',
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
            'startLine' => 333,
            'endLine' => 333,
            'startColumn' => 72,
            'endColumn' => 92,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 333,
        'endLine' => 362,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveItemRecipeToConsumableRecipe' => 
      array (
        'name' => 'moveItemRecipeToConsumableRecipe',
        'parameters' => 
        array (
          'sourceItemId' => 
          array (
            'name' => 'sourceItemId',
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
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 55,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetConsumableId' => 
          array (
            'name' => 'targetConsumableId',
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
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 74,
            'endColumn' => 96,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 364,
        'endLine' => 393,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveMedia' => 
      array (
        'name' => 'moveMedia',
        'parameters' => 
        array (
          'sourceId' => 
          array (
            'name' => 'sourceId',
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
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 32,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetId' => 
          array (
            'name' => 'targetId',
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
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 47,
            'endColumn' => 59,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'sourceClass' => 
          array (
            'name' => 'sourceClass',
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
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 62,
            'endColumn' => 80,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'targetClass' => 
          array (
            'name' => 'targetClass',
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
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 83,
            'endColumn' => 101,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 395,
        'endLine' => 404,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'aliasName' => NULL,
      ),
      'moveEffectUsages' => 
      array (
        'name' => 'moveEffectUsages',
        'parameters' => 
        array (
          'sourceId' => 
          array (
            'name' => 'sourceId',
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
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 39,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetId' => 
          array (
            'name' => 'targetId',
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
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 54,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'sourceType' => 
          array (
            'name' => 'sourceType',
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
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 69,
            'endColumn' => 86,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'targetType' => 
          array (
            'name' => 'targetType',
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
            'startLine' => 406,
            'endLine' => 406,
            'startColumn' => 89,
            'endColumn' => 106,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 406,
        'endLine' => 415,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRepairItemRoutingCommand',
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