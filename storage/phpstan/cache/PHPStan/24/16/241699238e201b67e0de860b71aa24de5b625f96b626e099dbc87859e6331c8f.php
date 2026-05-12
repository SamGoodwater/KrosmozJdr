<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbItemTypesCatalogService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-59a5575ad653fea96e1de4aa86133e14adced96c0164791e8ab47fa02e767a18-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbItemTypesCatalogService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Catalog',
    'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
    'shortName' => 'DofusDbItemTypesCatalogService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Catalogue DofusDB : item-types + superTypes (paginated).
 *
 * @description
 * DofusDB limite fréquemment les listes à 50 éléments par page.
 * Ce service pagine l\'endpoint `/item-types`, regroupe par `superTypeId`
 * et expose des helpers pour dériver des listes de `typeId` par superType.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 359,
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
      'PAGE_LIMIT' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'name' => 'PAGE_LIMIT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '50',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 19,
            'startTokenPos' => 35,
            'startFilePos' => 594,
            'endTokenPos' => 35,
            'endFilePos' => 595,
          ),
        ),
        'docComment' => '/** DofusDB cappe les listes (ex. /item-types) à 50 éléments par page. */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 34,
      ),
    ),
    'immediateProperties' => 
    array (
      'client' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'name' => 'client',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 33,
        'endColumn' => 61,
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
          'client' => 
          array (
            'name' => 'client',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Http\\DofusDbClient',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 33,
            'endColumn' => 61,
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
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 65,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'getCatalog' => 
      array (
        'name' => 'getCatalog',
        'parameters' => 
        array (
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 29,
                'endLine' => 29,
                'startTokenPos' => 68,
                'startFilePos' => 1001,
                'endTokenPos' => 68,
                'endFilePos' => 1004,
              ),
            ),
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 32,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 29,
                'endLine' => 29,
                'startTokenPos' => 77,
                'startFilePos' => 1025,
                'endTokenPos' => 77,
                'endFilePos' => 1029,
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
            'startLine' => 29,
            'endLine' => 29,
            'startColumn' => 53,
            'endColumn' => 75,
            'parameterIndex' => 1,
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
 * @return array{
 *   meta: array{total:int|null,pages:int,returned:int,lang:string,limit:int},
 *   superTypes: array<int, array{id:int,name:string|null,types:array<int,array{id:int,name:string|null,categoryId:int|null,isInEncyclopedia:bool|null}>}>
 * }
 */',
        'startLine' => 29,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'getTypeIdsForSuperTypes' => 
      array (
        'name' => 'getTypeIdsForSuperTypes',
        'parameters' => 
        array (
          'superTypeIds' => 
          array (
            'name' => 'superTypeIds',
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
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 45,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 163,
                'endLine' => 163,
                'startTokenPos' => 1352,
                'startFilePos' => 6097,
                'endTokenPos' => 1352,
                'endFilePos' => 6100,
              ),
            ),
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
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 66,
            'endColumn' => 84,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 163,
                'endLine' => 163,
                'startTokenPos' => 1361,
                'startFilePos' => 6121,
                'endTokenPos' => 1361,
                'endFilePos' => 6125,
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
            'startLine' => 163,
            'endLine' => 163,
            'startColumn' => 87,
            'endColumn' => 109,
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
 * @param  array<int,int>  $superTypeIds
 * @return array<int,int>
 */',
        'startLine' => 163,
        'endLine' => 192,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'getAllSuperTypeIds' => 
      array (
        'name' => 'getAllSuperTypeIds',
        'parameters' => 
        array (
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 197,
                'endLine' => 197,
                'startTokenPos' => 1628,
                'startFilePos' => 7081,
                'endTokenPos' => 1628,
                'endFilePos' => 7084,
              ),
            ),
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
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 40,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 197,
                'endLine' => 197,
                'startTokenPos' => 1637,
                'startFilePos' => 7105,
                'endTokenPos' => 1637,
                'endFilePos' => 7109,
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
            'startLine' => 197,
            'endLine' => 197,
            'startColumn' => 61,
            'endColumn' => 83,
            'parameterIndex' => 1,
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
 * @return array<int,int>
 */',
        'startLine' => 197,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'getSuperTypeIdForTypeId' => 
      array (
        'name' => 'getSuperTypeIdForTypeId',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 45,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 220,
                'endLine' => 220,
                'startTokenPos' => 1771,
                'startFilePos' => 7877,
                'endTokenPos' => 1771,
                'endFilePos' => 7880,
              ),
            ),
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 58,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 220,
                'endLine' => 220,
                'startTokenPos' => 1780,
                'startFilePos' => 7901,
                'endTokenPos' => 1780,
                'endFilePos' => 7905,
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 79,
            'endColumn' => 101,
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
 * Retourne le superTypeId DofusDB pour un typeId donné (ex: 15 → 9 pour Ressource).
 * Utilisé pour ne pas traiter comme "ressource" un typeId en registry resource_types
 * qui est en réalité un équipement (superType ≠ 9).
 *
 * @return int|null superTypeId ou null si typeId inconnu
 */',
        'startLine' => 220,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'inferSuperTypeIdFromItemRaw' => 
      array (
        'name' => 'inferSuperTypeIdFromItemRaw',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 49,
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
 * Infère le superTypeId depuis la charge utile DofusDB d\'un item (sans passer par le catalogue).
 * Utile quand getSuperTypeIdForTypeId échoue (cache incomplet, nouveau typeId) alors que la réponse
 * inclut déjà type.superTypeId ou type.superType.id.
 *
 * @param  array<string, mixed>  $raw  Réponse brute /items ou document normalisé
 * @return int|null superTypeId DofusDB ou null
 */',
        'startLine' => 251,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'stripDofusdbSuffix' => 
      array (
        'name' => 'stripDofusdbSuffix',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 280,
            'endLine' => 280,
            'startColumn' => 40,
            'endColumn' => 52,
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
 * Retire le suffixe « (DofusDB) » d\'un libellé.
 */',
        'startLine' => 280,
        'endLine' => 291,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'fetchName' => 
      array (
        'name' => 'fetchName',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'startLine' => 296,
            'endLine' => 296,
            'startColumn' => 31,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 296,
                'endLine' => 296,
                'startTokenPos' => 2339,
                'startFilePos' => 10253,
                'endTokenPos' => 2339,
                'endFilePos' => 10256,
              ),
            ),
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
            'startLine' => 296,
            'endLine' => 296,
            'startColumn' => 44,
            'endColumn' => 62,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 296,
                'endLine' => 296,
                'startTokenPos' => 2348,
                'startFilePos' => 10277,
                'endTokenPos' => 2348,
                'endFilePos' => 10281,
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
            'startLine' => 296,
            'endLine' => 296,
            'startColumn' => 65,
            'endColumn' => 87,
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
 * Résout un typeId DofusDB vers un nom (via le catalogue).
 */',
        'startLine' => 296,
        'endLine' => 314,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'aliasName' => NULL,
      ),
      'resolveTypeIdsByName' => 
      array (
        'name' => 'resolveTypeIdsByName',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 323,
            'endLine' => 323,
            'startColumn' => 42,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'lang' => 
          array (
            'name' => 'lang',
            'default' => 
            array (
              'code' => '\'fr\'',
              'attributes' => 
              array (
                'startLine' => 323,
                'endLine' => 323,
                'startTokenPos' => 2543,
                'startFilePos' => 11278,
                'endTokenPos' => 2543,
                'endFilePos' => 11281,
              ),
            ),
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
            'startLine' => 323,
            'endLine' => 323,
            'startColumn' => 56,
            'endColumn' => 74,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'skipCache' => 
          array (
            'name' => 'skipCache',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 323,
                'endLine' => 323,
                'startTokenPos' => 2552,
                'startFilePos' => 11302,
                'endTokenPos' => 2552,
                'endFilePos' => 11306,
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
            'startLine' => 323,
            'endLine' => 323,
            'startColumn' => 77,
            'endColumn' => 99,
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
 * Résout un nom (type d\'objet ou superType/catégorie) vers la liste des typeIds.
 * Si le nom correspond à un superType (ex. "Ressource"), retourne tous les typeIds de ce superType.
 * Sinon cherche un type dont le nom correspond et retourne [typeId].
 *
 * @return array<int,int> liste de typeIds (vide si non trouvé)
 */',
        'startLine' => 323,
        'endLine' => 358,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
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