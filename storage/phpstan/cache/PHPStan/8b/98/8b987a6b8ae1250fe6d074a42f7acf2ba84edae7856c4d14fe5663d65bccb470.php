<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ImportLegacyCapabilitiesCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Characteristics\ImportLegacyCapabilitiesCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-da66ae37de4d29e5ba5822bedaebda34da396f4ed0bd738cf011737f3ec43ab9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Characteristics/ImportLegacyCapabilitiesCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Characteristics',
    'name' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
    'shortName' => 'ImportLegacyCapabilitiesCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Importe les capacités depuis un export JSON PHPMyAdmin de l\'ancienne base.
 *
 * Format supporté : export JSON du plugin "Export to JSON" pour PHPMyAdmin
 * (array racine avec objets type=header|database|table).
 *
 * Mapping ancien → nouveau :
 * - id, uniqid, timestamp_* : non conservés (nouveaux IDs auto)
 * - usable "1" → state "playable", usable "0" → state "draft"
 * - poéditables "0"/"1" → po_editable bool
 * - Valeurs par défaut pour read_level, write_level, created_by
 *
 * @example
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --dry-run
 * php artisan capabilities:import-legacy database/seeders/data/capability.json --force-update
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 258,
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
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'capabilities:import-legacy
        {file : Chemin vers le fichier JSON (export PHPMyAdmin)}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-update : Met à jour les capacités existantes (même nom) au lieu de les ignorer}\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 32,
            'startTokenPos' => 54,
            'startFilePos' => 1046,
            'endTokenPos' => 54,
            'endFilePos' => 1297,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 32,
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
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Importe les capacités depuis un export JSON PHPMyAdmin de l\\\'ancienne base\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 63,
            'startFilePos' => 1330,
            'endTokenPos' => 63,
            'endFilePos' => 1406,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 107,
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
        'startLine' => 36,
        'endLine' => 122,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'extractCapabilityRows' => 
      array (
        'name' => 'extractCapabilityRows',
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
            'startLine' => 129,
            'endLine' => 129,
            'startColumn' => 44,
            'endColumn' => 54,
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
 * Extrait le tableau de lignes capability depuis un export PHPMyAdmin.
 *
 * @return array<int, array<string, mixed>>|null
 */',
        'startLine' => 129,
        'endLine' => 145,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'importOne' => 
      array (
        'name' => 'importOne',
        'parameters' => 
        array (
          'row' => 
          array (
            'name' => 'row',
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
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 32,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 44,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'dryRun' => 
          array (
            'name' => 'dryRun',
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
            'startLine' => 153,
            'endLine' => 153,
            'startColumn' => 63,
            'endColumn' => 74,
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
 * Importe une capacité et retourne le résultat.
 *
 * @param  array<string, mixed>  $row  Ligne brute de l\'export
 * @return array{action: \'created\'|\'updated\'|\'skipped\'|\'error\', message?: string}
 */',
        'startLine' => 153,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'mapLegacyRow' => 
      array (
        'name' => 'mapLegacyRow',
        'parameters' => 
        array (
          'row' => 
          array (
            'name' => 'row',
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
            'startLine' => 187,
            'endLine' => 187,
            'startColumn' => 35,
            'endColumn' => 44,
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
 * Mappe une ligne de l\'ancien format vers les champs du nouveau modèle.
 *
 * @param  array<string, mixed>  $row
 * @return array<string, mixed>|null
 */',
        'startLine' => 187,
        'endLine' => 222,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'toBool' => 
      array (
        'name' => 'toBool',
        'parameters' => 
        array (
          'val' => 
          array (
            'name' => 'val',
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
            'startLine' => 224,
            'endLine' => 224,
            'startColumn' => 29,
            'endColumn' => 38,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 224,
        'endLine' => 231,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'nullIfEmpty' => 
      array (
        'name' => 'nullIfEmpty',
        'parameters' => 
        array (
          'val' => 
          array (
            'name' => 'val',
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
            'startLine' => 233,
            'endLine' => 233,
            'startColumn' => 34,
            'endColumn' => 43,
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
        'startLine' => 233,
        'endLine' => 238,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'stringOrDefault' => 
      array (
        'name' => 'stringOrDefault',
        'parameters' => 
        array (
          'val' => 
          array (
            'name' => 'val',
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
            'startLine' => 240,
            'endLine' => 240,
            'startColumn' => 38,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'default' => 
          array (
            'name' => 'default',
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
            'startLine' => 240,
            'endLine' => 240,
            'startColumn' => 50,
            'endColumn' => 64,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 240,
        'endLine' => 245,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'aliasName' => NULL,
      ),
      'convertElement' => 
      array (
        'name' => 'convertElement',
        'parameters' => 
        array (
          'val' => 
          array (
            'name' => 'val',
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
            'startLine' => 247,
            'endLine' => 247,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 247,
        'endLine' => 257,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Characteristics',
        'declaringClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
        'currentClassName' => 'App\\Console\\Commands\\Characteristics\\ImportLegacyCapabilitiesCommand',
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