<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Pivots/BreedSpellPivot.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Pivots\BreedSpellPivot
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0acac7a91b446f5249eae8f73838741bee9c9c12292ab68e1223df6c1cd464cd-8.4.17-6.70.0.1',
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
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereBreedId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereCharacterLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereChoiceOrder($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereSlotIndex($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|BreedSpellPivot whereSpellId($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 27,
    'endLine' => 56,
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
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 32,
            'startFilePos' => 1400,
            'endTokenPos' => 32,
            'endFilePos' => 1403,
          ),
        ),
        'docComment' => '/** @var bool La table pivot possède une clé `id` auto-incrémentée. */',
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
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
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 41,
            'startFilePos' => 1430,
            'endTokenPos' => 41,
            'endFilePos' => 1442,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
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
            'startLine' => 37,
            'endLine' => 43,
            'startTokenPos' => 52,
            'startFilePos' => 1513,
            'endTokenPos' => 69,
            'endFilePos' => 1632,
          ),
        ),
        'docComment' => '/**
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 43,
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
        'startLine' => 48,
        'endLine' => 55,
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