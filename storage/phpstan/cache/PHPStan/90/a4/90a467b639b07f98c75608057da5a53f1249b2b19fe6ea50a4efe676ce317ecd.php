<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Scrapping/Core/Conversion/SpellEffects/DofusdbEffectMappingService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6d2a50e29564d23bc08196891344e1c1263a67ff724882b74df8519d8579e5b0-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'filename' => '/var/www/KrosmozJdr/app/Services/Scrapping/Core/Conversion/SpellEffects/DofusdbEffectMappingService.php',
      ),
    ),
    'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
    'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
    'shortName' => 'DofusdbEffectMappingService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Résolution du mapping effectId DofusDB → sous-effet Krosmoz (BDD + cache + fallback constante).
 *
 * Lit d\'abord la table dofusdb_effect_mappings (avec cache), sinon délègue à DofusDbEffectMapping (constante PHP).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 71,
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
      'CACHE_KEY' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'name' => 'CACHE_KEY',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'scrapping.dofusdb_effect_mappings\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 52,
            'startFilePos' => 664,
            'endTokenPos' => 52,
            'endFilePos' => 698,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 66,
      ),
      'CACHE_TTL_SECONDS' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'name' => 'CACHE_TTL_SECONDS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3600',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 63,
            'startFilePos' => 739,
            'endTokenPos' => 63,
            'endFilePos' => 742,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
    ),
    'immediateProperties' => 
    array (
      'model' => 
      array (
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'name' => 'model',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\DofusdbEffectMapping',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 9,
        'endColumn' => 43,
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
          'model' => 
          array (
            'name' => 'model',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\DofusdbEffectMapping',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 24,
            'endLine' => 24,
            'startColumn' => 9,
            'endColumn' => 43,
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
        'startLine' => 23,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'aliasName' => NULL,
      ),
      'getSubEffectForEffectId' => 
      array (
        'name' => 'getSubEffectForEffectId',
        'parameters' => 
        array (
          'effectId' => 
          array (
            'name' => 'effectId',
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
            'startLine' => 32,
            'endLine' => 32,
            'startColumn' => 45,
            'endColumn' => 57,
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
 * Retourne [sub_effect_slug, characteristic_source] ou [sub_effect_slug, characteristic_source, characteristic_key].
 * Si source = characteristic, characteristic_key peut être présent (Phase 2+).
 */',
        'startLine' => 32,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'aliasName' => NULL,
      ),
      'findByEffectId' => 
      array (
        'name' => 'findByEffectId',
        'parameters' => 
        array (
          'effectId' => 
          array (
            'name' => 'effectId',
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
            'startLine' => 45,
            'endLine' => 45,
            'startColumn' => 37,
            'endColumn' => 49,
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
                  'name' => 'App\\Models\\DofusdbEffectMapping',
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
        'docComment' => NULL,
        'startLine' => 45,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'aliasName' => NULL,
      ),
      'getAllMappingsIndexedById' => 
      array (
        'name' => 'getAllMappingsIndexedById',
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
 * @return array<int, DofusdbEffectMapping>
 */',
        'startLine' => 54,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
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
        'docComment' => '/** Invalide le cache après modification des mappings (store/update/destroy). */',
        'startLine' => 67,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects',
        'declaringClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'implementingClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
        'currentClassName' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\DofusdbEffectMappingService',
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