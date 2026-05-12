<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectInitCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-d30043fd5198c94b0ed09f8b59192dcca6643123b9e300e48859c3d519dd7516',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectInitCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
    'shortName' => 'ProjectInitCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Initialisation complète du projet : migrations, seeders, import règles, capacités (fichier local),
 * puis types et scrapping DofusDB (appels réseau en fin de pipeline).
 *
 * Phase seeders : `scrapping:setup` (types + caractéristiques + mappings), comptes/pages,
 * {@see SubEffectSeeder}, référentiels langues / conditions / traits, pages « Création »,
 * import legacy spécialisations (fichiers HTML optionnels), puis import TOC règles.
 * Capacités : commande `capabilities:import-legacy` sur `database/seeders/data/capability.json`.
 *
 * Les appels réseau vers DofusDB (`scrapping:types:seed`, `scrapping:races:seed`, `scrapping:run`)
 * sont exécutés en fin de pipeline : tu peux interrompre l’init après seeders / capacités
 * et garder une base utilisable pour les tests (`--skip-types`, `--skip-scrapping`).
 *
 * Transforme une base vide en un projet fonctionnel. Compatible exécution longue
 * (`set_time_limit(0)`, `DB::reconnect` entre phases). Notifie les admin/super_admin à la fin.
 *
 * @example php artisan project:init
 * @example php artisan project:init --fresh --noimage
 * @example php artisan project:init --skip-scrapping --entity=monster
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 49,
    'endLine' => 551,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Console\\Concerns\\NormalizesProjectSyncEntities',
      1 => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
    ),
    'immediateConstants' => 
    array (
      'SCRAPPING_ENTITIES' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'name' => 'SCRAPPING_ENTITIES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[
    \'class\',
    // breeds
    \'spell\',
    \'monster\',
    \'resource\',
    \'consumable\',
    \'item\',
    \'panoply\',
]',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 85,
            'startTokenPos' => 174,
            'startFilePos' => 3955,
            'endTokenPos' => 199,
            'endFilePos' => 4106,
          ),
        ),
        'docComment' => '/** Ordre des entités scrapping (dépendances). */',
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'MONSTER_LEVEL_CHUNK' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'name' => 'MONSTER_LEVEL_CHUNK',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '50',
          'attributes' => 
          array (
            'startLine' => 88,
            'endLine' => 88,
            'startTokenPos' => 212,
            'startFilePos' => 4214,
            'endTokenPos' => 212,
            'endFilePos' => 4215,
          ),
        ),
        'docComment' => '/** Tranches de niveau pour monstres (éviter timeouts). */',
        'attributes' => 
        array (
        ),
        'startLine' => 88,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:init|init
        {--deps : Exécuter d’abord project:deps (composer update + pnpm up + project:optimize)}
        {--fresh : migrate:fresh --force avant tout}
        {--skip-migrate : Ne pas lancer les migrations}
        {--skip-seeders : Ne pas exécuter les seeders (socle déjà fait)}
        {--skip-scrapping : Ne pas scraper}
        {--skip-capabilities : Ne pas importer les capabilities}
        {--skip-specializations : Ne pas exécuter le seeder legacy des spécialisations (HTML locaux)}
        {--skip-types : Ne pas extraire/seed les types (resources, consommables, équipements, races monstres)}
        {--noimage : Désactiver le téléchargement des images}
        {--skip-cache : Ignorer le cache HTTP pour le scrapping}
        {--entity= : Entités (virgules) : breed|class, spell, monster, resource, consumable, item, panoply}
        {--max-items=0 : Limite par entité (0=illimité)}
        {--update-mode=ignore : Mode remplacement existants: ignore|draft_raw_auto_update|auto_update|force (ignore=ne rien remplacer, reprise rapide)}
        {--simulate : Ne pas écrire en base (validation seule)}
        {--init-scheduler : Afficher la ligne cron pour le scheduler Laravel}
        {--skip-clear-queue : Ne pas vider la queue avant le scrapping}
        {--skip-notify : Ne pas notifier les admin à la fin}
        {--skip-super-admin-prompt : Ne pas demander la création du super_admin (CI / scripts)}\'',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 72,
            'startTokenPos' => 152,
            'startFilePos' => 2279,
            'endTokenPos' => 152,
            'endFilePos' => 3730,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 72,
        'startColumn' => 5,
        'endColumn' => 98,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Initialise le projet (migrations, seeders, capacités locales, puis types/scrapping DofusDB)\'',
          'attributes' => 
          array (
            'startLine' => 74,
            'endLine' => 74,
            'startTokenPos' => 161,
            'startFilePos' => 3763,
            'endTokenPos' => 161,
            'endFilePos' => 3856,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 74,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 124,
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
        'startLine' => 90,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'printInitSummary' => 
      array (
        'name' => 'printInitSummary',
        'parameters' => 
        array (
          'phaseStatuses' => 
          array (
            'name' => 'phaseStatuses',
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
            'startLine' => 217,
            'endLine' => 217,
            'startColumn' => 9,
            'endColumn' => 28,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'success' => 
          array (
            'name' => 'success',
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
            'startLine' => 218,
            'endLine' => 218,
            'startColumn' => 9,
            'endColumn' => 21,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'float',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 219,
            'endLine' => 219,
            'startColumn' => 9,
            'endColumn' => 23,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'finishedAt' => 
          array (
            'name' => 'finishedAt',
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
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 9,
            'endColumn' => 26,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'lastError' => 
          array (
            'name' => 'lastError',
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 221,
            'endLine' => 221,
            'startColumn' => 9,
            'endColumn' => 26,
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
 * @param array<string, string> $phaseStatuses
 */',
        'startLine' => 216,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runStorageLink' => 
      array (
        'name' => 'runStorageLink',
        'parameters' => 
        array (
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
        'startLine' => 257,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runMigrations' => 
      array (
        'name' => 'runMigrations',
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
        'docComment' => NULL,
        'startLine' => 273,
        'endLine' => 283,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runSeeders' => 
      array (
        'name' => 'runSeeders',
        'parameters' => 
        array (
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
        'startLine' => 285,
        'endLine' => 339,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runRulesPagesImport' => 
      array (
        'name' => 'runRulesPagesImport',
        'parameters' => 
        array (
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
        'docComment' => '/**
 * Importe la table des matières des règles dans les pages CMS pour un projet initialisé "clé en main".
 */',
        'startLine' => 344,
        'endLine' => 359,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runTypesSetup' => 
      array (
        'name' => 'runTypesSetup',
        'parameters' => 
        array (
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
        'startLine' => 361,
        'endLine' => 395,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runScrapping' => 
      array (
        'name' => 'runScrapping',
        'parameters' => 
        array (
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
        'startLine' => 397,
        'endLine' => 472,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runScrappingMonsters' => 
      array (
        'name' => 'runScrappingMonsters',
        'parameters' => 
        array (
          'baseArgs' => 
          array (
            'name' => 'baseArgs',
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
            'startLine' => 474,
            'endLine' => 474,
            'startColumn' => 43,
            'endColumn' => 57,
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
        'startLine' => 474,
        'endLine' => 496,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runCapabilitiesImport' => 
      array (
        'name' => 'runCapabilitiesImport',
        'parameters' => 
        array (
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
        'startLine' => 498,
        'endLine' => 518,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'runInitScheduler' => 
      array (
        'name' => 'runInitScheduler',
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
        'docComment' => NULL,
        'startLine' => 520,
        'endLine' => 538,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'aliasName' => NULL,
      ),
      'clearQueue' => 
      array (
        'name' => 'clearQueue',
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
        'docComment' => NULL,
        'startLine' => 540,
        'endLine' => 550,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectInitCommand',
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