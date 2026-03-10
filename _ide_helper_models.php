<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Caractéristique générale : propriétés communes et id unique.
 * 
 * Une ligne = une caractéristique (ex. PA créature, PA sort, PA objet = 3 lignes).
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string|null $short_name
 * @property string|null $helper
 * @property string|null $descriptions
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $unit
 * @property string $type
 * @property int $sort_order
 * @property string|null $group
 * @property int|null $linked_to_characteristic_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CharacteristicCreature> $creatureRows
 * @property-read int|null $creature_rows_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Characteristic> $linkedCharacteristics
 * @property-read int|null $linked_characteristics_count
 * @property-read Characteristic|null $masterCharacteristic
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CharacteristicObject> $objectRows
 * @property-read int|null $object_rows_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CharacteristicSpell> $spellRows
 * @property-read int|null $spell_rows_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereDescriptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereHelper($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereLinkedToCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUpdatedAt($value)
 */
	class Characteristic extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * Définition d’une caractéristique pour une entité du groupe créature (monster, class, npc).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function Identifiant d'une fonction de conversion enregistrée
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property array|null $labels
 * @property array|null $validation
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Characteristic $characteristic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereLabels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereValidation($value)
 */
	class CharacteristicCreature extends \Eloquent {}
}

namespace App\Models{
/**
 * Définition d’une caractéristique pour une entité du groupe objet (item, consumable, resource, panoply).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics (ex. item.effects[].characteristic)
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function Identifiant d'une fonction de conversion enregistrée
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property bool $forgemagie_allowed
 * @property int $forgemagie_max
 * @property float|null $base_price_per_unit
 * @property float|null $rune_price_per_unit
 * @property array|null $value_available
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemType> $allowedItemTypes
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $allowed_item_types_count
 * @property-read \App\Models\Characteristic $characteristic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereBasePricePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereForgemagieAllowed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereForgemagieMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereRunePricePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereValueAvailable($value)
 */
	class CharacteristicObject extends \Eloquent {}
}

namespace App\Models{
/**
 * Définition d’une caractéristique pour l’entité spell (groupe sort).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property int|null $dofusdb_characteristic_id Id DofusDB GET /characteristics
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
 * @property string|null $conversion_function Identifiant d'une fonction de conversion enregistrée
 * @property array|null $conversion_dofus_sample Niveau → valeur Dofus (ex. {"1":1,"200":200})
 * @property array|null $conversion_krosmoz_sample Niveau → valeur Krosmoz (ex. {"1":1,"20":20})
 * @property array|null $value_available
 * @property array<array-key, mixed>|null $conversion_sample_rows Lignes [{dofus_level, dofus_value, krosmoz_level, krosmoz_value}, ...]
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Characteristic $characteristic
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionDofusSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionFunction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDofusdbCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereFormulaDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereValueAvailable($value)
 */
	class CharacteristicSpell extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property \Illuminate\Support\Carbon $requested_at
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property array<array-key, mixed>|null $meta
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereRequestedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DataSubjectRequest whereUserId($value)
 */
	class DataSubjectRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * Mapping effectId DofusDB → sous-effet KrosmozJDR (sub_effect_slug + characteristic_source).
 *
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_IMPLEMENTATION_MAPPING_EFFETS.md
 * @property int $id
 * @property int $dofusdb_effect_id
 * @property string $sub_effect_slug
 * @property string $characteristic_source
 * @property string|null $characteristic_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCharacteristicSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereDofusdbEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereSubEffectSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DofusdbEffectMapping whereUpdatedAt($value)
 */
	class DofusdbEffectMapping extends \Eloquent {}
}

namespace App\Models{
/**
 * Effet (conteneur de sous-effets). Niveau sur effect_usage.
 * 
 * target_type : application directe sur la cible (direct), ou pose d’un piège (trap) / glyphe (glyph).
 * area : notation de la zone d’impact sur le damier (point, line-1x9, cross-2, circle-2, rect-3x4).
 *
 * @see docs/50-Fonctionnalités/Spell-Effects/ZONE_NOTATION.md
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property string|null $description
 * @property int|null $effect_group_id
 * @property int|null $degree
 * @property string $target_type
 * @property string|null $area
 * @property string|null $config_signature
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EffectGroup|null $effectGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectSubEffect> $effectSubEffects
 * @property-read int|null $effect_sub_effects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SubEffect> $subEffects
 * @property-read int|null $sub_effects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereConfigSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereEffectGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Effect whereUpdatedAt($value)
 */
	class Effect extends \Eloquent {}
}

namespace App\Models{
/**
 * Groupe d'effets (degres de puissance).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Effect> $effects
 * @property-read int|null $effects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectGroup whereUpdatedAt($value)
 */
	class EffectGroup extends \Eloquent {}
}

namespace App\Models{
/**
 * Pivot effect / sub_effect (ordre, scope, params).
 *
 * @property int $id
 * @property int $effect_id
 * @property int $sub_effect_id
 * @property int $order
 * @property string $scope
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property array<array-key, mixed>|null $params
 * @property bool $crit_only
 * @property string|null $duration_formula
 * @property string|null $logic_group
 * @property string|null $logic_operator
 * @property string|null $logic_condition
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Effect $effect
 * @property-read \App\Models\SubEffect $subEffect
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereCritOnly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDiceSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereDurationFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereLogicOperator($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereSubEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereValueMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectSubEffect whereValueMin($value)
 */
	class EffectSubEffect extends \Eloquent {}
}

namespace App\Models{
/**
 * Lien polymorphique entité (spell, item, consumable…) → effect.
 * 
 * level_min / level_max = tranche de niveau pour cet effet.
 *
 * @property int $id
 * @property string $entity_type
 * @property int $entity_id
 * @property int $effect_id
 * @property int|null $level_min
 * @property int|null $level_max
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Model $entity
 * @property-read Effect $effect
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereLevelMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereLevelMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EffectUsage whereUpdatedAt($value)
 */
	class EffectUsage extends \Eloquent {}
}

namespace App\Models{
/**
 * Modèle placeholder pour les uploads d'images d'entités sans entité cible (ex. bulk).
 * 
 * Un média est attaché à cette instance ; l'URL retournée peut être affectée au champ
 * image de plusieurs entités (string). Nettoyage des anciennes lignes à prévoir (job).
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EntityImageUpload whereUpdatedAt($value)
 */
	class EntityImageUpload extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @method static \Database\Factories\Entity\AttributeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attribute withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Attribute extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * Entité Breed (affichée « Classe » côté utilisateur).
 *
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description_fast
 * @property string|null $description
 * @property string|null $life
 * @property string|null $life_dice
 * @property string|null $specificity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property string|null $icon
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Entity\BreedFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed query()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDescriptionFast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereLifeDice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereSpecificity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Breed withoutTrashed()
 */
	class Breed extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property int $is_public
 * @property int $progress_state
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\Entity\CampaignFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereProgressState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaign withoutTrashed()
 * @mixin \Eloquent
 */
	class Campaign extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string $level
 * @property string $pa
 * @property string $po
 * @property bool $po_editable
 * @property string $time_before_use_again
 * @property string $casting_time
 * @property string $duration
 * @property string $element
 * @property bool $is_magic
 * @property bool $ritual_available
 * @property string|null $powerful
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Specialization> $specializations
 * @property-read int|null $specializations_count
 * @method static \Database\Factories\Entity\CapabilityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCastingTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereRitualAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereTimeBeforeUseAgain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Capability withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Capability extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $level
 * @property string|null $recipe
 * @property string|null $price
 * @property int $rarity
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string $dofus_version
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $consumable_type_id
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read ConsumableType|null $consumableType
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ConsumableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereConsumableTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumable withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Consumable extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attribute> $attributes
 * @property-read int|null $attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Monster|null $monster
 * @property-read Npc|null $npc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Entity\CreatureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAcrobatieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAcrobatieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAgi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArcaneBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereArcaneMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAthletismeBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereAthletismeMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereChance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDiscretionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDiscretionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDoFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDodgePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDressageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDressageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereDrop($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereEscamotageBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereEscamotageMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereFuite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHistoireBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHistoireMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereHostility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereIntimidationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvestigationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvestigationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereInvocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereKamas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLife($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereMedecineBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereMedecineMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereNatureBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereNatureMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherConsumable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherItem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherResource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereOtherSpell($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerceptionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerceptionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerspicaciteBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePerspicaciteMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePersuasionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePersuasionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePm($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature wherePo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReligionBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereReligionMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereRepresentationBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereRepresentationMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeEau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeFeu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResFixeTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResNeutre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereResTerre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSagesse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereStrong($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSupercherieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSupercherieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSurvieBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereSurvieMastery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTacle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereTouch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature whereVitality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Creature withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Creature extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string|null $level
 * @property string|null $description
 * @property string|null $effect
 * @property string|null $bonus
 * @property string|null $recipe
 * @property string|null $price
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $item_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read ItemType|null $itemType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereRecipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Item withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Item extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $dofus_version
 * @property bool $auto_update
 * @property int $size
 * @property int|null $monster_race_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Creature|null $creature
 * @property-read MonsterRace|null $monsterRace
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spellInvocations
 * @property-read int|null $spell_invocations_count
 * @method static \Database\Factories\Entity\MonsterFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereMonsterRaceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereUpdatedAt($value)
 * @property int $is_boss
 * @property string $boss_pa
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereBossPa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereIsBoss($value)
 * @mixin \Eloquent
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Monster whereWriteLevel($value)
 */
	class Monster extends \Eloquent {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property int|null $creature_id
 * @property string|null $story
 * @property string|null $historical
 * @property string|null $age
 * @property string|null $size
 * @property int|null $breed_id
 * @property int|null $specialization_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read Breed|null $breed
 * @property-read Creature|null $creature
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read Shop|null $shop
 * @property-read Specialization|null $specialization
 * @method static \Database\Factories\Entity\NpcFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereBreedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereCreatureId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereHistorical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereSpecializationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereStory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Npc whereWriteLevel($value)
 */
	class Npc extends \Eloquent {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $bonus
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\PanoplyFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereBonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $dofusdb_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Panoply whereDofusdbId($value)
 */
	class Panoply extends \Eloquent {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string|null $dofusdb_id
 * @property int|null $official_id
 * @property string $name
 * @property string|null $description
 * @property string|null $effect
 * @property string $level
 * @property string|null $price
 * @property string|null $weight
 * @property int $rarity
 * @property string $dofus_version
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $resource_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read ResourceType|null $resourceType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @method static \Database\Factories\Entity\ResourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDofusVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereRarity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereResourceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource whereWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Resource withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entity\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $recipeIngredients
 * @property-read int|null $recipe_ingredients_count
 */
	class Resource extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $slug
 * @property string|null $keyword
 * @property bool $is_public
 * @property int $progress_state
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ScenarioLink> $scenarioLinks
 * @property-read int|null $scenario_links_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $shops
 * @property-read int|null $shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\Entity\ScenarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereProgressState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scenario withoutTrashed()
 * @mixin \Eloquent
 */
	class Scenario extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $location
 * @property int $price
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $npc_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @property-read Npc|null $npc
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $panoplies
 * @property-read int|null $panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @method static \Database\Factories\Entity\ShopFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereNpcId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shop withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Shop extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Capability> $capabilities
 * @property-read int|null $capabilities_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Npc> $npcs
 * @property-read int|null $npcs_count
 * @method static \Database\Factories\Entity\SpecializationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Specialization withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 */
	class Specialization extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description
 * @property string|null $effect
 * @property string|null $area Zone d'impact (déléguée au premier effet lié ; voir Effect::area)
 * @property string $level
 * @property string|null $po_min Portée min (valeur ou formule, ex. "0", "[level]")
 * @property string|null $po_max Portée max (valeur ou formule, ex. "1", "6")
 * @property bool $po_editable
 * @property string $pa
 * @property string $cast_per_turn
 * @property string $cast_per_target
 * @property bool $sight_line
 * @property string $number_between_two_cast
 * @property bool $number_between_two_cast_editable
 * @property int $element
 * @property int $category
 * @property bool $is_magic
 * @property int $powerful
 * @property string $resolution_mode
 * @property string|null $attack_characteristic_key
 * @property string|null $save_characteristic_key
 * @property string|null $save_dc_formula
 * @property string|null $save_success_note
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property string|null $image
 * @property bool $auto_update
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Creature> $creatures
 * @property-read int|null $creatures_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SpellType> $spellTypes
 * @property-read int|null $spell_types_count
 * @method static \Database\Factories\Entity\SpellFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAutoUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCastPerTurn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereEffect($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereIsMagic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereNumberBetweenTwoCastEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereOfficialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePoEditable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePowerful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSightLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entity\Breed> $breeds
 * @property-read int|null $breeds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EffectUsage> $effectUsages
 * @property-read int|null $effect_usages_count
 * @property-read string $po_display
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpellEffect> $spellEffects
 * @property-read int|null $spell_effects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpellState> $spellStates
 * @property-read int|null $spell_states_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereAttackCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereResolutionMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveCharacteristicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveDcFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell whereSaveSuccessNote($value)
 */
	class Spell extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * Entrée en attente pour envoi en digest (quotidien, hebdo, mensuel).
 * 
 * Le payload est stocké en JSON ; NotificationService::pushToDigestQueue le normalise
 * (Carbon, Enum, etc.) avant enregistrement.
 *
 * @property int $id
 * @property int $user_id
 * @property string $notification_type
 * @property string $frequency
 * @property array $payload
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereNotificationType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationDigestQueue whereUserId($value)
 */
	class NotificationDigestQueue extends \Eloquent {}
}

namespace App\Models{
/**
 * Modèle Eloquent Page
 * 
 * Représente une page dynamique du site (menu, arborescence, sections, droits, etc.).
 * Gère la hiérarchie, la visibilité, l'état, les utilisateurs associés, les campagnes et scénarios liés.
 * Utilisé pour la construction dynamique du contenu et la gestion des droits d'accès.
 * 
 * Relations : sections, parent, children, users, campaigns, scenarios, createdBy
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property bool $in_menu
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property int|null $parent_id
 * @property int $menu_order
 * @property string|null $menu_group
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Page> $children
 * @property-read int|null $children_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read Page|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereInMenu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMenuOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereMenuGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @mixin \Eloquent
 * @property string|null $entity_key
 * @property string|null $icon
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page forMenu(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page inMenu()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page playable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page readableFor(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereEntityKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereWriteLevel($value)
 */
	class Page extends \Eloquent {}
}

namespace App\Models{
/**
 * Journal d'audit des actions RGPD (export, suppression).
 *
 * @property int $id
 * @property int|null $actor_id Utilisateur ayant effectué l'action
 * @property int|null $subject_user_id Utilisateur concerné par l'action
 * @property string $action Type d'action
 * @property array|null $context Contexte additionnel
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $actor
 * @property-read \App\Models\User|null $subjectUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereSubjectUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyAuditLog whereUserAgent($value)
 */
	class PrivacyAuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $data_subject_request_id
 * @property string $status
 * @property string $path
 * @property string|null $checksum
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $downloaded_at
 * @property array<array-key, mixed>|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataSubjectRequest|null $dataSubjectRequest
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereChecksum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereDataSubjectRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereDownloadedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyExport whereUserId($value)
 */
	class PrivacyExport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $kind
 * @property string $status
 * @property string|null $run_id
 * @property int|null $requested_by
 * @property array<array-key, mixed> $payload
 * @property array<array-key, mixed>|null $summary
 * @property array<array-key, mixed>|null $results
 * @property int $progress_done
 * @property int $progress_total
 * @property string|null $error
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereProgressDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereProgressTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereUpdatedAt($value)
 */
	class ScrappingJob extends \Eloquent {}
}

namespace App\Models\Scrapping{
/**
 * Modèle de stockage des items DofusDB "en attente" pour un typeId non encore autorisé.
 *
 * @example PendingResourceTypeItem::create([
 *   'dofusdb_type_id' => 99,
 *   'dofusdb_item_id' => 12345,
 *   'context' => 'recipe',
 * ]);
 * @property int $id
 * @property int $dofusdb_type_id
 * @property int $dofusdb_item_id
 * @property string $context
 * @property string|null $source_entity_type
 * @property int|null $source_entity_dofusdb_id
 * @property int|null $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereDofusdbItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereSourceEntityDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereSourceEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PendingResourceTypeItem whereUpdatedAt($value)
 */
	class PendingResourceTypeItem extends \Eloquent {}
}

namespace App\Models\Scrapping{
/**
 * Règle de mapping : une clé logique (ex. level, name) pour une source+entité DofusDB.
 * 
 * Lie un chemin API (from_path) à une ou plusieurs cibles Krosmoz (model.field) avec formatters.
 *
 * @property int $id
 * @property string $source
 * @property string $entity
 * @property string $mapping_key
 * @property string $from_path
 * @property bool $from_lang_aware
 * @property int|null $characteristic_id
 * @property array|null $formatters
 * @property string|null $spell_level_aggregation first|max|min|last (agrégation multi spell-level)
 * @property int $sort_order
 * @example ScrappingEntityMapping::where('source', 'dofusdb')->where('entity', 'monster')->orderBy('sort_order')->get();
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Characteristic|null $characteristic
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Characteristic> $characteristics
 * @property-read int|null $characteristics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scrapping\ScrappingEntityMappingTarget> $targets
 * @property-read int|null $targets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereCharacteristicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereEntity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereFormatters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereFromLangAware($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereFromPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereMappingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereSpellLevelAggregation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMapping whereUpdatedAt($value)
 */
	class ScrappingEntityMapping extends \Eloquent {}
}

namespace App\Models\Scrapping{
/**
 * Cible d'une règle de mapping : un couple (model, field) Krosmoz.
 * 
 * Une règle peut avoir plusieurs cibles (ex. item → resources, consumables, items).
 *
 * @property int $id
 * @property int $scrapping_entity_mapping_id
 * @property string $target_model
 * @property string $target_field
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Scrapping\ScrappingEntityMapping $scrappingEntityMapping
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereScrappingEntityMappingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereTargetField($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereTargetModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingEntityMappingTarget whereUpdatedAt($value)
 */
	class ScrappingEntityMappingTarget extends \Eloquent {}
}

namespace App\Models{
/**
 * Modèle Eloquent Section
 * 
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l'ordre, le type, les paramètres dynamiques, la visibilité, l'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d'accès.
 * 
 * Relations : page, users, createdBy ; médias via Media Library (collection files)
 *
 * @property int $id
 * @property int $page_id
 * @property string|null $title
 * @property string|null $slug
 * @property int $order
 * @property \App\Enums\SectionType $template
 * @property array<array-key, mixed>|null $settings
 * @property array<array-key, mixed>|null $data
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\Page $page
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\SectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereTemplate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section withoutTrashed()
 * @mixin \Eloquent
 * @property \App\Enums\SectionType|null $type
 * @property array<array-key, mixed>|null $params
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section displayable(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section playable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section readableFor(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Section whereWriteLevel($value)
 */
	class Section extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * Effet appliqué à un sort (instance).
 *
 * @property int $id
 * @property int $spell_id
 * @property int $spell_effect_type_id
 * @property int|null $value_min
 * @property int|null $value_max
 * @property int|null $dice_num
 * @property int|null $dice_side
 * @property int|null $duration
 * @property string $target_scope
 * @property string|null $zone_shape
 * @property bool $dispellable
 * @property int $order
 * @property string|null $raw_description
 * @property int|null $summon_monster_id
 * @property-read Spell $spell
 * @property-read SpellEffectType $spellEffectType
 * @property-read Monster|null $summonMonster
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereDiceNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereDiceSide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereDispellable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereRawDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereSpellEffectTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereSpellId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereSummonMonsterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereTargetScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereValueMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereValueMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffect whereZoneShape($value)
 */
	class SpellEffect extends \Eloquent {}
}

namespace App\Models{
/**
 * Type d'effet de sort (référentiel).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $category
 * @property string|null $description
 * @property string $value_type
 * @property string|null $element
 * @property string|null $unit
 * @property bool $is_positive
 * @property int $sort_order
 * @property int|null $dofusdb_effect_id
 * @property \Illuminate\Database\Eloquent\Collection<int, SpellEffect> $spellEffects
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $spell_effects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereDofusdbEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereElement($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereIsPositive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellEffectType whereValueType($value)
 */
	class SpellEffectType extends \Eloquent {}
}

namespace App\Models{
/**
 * Référentiel local des états DofusDB appliqués par les sorts.
 *
 * @property int $id
 * @property int $dofusdb_id
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $image
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
 * @property array<array-key, mixed>|null $raw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entity\Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBeMoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBePushed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantBeTackled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantDealDamage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantSwitchPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCantTackle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereDisplayTurnRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereDofusdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIncurable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerableMelee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereInvulnerableRange($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereIsMainState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState wherePreventsFight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState wherePreventsSpellCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellState whereUpdatedAt($value)
 */
	class SpellState extends \Eloquent {}
}

namespace App\Models{
/**
 * Sous-effet (atome : taper, soigner, vol_pa.
 * 
 * ..).
 *
 * @property int $id
 * @property string $slug
 * @property string $type_slug
 * @property string|null $template_text
 * @property string|null $formula
 * @property array<array-key, mixed>|null $variables_allowed
 * @property array<array-key, mixed>|null $param_schema
 * @property int|null $dofusdb_effect_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Effect> $effects
 * @property-read int|null $effects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereDofusdbEffectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereFormula($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereParamSchema($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereTemplateText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereTypeSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SubEffect whereVariablesAllowed($value)
 */
	class SubEffect extends \Eloquent {}
}

namespace App\Models{
/**
 * Preset de filtres pour les tableaux TanStack.
 * 
 * Stocke des snapshots de filtres par utilisateur, type d'entité et table.
 *
 * @property int $id
 * @property int $user_id
 * @property string $entity_type
 * @property string|null $table_id
 * @property string $name
 * @property string|null $search_text
 * @property array<array-key, mixed>|null $filters
 * @property int|null $limit
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereSearchText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TableFilterPreset whereUserId($value)
 */
	class TableFilterPreset extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $consumables
 * @property-read int|null $consumables_count
 * @property-read User|null $createdBy
 * @method static \Database\Factories\Type\ConsumableTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ConsumableType whereSeenCount($value)
 */
	class ConsumableType extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Type\ItemTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CharacteristicObject> $allowedCharacteristicObjects
 * @property-read int|null $allowed_characteristic_objects_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ItemType whereSeenCount($value)
 */
	class ItemType extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property int|null $dofusdb_race_id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property int|null $id_super_race
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Monster> $monsters
 * @property-read int|null $monsters_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MonsterRace> $subRaces
 * @property-read int|null $sub_races_count
 * @property-read MonsterRace|null $superRace
 * @method static \Database\Factories\Type\MonsterRaceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereIdSuperRace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace withoutTrashed()
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonsterRace whereDofusdbRaceId($value)
 */
	class MonsterRace extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property string $name
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $resources
 * @property-read int|null $resources_count
 * @method static \Database\Factories\Type\ResourceTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType withoutTrashed()
 * @mixin \Eloquent
 * @property int|null $dofusdb_type_id
 * @property string $decision
 * @property int $seen_count
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType allowed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType blocked()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereDofusdbTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResourceType whereSeenCount($value)
 */
	class ResourceType extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property int $scenario_id
 * @property int $next_scenario_id
 * @property string|null $condition
 * @property-read Scenario $nextScenario
 * @property-read Scenario $scenario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereCondition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereNextScenarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScenarioLink whereScenarioId($value)
 * @mixin \Eloquent
 */
	class ScenarioLink extends \Eloquent {}
}

namespace App\Models\Type{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property string $state
 * @property int $read_level
 * @property int $write_level
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $created_by
 * @property-read User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $spells
 * @property-read int|null $spells_count
 * @method static \Database\Factories\Type\SpellTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType whereWriteLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SpellType withoutTrashed()
 * @mixin \Eloquent
 */
	class SpellType extends \Eloquent {}
}

namespace App\Models{
/**
 * Modèle User central du projet Krosmoz JDR.
 * 
 * Gère l'authentification, les rôles, l'avatar, les notifications et les relations avec les entités du jeu.
 * 
 * Champs principaux :
 * - id, name, email, password, role, avatar
 * - notifications_enabled, notification_channels
 * 
 * Relations :
 * - scénarios, campagnes, pages, sections, et entités créées
 *
 * @property int $id Identifiant unique
 * @property string $name Nom d'utilisateur
 * @property string $email Email
 * @property string $password Mot de passe (hashé)
 * @property string $role Rôle (voir self::ROLES)
 * @property string|null $avatar Chemin de l'avatar ou null
 * @property bool $notifications_enabled Notifications activées ?
 * @property array $notification_channels Canaux de notification
 * @method bool wantsNotification(string $type = null) L'utilisateur veut-il des notifications ?
 * @method array notificationChannels() Retourne les canaux de notification
 * @method bool wantsProfileNotification() Toujours true (modif profil)
 * @method string avatarPath() URL de l'avatar (jamais null)
 * @method bool verifyRole(string|int $role) Possède au moins le rôle donné
 * @method bool updateRole(User $user) Peut-il modifier le rôle d'un autre ?
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $createdPages
 * @property-read int|null $created_pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $createdSections
 * @property-read int|null $created_sections_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Page> $pages
 * @property-read int|null $pages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Attribute> $createdAttributes
 * @property-read int|null $created_attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Capability> $createdCapabilities
 * @property-read int|null $created_capabilities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Breed> $createdBreeds
 * @property-read int|null $created_breeds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ConsumableType> $createdConsumableTypes
 * @property-read int|null $created_consumable_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Consumable> $createdConsumables
 * @property-read int|null $created_consumables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemType> $createdItemTypes
 * @property-read int|null $created_item_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $createdItems
 * @property-read int|null $created_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, MonsterRace> $createdMonsterRaces
 * @property-read int|null $created_monster_races_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Panoply> $createdPanoplies
 * @property-read int|null $created_panoplies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ResourceType> $createdResourceTypes
 * @property-read int|null $created_resource_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $createdResources
 * @property-read int|null $created_resources_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $createdScenarios
 * @property-read int|null $created_scenarios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Shop> $createdShops
 * @property-read int|null $created_shops_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Specialization> $createdSpecializations
 * @property-read int|null $created_specializations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SpellType> $createdSpellTypes
 * @property-read int|null $created_spell_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Spell> $createdSpells
 * @property-read int|null $created_spells_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Scenario> $scenarios
 * @property-read int|null $scenarios_count
 * @property-read string $role_name
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property bool $is_system
 * @property array<array-key, mixed>|null $notification_preferences
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNotificationPreferences($value)
 */
	class User extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

