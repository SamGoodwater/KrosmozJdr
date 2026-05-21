<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Pivots/BreedSpellPivot.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Pivots\BreedSpellPivot
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-acc48225657901040370305313cf7788ba3f93a73834ef981bf36acd20e4307c-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'filename' => '/var/www/KrosmozJdr/app/Models/Pivots/BreedSpellPivot.php',
      ),
    ),
    'namespace' => 'App\\Models\\Pivots',
    'name' => 'App\\Models\\Pivots\\BreedSpellPivot',
    'shortName' => 'BreedSpellPivot',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Pivot breed_spell : emplacement de sort (niveau PJ, slot, ordre des choix).
 *
 * @property int $breed_id
 * @property int $spell_id
 * @property int $character_level
 * @property int $slot_index
 * @property int $choice_order
 * @property int $id
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereBreedId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereCharacterLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereChoiceOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereSlotIndex($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereSpellId($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 29,
    'endLine' => 58,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
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
      'incrementing' => 
      array (
        'declaringClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'implementingClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'name' => 'incrementing',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 32,
            'startFilePos' => 1406,
            'endTokenPos' => 32,
            'endFilePos' => 1409,
          ),
        ),
        'docComment' => '/** @var bool La table pivot possède une clé `id` auto-incrémentée. */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'implementingClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'breed_spell\'',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 34,
            'startTokenPos' => 41,
            'startFilePos' => 1436,
            'endTokenPos' => 41,
            'endFilePos' => 1448,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 37,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'implementingClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'breed_id\', \'spell_id\', \'character_level\', \'slot_index\', \'choice_order\']',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 45,
            'startTokenPos' => 52,
            'startFilePos' => 1519,
            'endTokenPos' => 69,
            'endFilePos' => 1638,
          ),
        ),
        'docComment' => '/**
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 45,
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
      'casts' => 
      array (
        'name' => 'casts',
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
 * @return array<string, string>
 */',
        'startLine' => 50,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models\\Pivots',
        'declaringClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'implementingClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
        'currentClassName' => 'App\\Models\\Pivots\\BreedSpellPivot',
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