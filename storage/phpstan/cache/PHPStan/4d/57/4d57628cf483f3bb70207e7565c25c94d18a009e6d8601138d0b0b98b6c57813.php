<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Spell.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Spell
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b0c257bb2ac72c4c261ac7e01367ce693665be197ba4a81dcb32bbdf5ce9fe84-8.4.17-6.70.0.0',
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
 * @property-read Collection<int, Condition> $conditions
 * @property-read int|null $conditions_count
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
 * @property-read BreedSpellPivot|null $pivot
 *
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 138,
    'endLine' => 413,
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
            'startLine' => 143,
            'endLine' => 143,
            'startTokenPos' => 124,
            'startFilePos' => 8025,
            'endTokenPos' => 124,
            'endFilePos' => 8029,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 143,
        'endLine' => 143,
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
            'startLine' => 145,
            'endLine' => 145,
            'startTokenPos' => 135,
            'startFilePos' => 8064,
            'endTokenPos' => 135,
            'endFilePos' => 8070,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 145,
        'endLine' => 145,
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
            'startLine' => 147,
            'endLine' => 147,
            'startTokenPos' => 146,
            'startFilePos' => 8108,
            'endTokenPos' => 146,
            'endFilePos' => 8117,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 147,
        'endLine' => 147,
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
            'startLine' => 149,
            'endLine' => 149,
            'startTokenPos' => 157,
            'startFilePos' => 8155,
            'endTokenPos' => 157,
            'endFilePos' => 8164,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 149,
        'endLine' => 149,
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
            'startLine' => 151,
            'endLine' => 151,
            'startTokenPos' => 168,
            'startFilePos' => 8202,
            'endTokenPos' => 168,
            'endFilePos' => 8202,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 151,
        'endLine' => 151,
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
            'startLine' => 153,
            'endLine' => 153,
            'startTokenPos' => 179,
            'startFilePos' => 8243,
            'endTokenPos' => 179,
            'endFilePos' => 8243,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 153,
        'endLine' => 153,
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
            'startLine' => 155,
            'endLine' => 155,
            'startTokenPos' => 190,
            'startFilePos' => 8285,
            'endTokenPos' => 190,
            'endFilePos' => 8285,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 155,
        'endLine' => 155,
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
            'startLine' => 157,
            'endLine' => 157,
            'startTokenPos' => 201,
            'startFilePos' => 8328,
            'endTokenPos' => 201,
            'endFilePos' => 8328,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 157,
        'endLine' => 157,
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
            'startLine' => 159,
            'endLine' => 159,
            'startTokenPos' => 212,
            'startFilePos' => 8374,
            'endTokenPos' => 212,
            'endFilePos' => 8386,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 159,
        'endLine' => 159,
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
            'startLine' => 161,
            'endLine' => 161,
            'startTokenPos' => 223,
            'startFilePos' => 8433,
            'endTokenPos' => 223,
            'endFilePos' => 8446,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 161,
        'endLine' => 161,
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
            'startLine' => 163,
            'endLine' => 163,
            'startTokenPos' => 234,
            'startFilePos' => 8493,
            'endTokenPos' => 234,
            'endFilePos' => 8506,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 163,
        'endLine' => 163,
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
            'startLine' => 166,
            'endLine' => 166,
            'startTokenPos' => 247,
            'startFilePos' => 8601,
            'endTokenPos' => 249,
            'endFilePos' => 8627,
          ),
        ),
        'docComment' => '/** @deprecated Utiliser AreaConstants::SHAPE_ID_MAP */',
        'attributes' => 
        array (
        ),
        'startLine' => 166,
        'endLine' => 166,
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
            'startLine' => 169,
            'endLine' => 169,
            'startTokenPos' => 262,
            'startFilePos' => 8715,
            'endTokenPos' => 262,
            'endFilePos' => 8736,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 169,
        'endLine' => 169,
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
            'startLine' => 172,
            'endLine' => 172,
            'startTokenPos' => 275,
            'startFilePos' => 8877,
            'endTokenPos' => 275,
            'endFilePos' => 8895,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 172,
        'endLine' => 172,
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
            'startLine' => 180,
            'endLine' => 201,
            'startTokenPos' => 288,
            'startFilePos' => 9228,
            'endTokenPos' => 434,
            'endFilePos' => 9875,
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
        'startLine' => 180,
        'endLine' => 201,
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
            'startLine' => 208,
            'endLine' => 243,
            'startTokenPos' => 445,
            'startFilePos' => 10001,
            'endTokenPos' => 549,
            'endFilePos' => 10807,
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
        'startLine' => 208,
        'endLine' => 243,
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
            'startLine' => 250,
            'endLine' => 263,
            'startTokenPos' => 560,
            'startFilePos' => 10934,
            'endTokenPos' => 646,
            'endFilePos' => 11385,
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
        'startLine' => 250,
        'endLine' => 263,
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
            'startLine' => 269,
            'endLine' => 269,
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
            'startLine' => 269,
            'endLine' => 269,
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
        'startLine' => 269,
        'endLine' => 276,
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
            'startLine' => 281,
            'endLine' => 281,
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
        'startLine' => 281,
        'endLine' => 284,
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
        'startLine' => 289,
        'endLine' => 292,
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
        'startLine' => 297,
        'endLine' => 302,
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
        'startLine' => 307,
        'endLine' => 310,
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
        'startLine' => 315,
        'endLine' => 318,
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
        'startLine' => 323,
        'endLine' => 326,
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
        'startLine' => 331,
        'endLine' => 334,
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
        'startLine' => 341,
        'endLine' => 344,
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
        'startLine' => 349,
        'endLine' => 352,
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
      'conditions' => 
      array (
        'name' => 'conditions',
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
        'startLine' => 357,
        'endLine' => 362,
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
        'startLine' => 367,
        'endLine' => 370,
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
        'startLine' => 376,
        'endLine' => 399,
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
        'startLine' => 406,
        'endLine' => 412,
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