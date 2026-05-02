<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/SpellType.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\SpellType
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-9ab74590102da1db1b88bb793c70b4829db7275e9a9d40f27b1c3d8c7be12de4-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\SpellType',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/SpellType.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\SpellType',
    'shortName' => 'SpellType',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \\Database\\Factories\\Type\\SpellTypeFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereColor($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereIcon($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|SpellType withoutTrashed()
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 51,
    'endLine' => 105,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 77,
            'startFilePos' => 2777,
            'endTokenPos' => 77,
            'endFilePos' => 2781,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 88,
            'startFilePos' => 2816,
            'endTokenPos' => 88,
            'endFilePos' => 2822,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 99,
            'startFilePos' => 2860,
            'endTokenPos' => 99,
            'endFilePos' => 2869,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 62,
            'endLine' => 62,
            'startTokenPos' => 110,
            'startFilePos' => 2907,
            'endTokenPos' => 110,
            'endFilePos' => 2916,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 62,
        'endLine' => 62,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'color\', \'icon\', \'state\', \'read_level\', \'write_level\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 78,
            'startTokenPos' => 121,
            'startFilePos' => 3042,
            'endTokenPos' => 147,
            'endFilePos' => 3204,
          ),
        ),
        'docComment' => '/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 69,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 85,
            'endLine' => 88,
            'startTokenPos' => 158,
            'startFilePos' => 3331,
            'endTokenPos' => 174,
            'endFilePos' => 3408,
          ),
        ),
        'docComment' => '/**
 * The attributes that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 85,
        'endLine' => 88,
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
      'createdBy' => 
      array (
        'name' => 'createdBy',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the user that created the spell type.
 */',
        'startLine' => 93,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'currentClassName' => 'App\\Models\\Type\\SpellType',
        'aliasName' => NULL,
      ),
      'spells' => 
      array (
        'name' => 'spells',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les sorts de ce type.
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\SpellType',
        'implementingClassName' => 'App\\Models\\Type\\SpellType',
        'currentClassName' => 'App\\Models\\Type\\SpellType',
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