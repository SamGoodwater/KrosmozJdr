<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Services/Characteristic/Formula/CreatureFormulaPlaceholderValidator.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\Characteristic\Formula\CreatureFormulaPlaceholderValidator
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-55a3bfbed96d2b83b9ca179d617bb0bad090a32ee6daac2e71fb60cf0c43e7d8-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'filename' => '/var/www/KrosmozJdr/app/Services/Characteristic/Formula/CreatureFormulaPlaceholderValidator.php',
      ),
    ),
    'namespace' => 'App\\Services\\Characteristic\\Formula',
    'name' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
    'shortName' => 'CreatureFormulaPlaceholderValidator',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Vérifie que les placeholders `[id]` des définitions créature pointent vers des identifiants connus
 * (caractéristiques, colonnes créature, alias courts, bonus compétences français, `d` pour conversions).
 *
 * @example
 *   $v = app(CreatureFormulaPlaceholderValidator::class);
 *   $errors = $v->validateCreatureDefinitionsDirectory(database_path(\'seeders/data/characteristic-definitions/creature\'));
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 166,
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
      'formulas' => 
      array (
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'name' => 'formulas',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 9,
        'endColumn' => 59,
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
          'formulas' => 
          array (
            'name' => 'formulas',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\Characteristic\\Formula\\FormulaResolutionService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 9,
            'endColumn' => 59,
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
        'startLine' => 22,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Formula',
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'currentClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'aliasName' => NULL,
      ),
      'buildAllowedPlaceholderSet' => 
      array (
        'name' => 'buildAllowedPlaceholderSet',
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
 * Construit l’ensemble des identifiants autorisés dans les crochets (hors fonctions).
 *
 * @return array<string, true>
 */',
        'startLine' => 31,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Formula',
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'currentClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'aliasName' => NULL,
      ),
      'validateCreatureDefinitionsDirectory' => 
      array (
        'name' => 'validateCreatureDefinitionsDirectory',
        'parameters' => 
        array (
          'absoluteDirectoryPath' => 
          array (
            'name' => 'absoluteDirectoryPath',
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
            'startLine' => 65,
            'endLine' => 65,
            'startColumn' => 58,
            'endColumn' => 86,
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
 * @return list<array{file: string, characteristic: string, entity: string, field: string, unknown: string}>
 */',
        'startLine' => 65,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Formula',
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'currentClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'aliasName' => NULL,
      ),
      'validateCreatureDefinitionFile' => 
      array (
        'name' => 'validateCreatureDefinitionFile',
        'parameters' => 
        array (
          'absolutePath' => 
          array (
            'name' => 'absolutePath',
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
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 52,
            'endColumn' => 71,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'allowed' => 
          array (
            'name' => 'allowed',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 84,
                'endLine' => 84,
                'startTokenPos' => 414,
                'startFilePos' => 2889,
                'endTokenPos' => 414,
                'endFilePos' => 2892,
              ),
            ),
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 74,
            'endColumn' => 95,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  array<string, true>|null  $allowed  Si null, reconstruit via BDD.
 * @return list<array{file: string, characteristic: string, entity: string, field: string, unknown: string}>
 */',
        'startLine' => 84,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services\\Characteristic\\Formula',
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'currentClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'aliasName' => NULL,
      ),
      'registerKeyAndShortAliases' => 
      array (
        'name' => 'registerKeyAndShortAliases',
        'parameters' => 
        array (
          'allowed' => 
          array (
            'name' => 'allowed',
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
            'byRef' => true,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 152,
            'endLine' => 152,
            'startColumn' => 49,
            'endColumn' => 63,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 152,
            'endLine' => 152,
            'startColumn' => 66,
            'endColumn' => 76,
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
 * @param  array<string, true>  $allowed
 */',
        'startLine' => 152,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services\\Characteristic\\Formula',
        'declaringClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'implementingClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
        'currentClassName' => 'App\\Services\\Characteristic\\Formula\\CreatureFormulaPlaceholderValidator',
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