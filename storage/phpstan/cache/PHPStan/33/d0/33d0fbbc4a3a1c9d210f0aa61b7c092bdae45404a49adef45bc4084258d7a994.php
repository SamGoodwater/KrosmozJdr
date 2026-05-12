<?php declare(strict_types = 1);

// odsl-/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\Project\SetupCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.0-8.4.17-f741b2bb1d13b83ca03ab3fb52c9f31bbca670e525a6dde5b72aeecc40bb4da6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'filename' => '/var/www/KrosmozJdr/app/Console/Commands/Project/SetupCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands\\Project',
    'name' => 'App\\Console\\Commands\\Project\\SetupCommand',
    'shortName' => 'SetupCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Setup du projet : paquets apt, mises à jour, base de données, nettoyage, réinstallation.
 *
 * Utilise .env pour la BDD (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD).
 *
 * @example
 * php artisan setup --install
 * php artisan setup --update
 * php artisan setup --db
 * php artisan setup --db --no-seed
 * php artisan setup --clean
 * php artisan setup --refresh
 * php artisan setup --install --db
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 502,
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
      'APT_PACKAGES' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'name' => 'APT_PACKAGES',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'php\' => \'PHP\', \'php-cli\' => \'PHP CLI\', \'php-mysql\' => \'PHP extension MySQL\', \'php-mbstring\' => \'PHP mbstring\', \'php-xml\' => \'PHP XML\', \'php-curl\' => \'PHP cURL\', \'php-zip\' => \'PHP ZIP\', \'php-tokenizer\' => \'PHP tokenizer\', \'default-mysql-server\' => \'Serveur MySQL\', \'default-mysql-client\' => \'Client MySQL\', \'nodejs\' => \'Node.js\', \'npm\' => \'npm (pour pnpm)\', \'git\' => \'Git\', \'unzip\' => \'Unzip\', \'curl\' => \'cURL\']',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 56,
            'startTokenPos' => 84,
            'startFilePos' => 1574,
            'endTokenPos' => 191,
            'endFilePos' => 2112,
          ),
        ),
        'docComment' => '/** Paquets apt requis pour le projet (Debian/Ubuntu). */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'setup
                            {--install : Vérifier/installer les paquets apt et les dépendances (composer, pnpm)}
                            {--update : Mettre à jour apt, pnpm et composer}
                            {--db : Base de données (MySQL/PostgreSQL : création user et base si besoin, puis migrations + seeders)}
                            {--no-seed : Avec --db, ne pas lancer les seeders}
                            {--clean : Supprimer node_modules, vendor, locks et vider la config Laravel}
                            {--refresh : Clean puis réinstaller (composer + pnpm) et clear config}\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 35,
            'startTokenPos' => 62,
            'startFilePos' => 740,
            'endTokenPos' => 62,
            'endFilePos' => 1358,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 35,
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
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Setup projet : install (apt + deps), update (apt/pnpm/composer), db, clean, refresh\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 71,
            'startFilePos' => 1391,
            'endTokenPos' => 71,
            'endFilePos' => 1475,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
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
        'startLine' => 58,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runInstall' => 
      array (
        'name' => 'runInstall',
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
        'docComment' => '/** Vérifie et installe les paquets apt manquants, affiche un tableau, puis composer install et pnpm install. */',
        'startLine' => 101,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'isAptAvailable' => 
      array (
        'name' => 'isAptAvailable',
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
        'startLine' => 113,
        'endLine' => 118,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'installAptPackages' => 
      array (
        'name' => 'installAptPackages',
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
        'startLine' => 120,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'isAptPackageInstalled' => 
      array (
        'name' => 'isAptPackageInstalled',
        'parameters' => 
        array (
          'package' => 
          array (
            'name' => 'package',
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
            'startLine' => 158,
            'endLine' => 158,
            'startColumn' => 44,
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
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 158,
        'endLine' => 163,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'installComposerDeps' => 
      array (
        'name' => 'installComposerDeps',
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
        'startLine' => 165,
        'endLine' => 182,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'installPnpmDeps' => 
      array (
        'name' => 'installPnpmDeps',
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
        'startLine' => 184,
        'endLine' => 219,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runUpdate' => 
      array (
        'name' => 'runUpdate',
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
        'docComment' => '/** Met à jour : apt upgrade, pnpm global, composer self-update. */',
        'startLine' => 222,
        'endLine' => 237,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runDb' => 
      array (
        'name' => 'runDb',
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
        'docComment' => '/** Base de données : vérif extension, création user/base si besoin (MySQL ou PostgreSQL), puis migrate + seed (sauf --no-seed). */',
        'startLine' => 240,
        'endLine' => 298,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'tryAppConnection' => 
      array (
        'name' => 'tryAppConnection',
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
        'startLine' => 300,
        'endLine' => 310,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'createUserAndDatabaseWithMysql' => 
      array (
        'name' => 'createUserAndDatabaseWithMysql',
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
        'startLine' => 312,
        'endLine' => 351,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'createUserAndDatabaseWithPostgres' => 
      array (
        'name' => 'createUserAndDatabaseWithPostgres',
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
        'startLine' => 353,
        'endLine' => 389,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'quoteIdentifier' => 
      array (
        'name' => 'quoteIdentifier',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
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
            'startLine' => 391,
            'endLine' => 391,
            'startColumn' => 38,
            'endColumn' => 49,
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
        'startLine' => 391,
        'endLine' => 394,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runMigrationAndSeed' => 
      array (
        'name' => 'runMigrationAndSeed',
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
        'startLine' => 396,
        'endLine' => 413,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runClean' => 
      array (
        'name' => 'runClean',
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
        'docComment' => '/** Supprime node_modules, pnpm-lock.yaml, vendor, composer.lock et vide config/cache Laravel. */',
        'startLine' => 416,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runShell' => 
      array (
        'name' => 'runShell',
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
            'startLine' => 446,
            'endLine' => 446,
            'startColumn' => 31,
            'endColumn' => 45,
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
        'startLine' => 446,
        'endLine' => 456,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runPnpmInstallWithRecovery' => 
      array (
        'name' => 'runPnpmInstallWithRecovery',
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
        'startLine' => 458,
        'endLine' => 475,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'runShellCapture' => 
      array (
        'name' => 'runShellCapture',
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
            'startLine' => 480,
            'endLine' => 480,
            'startColumn' => 38,
            'endColumn' => 52,
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
 * @return array{code:int, output:string}
 */',
        'startLine' => 480,
        'endLine' => 492,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'aliasName' => NULL,
      ),
      'isTailwindNativeBindingHealthy' => 
      array (
        'name' => 'isTailwindNativeBindingHealthy',
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
        'startLine' => 494,
        'endLine' => 501,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Console\\Commands\\Project',
        'declaringClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'implementingClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
        'currentClassName' => 'App\\Console\\Commands\\Project\\SetupCommand',
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