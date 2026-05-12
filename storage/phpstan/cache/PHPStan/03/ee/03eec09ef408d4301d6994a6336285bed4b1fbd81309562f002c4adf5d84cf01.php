<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbMonsterRacesCatalogService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-af6e52fa1522d8a443fc7e0f6ac5d100fc60c6c9923e39e292a91954504fc955-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Catalog/DofusDbMonsterRacesCatalogService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Catalog',
    'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
    'shortName' => 'DofusDbMonsterRacesCatalogService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Catalogue des races de monstres DofusDB (/monster-races).
 *
 * @description
 * Permet de récupérer la liste complète des races et de résoudre raceId -> nom,
 * avec cache applicatif (évite N requêtes).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 157,
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
      'client' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
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
        'startLine' => 17,
        'endLine' => 17,
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
            'startLine' => 17,
            'endLine' => 17,
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
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 65,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'aliasName' => NULL,
      ),
      'listAll' => 
      array (
        'name' => 'listAll',
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
                'startLine' => 22,
                'endLine' => 22,
                'startTokenPos' => 55,
                'startFilePos' => 584,
                'endTokenPos' => 55,
                'endFilePos' => 587,
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 29,
            'endColumn' => 47,
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
                'startLine' => 22,
                'endLine' => 22,
                'startTokenPos' => 64,
                'startFilePos' => 608,
                'endTokenPos' => 64,
                'endFilePos' => 612,
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 50,
            'endColumn' => 72,
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
 * @return array<int, array{id:int, name: string|null}>
 */',
        'startLine' => 22,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'aliasName' => NULL,
      ),
      'mapNames' => 
      array (
        'name' => 'mapNames',
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
                'startLine' => 96,
                'endLine' => 96,
                'startTokenPos' => 793,
                'startFilePos' => 3226,
                'endTokenPos' => 793,
                'endFilePos' => 3229,
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
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 30,
            'endColumn' => 48,
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
                'startLine' => 96,
                'endLine' => 96,
                'startTokenPos' => 802,
                'startFilePos' => 3250,
                'endTokenPos' => 802,
                'endFilePos' => 3254,
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
            'startLine' => 96,
            'endLine' => 96,
            'startColumn' => 51,
            'endColumn' => 73,
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
 * @return array<int,string> map raceId => name
 */',
        'startLine' => 96,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'aliasName' => NULL,
      ),
      'fetchName' => 
      array (
        'name' => 'fetchName',
        'parameters' => 
        array (
          'raceId' => 
          array (
            'name' => 'raceId',
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
            'startLine' => 113,
            'endLine' => 113,
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
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 942,
                'startFilePos' => 3744,
                'endTokenPos' => 942,
                'endFilePos' => 3747,
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
            'startLine' => 113,
            'endLine' => 113,
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
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 951,
                'startFilePos' => 3768,
                'endTokenPos' => 951,
                'endFilePos' => 3772,
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
            'startLine' => 113,
            'endLine' => 113,
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
 * Résout un raceId DofusDB vers un nom (via le catalogue).
 */',
        'startLine' => 113,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'aliasName' => NULL,
      ),
      'findRaceIdByName' => 
      array (
        'name' => 'findRaceIdByName',
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
            'startLine' => 130,
            'endLine' => 130,
            'startColumn' => 38,
            'endColumn' => 49,
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
                'startLine' => 130,
                'endLine' => 130,
                'startTokenPos' => 1036,
                'startFilePos' => 4244,
                'endTokenPos' => 1036,
                'endFilePos' => 4247,
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
            'startLine' => 130,
            'endLine' => 130,
            'startColumn' => 52,
            'endColumn' => 70,
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
                'startLine' => 130,
                'endLine' => 130,
                'startTokenPos' => 1045,
                'startFilePos' => 4268,
                'endTokenPos' => 1045,
                'endFilePos' => 4272,
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
            'startLine' => 130,
            'endLine' => 130,
            'startColumn' => 73,
            'endColumn' => 95,
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
 * Résout un nom de race (ou slug) vers l\'ID DofusDB.
 * Comparaison insensible à la casse et aux espaces.
 *
 * @return int|null raceId ou null si non trouvé
 */',
        'startLine' => 130,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Catalog',
        'declaringClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
        'currentClassName' => 'App\\Services\\Scrapping\\Catalog\\DofusDbMonsterRacesCatalogService',
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