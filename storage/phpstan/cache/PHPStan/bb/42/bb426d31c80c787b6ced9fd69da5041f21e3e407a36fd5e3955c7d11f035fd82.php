<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectReviewCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\ProjectReviewCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-fe40fbfdf561b793982a1e3c2ca47b60431f5c5d69e5f007a6180bafd2115f7e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/ProjectReviewCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
    'shortName' => 'ProjectReviewCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Alias « projet » de {@see \\App\\Console\\Commands\\Dev\\DevReviewCommand} : même rapport Markdown
 * (tests, qualité, sécurité, doc) pour le fournir à un agent Cursor.
 *
 * @example php artisan project:review
 * @example php artisan project:review tests --report-path=storage/app/dev-reports/last-review.md
 * @example php artisan review quality
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 51,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'project:review|review
        {profile? : Profil : tests, quality, security, docs ou all (défaut : all)}
        {--report-path= : Chemin absolu ou relatif au rapport Markdown (défaut : storage/app/dev-reports/review-<timestamp>.md)}
        {--no-cursor-prompts : N’affiche pas le rappel terminal sur les prompts (les prompts restent en fin de rapport Markdown)}
        {--fix-pint : Après le profil quality (ou all), appliquer Laravel Pint sans mode test (modifie les fichiers)}
        {--cursor-agent : Enchaîne des Agent.prompt locaux (@cursor/sdk) ; requiert CURSOR_API_KEY, Node et pnpm install}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 24,
            'startTokenPos' => 38,
            'startFilePos' => 540,
            'endTokenPos' => 38,
            'endFilePos' => 1150,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 124,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Rapport dev Markdown (alias de dev:review) — à joindre à un agent\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 47,
            'startFilePos' => 1183,
            'endTokenPos' => 47,
            'endFilePos' => 1253,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 101,
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
        'startLine' => 28,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\ProjectReviewCommand',
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