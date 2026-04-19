<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Type/MonsterRace.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Type\MonsterRace
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-bd5c5b03e88d3a7cc79b948bcbd16270931e6872e3ea070f77d5b984e959a532-8.4.17-6.70.0.0',
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
 *
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
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 53,
    'endLine' => 158,
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
            'startLine' => 58,
            'endLine' => 58,
            'startTokenPos' => 72,
            'startFilePos' => 2856,
            'endTokenPos' => 72,
            'endFilePos' => 2860,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 58,
        'endLine' => 58,
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
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 83,
            'startFilePos' => 2895,
            'endTokenPos' => 83,
            'endFilePos' => 2901,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
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
            'startLine' => 62,
            'endLine' => 62,
            'startTokenPos' => 94,
            'startFilePos' => 2939,
            'endTokenPos' => 94,
            'endFilePos' => 2948,
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
            'startLine' => 64,
            'endLine' => 64,
            'startTokenPos' => 105,
            'startFilePos' => 2986,
            'endTokenPos' => 105,
            'endFilePos' => 2995,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 64,
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
            'startLine' => 71,
            'endLine' => 79,
            'startTokenPos' => 116,
            'startFilePos' => 3121,
            'endTokenPos' => 139,
            'endFilePos' => 3279,
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
        'startLine' => 71,
        'endLine' => 79,
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
            'startLine' => 86,
            'endLine' => 90,
            'startTokenPos' => 150,
            'startFilePos' => 3406,
            'endTokenPos' => 173,
            'endFilePos' => 3523,
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
        'startLine' => 86,
        'endLine' => 90,
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
            'startLine' => 95,
            'endLine' => 95,
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
                'startLine' => 95,
                'endLine' => 95,
                'startTokenPos' => 198,
                'startFilePos' => 3694,
                'endTokenPos' => 198,
                'endFilePos' => 3697,
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
            'startLine' => 95,
            'endLine' => 95,
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
        'startLine' => 95,
        'endLine' => 125,
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
        'startLine' => 130,
        'endLine' => 133,
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
        'startLine' => 138,
        'endLine' => 141,
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
        'startLine' => 146,
        'endLine' => 149,
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
        'startLine' => 154,
        'endLine' => 157,
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