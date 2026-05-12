<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Pages/RulesInjectCharacteristicKrefsInMarkdownCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Pages\RulesInjectCharacteristicKrefsInMarkdownCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-c1d4edb8c89988abab205215d15c297f76535daee50989f799f5bf6df300c6ff',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Pages/RulesInjectCharacteristicKrefsInMarkdownCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Pages',
    'name' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
    'shortName' => 'RulesInjectCharacteristicKrefsInMarkdownCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Insère dans les Markdown des règles les shortcodes {@code [[kref:characteristic:…]]} selon le catalogue partagé.
 *
 * @example php artisan pages:rules-inject-characteristic-krefs --dry-run
 * @example php artisan pages:rules-inject-characteristic-krefs
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 96,
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
      'EXCLUDED_BASENAMES' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'name' => 'EXCLUDED_BASENAMES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'TABLE_DES_MATIERES.md\', \'INDEX.md\', \'FORMAT_REGLES.md\', \'REFERENCE_CLES_CARACTERISTIQUES.md\', \'REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md\', \'AUDIT_REGLES_PUBLICATION.md\', \'RECAP.md\', \'COHERENCE_SEEDER_REGLES.md\']',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 35,
            'startTokenPos' => 76,
            'startFilePos' => 1091,
            'endTokenPos' => 102,
            'endFilePos' => 1379,
          ),
        ),
        'docComment' => '/** @var array<int, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'pages:rules-inject-characteristic-krefs
        {--path= : Répertoire racine des règles (défaut : docs/400- Jeu/420- Règles)}
        {--dry-run : Affiche les fichiers modifiés sans écrire sur le disque}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 21,
            'startTokenPos' => 54,
            'startFilePos' => 639,
            'endTokenPos' => 54,
            'endFilePos' => 849,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 81,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Injecte les références kref caractéristiques dans les .md des règles (liste : REFERENCE_KREF_CONVERSIONS_CARACTERISTIQUES.md).\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 63,
            'startFilePos' => 882,
            'endTokenPos' => 63,
            'endFilePos' => 1013,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 162,
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
        'startLine' => 37,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Pages',
        'declaringClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
        'currentClassName' => 'App\\Console\\Commands\\Pages\\RulesInjectCharacteristicKrefsInMarkdownCommand',
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