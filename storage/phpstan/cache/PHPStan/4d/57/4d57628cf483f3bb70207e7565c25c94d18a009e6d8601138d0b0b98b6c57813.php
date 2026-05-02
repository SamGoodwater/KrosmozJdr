<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Spell.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Spell
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-17d5ea70254af6566aa2471bba1d379b3a216c81ec5846d6943a7606708a0509-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Spell',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Spell.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Spell',
    'shortName' => 'Spell',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description Toujours non null en base ; une valeur API `null` est normalisée en chaîne vide.
 * @property string|null $effect
 * @property string $level
 * @property string|null $po_min Portée min (valeur ou formule, ex. "0", "[level]")
 * @property string|null $po_max Portée max (valeur ou formule, ex. "1", "6")
 * @property bool $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property string $cast_per_target
 * @property bool $sight_line
 * @property string $number_between_two_cast
 * @property int $element
 * @property int $category
 * @property bool $is_magic
 * @property int $powerful
 * @property string $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property bool $auto_success_if_willing_target Réussite auto si la cible est consentante
 * @property bool $allows_reaction Utilisable comme réaction de combat (PA non récupérés au tour suivant)
 * @property string|null $casting_time Temps d\'incantation (texte libre)
 * @property bool|null $ritual_available Utilisable en mode rituel (null = non renseigné)
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Collection<int, SpellType> $spellTypes
 * @property-read int|null $spell_types_count
 *
 * @method static \\Database\\Factories\\Entity\\SpellFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereArea($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereAutoUpdate($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCategory($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereDofusdbId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereEffect($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereElement($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereIsMagic($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereOfficialId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell wherePa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell wherePoMin($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell wherePoMax($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell wherePoEditable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell wherePowerful($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereSightLine($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell withoutTrashed()
 *
 * @property string|null $duration
 * @property-read Collection<int, Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read Collection<int, Effect> $effects
 * @property-read int|null $effects_count
 * @property-read string|null $area
 * @property-read string $po_display
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, SpellEffect> $spellEffects
 * @property-read int|null $spell_effects_count
 * @property-read Collection<int, SpellState> $spellStates
 * @property-read int|null $spell_states_count
 *
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereAllowsReaction($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereAttackCharacteristicKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereAutoSuccessIfWillingTarget($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereCastingTime($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereDuration($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereResolutionMode($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereRitualAvailable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereSaveCharacteristicKey($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereSaveDcFormula($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Spell whereSaveSuccessNote($value)
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 137,
    'endLine' => 412,
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
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 142,
            'endLine' => 142,
            'startTokenPos' => 129,
            'startFilePos' => 8007,
            'endTokenPos' => 129,
            'endFilePos' => 8011,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 142,
        'endLine' => 142,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 144,
            'endLine' => 144,
            'startTokenPos' => 140,
            'startFilePos' => 8046,
            'endTokenPos' => 140,
            'endFilePos' => 8052,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 144,
        'endLine' => 144,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 146,
            'endLine' => 146,
            'startTokenPos' => 151,
            'startFilePos' => 8090,
            'endTokenPos' => 151,
            'endFilePos' => 8099,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 146,
        'endLine' => 146,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 148,
            'endLine' => 148,
            'startTokenPos' => 162,
            'startFilePos' => 8137,
            'endTokenPos' => 162,
            'endFilePos' => 8146,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 148,
        'endLine' => 148,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'CATEGORY_CLASS' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'CATEGORY_CLASS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '0',
          'attributes' => 
          array (
            'startLine' => 150,
            'endLine' => 150,
            'startTokenPos' => 173,
            'startFilePos' => 8184,
            'endTokenPos' => 173,
            'endFilePos' => 8184,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 150,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'CATEGORY_CREATURE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'CATEGORY_CREATURE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '1',
          'attributes' => 
          array (
            'startLine' => 152,
            'endLine' => 152,
            'startTokenPos' => 184,
            'startFilePos' => 8225,
            'endTokenPos' => 184,
            'endFilePos' => 8225,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 152,
        'endLine' => 152,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'CATEGORY_LEARNABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'CATEGORY_LEARNABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '2',
          'attributes' => 
          array (
            'startLine' => 154,
            'endLine' => 154,
            'startTokenPos' => 195,
            'startFilePos' => 8267,
            'endTokenPos' => 195,
            'endFilePos' => 8267,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 154,
        'endLine' => 154,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'CATEGORY_CONSUMABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'CATEGORY_CONSUMABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '3',
          'attributes' => 
          array (
            'startLine' => 156,
            'endLine' => 156,
            'startTokenPos' => 206,
            'startFilePos' => 8310,
            'endTokenPos' => 206,
            'endFilePos' => 8310,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 156,
        'endLine' => 156,
        'startColumn' => 5,
        'endColumn' => 41,
      ),
      'RESOLUTION_ATTACK_ROLL' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'RESOLUTION_ATTACK_ROLL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'attack_roll\'',
          'attributes' => 
          array (
            'startLine' => 158,
            'endLine' => 158,
            'startTokenPos' => 217,
            'startFilePos' => 8356,
            'endTokenPos' => 217,
            'endFilePos' => 8368,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 158,
        'endLine' => 158,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'RESOLUTION_SAVING_THROW' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'RESOLUTION_SAVING_THROW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'saving_throw\'',
          'attributes' => 
          array (
            'startLine' => 160,
            'endLine' => 160,
            'startTokenPos' => 228,
            'startFilePos' => 8415,
            'endTokenPos' => 228,
            'endFilePos' => 8428,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 160,
        'endLine' => 160,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'RESOLUTION_AUTO_SUCCESS' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'RESOLUTION_AUTO_SUCCESS',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'auto_success\'',
          'attributes' => 
          array (
            'startLine' => 162,
            'endLine' => 162,
            'startTokenPos' => 239,
            'startFilePos' => 8475,
            'endTokenPos' => 239,
            'endFilePos' => 8488,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 162,
        'endLine' => 162,
        'startColumn' => 5,
        'endColumn' => 58,
      ),
      'AREAS_SHAPE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'AREAS_SHAPE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\\App\\Support\\AreaConstants::SHAPE_ID_MAP',
          'attributes' => 
          array (
            'startLine' => 165,
            'endLine' => 165,
            'startTokenPos' => 252,
            'startFilePos' => 8583,
            'endTokenPos' => 254,
            'endFilePos' => 8609,
          ),
        ),
        'docComment' => '/** @deprecated Utiliser AreaConstants::SHAPE_ID_MAP */',
        'attributes' => 
        array (
        ),
        'startLine' => 165,
        'endLine' => 165,
        'startColumn' => 5,
        'endColumn' => 59,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/spells\'',
          'attributes' => 
          array (
            'startLine' => 168,
            'endLine' => 168,
            'startTokenPos' => 267,
            'startFilePos' => 8697,
            'endTokenPos' => 267,
            'endFilePos' => 8718,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 168,
        'endLine' => 168,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 171,
            'endLine' => 171,
            'startTokenPos' => 280,
            'startFilePos' => 8859,
            'endTokenPos' => 280,
            'endFilePos' => 8877,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 171,
        'endLine' => 171,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'ATTRIBUTE_FALLBACK_WHEN_NULL' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'ATTRIBUTE_FALLBACK_WHEN_NULL',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'description\' => \'\', \'level\' => \'1\', \'po_min\' => \'1\', \'po_max\' => \'1\', \'pa\' => \'3\', \'cast_per_turn\' => \'1\', \'cast_per_target\' => \'0\', \'number_between_two_cast\' => \'0\', \'po_editable\' => true, \'sight_line\' => true, \'category\' => 0, \'is_magic\' => true, \'powerful\' => 0, \'resolution_mode\' => self::RESOLUTION_ATTACK_ROLL, \'state\' => self::STATE_DRAFT, \'read_level\' => 0, \'write_level\' => 3, \'auto_update\' => true, \'auto_success_if_willing_target\' => false, \'allows_reaction\' => false]',
          'attributes' => 
          array (
            'startLine' => 179,
            'endLine' => 200,
            'startTokenPos' => 293,
            'startFilePos' => 9210,
            'endTokenPos' => 439,
            'endFilePos' => 9857,
          ),
        ),
        'docComment' => '/**
 * Colonnes NOT NULL : les FormRequest acceptent souvent `nullable` alors que MySQL refuse NULL.
 * Valeurs alignées sur les défauts du schéma (migrations `entity_spells` et colonnes ajoutées ensuite).
 *
 * @var array<string, bool|int|string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 179,
        'endLine' => 200,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'official_id\', \'dofusdb_id\', \'name\', \'description\', \'effect\', \'level\', \'po_min\', \'po_max\', \'po_editable\', \'pa\', \'casting_time\', \'ritual_available\', \'cast_per_turn\', \'cast_per_target\', \'sight_line\', \'number_between_two_cast\', \'duration\', \'element\', \'category\', \'is_magic\', \'powerful\', \'resolution_mode\', \'attack_characteristic_key\', \'save_characteristic_key\', \'save_dc_formula\', \'save_success_note\', \'auto_success_if_willing_target\', \'allows_reaction\', \'state\', \'read_level\', \'write_level\', \'image\', \'auto_update\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 207,
            'endLine' => 242,
            'startTokenPos' => 450,
            'startFilePos' => 9983,
            'endTokenPos' => 554,
            'endFilePos' => 10789,
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
        'startLine' => 207,
        'endLine' => 242,
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
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'element\' => \'integer\', \'category\' => \'integer\', \'powerful\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\', \'po_editable\' => \'boolean\', \'sight_line\' => \'boolean\', \'is_magic\' => \'boolean\', \'auto_update\' => \'boolean\', \'auto_success_if_willing_target\' => \'boolean\', \'allows_reaction\' => \'boolean\', \'ritual_available\' => \'boolean\']',
          'attributes' => 
          array (
            'startLine' => 249,
            'endLine' => 262,
            'startTokenPos' => 565,
            'startFilePos' => 10916,
            'endTokenPos' => 651,
            'endFilePos' => 11367,
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
        'startLine' => 249,
        'endLine' => 262,
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
      'setAttribute' => 
      array (
        'name' => 'setAttribute',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 34,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 268,
            'endLine' => 268,
            'startColumn' => 40,
            'endColumn' => 45,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 268,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'setDescriptionAttribute' => 
      array (
        'name' => 'setDescriptionAttribute',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'mixed',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 280,
            'endLine' => 280,
            'startColumn' => 45,
            'endColumn' => 56,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Stockage texte : évite les types non string et les avertissements PHP 8.4+ sur (string) null.
 */',
        'startLine' => 280,
        'endLine' => 283,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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
 * Get the user that created the spell.
 */',
        'startLine' => 288,
        'endLine' => 291,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'breeds' => 
      array (
        'name' => 'breeds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les breeds (affichées « Classes ») associées à ce sort.
 */',
        'startLine' => 296,
        'endLine' => 301,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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
        'docComment' => '/**
 * Les créatures associées à ce sort.
 */',
        'startLine' => 306,
        'endLine' => 309,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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
 * Les scénarios associés à ce sort.
 */',
        'startLine' => 314,
        'endLine' => 317,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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
 * Les campagnes associées à ce sort.
 */',
        'startLine' => 322,
        'endLine' => 325,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'spellTypes' => 
      array (
        'name' => 'spellTypes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les types de ce sort.
 */',
        'startLine' => 330,
        'endLine' => 333,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'spellEffects' => 
      array (
        'name' => 'spellEffects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les effets de ce sort (instances liées aux types d\'effet).
 *
 * @return HasMany<SpellEffect, $this>
 */',
        'startLine' => 340,
        'endLine' => 343,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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
 * Les monstres invoqués par ce sort.
 */',
        'startLine' => 348,
        'endLine' => 351,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'spellStates' => 
      array (
        'name' => 'spellStates',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les états que ce sort peut appliquer (sur cible ou lanceur).
 */',
        'startLine' => 356,
        'endLine' => 361,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'effects' => 
      array (
        'name' => 'effects',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Définitions d’effets liées à ce sort (pivot effect_spell).
 */',
        'startLine' => 366,
        'endLine' => 369,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'getPoDisplayAttribute' => 
      array (
        'name' => 'getPoDisplayAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Portée affichable à partir de po_min / po_max : une seule valeur si une borne est vide ou si min = max ;
 * sinon « min - max » (espaces). Chaîne vide si les deux sont vides. 1 seul (ou 1 et 1) = CàC côté UI.
 */',
        'startLine' => 375,
        'endLine' => 398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
        'aliasName' => NULL,
      ),
      'getAreaAttribute' => 
      array (
        'name' => 'getAreaAttribute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
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
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Zone d’impact affichée (premier degré du premier effet lié).
 *
 * @return string|null Notation zone (point, line-1x9, …) ou null
 */',
        'startLine' => 405,
        'endLine' => 411,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Spell',
        'implementingClassName' => 'App\\Models\\Entity\\Spell',
        'currentClassName' => 'App\\Models\\Entity\\Spell',
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