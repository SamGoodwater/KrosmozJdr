<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsPipelineCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingEffectsPipelineCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-71ec7928f8d8d8dc6f43a27602f291f3968cdb5f90730550b44447ac0de350fa',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingEffectsPipelineCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
    'shortName' => 'ScrappingEffectsPipelineCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Pipeline prêt à l\'emploi: import batch de sorts puis quality gate des effets.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 134,
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
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:effects:pipeline
                            {--simulate : Import sans écriture BDD}
                            {--allow-empty : Autorise expected_rows=0 à la gate}
                            {--ids= : Liste d\\\'IDs de sorts (virgules)}
                            {--levelMin= : Filtre niveau minimum}
                            {--levelMax= : Filtre niveau maximum}
                            {--limit=100 : Taille de page API}
                            {--max-pages=0 : Nombre max de pages (0=illimité)}
                            {--max-items=300 : Nombre max d\\\'items collectés}
                            {--skip-cache : Ignore le cache HTTP}
                            {--include-relations=1 : Inclure les relations à l\\\'import (1/0)}
                            {--min-coverage=99 : Couverture minimale attendue pour la gate}
                            {--max-missing-mappings=0 : Max mappings source=characteristic sans key}
                            {--max-missing-value-converted=0 : Max sous-effets attendus sans value_converted}
                            {--sample-limit=20 : Echantillons max pour l\\\'audit}
                            {--json : Sortie JSON consolidée}\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 31,
            'startTokenPos' => 54,
            'startFilePos' => 394,
            'endTokenPos' => 54,
            'endFilePos' => 1606,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 64,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Import des sorts puis quality gate des effets de sorts\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 63,
            'startFilePos' => 1639,
            'endTokenPos' => 63,
            'endFilePos' => 1694,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 86,
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
        'startLine' => 35,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'aliasName' => NULL,
      ),
      'decodeJsonPayload' => 
      array (
        'name' => 'decodeJsonPayload',
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
            'startLine' => 128,
            'endLine' => 128,
            'startColumn' => 40,
            'endColumn' => 50,
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
 * @return array<string,mixed>|null
 */',
        'startLine' => 128,
        'endLine' => 133,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingEffectsPipelineCommand',
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