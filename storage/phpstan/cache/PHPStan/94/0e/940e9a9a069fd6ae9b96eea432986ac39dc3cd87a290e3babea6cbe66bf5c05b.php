<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityAuditCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingEffectsQualityAuditCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-78fb62fec5c7ca23868287e2571781a65882833a973064047ae1560ccc241d8b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityAuditCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
    'shortName' => 'ScrappingEffectsQualityAuditCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Audit qualité des effets de sorts: couverture mapping + qualité value_converted.
 *
 * Objectif:
 * - Identifier les mappings incomplets (source=characteristic sans characteristic_key).
 * - Mesurer les sous-effets de sorts qui devraient avoir value_converted mais ne l\'ont pas.
 *
 * Commande orientée robustesse: sortie compacte, JSON optionnel, scan en chunks.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 22,
    'endLine' => 282,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:effects:audit-quality
                            {--json : Sortie JSON}
                            {--sample-limit=20 : Nombre max d\\\'exemples par catégorie}\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 26,
            'startTokenPos' => 64,
            'startFilePos' => 811,
            'endTokenPos' => 64,
            'endFilePos' => 982,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 26,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Audit qualité du pipeline effets de sorts (mappings + conversion value_converted)\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 73,
            'startFilePos' => 1015,
            'endTokenPos' => 73,
            'endFilePos' => 1098,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 114,
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
          'formulaResolver' => 
          array (
            'name' => 'formulaResolver',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 30,
            'endLine' => 30,
            'startColumn' => 28,
            'endColumn' => 80,
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
        'startLine' => 30,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'aliasName' => NULL,
      ),
      'buildMappingAudit' => 
      array (
        'name' => 'buildMappingAudit',
        'parameters' => 
        array (
          'sampleLimit' => 
          array (
            'name' => 'sampleLimit',
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
            'startColumn' => 40,
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
 * @return array{
 *   total_rows:int,
 *   by_source:array<string,int>,
 *   missing_characteristic_key_count:int,
 *   missing_characteristic_key_samples:list<array{dofusdb_effect_id:int,sub_effect_slug:string,characteristic_source:string}>
 * }
 */',
        'startLine' => 113,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'aliasName' => NULL,
      ),
      'buildConversionAudit' => 
      array (
        'name' => 'buildConversionAudit',
        'parameters' => 
        array (
          'formulaResolver' => 
          array (
            'name' => 'formulaResolver',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Conversion\\SpellEffects\\SpellEffectConversionFormulaResolver',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 159,
            'endLine' => 159,
            'startColumn' => 43,
            'endColumn' => 95,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'sampleLimit' => 
          array (
            'name' => 'sampleLimit',
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
            'startLine' => 159,
            'endLine' => 159,
            'startColumn' => 98,
            'endColumn' => 113,
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
 * @return array{
 *   total_spell_effect_sub_effect_rows:int,
 *   expected_rows:int,
 *   missing_value_converted_rows:int,
 *   coverage_percent:float,
 *   missing_by_slug:list<array{slug:string,count:int,sample_ids:list<int>}>
 * }
 */',
        'startLine' => 159,
        'endLine' => 240,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
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
            'startLine' => 245,
            'endLine' => 245,
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
 * @return array<string,mixed>
 */',
        'startLine' => 245,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'aliasName' => NULL,
      ),
      'buildWarnings' => 
      array (
        'name' => 'buildWarnings',
        'parameters' => 
        array (
          'mappingAudit' => 
          array (
            'name' => 'mappingAudit',
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
            'startLine' => 264,
            'endLine' => 264,
            'startColumn' => 36,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'conversionAudit' => 
          array (
            'name' => 'conversionAudit',
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
            'startLine' => 264,
            'endLine' => 264,
            'startColumn' => 57,
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
 * @param  array{missing_characteristic_key_count:int}  $mappingAudit
 * @param  array{expected_rows:int,missing_value_converted_rows:int}  $conversionAudit
 * @return list<string>
 */',
        'startLine' => 264,
        'endLine' => 281,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityAuditCommand',
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