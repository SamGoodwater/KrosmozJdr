<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Condition.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Condition
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-5c1ea1e4863746f6201cb69356edd93d476c7090ce9cc25f9753925a1df33ffa-8.4.17-6.70.0.1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Condition',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Condition.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Condition',
    'shortName' => 'Condition',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Référentiel canonique des états/conditions de jeu.
 *
 * @property int $id
 * @property int|null $dofusdb_id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $icon
 * @property string|null $image
 * @property bool $dissipable
 * @property array<array-key, mixed>|null $raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read Collection<int, Spell> $spells
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property bool $prevents_spell_cast
 * @property bool $prevents_fight
 * @property bool $cant_be_moved
 * @property bool $cant_be_pushed
 * @property bool $cant_deal_damage
 * @property bool $invulnerable
 * @property bool $cant_switch_position
 * @property bool $incurable
 * @property bool $invulnerable_melee
 * @property bool $invulnerable_range
 * @property bool $cant_tackle
 * @property bool $cant_be_tackled
 * @property bool $display_turn_remaining
 * @property bool $is_main_state
 * @property-read int|null $creatures_count
 * @property-read int|null $spells_count
 *
 * @method static \\Database\\Factories\\Entity\\ConditionFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantBeMoved($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantBePushed($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantBeTackled($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantDealDamage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantSwitchPosition($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCantTackle($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereDisplayTurnRemaining($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereDissipable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereIcon($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereIncurable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereInvulnerable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereInvulnerableMelee($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereInvulnerableRange($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereIsMainState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition wherePreventsFight($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition wherePreventsSpellCast($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereRaw($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition withTrashed(bool $withTrashed = true)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Condition withoutTrashed()
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 95,
    'endLine' => 132,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
      0 => 'Spatie\\MediaLibrary\\HasMedia',
    ),
    'traitClassNames' => 
    array (
      0 => 'App\\Models\\Concerns\\HasEntityImageMedia',
      1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'STATE_RAW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 100,
            'endLine' => 100,
            'startTokenPos' => 94,
            'startFilePos' => 5598,
            'endTokenPos' => 94,
            'endFilePos' => 5602,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 100,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 102,
            'endLine' => 102,
            'startTokenPos' => 105,
            'startFilePos' => 5637,
            'endTokenPos' => 105,
            'endFilePos' => 5643,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 102,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 104,
            'endLine' => 104,
            'startTokenPos' => 116,
            'startFilePos' => 5681,
            'endTokenPos' => 116,
            'endFilePos' => 5690,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 104,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 106,
            'endLine' => 106,
            'startTokenPos' => 127,
            'startFilePos' => 5728,
            'endTokenPos' => 127,
            'endFilePos' => 5737,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 106,
        'endLine' => 106,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/conditions\'',
          'attributes' => 
          array (
            'startLine' => 108,
            'endLine' => 108,
            'startTokenPos' => 138,
            'startFilePos' => 5771,
            'endTokenPos' => 138,
            'endFilePos' => 5796,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 108,
        'endLine' => 108,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 110,
            'endLine' => 110,
            'startTokenPos' => 149,
            'startFilePos' => 5845,
            'endTokenPos' => 149,
            'endFilePos' => 5863,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 110,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_id\', \'name\', \'description\', \'state\', \'read_level\', \'write_level\', \'icon\', \'image\', \'prevents_spell_cast\', \'prevents_fight\', \'cant_be_moved\', \'cant_be_pushed\', \'cant_deal_damage\', \'invulnerable\', \'cant_switch_position\', \'incurable\', \'invulnerable_melee\', \'invulnerable_range\', \'cant_tackle\', \'cant_be_tackled\', \'display_turn_remaining\', \'is_main_state\', \'dissipable\', \'raw\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 113,
            'endLine' => 113,
            'startTokenPos' => 160,
            'startFilePos' => 5922,
            'endTokenPos' => 234,
            'endFilePos' => 6318,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 113,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 424,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'dofusdb_id\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'prevents_spell_cast\' => \'boolean\', \'prevents_fight\' => \'boolean\', \'cant_be_moved\' => \'boolean\', \'cant_be_pushed\' => \'boolean\', \'cant_deal_damage\' => \'boolean\', \'invulnerable\' => \'boolean\', \'cant_switch_position\' => \'boolean\', \'incurable\' => \'boolean\', \'invulnerable_melee\' => \'boolean\', \'invulnerable_range\' => \'boolean\', \'cant_tackle\' => \'boolean\', \'cant_be_tackled\' => \'boolean\', \'display_turn_remaining\' => \'boolean\', \'is_main_state\' => \'boolean\', \'dissipable\' => \'boolean\', \'raw\' => \'array\']',
          'attributes' => 
          array (
            'startLine' => 116,
            'endLine' => 116,
            'startTokenPos' => 245,
            'startFilePos' => 6383,
            'endTokenPos' => 377,
            'endFilePos' => 6961,
          ),
        ),
        'docComment' => '/** @var array<string, string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 116,
        'endLine' => 116,
        'startColumn' => 5,
        'endColumn' => 603,
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
        'docComment' => NULL,
        'startLine' => 118,
        'endLine' => 121,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
        'aliasName' => NULL,
      ),
      'creatures' => 
      array (
        'name' => 'creatures',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 123,
        'endLine' => 126,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
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
        'docComment' => NULL,
        'startLine' => 128,
        'endLine' => 131,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Condition',
        'implementingClassName' => 'App\\Models\\Entity\\Condition',
        'currentClassName' => 'App\\Models\\Entity\\Condition',
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