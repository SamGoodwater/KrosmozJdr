<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Effects/EffectsRebuildSignaturesCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Effects\EffectsRebuildSignaturesCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-21e9dfb626a850a362469ba62edb2248899dde3cf9719e60fc13f4e287dee58d',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Effects/EffectsRebuildSignaturesCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Effects',
    'name' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
    'shortName' => 'EffectsRebuildSignaturesCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Recalcule config_signature pour tous les degrés d’effet.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 84,
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
        'declaringClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'effects:rebuild-signatures
                            {--dry-run : N\\\'écrit pas en base, affiche seulement les changements}
                            {--ids= : IDs de effect_degrees séparés par des virgules (optionnel)}\'',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 19,
            'startTokenPos' => 59,
            'startFilePos' => 425,
            'endTokenPos' => 59,
            'endFilePos' => 651,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 101,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Recalcule config_signature des degrés d’effet (target_type + zone + sous-effets)\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 68,
            'startFilePos' => 684,
            'endTokenPos' => 68,
            'endFilePos' => 768,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 115,
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
          'integrationService' => 
          array (
            'name' => 'integrationService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Integration\\IntegrationService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 28,
            'endColumn' => 65,
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
        'startLine' => 23,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Effects',
        'declaringClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Effects\\EffectsRebuildSignaturesCommand',
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