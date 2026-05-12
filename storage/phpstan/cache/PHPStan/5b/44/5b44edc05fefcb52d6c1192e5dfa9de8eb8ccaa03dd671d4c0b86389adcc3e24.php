<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Core/Integration/IntegrationService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Core\Integration\IntegrationService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-964d6901048a3eaa07098aed14b60334d39d0b131c2e6d711d4a9ddb1ded4a55-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Core/Integration/IntegrationService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
    'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
    'shortName' => 'IntegrationService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Service d’intégration V2 : enregistre les données converties en base (ou simule).
 *
 * Pour l’instant : entité monster uniquement (Creature + Monster).
 * Option dry_run : pas d’écriture, retourne un résumé (would_create / would_update).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 38,
    'endLine' => 1961,
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
      'itemTypesCatalog' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'name' => 'itemTypesCatalog',
        'modifiers' => 132,
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
                  'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
                  'isIdentifier' => false,
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 165,
            'startFilePos' => 1346,
            'endTokenPos' => 165,
            'endFilePos' => 1349,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 9,
        'endColumn' => 81,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'superTypeMapping' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'name' => 'superTypeMapping',
        'modifiers' => 132,
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
                  'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
                  'isIdentifier' => false,
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
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 179,
            'startFilePos' => 1433,
            'endTokenPos' => 179,
            'endFilePos' => 1436,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 9,
        'endColumn' => 85,
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
          'itemTypesCatalog' => 
          array (
            'name' => 'itemTypesCatalog',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 165,
                'startFilePos' => 1346,
                'endTokenPos' => 165,
                'endFilePos' => 1349,
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
                      'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemTypesCatalogService',
                      'isIdentifier' => false,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 9,
            'endColumn' => 81,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'superTypeMapping' => 
          array (
            'name' => 'superTypeMapping',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 42,
                'endLine' => 42,
                'startTokenPos' => 179,
                'startFilePos' => 1433,
                'endTokenPos' => 179,
                'endFilePos' => 1436,
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
                      'name' => 'App\\Services\\Scrapping\\Catalog\\DofusDbItemSuperTypeMappingService',
                      'isIdentifier' => false,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 9,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 40,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrate' => 
      array (
        'name' => 'integrate',
        'parameters' => 
        array (
          'entityType' => 
          array (
            'name' => 'entityType',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 31,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 51,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 52,
                'endLine' => 52,
                'startTokenPos' => 210,
                'startFilePos' => 1935,
                'endTokenPos' => 211,
                'endFilePos' => 1936,
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
            'startLine' => 52,
            'endLine' => 52,
            'startColumn' => 73,
            'endColumn' => 91,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Intègre les données converties pour un type d’entité.
 *
 * @param  string  $entityType  Type KrosmozJDR (ex. monster)
 * @param  array<string, array<string, mixed>>  $convertedData  Structure par modèle (creatures, monsters)
 * @param  array{dry_run?: bool, force_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>}  $options
 */',
        'startLine' => 52,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'wouldReplaceExisting' => 
      array (
        'name' => 'wouldReplaceExisting',
        'parameters' => 
        array (
          'forceUpdate' => 
          array (
            'name' => 'forceUpdate',
            'default' => NULL,
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
            'startLine' => 82,
            'endLine' => 82,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'replaceMode' => 
          array (
            'name' => 'replaceMode',
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
            'startLine' => 83,
            'endLine' => 83,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'existing' => 
          array (
            'name' => 'existing',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 9,
            'endColumn' => 17,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'entityWithAutoUpdate' => 
          array (
            'name' => 'entityWithAutoUpdate',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 85,
                'endLine' => 85,
                'startTokenPos' => 430,
                'startFilePos' => 3464,
                'endTokenPos' => 430,
                'endFilePos' => 3467,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 85,
            'endLine' => 85,
            'startColumn' => 9,
            'endColumn' => 36,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
          'respectAutoUpdate' => 
          array (
            'name' => 'respectAutoUpdate',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 86,
                'endLine' => 86,
                'startTokenPos' => 439,
                'startFilePos' => 3504,
                'endTokenPos' => 439,
                'endFilePos' => 3507,
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
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 9,
            'endColumn' => 38,
            'parameterIndex' => 4,
            'isOptional' => true,
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
        'docComment' => '/**
 * Indique si on remplacerait une entité existante (public pour pré-vérification batch).
 *
 * @param  bool  $forceUpdate  Valeur legacy force_update
 * @param  string|null  $replaceMode  \'never\' | \'draft_raw_only\' | \'always\'
 * @param  Creature|Item|resource|Consumable|Spell|Breed|Panoply|Monster|null  $existing  Entité existante (avec state)
 * @param  Creature|Item|resource|Consumable|Spell|Breed|Monster|null  $entityWithAutoUpdate  Entité portant le champ auto_update (Monster pour Creature)
 */',
        'startLine' => 81,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateMonster' => 
      array (
        'name' => 'integrateMonster',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 115,
            'endLine' => 115,
            'startColumn' => 39,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 115,
                'endLine' => 115,
                'startTokenPos' => 649,
                'startFilePos' => 4643,
                'endTokenPos' => 650,
                'endFilePos' => 4644,
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
            'startLine' => 115,
            'endLine' => 115,
            'startColumn' => 61,
            'endColumn' => 79,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, ignore_unvalidated?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
 */',
        'startLine' => 115,
        'endLine' => 278,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'mapCreatureAttributes' => 
      array (
        'name' => 'mapCreatureAttributes',
        'parameters' => 
        array (
          'creatureData' => 
          array (
            'name' => 'creatureData',
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
            'startLine' => 284,
            'endLine' => 284,
            'startColumn' => 44,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'createdBy' => 
          array (
            'name' => 'createdBy',
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
            'startLine' => 284,
            'endLine' => 284,
            'startColumn' => 65,
            'endColumn' => 78,
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
 * @param  array<string, mixed>  $creatureData
 * @return array<string, mixed>
 */',
        'startLine' => 284,
        'endLine' => 306,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'sizeStringToInt' => 
      array (
        'name' => 'sizeStringToInt',
        'parameters' => 
        array (
          'size' => 
          array (
            'name' => 'size',
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
            'startLine' => 308,
            'endLine' => 308,
            'startColumn' => 38,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 308,
        'endLine' => 319,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'filterExcludedFromUpdate' => 
      array (
        'name' => 'filterExcludedFromUpdate',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 328,
            'endLine' => 328,
            'startColumn' => 47,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'exclude' => 
          array (
            'name' => 'exclude',
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
            'startLine' => 328,
            'endLine' => 328,
            'startColumn' => 60,
            'endColumn' => 73,
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
 * Retire des données les clés listées dans exclude (pour ne pas écraser à la mise à jour).
 *
 * @param  array<string, mixed>  $data
 * @param  list<string>  $exclude
 * @return array<string, mixed>
 */',
        'startLine' => 328,
        'endLine' => 336,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'filterByWhitelist' => 
      array (
        'name' => 'filterByWhitelist',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 345,
            'endLine' => 345,
            'startColumn' => 40,
            'endColumn' => 50,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'whitelist' => 
          array (
            'name' => 'whitelist',
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
            'startLine' => 345,
            'endLine' => 345,
            'startColumn' => 53,
            'endColumn' => 68,
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
 * Restreint les clés au whitelist si non vide.
 *
 * @param  array<string, mixed>  $data
 * @param  list<string>  $whitelist
 * @return array<string, mixed>
 */',
        'startLine' => 345,
        'endLine' => 353,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'attachImageFromUrl' => 
      array (
        'name' => 'attachImageFromUrl',
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
                'name' => 'object',
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
            'startColumn' => 40,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'imageUrl' => 
          array (
            'name' => 'imageUrl',
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
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 56,
            'endColumn' => 72,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 364,
                'endLine' => 364,
                'startTokenPos' => 2518,
                'startFilePos' => 14598,
                'endTokenPos' => 2519,
                'endFilePos' => 14599,
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
            'startLine' => 364,
            'endLine' => 364,
            'startColumn' => 75,
            'endColumn' => 93,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Attache une image à l\'entité via Media Library (addMediaFromUrl).
 * Respecte download_images et allowed_hosts (config scrapping.images).
 * Met à jour la colonne image de l\'entité avec l\'URL du média.
 *
 * @param  object  $entity  Modèle avec HasMedia et collection \'images\'
 * @param  array{dry_run?: bool, download_images?: bool}  $options
 * @return bool true si le média a été attaché, false si ignoré ou erreur
 */',
        'startLine' => 364,
        'endLine' => 405,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'resolveMonsterRaceId' => 
      array (
        'name' => 'resolveMonsterRaceId',
        'parameters' => 
        array (
          'monsterRaceId' => 
          array (
            'name' => 'monsterRaceId',
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
            'startLine' => 407,
            'endLine' => 407,
            'startColumn' => 43,
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
        'docComment' => NULL,
        'startLine' => 407,
        'endLine' => 419,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateSpell' => 
      array (
        'name' => 'integrateSpell',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 37,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 424,
                'endLine' => 424,
                'startTokenPos' => 3076,
                'startFilePos' => 16975,
                'endTokenPos' => 3077,
                'endFilePos' => 16976,
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
            'startLine' => 424,
            'endLine' => 424,
            'startColumn' => 59,
            'endColumn' => 77,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
 */',
        'startLine' => 424,
        'endLine' => 558,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'buildSpellPoMinMax' => 
      array (
        'name' => 'buildSpellPoMinMax',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
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
            'startLine' => 568,
            'endLine' => 568,
            'startColumn' => 41,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Construit la portée du sort (po_min, po_max).
 * Accepte des valeurs numériques ou des formules (ex. "[level]", "[level]*2").
 * 0 = soi-même, 1-1 = cac, 2-6 = plage.
 *
 * @param  array<string, mixed>  $data  Données converties du sort (spells)
 * @return array{0: string, 1: string} [po_min, po_max]
 */',
        'startLine' => 568,
        'endLine' => 588,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateSpellEffectsForSpell' => 
      array (
        'name' => 'integrateSpellEffectsForSpell',
        'parameters' => 
        array (
          'spell' => 
          array (
            'name' => 'spell',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Entity\\Spell',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 610,
            'endLine' => 610,
            'startColumn' => 52,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'payload' => 
          array (
            'name' => 'payload',
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
            'startLine' => 610,
            'endLine' => 610,
            'startColumn' => 66,
            'endColumn' => 79,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Intègre les effets convertis d\'un sort (EffectGroup, Effects, EffectSubEffect, EffectUsage).
 * Réutilise un Effect existant si sa signature de configuration (sous-effets) est identique.
 *
 * @param array{
 *   effect_group: array{name: string, slug: string},
 *   effects: list<array{
 *     degree: int,
 *     name: string,
 *     slug: string,
 *     description: string|null,
 *     sub_effects: list<array{
 *       order: int,
 *       sub_effect_slug: string,
 *       params: array<string, mixed>,
 *       crit_only: bool
 *     }>
 *   }>
 * } $payload
 */',
        'startLine' => 610,
        'endLine' => 749,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'makeUniqueEffectDegreeSlug' => 
      array (
        'name' => 'makeUniqueEffectDegreeSlug',
        'parameters' => 
        array (
          'preferred' => 
          array (
            'name' => 'preferred',
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
            'startLine' => 751,
            'endLine' => 751,
            'startColumn' => 49,
            'endColumn' => 65,
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
        'startLine' => 751,
        'endLine' => 765,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'simulateSpellEffects' => 
      array (
        'name' => 'simulateSpellEffects',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
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
            'startLine' => 774,
            'endLine' => 774,
            'startColumn' => 42,
            'endColumn' => 55,
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
 * Simule la création des effets d\'un sort sans écrire en base.
 * Retourne pour chaque effet du payload : action (create|reuse), existing_effect_id si réutilisation.
 *
 * @param  array{effect_group: array{name?: string, slug?: string}, effects: list<array{degree?: int, name?: string, slug?: string, target_type?: string, area?: string, sub_effects?: list}>}  $payload
 * @return list<array{index: int, degree: int, name: string, slug: string, target_type: string, area: string|null, sub_effects_count: int, action: \'create\'|\'reuse\', existing_effect_id: int|null}>
 */',
        'startLine' => 774,
        'endLine' => 825,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'collectSubEffectIdsFromSpellPayload' => 
      array (
        'name' => 'collectSubEffectIdsFromSpellPayload',
        'parameters' => 
        array (
          'effectsData' => 
          array (
            'name' => 'effectsData',
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
            'startLine' => 833,
            'endLine' => 833,
            'startColumn' => 58,
            'endColumn' => 75,
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
 * Collecte tous les slugs de sous-effets présents dans le payload et retourne slug => id.
 *
 * @param  list<array{sub_effects?: list<array{sub_effect_slug?: string}>}>  $effectsData
 * @return array<string, int>
 */',
        'startLine' => 833,
        'endLine' => 875,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'normalizeSubEffectsRowsForSignature' => 
      array (
        'name' => 'normalizeSubEffectsRowsForSignature',
        'parameters' => 
        array (
          'rows' => 
          array (
            'name' => 'rows',
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
            'startLine' => 884,
            'endLine' => 884,
            'startColumn' => 58,
            'endColumn' => 68,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'slugToId' => 
          array (
            'name' => 'slugToId',
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
            'startLine' => 884,
            'endLine' => 884,
            'startColumn' => 71,
            'endColumn' => 85,
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
 * Normalise les lignes sous-effets pour le calcul de signature : résolution slug → id, déduplication.
 *
 * @param  list<array{order?: int, sub_effect_slug?: string, params?: array, crit_only?: bool}>  $rows
 * @param  array<string, int>  $slugToId
 * @return list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value: mixed, condition_dofusdb_id: mixed}>
 */',
        'startLine' => 884,
        'endLine' => 924,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'computeEffectConfigSignature' => 
      array (
        'name' => 'computeEffectConfigSignature',
        'parameters' => 
        array (
          'normalizedRows' => 
          array (
            'name' => 'normalizedRows',
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
            'startLine' => 932,
            'endLine' => 932,
            'startColumn' => 51,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'targetType' => 
          array (
            'name' => 'targetType',
            'default' => 
            array (
              'code' => '\\App\\Models\\Effect::TARGET_DIRECT',
              'attributes' => 
              array (
                'startLine' => 932,
                'endLine' => 932,
                'startTokenPos' => 7444,
                'startFilePos' => 39207,
                'endTokenPos' => 7446,
                'endFilePos' => 39227,
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
            'startLine' => 932,
            'endLine' => 932,
            'startColumn' => 74,
            'endColumn' => 115,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'area' => 
          array (
            'name' => 'area',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 932,
                'endLine' => 932,
                'startTokenPos' => 7456,
                'startFilePos' => 39246,
                'endTokenPos' => 7456,
                'endFilePos' => 39249,
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
            'startLine' => 932,
            'endLine' => 932,
            'startColumn' => 118,
            'endColumn' => 137,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Calcule une signature (hash) pour réutiliser un Effect existant.
 * Inclut target_type et area pour éviter de fusionner des effets directs/piège/glyphe.
 *
 * @param  list<array{order: int, sub_effect_id: int, crit_only: bool, characteristic: mixed, value_formula: mixed, value_formula_crit: mixed, value?: mixed, condition_dofusdb_id?: mixed}>  $normalizedRows
 */',
        'startLine' => 932,
        'endLine' => 951,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'rebuildConfigSignatureForEffectDegree' => 
      array (
        'name' => 'rebuildConfigSignatureForEffectDegree',
        'parameters' => 
        array (
          'degree' => 
          array (
            'name' => 'degree',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\EffectDegree',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 956,
            'endLine' => 956,
            'startColumn' => 59,
            'endColumn' => 90,
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
 * Recalcule la config_signature d’un degré d’effet (target_type sur la définition, area sur le degré).
 */',
        'startLine' => 956,
        'endLine' => 985,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'effectSubEffectDedupKey' => 
      array (
        'name' => 'effectSubEffectDedupKey',
        'parameters' => 
        array (
          'subEffectId' => 
          array (
            'name' => 'subEffectId',
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
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 46,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'critOnly' => 
          array (
            'name' => 'critOnly',
            'default' => NULL,
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
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 64,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
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
            'startLine' => 990,
            'endLine' => 990,
            'startColumn' => 80,
            'endColumn' => 92,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Clé de déduplication pour un pivot sous-effet (même action + params = même ligne).
 */',
        'startLine' => 990,
        'endLine' => 996,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'effectSubEffectDedupWhere' => 
      array (
        'name' => 'effectSubEffectDedupWhere',
        'parameters' => 
        array (
          'subEffectId' => 
          array (
            'name' => 'subEffectId',
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
            'startLine' => 1003,
            'endLine' => 1003,
            'startColumn' => 48,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'critOnly' => 
          array (
            'name' => 'critOnly',
            'default' => NULL,
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
            'startLine' => 1003,
            'endLine' => 1003,
            'startColumn' => 66,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
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
            'startLine' => 1003,
            'endLine' => 1003,
            'startColumn' => 82,
            'endColumn' => 94,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Conditions where pour vérifier l\'existence d\'un pivot identique (déduplication).
 *
 * @return array<string, mixed>
 */',
        'startLine' => 1003,
        'endLine' => 1023,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateConditionFromParams' => 
      array (
        'name' => 'integrateConditionFromParams',
        'parameters' => 
        array (
          'spell' => 
          array (
            'name' => 'spell',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Entity\\Spell',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1030,
            'endLine' => 1030,
            'startColumn' => 51,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'subEffectSlug' => 
          array (
            'name' => 'subEffectSlug',
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
            'startLine' => 1030,
            'endLine' => 1030,
            'startColumn' => 65,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'params' => 
          array (
            'name' => 'params',
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
            'startLine' => 1030,
            'endLine' => 1030,
            'startColumn' => 88,
            'endColumn' => 100,
            'parameterIndex' => 2,
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
                  'name' => 'App\\Models\\Entity\\Condition',
                  'isIdentifier' => false,
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
 * Enregistre l\'état DofusDB lié à un sous-effet de sort et le relie au sort.
 *
 * @param  array<string, mixed>  $params
 */',
        'startLine' => 1030,
        'endLine' => 1095,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'inferSpellElementMaskFromEffectsPayload' => 
      array (
        'name' => 'inferSpellElementMaskFromEffectsPayload',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
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
            'startLine' => 1105,
            'endLine' => 1105,
            'startColumn' => 62,
            'endColumn' => 75,
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
 * Masque élémentaire du sort : uniquement params.dofus_element_id sur chaque sous-effet
 * (conversion : spell-level effectElement DofusDB, 0–4). Pas d’inférence depuis characteristic.
 *
 * @param  array{
 *   effects?: list<array{sub_effects?: list<array{params?: array<string, mixed>}>}>
 * }  $payload
 */',
        'startLine' => 1105,
        'endLine' => 1146,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'inferSpellTypeIdsFromEffectsPayload' => 
      array (
        'name' => 'inferSpellTypeIdsFromEffectsPayload',
        'parameters' => 
        array (
          'payload' => 
          array (
            'name' => 'payload',
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
            'startLine' => 1156,
            'endLine' => 1156,
            'startColumn' => 58,
            'endColumn' => 71,
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
 * Déduit les types de sort à partir des actions de sous-effets.
 *
 * @param  array{
 *   effects?: list<array{sub_effects?: list<array{sub_effect_slug?: string, params?: array<string, mixed>}>}>
 * }  $payload
 * @return list<int>
 */',
        'startLine' => 1156,
        'endLine' => 1273,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'normalizeTextKey' => 
      array (
        'name' => 'normalizeTextKey',
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
            'startLine' => 1275,
            'endLine' => 1275,
            'startColumn' => 39,
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
        'docComment' => NULL,
        'startLine' => 1275,
        'endLine' => 1291,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateBreed' => 
      array (
        'name' => 'integrateBreed',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 1296,
            'endLine' => 1296,
            'startColumn' => 37,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1296,
                'endLine' => 1296,
                'startTokenPos' => 10770,
                'startFilePos' => 54600,
                'endTokenPos' => 10771,
                'endFilePos' => 54601,
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
            'startLine' => 1296,
            'endLine' => 1296,
            'startColumn' => 59,
            'endColumn' => 77,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
 */',
        'startLine' => 1296,
        'endLine' => 1385,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integrateItem' => 
      array (
        'name' => 'integrateItem',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 1390,
            'endLine' => 1390,
            'startColumn' => 36,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1390,
                'endLine' => 1390,
                'startTokenPos' => 11641,
                'startFilePos' => 58637,
                'endTokenPos' => 11642,
                'endFilePos' => 58638,
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
            'startLine' => 1390,
            'endLine' => 1390,
            'startColumn' => 58,
            'endColumn' => 76,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, include_relations?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
 */',
        'startLine' => 1390,
        'endLine' => 1537,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'syncResourceRecipe' => 
      array (
        'name' => 'syncResourceRecipe',
        'parameters' => 
        array (
          'resource' => 
          array (
            'name' => 'resource',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Entity\\Resource',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1546,
            'endLine' => 1546,
            'startColumn' => 41,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'recipeIngredients' => 
          array (
            'name' => 'recipeIngredients',
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
            'startLine' => 1546,
            'endLine' => 1546,
            'startColumn' => 61,
            'endColumn' => 84,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Synchronise la recette d\'une ressource (pivot resource_recipe) à partir des
 * ingrédients convertis (recipe_ingredients issus de recipeIds DofusDB).
 * Seuls les ingrédients déjà présents en base (Resource avec ce dofusdb_id) sont liés.
 *
 * @param  list<array{ingredient_dofusdb_id: string, quantity: int}>  $recipeIngredients
 */',
        'startLine' => 1546,
        'endLine' => 1574,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'getItemTargetTableFromRaw' => 
      array (
        'name' => 'getItemTargetTableFromRaw',
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
            'startLine' => 1580,
            'endLine' => 1580,
            'startColumn' => 47,
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
        'docComment' => '/**
 * Détermine la table cible (resources, consumables, items) à partir des données brutes (item).
 * Permet de ne convertir que le bloc cible (performance + affichage ciblé).
 */',
        'startLine' => 1580,
        'endLine' => 1588,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'getItemTargetTable' => 
      array (
        'name' => 'getItemTargetTable',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 1594,
            'endLine' => 1594,
            'startColumn' => 40,
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
        'docComment' => '/**
 * Détermine la table cible (resources, consumables, items) à partir des données converties.
 * Utilisé par l\'orchestrateur pour la validation et par integrateItem.
 */',
        'startLine' => 1594,
        'endLine' => 1623,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'resolveItemTargetTable' => 
      array (
        'name' => 'resolveItemTargetTable',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 45,
            'endColumn' => 56,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'itemRaw' => 
          array (
            'name' => 'itemRaw',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 1636,
                'endLine' => 1636,
                'startTokenPos' => 14305,
                'startFilePos' => 70828,
                'endTokenPos' => 14305,
                'endFilePos' => 70831,
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1636,
            'endLine' => 1636,
            'startColumn' => 59,
            'endColumn' => 80,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Détermine la table cible (items, consumables, resources) à partir du typeId DofusDB.
 *
 * Ordre volontaire :
 * 1. Catalogue DofusDB (superType du typeId) + item-super-types.json — aligné sur resolveResourceTypeId /
 *    resolveConsumableTypeId / resolveItemTypeId. Doit primer sur les registries : un typeId présent à tort
 *    dans resource_types ne doit pas forcer targetModel=resources (sinon category ≠ resource → type FK null).
 * 2. Registries Krosmoz (consumable_types, resource_types, item_types) si superType non mappé ou catalogue absent.
 *
 * @param  array<string, mixed>|null  $itemRaw  Réponse brute item (optionnel) pour inférer superTypeId si le catalogue ne connaît pas le typeId
 */',
        'startLine' => 1636,
        'endLine' => 1669,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'integratePanoply' => 
      array (
        'name' => 'integratePanoply',
        'parameters' => 
        array (
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 1674,
            'endLine' => 1674,
            'startColumn' => 39,
            'endColumn' => 58,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
            'default' => 
            array (
              'code' => '[]',
              'attributes' => 
              array (
                'startLine' => 1674,
                'endLine' => 1674,
                'startTokenPos' => 14606,
                'startFilePos' => 72352,
                'endTokenPos' => 14607,
                'endFilePos' => 72353,
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
            'startLine' => 1674,
            'endLine' => 1674,
            'startColumn' => 61,
            'endColumn' => 79,
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
            'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationResult',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{dry_run?: bool, force_update?: bool, replace_mode?: string, respect_auto_update?: bool, exclude_from_update?: list<string>, property_whitelist?: list<string>}  $options
 */',
        'startLine' => 1674,
        'endLine' => 1774,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'getExistingAttributesForComparison' => 
      array (
        'name' => 'getExistingAttributesForComparison',
        'parameters' => 
        array (
          'entityType' => 
          array (
            'name' => 'entityType',
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
            'startLine' => 1784,
            'endLine' => 1784,
            'startColumn' => 56,
            'endColumn' => 73,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'convertedData' => 
          array (
            'name' => 'convertedData',
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
            'startLine' => 1784,
            'endLine' => 1784,
            'startColumn' => 76,
            'endColumn' => 95,
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
 * Retourne les attributs de l\'entité existante (si trouvée) avec les mêmes clés que les données converties (merged).
 * Utilisé pour la sortie verbose de la commande scrapping (comparaison DofusDB / converti / existant).
 *
 * @param  string  $entityType  monster, spell, breed, class, item, panoply
 * @param  array<string, array<string, mixed>>  $convertedData  Structure par modèle (creatures, monsters, spells, …)
 * @return array<string, mixed>|null Attributs avec clés "converted" (ex. strength, intelligence) ou null si pas trouvé
 */',
        'startLine' => 1784,
        'endLine' => 1902,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'resolveItemEntityType' => 
      array (
        'name' => 'resolveItemEntityType',
        'parameters' => 
        array (
          'typeId' => 
          array (
            'name' => 'typeId',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1908,
            'endLine' => 1908,
            'startColumn' => 43,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Retourne le type d\'entité UI (resource, consumable, equipment) pour un typeId DofusDB.
 * Utilisé pour l\'affichage et la comparaison des relations « item » (recettes, drops).
 */',
        'startLine' => 1908,
        'endLine' => 1917,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'getSystemUserId' => 
      array (
        'name' => 'getSystemUserId',
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
        'startLine' => 1919,
        'endLine' => 1939,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'aliasName' => NULL,
      ),
      'localizedToString' => 
      array (
        'name' => 'localizedToString',
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
            'startLine' => 1944,
            'endLine' => 1944,
            'startColumn' => 40,
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
        'docComment' => '/**
 * Convertit un champ potentiellement localisé ({fr,en,...}) vers une chaîne.
 */',
        'startLine' => 1944,
        'endLine' => 1960,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Integration',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
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