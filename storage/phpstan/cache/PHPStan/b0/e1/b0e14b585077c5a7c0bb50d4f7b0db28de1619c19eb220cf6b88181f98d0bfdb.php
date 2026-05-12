<?php declare(strict_types = 1);

// osfsl-/var/www/KrosmozJdr/app/Models/Entity/Creature.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Entity\Creature
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-91182146397ab1d7fb4fbe80856369d42d8a179f8062b239a010c4db4edbb753-8.4.17-6.70.0.0',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Entity\\Creature',
        'filename' => '/var/www/KrosmozJdr/app/Models/Entity/Creature.php',
      ),
    ),
    'namespace' => 'App\\Models\\Entity',
    'name' => 'App\\Models\\Entity\\Creature',
    'shortName' => 'Creature',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $hostility
 * @property string|null $location
 * @property string $level
 * @property string|null $other_info
 * @property string $life
 * @property string $pa
 * @property string $pm
 * @property string $po
 * @property string $ini
 * @property string $invocation
 * @property string $touch
 * @property string $ca
 * @property string $dodge_pa
 * @property string $dodge_pm
 * @property string $fuite
 * @property string $tacle
 * @property string $vitality
 * @property string $sagesse
 * @property string $strong
 * @property string $intel
 * @property string $agi
 * @property string $chance
 * @property string $do_fixe_neutre
 * @property string $do_fixe_terre
 * @property string $do_fixe_feu
 * @property string $do_fixe_air
 * @property string $do_fixe_eau
 * @property string $do_sagesse
 * @property string $do_vitalite
 * @property string $res_fixe_neutre
 * @property string $res_fixe_terre
 * @property string $res_fixe_feu
 * @property string $res_fixe_air
 * @property string $res_fixe_eau
 * @property string $res_neutre
 * @property string $res_terre
 * @property string $res_feu
 * @property string $res_air
 * @property string $res_eau
 * @property string $res_sagesse
 * @property string $res_vitalite
 * @property string $acrobatie_bonus
 * @property string $discretion_bonus
 * @property string $escamotage_bonus
 * @property string $athletisme_bonus
 * @property string $intimidation_bonus
 * @property string $arcane_bonus
 * @property string $histoire_bonus
 * @property string $investigation_bonus
 * @property string $nature_bonus
 * @property string $religion_bonus
 * @property string $dressage_bonus
 * @property string $medecine_bonus
 * @property string $perception_bonus
 * @property string $perspicacite_bonus
 * @property string $survie_bonus
 * @property string $persuasion_bonus
 * @property string $representation_bonus
 * @property string $supercherie_bonus
 * @property int $acrobatie_mastery
 * @property int $discretion_mastery
 * @property int $escamotage_mastery
 * @property int $athletisme_mastery
 * @property int $intimidation_mastery
 * @property int $arcane_mastery
 * @property int $histoire_mastery
 * @property int $investigation_mastery
 * @property int $nature_mastery
 * @property int $religion_mastery
 * @property int $dressage_mastery
 * @property int $medecine_mastery
 * @property int $perception_mastery
 * @property int $perspicacite_mastery
 * @property int $survie_mastery
 * @property int $persuasion_mastery
 * @property int $representation_mastery
 * @property int $supercherie_mastery
 * @property int $save_vitality_bonus
 * @property int $save_wisdom_bonus
 * @property int $save_strength_bonus
 * @property int $save_intelligence_bonus
 * @property int $save_chance_bonus
 * @property int $save_agility_bonus
 * @property int $save_vitality_mastery
 * @property int $save_wisdom_mastery
 * @property int $save_strength_mastery
 * @property int $save_intelligence_mastery
 * @property int $save_chance_mastery
 * @property int $save_agility_mastery
 * @property string|null $kamas
 * @property string|null $drop_
 * @property string|null $other_item
 * @property string|null $other_consumable
 * @property string|null $other_resource
 * @property string|null $other_spell
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $created_by
 * @property-read Collection<int, Condition> $conditions
 * @property-read int|null $conditions_count
 * @property-read Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Monster|null $monster
 * @property-read Npc|null $npc
 * @property-read Collection<int, resource> $resources
 * @property-read int|null $resources_count
 * @property-read Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \\Database\\Factories\\Entity\\CreatureFactory factory($count = null, $state = [])
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature newModelQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature newQuery()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature onlyTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature query()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereAcrobatieBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereAcrobatieMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereAgi($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereArcaneBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereArcaneMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereAthletismeBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereAthletismeMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereCa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereChance($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereCreatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereCreatedBy($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDeletedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDescription($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDiscretionBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDiscretionMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoFixeAir($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoFixeEau($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoFixeFeu($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoFixeNeutre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoFixeTerre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDodgePa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDodgePm($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDressageBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDressageMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDrop($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereEscamotageBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereEscamotageMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereFuite($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereHistoireBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereHistoireMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereHostility($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereId($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereImage($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereIni($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereIntel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereIntimidationBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereIntimidationMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereInvestigationBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereInvestigationMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereInvocation($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereReadLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereKamas($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereLife($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereLocation($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereMedecineBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereMedecineMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereName($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereNatureBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereNatureMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereOtherConsumable($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereOtherInfo($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereOtherItem($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereOtherResource($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereOtherSpell($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePa($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePerceptionBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePerceptionMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePerspicaciteBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePerspicaciteMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePersuasionBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePersuasionMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePm($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature wherePo($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereReligionBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereReligionMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereRepresentationBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereRepresentationMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResAir($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResEau($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFeu($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFixeAir($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFixeEau($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFixeFeu($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFixeNeutre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResFixeTerre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResNeutre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResTerre($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSagesse($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereStrong($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSupercherieBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSupercherieMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSurvieBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSurvieMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereTacle($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereTouch($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereUpdatedAt($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereState($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereWriteLevel($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereVitality($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature withTrashed()
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature withoutTrashed()
 * @property string $critical_hit
 * @property string $heal_bonus
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereCriticalHit($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoSagesse($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereDoVitalite($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereHealBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResSagesse($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereResVitalite($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveAgilityBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveAgilityMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveChanceBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveChanceMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveIntelligenceBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveIntelligenceMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveStrengthBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveStrengthMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveVitalityBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveVitalityMastery($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveWisdomBonus($value)
 * @method static \\Illuminate\\Database\\Eloquent\\Builder<static>|Creature whereSaveWisdomMastery($value)
 * @mixin \\Eloquent
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 260,
    'endLine' => 479,
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
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'STATE_RAW',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'raw\'',
          'attributes' => 
          array (
            'startLine' => 265,
            'endLine' => 265,
            'startTokenPos' => 99,
            'startFilePos' => 16435,
            'endTokenPos' => 99,
            'endFilePos' => 16439,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 265,
        'endLine' => 265,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'STATE_DRAFT' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'STATE_DRAFT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'draft\'',
          'attributes' => 
          array (
            'startLine' => 267,
            'endLine' => 267,
            'startTokenPos' => 110,
            'startFilePos' => 16474,
            'endTokenPos' => 110,
            'endFilePos' => 16480,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 267,
        'endLine' => 267,
        'startColumn' => 5,
        'endColumn' => 39,
      ),
      'STATE_PLAYABLE' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'STATE_PLAYABLE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'playable\'',
          'attributes' => 
          array (
            'startLine' => 269,
            'endLine' => 269,
            'startTokenPos' => 121,
            'startFilePos' => 16518,
            'endTokenPos' => 121,
            'endFilePos' => 16527,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 269,
        'endLine' => 269,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'STATE_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'STATE_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 271,
            'endLine' => 271,
            'startTokenPos' => 132,
            'startFilePos' => 16565,
            'endTokenPos' => 132,
            'endFilePos' => 16574,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 271,
        'endLine' => 271,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'MEDIA_PATH' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'MEDIA_PATH',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'images/entity/creatures\'',
          'attributes' => 
          array (
            'startLine' => 274,
            'endLine' => 274,
            'startTokenPos' => 145,
            'startFilePos' => 16662,
            'endTokenPos' => 145,
            'endFilePos' => 16686,
          ),
        ),
        'docComment' => '/** Répertoire Media Library pour ce modèle. */',
        'attributes' => 
        array (
        ),
        'startLine' => 274,
        'endLine' => 274,
        'startColumn' => 5,
        'endColumn' => 56,
      ),
      'MEDIA_FILE_PATTERN_IMAGES' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'MEDIA_FILE_PATTERN_IMAGES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image-[id]-[name]\'',
          'attributes' => 
          array (
            'startLine' => 277,
            'endLine' => 277,
            'startTokenPos' => 158,
            'startFilePos' => 16827,
            'endTokenPos' => 158,
            'endFilePos' => 16845,
          ),
        ),
        'docComment' => '/** Motif de nommage pour la collection images (placeholders: [name], [date], [id]). */',
        'attributes' => 
        array (
        ),
        'startLine' => 277,
        'endLine' => 277,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'name\', \'description\', \'hostility\', \'location\', \'level\', \'other_info\', \'life\', \'pa\', \'pm\', \'po\', \'ini\', \'invocation\', \'touch\', \'ca\', \'dodge_pa\', \'dodge_pm\', \'fuite\', \'tacle\', \'critical_hit\', \'heal_bonus\', \'vitality\', \'sagesse\', \'strong\', \'intel\', \'agi\', \'chance\', \'do_fixe_neutre\', \'do_fixe_terre\', \'do_fixe_feu\', \'do_fixe_air\', \'do_fixe_eau\', \'do_sagesse\', \'do_vitalite\', \'res_fixe_neutre\', \'res_fixe_terre\', \'res_fixe_feu\', \'res_fixe_air\', \'res_fixe_eau\', \'res_neutre\', \'res_terre\', \'res_feu\', \'res_air\', \'res_eau\', \'res_sagesse\', \'res_vitalite\', \'acrobatie_bonus\', \'discretion_bonus\', \'escamotage_bonus\', \'athletisme_bonus\', \'intimidation_bonus\', \'arcane_bonus\', \'histoire_bonus\', \'investigation_bonus\', \'nature_bonus\', \'religion_bonus\', \'dressage_bonus\', \'medecine_bonus\', \'perception_bonus\', \'perspicacite_bonus\', \'survie_bonus\', \'persuasion_bonus\', \'representation_bonus\', \'supercherie_bonus\', \'acrobatie_mastery\', \'discretion_mastery\', \'escamotage_mastery\', \'athletisme_mastery\', \'intimidation_mastery\', \'arcane_mastery\', \'histoire_mastery\', \'investigation_mastery\', \'nature_mastery\', \'religion_mastery\', \'dressage_mastery\', \'medecine_mastery\', \'perception_mastery\', \'perspicacite_mastery\', \'survie_mastery\', \'persuasion_mastery\', \'representation_mastery\', \'supercherie_mastery\', \'save_vitality_bonus\', \'save_wisdom_bonus\', \'save_strength_bonus\', \'save_intelligence_bonus\', \'save_chance_bonus\', \'save_agility_bonus\', \'save_vitality_mastery\', \'save_wisdom_mastery\', \'save_strength_mastery\', \'save_intelligence_mastery\', \'save_chance_mastery\', \'save_agility_mastery\', \'kamas\', \'drop_\', \'other_item\', \'other_consumable\', \'other_resource\', \'other_spell\', \'state\', \'read_level\', \'write_level\', \'image\', \'created_by\']',
          'attributes' => 
          array (
            'startLine' => 284,
            'endLine' => 389,
            'startTokenPos' => 169,
            'startFilePos' => 16971,
            'endTokenPos' => 483,
            'endFilePos' => 19528,
          ),
        ),
        'docComment' => '/**
 * The conditions that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 284,
        'endLine' => 389,
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
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'hostility\' => \'integer\', \'read_level\' => \'integer\', \'write_level\' => \'integer\']',
          'attributes' => 
          array (
            'startLine' => 396,
            'endLine' => 400,
            'startTokenPos' => 494,
            'startFilePos' => 19655,
            'endTokenPos' => 517,
            'endFilePos' => 19766,
          ),
        ),
        'docComment' => '/**
 * The conditions that should be cast.
 *
 * @var array<string, string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 396,
        'endLine' => 400,
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
 * Get the user that created the creature.
 */',
        'startLine' => 405,
        'endLine' => 408,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
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
 * Les attributs de la créature.
 */',
        'startLine' => 413,
        'endLine' => 416,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'creatureTraits' => 
      array (
        'name' => 'creatureTraits',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 418,
        'endLine' => 422,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'capabilities' => 
      array (
        'name' => 'capabilities',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les capacités de la créature.
 */',
        'startLine' => 427,
        'endLine' => 430,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'items' => 
      array (
        'name' => 'items',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les objets de la créature.
 */',
        'startLine' => 435,
        'endLine' => 438,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'resources' => 
      array (
        'name' => 'resources',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les ressources de la créature.
 */',
        'startLine' => 443,
        'endLine' => 446,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
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
 * Les sorts de la créature.
 */',
        'startLine' => 451,
        'endLine' => 454,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'consumables' => 
      array (
        'name' => 'consumables',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Les consommables de la créature.
 */',
        'startLine' => 459,
        'endLine' => 462,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'npc' => 
      array (
        'name' => 'npc',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Le PNJ associé à la créature.
 */',
        'startLine' => 467,
        'endLine' => 470,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
        'aliasName' => NULL,
      ),
      'monster' => 
      array (
        'name' => 'monster',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Le monstre associé à la créature.
 */',
        'startLine' => 475,
        'endLine' => 478,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models\\Entity',
        'declaringClassName' => 'App\\Models\\Entity\\Creature',
        'implementingClassName' => 'App\\Models\\Entity\\Creature',
        'currentClassName' => 'App\\Models\\Entity\\Creature',
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