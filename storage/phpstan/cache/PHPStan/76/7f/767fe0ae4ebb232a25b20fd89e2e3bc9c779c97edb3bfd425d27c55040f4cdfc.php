<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbItemSuperTypeMappingService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-1cf7babc05251644cf4438baa45ddc1023a641f23f83f916570f8834bf93eea6-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbItemSuperTypeMappingService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Catalog',
    'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
    'shortName' => 'DofusDbItemSuperTypeMappingService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Lecture de la config de mapping superTypeId -> catégories métier.
 *
 * Source de vérité : `resources/scrapping/config/sources/dofusdb/item-super-types.json`.
 * L\'ancien chemin (resources/scrapping/sources/dofusdb/) est déprécié.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 160,
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
      'basePath' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'name' => 'basePath',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 15,
        'startColumn' => 5,
        'endColumn' => 29,
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
          'basePath' => 
          array (
            'name' => 'basePath',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 17,
                'endLine' => 17,
                'startTokenPos' => 43,
                'startFilePos' => 451,
                'endTokenPos' => 43,
                'endFilePos' => 454,
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
            'startLine' => 17,
            'endLine' => 17,
            'startColumn' => 33,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 17,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'aliasName' => NULL,
      ),
      'getConfig' => 
      array (
        'name' => 'getConfig',
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
 * @return array<string,mixed>
 */',
        'startLine' => 25,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'aliasName' => NULL,
      ),
      'getGroup' => 
      array (
        'name' => 'getGroup',
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
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 30,
            'endColumn' => 42,
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
 * @return array{strategy:string,superTypeIds:array<int,int>,excludeSuperTypeIds:array<int,int>}
 */',
        'startLine' => 48,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'aliasName' => NULL,
      ),
      'getCategoryForSuperTypeId' => 
      array (
        'name' => 'getCategoryForSuperTypeId',
        'parameters' => 
        array (
          'superTypeId' => 
          array (
            'name' => 'superTypeId',
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 47,
            'endColumn' => 62,
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
        'docComment' => '/**
 * Retourne la catégorie Krosmoz (resource, consumable, equipment) pour un superTypeId DofusDB.
 * Priorité : resource > consumable > equipment. null = superType non mappé.
 *
 * @return \'resource\'|\'consumable\'|\'equipment\'|null null = superType non configuré
 */',
        'startLine' => 89,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'aliasName' => NULL,
      ),
      'getNameForSuperTypeId' => 
      array (
        'name' => 'getNameForSuperTypeId',
        'parameters' => 
        array (
          'superTypeId' => 
          array (
            'name' => 'superTypeId',
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
            'startLine' => 120,
            'endLine' => 120,
            'startColumn' => 43,
            'endColumn' => 58,
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
        'docComment' => '/**
 * Retourne le nom français d\'un superTypeId depuis superTypesReference.
 *
 * @return string|null null si superTypeId inconnu
 */',
        'startLine' => 120,
        'endLine' => 141,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'aliasName' => NULL,
      ),
      'getExcludedTypeIds' => 
      array (
        'name' => 'getExcludedTypeIds',
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
 * TypeIds à exclure de toute collecte item (ressource, consumable, equipment).
 * Ex. : consommables obsolètes des Songes, La source - l\'héritage des Dofus, apparat.
 *
 * @return list<int>
 */',
        'startLine' => 149,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
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