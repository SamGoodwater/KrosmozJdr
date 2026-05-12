<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataImportRulesTocCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectDataImportRulesTocCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-bfb4556bbfe9317b63af787aa735b5c0f4fe5b8a7e09cf4d57811e570c8225c2',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectDataImportRulesTocCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
    'shortName' => 'ProjectDataImportRulesTocCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Point d’entrée « données projet » pour l’import de la table des matières des règles.
 *
 * Délègue à `pages:import-rules-toc` pour garder une seule implémentation tout en exposant
 * le flux sous le namespace `project:data:*` (cohérent avec la doc domaine « données »).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 15,
    'endLine' => 40,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:data:import-rules-toc
        {path? : Chemin du fichier TABLE_DES_MATIERES.md}
        {--dry-run : Affiche le plan sans écrire en base}
        {--force-content : Écrase le contenu existant des sections avec les markdown source}\'',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 20,
            'startTokenPos' => 38,
            'startFilePos' => 490,
            'endTokenPos' => 38,
            'endFilePos' => 731,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 95,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Alias project:data — import TOC règles (délègue à pages:import-rules-toc).\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 47,
            'startFilePos' => 764,
            'endTokenPos' => 47,
            'endFilePos' => 845,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 112,
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
        'startLine' => 24,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectDataImportRulesTocCommand',
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