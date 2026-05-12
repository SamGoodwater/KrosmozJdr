<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMissingCharacteristicsReportCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingEffectsMissingCharacteristicsReportCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-957843a82af64cb2b3aa65a0f44afe99d378dd32fd9495026408d9c01b545175',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsMissingCharacteristicsReportCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
    'shortName' => 'ScrappingEffectsMissingCharacteristicsReportCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Rapport des mappings d\'effets incomplets (characteristic_source=characteristic, key manquante).
 *
 * Produit un regroupement par characteristic DofusDB (GET /effects/{id}.characteristic),
 * trié par fréquence, pour prioriser les prochains ajouts de conversion.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 19,
    'endLine' => 242,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:effects:report-missing-characteristics
                            {--ids= : Liste d\\\'effectId séparés par des virgules (optionnel)}
                            {--limit=20 : Nombre max de lignes dans le top}
                            {--lang=fr : Langue API DofusDB}
                            {--json : Sortie JSON}
                            {--skip-cache : Ignore le cache HTTP DofusDB}\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 26,
            'startTokenPos' => 64,
            'startFilePos' => 711,
            'endTokenPos' => 64,
            'endFilePos' => 1118,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 75,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Rapport des mappings d\\\'effets sans characteristic_key, regroupés par characteristic DofusDB\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 73,
            'startFilePos' => 1151,
            'endTokenPos' => 73,
            'endFilePos' => 1245,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 125,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb:report-missing-effect-characteristics\']',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 82,
            'startFilePos' => 1274,
            'endTokenPos' => 84,
            'endFilePos' => 1322,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 75,
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
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 9,
            'endColumn' => 29,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'characteristicGetter' => 
          array (
            'name' => 'characteristicGetter',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 9,
            'endColumn' => 57,
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
        'startLine' => 32,
        'endLine' => 190,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'aliasName' => NULL,
      ),
      'parseIdsOption' => 
      array (
        'name' => 'parseIdsOption',
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
            'startLine' => 195,
            'endLine' => 195,
            'startColumn' => 37,
            'endColumn' => 47,
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
 * @return list<int>
 */',
        'startLine' => 195,
        'endLine' => 214,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'aliasName' => NULL,
      ),
      'loadSpellCharacteristicMapFromConfig' => 
      array (
        'name' => 'loadSpellCharacteristicMapFromConfig',
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
 * @return array<int, string>
 */',
        'startLine' => 219,
        'endLine' => 241,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsMissingCharacteristicsReportCommand',
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