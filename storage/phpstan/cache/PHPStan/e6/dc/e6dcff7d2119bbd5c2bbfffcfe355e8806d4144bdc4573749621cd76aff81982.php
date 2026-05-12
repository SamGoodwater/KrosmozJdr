<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Console/Concerns/GuardsProductionEnvironment.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Concerns\GuardsProductionEnvironment
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-87ef059d5e30de0d572a63a6449fc018a5c2fccfc0746c36a1d9bda76d26bff3-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'filename' => '/var/www/KrosmozJdr/app/Console/Concerns/GuardsProductionEnvironment.php',
      ),
    ),
    'namespace' => 'App\\Console\\Concerns',
    'name' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
    'shortName' => 'GuardsProductionEnvironment',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Trait pour les commandes qui ne doivent pas s’exécuter en production (ou uniquement en local/testing).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 43,
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
      'guardDevelopmentOnly' => 
      array (
        'name' => 'guardDevelopmentOnly',
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
 * Vérifie que l\'environnement est local ou testing. Sinon affiche une erreur.
 *
 * @return bool true si la commande peut continuer, false sinon (quitter avec FAILURE)
 */',
        'startLine' => 17,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'implementingClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'currentClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'aliasName' => NULL,
      ),
      'guardNotProduction' => 
      array (
        'name' => 'guardNotProduction',
        'parameters' => 
        array (
          'message' => 
          array (
            'name' => 'message',
            'default' => 
            array (
              'code' => '\'Cette commande est interdite en production.\'',
              'attributes' => 
              array (
                'startLine' => 33,
                'endLine' => 33,
                'startTokenPos' => 94,
                'startFilePos' => 952,
                'endTokenPos' => 94,
                'endFilePos' => 996,
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
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 43,
            'endColumn' => 105,
            'parameterIndex' => 0,
            'isOptional' => true,
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
        'docComment' => '/**
 * Interdit uniquement `APP_ENV=production` (staging et autres environnements non prod restent autorisés).
 *
 * @return bool true si la commande peut continuer
 */',
        'startLine' => 33,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Console\\Concerns',
        'declaringClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'implementingClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
        'currentClassName' => 'App\\Console\\Concerns\\GuardsProductionEnvironment',
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