<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Core/Preview/ScrappingPreviewBuilder.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Core\Preview\ScrappingPreviewBuilder
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-aadeb1f99e07675db21f2e8602952be5c00a9443d2740ca39b2be30d23d187dd-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Core/Preview/ScrappingPreviewBuilder.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Core\\Preview',
    'name' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
    'shortName' => 'ScrappingPreviewBuilder',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Construit les sorties prévisualisation pour la commande scrapping :
 * - raw_useful : extraction des champs bruts utiles à Krosmoz (d\'après le mapping).
 * - verbose : pour chaque propriété Krosmoz, raw_value, converted_value, valid, existing_value.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 135,
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
      'buildRawUseful' => 
      array (
        'name' => 'buildRawUseful',
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 43,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entityConfig' => 
          array (
            'name' => 'entityConfig',
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
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 55,
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
 * Extrait du brut DofusDB les valeurs utiles à Krosmoz (chemins du mapping).
 * Clés = champ cible (field), valeur = valeur brute à ce chemin.
 *
 * @param array<string, mixed> $raw Données brutes DofusDB
 * @param array<string, mixed> $entityConfig Config entité (mapping avec from.path et to[].field)
 * @return array<string, mixed>
 */',
        'startLine' => 22,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Preview',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'aliasName' => NULL,
      ),
      'mergeConverted' => 
      array (
        'name' => 'mergeConverted',
        'parameters' => 
        array (
          'converted' => 
          array (
            'name' => 'converted',
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
            'startLine' => 62,
            'endLine' => 62,
            'startColumn' => 43,
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
 * Fusionne les modèles convertis en un seul tableau (clé => valeur).
 *
 * @param array<string, array<string, mixed>> $converted Structure par modèle (creatures, monsters, …)
 * @return array<string, mixed>
 */',
        'startLine' => 62,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Preview',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'aliasName' => NULL,
      ),
      'buildVerboseProperties' => 
      array (
        'name' => 'buildVerboseProperties',
        'parameters' => 
        array (
          'rawUseful' => 
          array (
            'name' => 'rawUseful',
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
            'startLine' => 86,
            'endLine' => 86,
            'startColumn' => 9,
            'endColumn' => 24,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'convertedMerged' => 
          array (
            'name' => 'convertedMerged',
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
            'startLine' => 87,
            'endLine' => 87,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'validationErrors' => 
          array (
            'name' => 'validationErrors',
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
            'startLine' => 88,
            'endLine' => 88,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'existing' => 
          array (
            'name' => 'existing',
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 9,
            'endColumn' => 24,
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
 * Construit la structure verbose pour un item : par propriété, raw / converti / valide / existant.
 *
 * @param array<string, mixed> $rawUseful Valeurs brutes utiles (champ => valeur)
 * @param array<string, mixed> $convertedMerged Données converties fusionnées
 * @param list<array{path: string, message: string}> $validationErrors Erreurs de validation
 * @param array<string, mixed>|null $existing Attributs de l\'entité existante en BDD (mêmes clés)
 * @return array<string, array{raw_value: mixed, converted_value: mixed, valid: bool, existing_value: mixed}> Propriété => détail
 */',
        'startLine' => 85,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Preview',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'aliasName' => NULL,
      ),
      'getByPath' => 
      array (
        'name' => 'getByPath',
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
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 39,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 119,
            'endLine' => 119,
            'startColumn' => 52,
            'endColumn' => 63,
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
            'name' => 'mixed',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param array<string, mixed> $data
 * @return mixed
 */',
        'startLine' => 119,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 20,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Preview',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Preview\\ScrappingPreviewBuilder',
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