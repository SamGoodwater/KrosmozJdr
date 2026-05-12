<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Console/Concerns/PromptsPrimarySuperAdmin.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Concerns\PromptsPrimarySuperAdmin
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-476a738dcbd0dccb64c10132cb6926c045c8fffc8da77837c2376a08cba46edd-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
        'filename' => '/var/www/KrosmozJdr/app/Console/Concerns/PromptsPrimarySuperAdmin.php',
      ),
    ),
    'namespace' => 'App\\Console\\Concerns',
    'name' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
    'shortName' => 'PromptsPrimarySuperAdmin',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Création interactive du premier compte super_admin (après UserSeeder ou en standalone).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 16,
    'endLine' => 107,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
    ),
    'immediateMethods' => 
    array (
      'runPrimarySuperAdminPrompt' => 
      array (
        'name' => 'runPrimarySuperAdminPrompt',
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
 * Si la commande définit l’option `--skip-super-admin-prompt`, elle est respectée.
 * Sinon (ex. `project:super-admin`), la saisie a lieu si interactif et aucun super_admin humain.
 *
 * @throws \\RuntimeException Si l’utilisateur abandonne la saisie après erreurs de validation
 */',
        'startLine' => 24,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
        'implementingClassName' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
        'currentClassName' => 'App\\Console\\Concerns\\PromptsPrimarySuperAdmin',
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