<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Scrapping\ScrappingRunCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-6255756d8e1d5bdb2a6b3a9165cf20a5c6ee94b5a87a11de818d435e7247cc4c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Scrapping/ScrappingRunCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Scrapping',
    'name' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
    'shortName' => 'ScrappingRunCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Commande unique pour le scrapping (récupération + import).
 *
 * Par défaut : récupère les données, télécharge les images et importe en base.
 * --simulate : récupère et convertit sans écrire en base.
 * --noimage : désactive le téléchargement des images (on stocke l\'URL DofusDB telle quelle).
 * --entity peut contenir plusieurs entités (virgules) pour enchaîner les imports.
 *
 * @example
 * php artisan scrapping:run --entity=monster --id=31
 * php artisan scrapping:run --entity=monster,item --levelMin=1 --levelMax=50 --simulate
 * php artisan scrapping:run --entity=resource --type-name=Ressource --limit=100
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 32,
    'endLine' => 1331,
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
      'DISPLAY_VALUE_MAX_LENGTH' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'name' => 'DISPLAY_VALUE_MAX_LENGTH',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '200',
          'attributes' => 
          array (
            'startLine' => 668,
            'endLine' => 668,
            'startTokenPos' => 6277,
            'startFilePos' => 35292,
            'endTokenPos' => 6277,
            'endFilePos' => 35294,
          ),
        ),
        'docComment' => '/** Longueur max d\'affichage pour une valeur (évite le wrap terminal et les lignes répétées). */',
        'attributes' => 
        array (
        ),
        'startLine' => 668,
        'endLine' => 668,
        'startColumn' => 5,
        'endColumn' => 49,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scrapping:run
        {--entity= : Entité(s) à traiter (ex: monster,item,resource,consumable,panoply,spell,class). Plusieurs possibles (virgules)}
        {--batch= : Fichier JSON d\\\'import en lot (tableau ou {entities:[...]})}
        {--simulate : Ne pas écrire en base (récupération + conversion uniquement)}
        {--compare : Prévisualise et compare (raw/converted/existing)}
        {--include-relations=1 : Inclure relations (1/0) pour import/preview}
        {--replace-existing : Force mise à jour si l\\\'entité existe déjà}
        {--update-mode= : ignore|draft_raw_auto_update|auto_update|force (prioritaire sur replace-existing)}
        {--skip-existing : Ne pas appeler l\\\'API pour les entités déjà en base qu\\\'on n\\\'écraserait pas (défaut: false en init, true en update)}
        {--no-validate : Désactiver la validation}
        {--exclude-from-update= : Champs à ne pas écraser (ex: name,image,level)}
        {--ignore-unvalidated : Ignorer objets dont race/type non validé}
        {--lang=fr : Langue (pickLang)}
        {--noimage : Désactiver téléchargement/stockage d\\\'images}
        {--skip-cache : Ignorer le cache HTTP}
        {--id= : ID unique DofusDB}
        {--ids= : Liste d\\\'IDs DofusDB (virgules)}
        {--idMin= : Filtre ID minimum (plage)}
        {--idMax= : Filtre ID maximum (plage)}
        {--name= : Filtre recherche texte}
        {--typeId= : Filtre typeId DofusDB (items, resource, consumable)}
        {--typeIds= : Liste typeIds (virgules, items)}
        {--type-name= : Filtre par nom de type (ex: Ressource, Pierre brute)}
        {--raceId= : Filtre raceId (monster)}
        {--race-name= : Filtre par nom de race (ex: Bandits d\\\'Amakna)}
        {--breedId= : Filtre breedId (spell)}
        {--levelMin= : Niveau minimum (monster, item, resource, consumable, panoply)}
        {--levelMax= : Niveau maximum (monster, item, resource, consumable, panoply)}
        {--resource-types= : Pour resource: typeId depuis resource_types (allowed)}
        {--per-type=1 : (resource-types=allowed) itérer par typeId (1/0)}
        {--limit=100 : Taille de page (et de chaque requête API, moins de pages = plus rapide)}
        {--start-skip=0 : Skip initial (pagination)}
        {--max-pages=0 : Nombre max de pages (0=illimité)}
        {--max-items=500 : Nombre max d\\\'items à collecter (0=illimité ; défaut 500 pour éviter des runs trop longs)}
        {--output= : raw|useful|summary}
        {--useful= : Si output=useful: raw,converted,validated,compared}
        {--json : Sortie JSON}
        {--debug : Affiche le détail des étapes (collecte, conversion, import) pour diagnostiquer les blocages}
        {--backfill-images : Rattrapage images : télécharge et stocke les images pour les entités déjà en base (--entity=resource,item,... ou vide=tous)}
        {--backfill-force : (backfill-images) Re-télécharge même si l\\\'image locale existe déjà}
        {--backfill-chunk=200 : (backfill-images) Taille de chunk par entité}
        {--backfill-delay-ms=0 : (backfill-images) Pause entre téléchargements (ms)}\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 75,
            'startTokenPos' => 90,
            'startFilePos' => 1447,
            'endTokenPos' => 90,
            'endFilePos' => 4563,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 88,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Récupération et import DofusDB (--entity=... par défaut importe ; --simulate pour ne pas écrire ; --backfill-images pour rattraper les images).\'',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 99,
            'startFilePos' => 4596,
            'endTokenPos' => 99,
            'endFilePos' => 4744,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 179,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'aliases' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'name' => 'aliases',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'scrapping\']',
          'attributes' => 
          array (
            'startLine' => 79,
            'endLine' => 79,
            'startTokenPos' => 108,
            'startFilePos' => 4773,
            'endTokenPos' => 110,
            'endFilePos' => 4785,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 79,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 39,
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
      'isDebug' => 
      array (
        'name' => 'isDebug',
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
        'startLine' => 81,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'debugLine' => 
      array (
        'name' => 'debugLine',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 32,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'withTime' => 
          array (
            'name' => 'withTime',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 89,
                'endLine' => 89,
                'startTokenPos' => 159,
                'startFilePos' => 5064,
                'endTokenPos' => 159,
                'endFilePos' => 5067,
              ),
            ),
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
            'startLine' => 89,
            'endLine' => 89,
            'startColumn' => 49,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => true,
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
 * Affiche une ligne en mode debug uniquement (préfixe [debug] + optionnellement l’heure).
 */',
        'startLine' => 89,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'collectService' => 
          array (
            'name' => 'collectService',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Collect\\CollectService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 28,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'orchestrator' => 
          array (
            'name' => 'orchestrator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Orchestrator\\Orchestrator',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 60,
            'endColumn' => 85,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
            'startLine' => 98,
            'endLine' => 98,
            'startColumn' => 88,
            'endColumn' => 125,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 98,
        'endLine' => 517,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'createProgressBar' => 
      array (
        'name' => 'createProgressBar',
        'parameters' => 
        array (
          'max' => 
          array (
            'name' => 'max',
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
            'startLine' => 526,
            'endLine' => 526,
            'startColumn' => 40,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 526,
                'endLine' => 526,
                'startTokenPos' => 4787,
                'startFilePos' => 28367,
                'endTokenPos' => 4787,
                'endFilePos' => 28368,
              ),
            ),
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
            'startLine' => 526,
            'endLine' => 526,
            'startColumn' => 50,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Symfony\\Component\\Console\\Helper\\ProgressBar',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Crée une barre de progression avec pourcentage et libellé.
 * N\'est pas affichée en mode JSON pour ne pas corrompre la sortie.
 *
 * @param  int  $max  Nombre total d\'étapes
 * @param  string  $message  Libellé affiché à gauche (ex. "  Import monster")
 */',
        'startLine' => 526,
        'endLine' => 536,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'parseUsefulInclude' => 
      array (
        'name' => 'parseUsefulInclude',
        'parameters' => 
        array (
          'optionValue' => 
          array (
            'name' => 'optionValue',
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
            'startLine' => 544,
            'endLine' => 544,
            'startColumn' => 41,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'outputMode' => 
          array (
            'name' => 'outputMode',
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
            'startLine' => 544,
            'endLine' => 544,
            'startColumn' => 62,
            'endColumn' => 79,
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
 * Parse --useful=raw,converted,validated en liste de chaînes.
 * Si vide et output=useful, défaut : [\'raw\',\'converted\',\'validated\'].
 *
 * @return list<string>
 */',
        'startLine' => 544,
        'endLine' => 563,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'renderVerboseOutput' => 
      array (
        'name' => 'renderVerboseOutput',
        'parameters' => 
        array (
          'results' => 
          array (
            'name' => 'results',
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
            'startLine' => 570,
            'endLine' => 570,
            'startColumn' => 42,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'outputMode' => 
          array (
            'name' => 'outputMode',
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
            'startLine' => 570,
            'endLine' => 570,
            'startColumn' => 58,
            'endColumn' => 75,
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
 * Affiche la sortie en mode verbose (lisible, coloré).
 *
 * @param  array<string, mixed>  $results
 */',
        'startLine' => 570,
        'endLine' => 655,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'shortValue' => 
      array (
        'name' => 'shortValue',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 657,
            'endLine' => 657,
            'startColumn' => 33,
            'endColumn' => 40,
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
        'startLine' => 657,
        'endLine' => 665,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'formatValueForDisplay' => 
      array (
        'name' => 'formatValueForDisplay',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
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
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 44,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
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
            'startLine' => 675,
            'endLine' => 675,
            'startColumn' => 57,
            'endColumn' => 68,
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
        'docComment' => '/**
 * Formate une valeur pour l\'affichage verbose : ajoute le nom à côté des IDs type et le label pour la rareté.
 * Les champs texte longs (description, name) ne sont pas tronqués côté contenu, mais une seule ligne est émise
 * (sans retours à la ligne) pour éviter la répétition du préfixe "DofusDB" / "Converti" en sortie.
 */',
        'startLine' => 675,
        'endLine' => 727,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'longValue' => 
      array (
        'name' => 'longValue',
        'parameters' => 
        array (
          'v' => 
          array (
            'name' => 'v',
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
            'startLine' => 732,
            'endLine' => 732,
            'startColumn' => 32,
            'endColumn' => 39,
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
        'docComment' => '/**
 * Valeur affichée sans troncature (pour description, name).
 */',
        'startLine' => 732,
        'endLine' => 739,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'formatRecipeIngredientsForDisplay' => 
      array (
        'name' => 'formatRecipeIngredientsForDisplay',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
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
            'startLine' => 746,
            'endLine' => 746,
            'startColumn' => 56,
            'endColumn' => 67,
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
        'docComment' => '/**
 * Formate recipe_ingredients (liste d\'ingrédients + quantités) pour l\'affichage verbose.
 * Accepte : [ { ingredient_dofusdb_id, quantity } ], [ { ingredient_resource_id, quantity } ],
 * ou { ingredientIds: [], quantities: [] } (brut DofusDB).
 */',
        'startLine' => 746,
        'endLine' => 776,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'formatErrorMessage' => 
      array (
        'name' => 'formatErrorMessage',
        'parameters' => 
        array (
          'err' => 
          array (
            'name' => 'err',
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
            'startLine' => 781,
            'endLine' => 781,
            'startColumn' => 41,
            'endColumn' => 51,
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
        'docComment' => '/**
 * Rend un message d\'erreur HTTP lisible (extrait le message JSON si présent).
 */',
        'startLine' => 781,
        'endLine' => 793,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'handleBatchImport' => 
      array (
        'name' => 'handleBatchImport',
        'parameters' => 
        array (
          'orchestrator' => 
          array (
            'name' => 'orchestrator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Orchestrator\\Orchestrator',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 795,
            'endLine' => 795,
            'startColumn' => 40,
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
        'startLine' => 795,
        'endLine' => 883,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'apiTypeToEntity' => 
      array (
        'name' => 'apiTypeToEntity',
        'parameters' => 
        array (
          'type' => 
          array (
            'name' => 'type',
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
            'startLine' => 885,
            'endLine' => 885,
            'startColumn' => 38,
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
        'startLine' => 885,
        'endLine' => 895,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'parseEntityList' => 
      array (
        'name' => 'parseEntityList',
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
            'startLine' => 900,
            'endLine' => 900,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<int,string>
 */',
        'startLine' => 900,
        'endLine' => 909,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'normalizeEntity' => 
      array (
        'name' => 'normalizeEntity',
        'parameters' => 
        array (
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 911,
            'endLine' => 911,
            'startColumn' => 38,
            'endColumn' => 51,
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
        'startLine' => 911,
        'endLine' => 919,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'parseIds' => 
      array (
        'name' => 'parseIds',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
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
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 31,
            'endColumn' => 39,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'ids' => 
          array (
            'name' => 'ids',
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
            'startLine' => 924,
            'endLine' => 924,
            'startColumn' => 42,
            'endColumn' => 51,
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
 * @return array<int,int>
 */',
        'startLine' => 924,
        'endLine' => 945,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'extractFilters' => 
      array (
        'name' => 'extractFilters',
        'parameters' => 
        array (
          'ids' => 
          array (
            'name' => 'ids',
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
            'startLine' => 959,
            'endLine' => 959,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Extrait les filtres de collecte depuis les options de la commande.
 * levelMin/levelMax s\'appliquent aux entités avec niveau (monster, item, resource, consumable, panoply).
 * idMin/idMax à toutes les entités qui les supportent (monster, item, spell, breed, panoply).
 *
 * @param  array<int,int>  $ids
 * @return array<string,mixed>
 */',
        'startLine' => 959,
        'endLine' => 1018,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'buildImportOptions' => 
      array (
        'name' => 'buildImportOptions',
        'parameters' => 
        array (
          'collectOptions' => 
          array (
            'name' => 'collectOptions',
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
            'startLine' => 1023,
            'endLine' => 1023,
            'startColumn' => 41,
            'endColumn' => 61,
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
 * @return array{skip_cache?: bool, include_relations?: bool, force_update: bool, dry_run: bool, validate_only: bool, lang: string, exclude_from_update: list<string>, ignore_unvalidated: bool, replace_mode?: string, respect_auto_update?: bool, skip_existing?: bool}
 */',
        'startLine' => 1023,
        'endLine' => 1084,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'extractCollectOptions' => 
      array (
        'name' => 'extractCollectOptions',
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
 * @return array{skip_cache?:bool, limit?:int, page_size?:int, max_pages?:int, max_items?:int}
 */',
        'startLine' => 1089,
        'endLine' => 1102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'handleBackfillImages' => 
      array (
        'name' => 'handleBackfillImages',
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
            'startLine' => 1109,
            'endLine' => 1109,
            'startColumn' => 43,
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
        'docComment' => '/**
 * Mode rattrapage images : télécharge et attache les images via Media Library pour les entités déjà en base.
 * Utilise --entity pour limiter (resource, item, consumable, spell, monster) ou vide = tous.
 * --limit = max enregistrements à traiter, --simulate = dry-run.
 */',
        'startLine' => 1109,
        'endLine' => 1263,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'guessDofusdbImageUrl' => 
      array (
        'name' => 'guessDofusdbImageUrl',
        'parameters' => 
        array (
          'baseUrl' => 
          array (
            'name' => 'baseUrl',
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
            'startLine' => 1268,
            'endLine' => 1268,
            'startColumn' => 43,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'folder' => 
          array (
            'name' => 'folder',
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
            'startLine' => 1268,
            'endLine' => 1268,
            'startColumn' => 60,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'dofusdbId' => 
          array (
            'name' => 'dofusdbId',
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
            'startLine' => 1268,
            'endLine' => 1268,
            'startColumn' => 76,
            'endColumn' => 92,
            'parameterIndex' => 2,
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
        'docComment' => '/**
 * Déduit l\'URL d\'image DofusDB selon le dossier entité.
 */',
        'startLine' => 1268,
        'endLine' => 1278,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'aliasName' => NULL,
      ),
      'importOne' => 
      array (
        'name' => 'importOne',
        'parameters' => 
        array (
          'orchestrator' => 
          array (
            'name' => 'orchestrator',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Scrapping\\Core\\Orchestrator\\Orchestrator',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 1283,
            'endLine' => 1283,
            'startColumn' => 32,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'entity' => 
          array (
            'name' => 'entity',
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
            'startLine' => 1283,
            'endLine' => 1283,
            'startColumn' => 60,
            'endColumn' => 73,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'id' => 
          array (
            'name' => 'id',
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
            'startLine' => 1283,
            'endLine' => 1283,
            'startColumn' => 76,
            'endColumn' => 82,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'options' => 
          array (
            'name' => 'options',
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
            'startLine' => 1283,
            'endLine' => 1283,
            'startColumn' => 85,
            'endColumn' => 98,
            'parameterIndex' => 3,
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
        'startLine' => 1283,
        'endLine' => 1330,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Scrapping',
        'declaringClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
        'currentClassName' => 'App\\Console\\Commands\\Scrapping\\ScrappingRunCommand',
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