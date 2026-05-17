<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/ScenarioLink.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\ScenarioLink
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-a337100d63f69c4fee47a435988c16841b5e8a8dbe61107c966657ba8523cbbd-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\ScenarioLink',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/ScenarioLink.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\ScenarioLink',
    'shortName' => 'ScenarioLink',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $scenario_id
 * @property int $next_scenario_id
 * @property string|null $condition
 * @property-read Scenario $nextScenario
 * @property-read Scenario $scenario
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink whereCondition($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink whereNextScenarioId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|ScenarioLink whereScenarioId($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 53,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
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
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ScenarioLink',
        'implementingClassName' => 'App\\Models\\Type\\ScenarioLink',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'scenario_link\'',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 35,
            'startFilePos' => 1065,
            'endTokenPos' => 35,
            'endFilePos' => 1079,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'timestamps' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ScenarioLink',
        'implementingClassName' => 'App\\Models\\Type\\ScenarioLink',
        'name' => 'timestamps',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 44,
            'startFilePos' => 1108,
            'endTokenPos' => 44,
            'endFilePos' => 1112,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\ScenarioLink',
        'implementingClassName' => 'App\\Models\\Type\\ScenarioLink',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'scenario_id\', \'next_scenario_id\', \'condition\']',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 36,
            'startTokenPos' => 53,
            'startFilePos' => 1142,
            'endTokenPos' => 64,
            'endFilePos' => 1220,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 6,
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
      'scenario' => 
      array (
        'name' => 'scenario',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Le scénario source du lien.
 */',
        'startLine' => 41,
        'endLine' => 44,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ScenarioLink',
        'implementingClassName' => 'App\\Models\\Type\\ScenarioLink',
        'currentClassName' => 'App\\Models\\Type\\ScenarioLink',
        'aliasName' => NULL,
      ),
      'nextScenario' => 
      array (
        'name' => 'nextScenario',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Le scénario cible du lien.
 */',
        'startLine' => 49,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\ScenarioLink',
        'implementingClassName' => 'App\\Models\\Type\\ScenarioLink',
        'currentClassName' => 'App\\Models\\Type\\ScenarioLink',
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