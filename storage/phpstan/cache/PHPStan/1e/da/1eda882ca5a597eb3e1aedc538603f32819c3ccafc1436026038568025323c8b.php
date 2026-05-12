<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityGateCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingEffectsQualityGateCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-f479e8141b94851ec69affbece7de6a6d1dfaad6055b1aceadbc4c9211ddfc7e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsQualityGateCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
    'shortName' => 'ScrappingEffectsQualityGateCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Quality gate CI pour bloquer un import massif d\'effets de sorts si les seuils ne sont pas atteints.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 185,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:effects:quality-gate
                            {--min-coverage=99 : Couverture minimale de conversion attendue (0-100)}
                            {--max-missing-mappings=0 : Nombre maximal de mappings source=characteristic sans key}
                            {--max-missing-value-converted=0 : Nombre maximal de sous-effets attendus sans value_converted}
                            {--allow-empty : Autorise expected_rows=0 (base vide) sans faire echouer la gate}
                            {--sample-limit=20 : Nombre max d\\\'exemples collecte par l\\\'audit sous-jacent}
                            {--json : Sortie JSON}\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 24,
            'startTokenPos' => 64,
            'startFilePos' => 543,
            'endTokenPos' => 64,
            'endFilePos' => 1182,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 52,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Gate de qualité des effets de sorts (utile en CI)\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 73,
            'startFilePos' => 1215,
            'endTokenPos' => 73,
            'endFilePos' => 1266,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 82,
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
            'startLine' => 28,
            'endLine' => 28,
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
        'startLine' => 28,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'aliasName' => NULL,
      ),
      'buildConversionSummary' => 
      array (
        'name' => 'buildConversionSummary',
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
            'startLine' => 118,
            'endLine' => 118,
            'startColumn' => 45,
            'endColumn' => 97,
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
 * @return array{expected_rows:int,missing_value_converted_rows:int,coverage_percent:float}
 */',
        'startLine' => 118,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
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
            'startLine' => 172,
            'endLine' => 172,
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
        'startLine' => 172,
        'endLine' => 184,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsQualityGateCommand',
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