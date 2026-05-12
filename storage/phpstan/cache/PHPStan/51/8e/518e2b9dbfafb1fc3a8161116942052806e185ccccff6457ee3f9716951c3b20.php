<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Dev\DevReviewCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-2d75736f6a2fa255659513ccdf34f57d8edb961efb819599266cc31dd508bd81',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Dev/DevReviewCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Dev',
    'name' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
    'shortName' => 'DevReviewCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Lance des vérifications locales (tests, analyse statique, audit, doc) et produit un rapport Markdown
 * + des prompts prêts à coller dans Cursor pour analyse / correctifs « agent ».
 *
 * @example php artisan dev:review tests
 * @example php artisan dev:review quality
 * @example php artisan dev:review all --fix-pint
 * @example php artisan dev:review security --no-cursor-prompts
 * @example php artisan dev:review all --cursor-agent
 *
 * @see docs/10-BestPractices/SECURITY_PRACTICES.md
 * @see docs/10-BestPractices/TESTING_PRACTICES.md
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 421,
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
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'dev:review
        {profile? : Profil : tests, quality, security, docs ou all (défaut : all)}
        {--report-path= : Chemin absolu ou relatif au rapport Markdown (défaut : storage/app/dev-reports/review-<timestamp>.md)}
        {--no-cursor-prompts : N’affiche pas le rappel terminal sur les prompts (les prompts restent en fin de rapport Markdown)}
        {--fix-pint : Après le profil quality (ou all), appliquer Laravel Pint sans mode test (modifie les fichiers)}
        {--cursor-agent : Après le rapport, enchaîne des Agent.prompt locaux (@cursor/sdk) pour chaque bloc « Prompts Cursor » ; requiert CURSOR_API_KEY, Node et pnpm install}\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 36,
            'startTokenPos' => 72,
            'startFilePos' => 979,
            'endTokenPos' => 72,
            'endFilePos' => 1635,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 181,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Rapport dev local (tests, qualité, sécurité, doc) + prompts Cursor ; option SDK pour agents locaux.\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 81,
            'startFilePos' => 1668,
            'endTokenPos' => 81,
            'endFilePos' => 1771,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 134,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'failures' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'name' => 'failures',
        'modifiers' => 4,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 94,
            'startFilePos' => 1834,
            'endTokenPos' => 95,
            'endFilePos' => 1835,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 33,
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
        'startLine' => 43,
        'endLine' => 88,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'runCursorSdkAgents' => 
      array (
        'name' => 'runCursorSdkAgents',
        'parameters' => 
        array (
          'reportPath' => 
          array (
            'name' => 'reportPath',
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
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 41,
            'endColumn' => 58,
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
 * Enchaîne le script Node `@cursor/sdk` (un Agent.prompt par bloc du rapport).
 */',
        'startLine' => 93,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'resolveReportPath' => 
      array (
        'name' => 'resolveReportPath',
        'parameters' => 
        array (
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
        'startLine' => 131,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'headerMarkdown' => 
      array (
        'name' => 'headerMarkdown',
        'parameters' => 
        array (
          'profile' => 
          array (
            'name' => 'profile',
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
            'startColumn' => 37,
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
        'startLine' => 145,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'footerMarkdown' => 
      array (
        'name' => 'footerMarkdown',
        'parameters' => 
        array (
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
        'startLine' => 163,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'runProfiles' => 
      array (
        'name' => 'runProfiles',
        'parameters' => 
        array (
          'profile' => 
          array (
            'name' => 'profile',
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
            'startLine' => 168,
            'endLine' => 168,
            'startColumn' => 34,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 168,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'sectionTests' => 
      array (
        'name' => 'sectionTests',
        'parameters' => 
        array (
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
        'startLine' => 197,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'sectionQuality' => 
      array (
        'name' => 'sectionQuality',
        'parameters' => 
        array (
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
        'startLine' => 213,
        'endLine' => 243,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'sectionSecurity' => 
      array (
        'name' => 'sectionSecurity',
        'parameters' => 
        array (
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
        'startLine' => 245,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'sectionDocs' => 
      array (
        'name' => 'sectionDocs',
        'parameters' => 
        array (
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
        'startLine' => 260,
        'endLine' => 297,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'sectionPintFix' => 
      array (
        'name' => 'sectionPintFix',
        'parameters' => 
        array (
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
        'startLine' => 299,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'runProcess' => 
      array (
        'name' => 'runProcess',
        'parameters' => 
        array (
          'command' => 
          array (
            'name' => 'command',
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
            'startLine' => 316,
            'endLine' => 316,
            'startColumn' => 33,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'timeoutSeconds' => 
          array (
            'name' => 'timeoutSeconds',
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
            'startLine' => 316,
            'endLine' => 316,
            'startColumn' => 49,
            'endColumn' => 67,
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
 * @param  list<string>  $command
 * @return array{exit: int, output: string, combined: string}
 */',
        'startLine' => 316,
        'endLine' => 338,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'markdownProcessResult' => 
      array (
        'name' => 'markdownProcessResult',
        'parameters' => 
        array (
          'result' => 
          array (
            'name' => 'result',
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
            'startLine' => 343,
            'endLine' => 343,
            'startColumn' => 44,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array{exit: int, output: string, combined: string}  $result
 */',
        'startLine' => 343,
        'endLine' => 350,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'cursorPromptItems' => 
      array (
        'name' => 'cursorPromptItems',
        'parameters' => 
        array (
          'profile' => 
          array (
            'name' => 'profile',
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
            'startLine' => 357,
            'endLine' => 357,
            'startColumn' => 40,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prompts détaillés : uniquement dans le rapport (évite les retours à la ligne trompeurs du terminal).
 *
 * @return list<array{title: string, prompt: string}>
 */',
        'startLine' => 357,
        'endLine' => 390,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'cursorPromptsMarkdownAppendix' => 
      array (
        'name' => 'cursorPromptsMarkdownAppendix',
        'parameters' => 
        array (
          'profile' => 
          array (
            'name' => 'profile',
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
            'startLine' => 392,
            'endLine' => 392,
            'startColumn' => 52,
            'endColumn' => 66,
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
        'startLine' => 392,
        'endLine' => 408,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'aliasName' => NULL,
      ),
      'writeCursorPromptsConsoleHint' => 
      array (
        'name' => 'writeCursorPromptsConsoleHint',
        'parameters' => 
        array (
          'reportPath' => 
          array (
            'name' => 'reportPath',
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
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 52,
            'endColumn' => 69,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'profile' => 
          array (
            'name' => 'profile',
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
            'startLine' => 410,
            'endLine' => 410,
            'startColumn' => 72,
            'endColumn' => 86,
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
        'docComment' => NULL,
        'startLine' => 410,
        'endLine' => 420,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Dev',
        'declaringClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
        'currentClassName' => 'App\\Console\\Commands\\Dev\\DevReviewCommand',
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