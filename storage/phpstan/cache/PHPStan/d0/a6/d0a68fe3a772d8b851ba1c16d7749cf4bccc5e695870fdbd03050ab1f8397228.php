<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingSeedersExportCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-e201c2422c93e444008c3bb0c01f664744b4e0b41fb8b1032fb2a7a2ae40a037',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingSeedersExportCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
    'shortName' => 'ScrappingSeedersExportCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Exporte les données de la BDD vers database/seeders/data/ pour que les seeders
 * utilisent ces fichiers comme source (au lieu de config/). Les caractéristiques
 * sont exportées en JSON (`characteristic-definitions/{groupe}/*.json`).
 *
 * À lancer après modification des caractéristiques / formules / types d\'effets via l\'interface.
 * Crée une sauvegarde ZIP des fichiers existants avant export, puis nettoie les backups > 7 ou > 7 jours.
 *
 * Disponible uniquement en environnement local et testing (désactivé en production pour limiter la surface d\'attaque).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 33,
    'endLine' => 311,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
    ),
    'immediateConstants' => 
    array (
      'BACKUP_DIR' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'BACKUP_DIR',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'seeders-data-backups\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 119,
            'startFilePos' => 1462,
            'endTokenPos' => 119,
            'endFilePos' => 1483,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 54,
      ),
      'BACKUP_MAX_COUNT' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'BACKUP_MAX_COUNT',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '7',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 130,
            'startFilePos' => 1524,
            'endTokenPos' => 130,
            'endFilePos' => 1524,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'BACKUP_MAX_AGE_DAYS' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'BACKUP_MAX_AGE_DAYS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '7',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 141,
            'startFilePos' => 1568,
            'endTokenPos' => 141,
            'endFilePos' => 1568,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:seeders:export
                            {--characteristics : Exporter uniquement characteristics}
                            {--formulas : Exporter les formules de conversion (tables characteristic_creature/object/spell)}
                            {--spell-effect-types : Exporter uniquement spell_effect_types}
                            {--scrapping-mappings : Exporter les règles de mapping scrapping (DofusDB → Krosmoz)}
                            {--item-types : Exporter resource_types, consumable_types, item_types (types item scrapping)}\'',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 48,
            'startTokenPos' => 150,
            'startFilePos' => 1599,
            'endTokenPos' => 150,
            'endFilePos' => 2166,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 123,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Exporte définitions JSON caractéristiques, spell_effect_types, mapping scrapping et types item vers database/seeders/data/\'',
          'attributes' => 
          array (
            'startLine' => 50,
            'endLine' => 50,
            'startTokenPos' => 159,
            'startFilePos' => 2199,
            'endTokenPos' => 159,
            'endFilePos' => 2324,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 50,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 156,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'db:export-seeder-data\']',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 52,
            'startTokenPos' => 168,
            'startFilePos' => 2353,
            'endTokenPos' => 170,
            'endFilePos' => 2377,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'getter' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'name' => 'getter',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Characteristic\\Getter\\CharacteristicGetterService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 55,
        'endLine' => 55,
        'startColumn' => 9,
        'endColumn' => 60,
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
          'getter' => 
          array (
            'name' => 'getter',
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 55,
            'endLine' => 55,
            'startColumn' => 9,
            'endColumn' => 60,
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
        'startLine' => 54,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
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
        'startLine' => 60,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'getFilesToExportForCurrentRun' => 
      array (
        'name' => 'getFilesToExportForCurrentRun',
        'parameters' => 
        array (
          'all' => 
          array (
            'name' => 'all',
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
            'startLine' => 109,
            'endLine' => 109,
            'startColumn' => 52,
            'endColumn' => 60,
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
 * Fichiers qui seront écrits par cette exécution (noms de fichiers uniquement).
 *
 * @return list<string>
 */',
        'startLine' => 109,
        'endLine' => 138,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'createBackupZip' => 
      array (
        'name' => 'createBackupZip',
        'parameters' => 
        array (
          'dataDir' => 
          array (
            'name' => 'dataDir',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 38,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'basenames' => 
          array (
            'name' => 'basenames',
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
            'startLine' => 145,
            'endLine' => 145,
            'startColumn' => 55,
            'endColumn' => 70,
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
 * Crée une archive ZIP des fichiers existants dans data/ et la stocke dans storage/app/seeders-data-backups/.
 *
 * @param  list<string>  $basenames
 */',
        'startLine' => 145,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'cleanupOldBackups' => 
      array (
        'name' => 'cleanupOldBackups',
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
        'docComment' => '/**
 * Supprime les backups en trop : si plus de BACKUP_MAX_COUNT, supprime ceux plus vieux que BACKUP_MAX_AGE_DAYS.
 */',
        'startLine' => 176,
        'endLine' => 194,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportCharacteristics' => 
      array (
        'name' => 'exportCharacteristics',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 196,
            'endLine' => 196,
            'startColumn' => 44,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 196,
        'endLine' => 208,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportConversionFormulasInGroups' => 
      array (
        'name' => 'exportConversionFormulasInGroups',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 210,
            'endLine' => 210,
            'startColumn' => 55,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 210,
        'endLine' => 213,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportSpellEffectTypes' => 
      array (
        'name' => 'exportSpellEffectTypes',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 215,
            'endLine' => 215,
            'startColumn' => 45,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 215,
        'endLine' => 239,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportItemTypes' => 
      array (
        'name' => 'exportItemTypes',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 241,
            'endLine' => 241,
            'startColumn' => 38,
            'endColumn' => 48,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 241,
        'endLine' => 246,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportItemTypesTable' => 
      array (
        'name' => 'exportItemTypesTable',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 43,
            'endColumn' => 53,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rows' => 
          array (
            'name' => 'rows',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 56,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'filename' => 
          array (
            'name' => 'filename',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 63,
            'endColumn' => 78,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'label' => 
          array (
            'name' => 'label',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 81,
            'endColumn' => 93,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'comment' => 
          array (
            'name' => 'comment',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 96,
            'endColumn' => 110,
            'parameterIndex' => 4,
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
 * @param  Collection<int, ResourceType|ConsumableType|ItemType>  $rows
 */',
        'startLine' => 251,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'exportScrappingMappings' => 
      array (
        'name' => 'exportScrappingMappings',
        'parameters' => 
        array (
          'dir' => 
          array (
            'name' => 'dir',
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
            'startLine' => 267,
            'endLine' => 267,
            'startColumn' => 46,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 267,
        'endLine' => 305,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'aliasName' => NULL,
      ),
      'varExportShort' => 
      array (
        'name' => 'varExportShort',
        'parameters' => 
        array (
          'var' => 
          array (
            'name' => 'var',
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
            'startLine' => 307,
            'endLine' => 307,
            'startColumn' => 37,
            'endColumn' => 46,
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
        'startLine' => 307,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingSeedersExportCommand',
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