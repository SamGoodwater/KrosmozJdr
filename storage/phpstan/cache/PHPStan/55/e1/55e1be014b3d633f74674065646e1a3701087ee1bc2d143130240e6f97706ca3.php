<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/MonsterRace.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\MonsterRace
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-54a713e9a8369e1d77a8e31f0d552b8f9dbb89330a20abee360d7f234c31bc22-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Type\\MonsterRace',
        'filename' => '/var/www/KrosmozJdr/app/Models/Type/MonsterRace.php',
      ),
    ),
    'namespace' => 'App\\Models\\Type',
    'name' => 'App\\Models\\Type\\MonsterRace',
    'shortName' => 'MonsterRace',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int|null $dofusdb_race_id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $id_super_race
 * @property-read User|null $createdBy
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read Collection<int, MonsterRace> $subRaces
 * @property-read int|null $sub_races_count
 * @property-read MonsterRace|null $superRace
 * @method static \\Database\\Factories\\Type\\MonsterRaceFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereIdSuperRace($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace withoutTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|MonsterRace whereDofusdbRaceId($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 51,
    'endLine' => 156,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
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
            'startTokenPos' => 72,
            'startFilePos' => 2850,
            'endTokenPos' => 72,
            'endFilePos' => 2854,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
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
            'startTokenPos' => 83,
            'startFilePos' => 2889,
            'endTokenPos' => 83,
            'endFilePos' => 2895,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
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
            'startTokenPos' => 94,
            'startFilePos' => 2933,
            'endTokenPos' => 94,
            'endFilePos' => 2942,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
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
            'startTokenPos' => 105,
            'startFilePos' => 2980,
            'endTokenPos' => 105,
            'endFilePos' => 2989,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_race_id\', \'name\', \'state\', \'read_level\', \'write_level\', \'created_by\', \'id_super_race\']',
          'attributes' => 
          array (
            'startLine' => 69,
            'endLine' => 77,
            'startTokenPos' => 116,
            'startFilePos' => 3115,
            'endTokenPos' => 139,
            'endFilePos' => 3273,
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
        'endLine' => 77,
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
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'read_level\' => \'integer\', \'write_level\' => \'integer\', \'dofusdb_race_id\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 84,
            'endLine' => 88,
            'startTokenPos' => 150,
            'startFilePos' => 3400,
            'endTokenPos' => 173,
            'endFilePos' => 3517,
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
        'startLine' => 84,
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
      'touchDofusdbRace' => 
      array (
        'name' => 'touchDofusdbRace',
        'parameters' => 
        array (
          'dofusdbRaceId' => 
          array (
            'name' => 'dofusdbRaceId',
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
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 45,
            'endColumn' => 62,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 93,
                'endLine' => 93,
                'startTokenPos' => 198,
                'startFilePos' => 3688,
                'endTokenPos' => 198,
                'endFilePos' => 3691,
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
                      'name' => 'string',
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
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 65,
            'endColumn' => 84,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Enregistre/actualise une race DofusDB vue pendant le scrapping.
 */',
        'startLine' => 93,
        'endLine' => 123,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'currentClassName' => 'App\\Models\\Type\\MonsterRace',
        'aliasName' => NULL,
      ),
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
 * Get the user that created the monster race.
 */',
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'currentClassName' => 'App\\Models\\Type\\MonsterRace',
        'aliasName' => NULL,
      ),
      'superRace' => 
      array (
        'name' => 'superRace',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the super race (parent race) of this monster race.
 */',
        'startLine' => 136,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'currentClassName' => 'App\\Models\\Type\\MonsterRace',
        'aliasName' => NULL,
      ),
      'monsters' => 
      array (
        'name' => 'monsters',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les monstres de cette race.
 */',
        'startLine' => 144,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'currentClassName' => 'App\\Models\\Type\\MonsterRace',
        'aliasName' => NULL,
      ),
      'subRaces' => 
      array (
        'name' => 'subRaces',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les sous-races de cette race.
 */',
        'startLine' => 152,
        'endLine' => 155,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Type',
        'declaringClassName' => 'App\\Models\\Type\\MonsterRace',
        'implementingClassName' => 'App\\Models\\Type\\MonsterRace',
        'currentClassName' => 'App\\Models\\Type\\MonsterRace',
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