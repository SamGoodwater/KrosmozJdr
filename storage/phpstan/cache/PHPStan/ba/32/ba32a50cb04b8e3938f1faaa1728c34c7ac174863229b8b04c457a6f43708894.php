<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Monster.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Monster
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-074caed75d09ea6ebb48e2df8ebaa56fef0dd9a0a205d00a2abd443b08bd7f4b-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Monster',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Monster.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Monster',
    'shortName' => 'Monster',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
 * @property bool $auto_update
 * @property int $size
 * @property int|null $monster_race_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Creature|null $creature
 * @property-read MonsterRace|null $monsterRace
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, Spell> $spellInvocations
 * @property-read int|null $spell_invocations_count
 * @method static \\Database\\Factories\\Entity\\MonsterFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereCreatureId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereDofusVersion($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereMonsterRaceId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereSize($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereUpdatedAt($value)
 * @property int $is_boss
 * @property string $boss_pa
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereBossPa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereIsBoss($value)
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Monster whereWriteLevel($value)
 * @property-read Collection<int, Language> $languages
 * @property-read int|null $languages_count
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 59,
    'endLine' => 160,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
      'SIZE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'name' => 'SIZE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[0 => \'Minuscule\', 1 => \'Petit\', 2 => \'Moyen\', 3 => \'Grand\', 4 => \'Colossal\', 5 => \'Gigantesque\']',
          'attributes' => 
          array (
            'startLine' => 64,
            'endLine' => 71,
            'startTokenPos' => 62,
            'startFilePos' => 3153,
            'endTokenPos' => 106,
            'endFilePos' => 3304,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 64,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
      'HOSTILITY' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'name' => 'HOSTILITY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[0 => \'Amical\', 1 => \'Curieux\', 2 => \'Neutre\', 3 => \'Hostile\', 4 => \'Aggressif\']',
          'attributes' => 
          array (
            'startLine' => 73,
            'endLine' => 79,
            'startTokenPos' => 115,
            'startFilePos' => 3330,
            'endTokenPos' => 152,
            'endFilePos' => 3456,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 73,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'creature_id\', \'official_id\', \'dofusdb_id\', \'dofus_version\', \'auto_update\', \'size\', \'is_boss\', \'boss_pa\', \'monster_race_id\', \'state\', \'read_level\', \'write_level\']',
          'attributes' => 
          array (
            'startLine' => 86,
            'endLine' => 99,
            'startTokenPos' => 163,
            'startFilePos' => 3582,
            'endTokenPos' => 201,
            'endFilePos' => 3847,
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
        'startLine' => 86,
        'endLine' => 99,
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
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'size\' => \'integer\', \'auto_update\' => \'boolean\', \'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 111,
            'startTokenPos' => 212,
            'startFilePos' => 3974,
            'endTokenPos' => 242,
            'endFilePos' => 4116,
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
        'startLine' => 106,
        'endLine' => 111,
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
      'creature' => 
      array (
        'name' => 'creature',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the creature associated with the monster.
 */',
        'startLine' => 116,
        'endLine' => 119,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
        'aliasName' => NULL,
      ),
      'monsterRace' => 
      array (
        'name' => 'monsterRace',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the race of the monster.
 */',
        'startLine' => 124,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
        'aliasName' => NULL,
      ),
      'scenarios' => 
      array (
        'name' => 'scenarios',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les scénarios associés à ce monstre.
 */',
        'startLine' => 132,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
        'aliasName' => NULL,
      ),
      'campaigns' => 
      array (
        'name' => 'campaigns',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les campagnes associées à ce monstre.
 */',
        'startLine' => 140,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
        'aliasName' => NULL,
      ),
      'languages' => 
      array (
        'name' => 'languages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 145,
        'endLine' => 151,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
        'aliasName' => NULL,
      ),
      'spellInvocations' => 
      array (
        'name' => 'spellInvocations',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les sorts d\'invocation de ce monstre.
 */',
        'startLine' => 156,
        'endLine' => 159,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Monster',
        'implementingClassName' => 'App\\Models\\Entity\\Monster',
        'currentClassName' => 'App\\Models\\Entity\\Monster',
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