<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingTypesMigrateItemsCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-190c547d9145fb650e9b1ffa35407cd1a911ee26bfaf5fc51cea20157b4665f6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingTypesMigrateItemsCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
    'shortName' => 'ScrappingTypesMigrateItemsCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Migre les item_type_id des équipements existants vers les superTypes.
 *
 * Avant : item_types stockait des typeIds (sous-types : Arc, Baguette, Marteau).
 * Après : item_types stocke des superTypeIds (types : Amulette, Arme, Bouclier).
 *
 * Cette commande met à jour les items existants pour pointer vers le bon ItemType (superTypeId).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PROPRIETES_ITEMS_RESOURCES_CONSUMABLES.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 24,
    'endLine' => 123,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:types:migrate-items
                            {--dry-run : Afficher les changements sans les appliquer}
                            {--skip-cache : Ignorer le cache du catalogue}\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 28,
            'startTokenPos' => 67,
            'startFilePos' => 895,
            'endTokenPos' => 67,
            'endFilePos' => 1086,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 76,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Migre les item_type_id des équipements vers les superTypes (type au lieu de sous-type)\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 76,
            'startFilePos' => 1119,
            'endTokenPos' => 76,
            'endFilePos' => 1207,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 119,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'catalogService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'name' => 'catalogService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 9,
        'endColumn' => 71,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'mappingService' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'name' => 'mappingService',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 9,
        'endColumn' => 75,
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
          'catalogService' => 
          array (
            'name' => 'catalogService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 9,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'mappingService' => 
          array (
            'name' => 'mappingService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 9,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 32,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'aliasName' => NULL,
      ),
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
        'startLine' => 39,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingTypesMigrateItemsCommand',
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