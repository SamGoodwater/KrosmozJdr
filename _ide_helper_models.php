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
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CharacteristicCreature> $creatureRows
 * @property-read int|null $creature_rows_count
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereHelper($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Characteristic whereUpdatedAt($value)
 */
	class Characteristic extends \Eloquent {}
}

namespace App\Models{
/**
 * Définition d’une caractéristique pour une entité du groupe créature (monster, class, npc).
 *
 * @property int $id
 * @property int $characteristic_id
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicCreature whereDefaultValue($value)
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
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicObject whereDefaultValue($value)
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
 * @property string $entity
 * @property string|null $db_column
 * @property string|null $min Valeur fixe, formule ou table JSON
 * @property string|null $max Valeur fixe, formule ou table JSON
 * @property string|null $formula
 * @property string|null $formula_display
 * @property string|null $default_value
 * @property string|null $conversion_formula
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionKrosmozSample($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereConversionSampleRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDbColumn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CharacteristicSpell whereDefaultValue($value)
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
 */
	class Attribute extends \Eloquent {}
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
	class Breed extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, File> $files
 * @property-read int|null $files_count
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
	class Campaign extends \Eloquent {}
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
 */
	class Capability extends \Eloquent {}
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
 */
	class Consumable extends \Eloquent {}
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
 */
	class Creature extends \Eloquent {}
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
 */
	class Item extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Resource> $recipeIngredients
 * @property-read int|null $recipe_ingredients_count
 */
	class Resource extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, File> $files
 * @property-read int|null $files_count
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
	class Scenario extends \Eloquent {}
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
 */
	class Shop extends \Eloquent {}
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
 */
	class Specialization extends \Eloquent {}
}

namespace App\Models\Entity{
/**
 * @property int $id
 * @property string|null $official_id
 * @property string|null $dofusdb_id
 * @property string $name
 * @property string $description
 * @property string|null $effect
 * @property int $area
 * @property string $level
 * @property string $po
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Spell wherePo($value)
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SpellEffect> $spellEffects
 * @property-read int|null $spell_effects_count
 */
	class Spell extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $file
 * @property string|null $title
 * @property string|null $comment
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Section> $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File withoutTrashed()
 * @mixin \Eloquent
 */
	class File extends \Eloquent {}
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page forMenu(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page inMenu()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page playable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page readableFor(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereReadLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Page whereWriteLevel($value)
 */
	class Page extends \Eloquent {}
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

namespace App\Models{
/**
 * Modèle Eloquent Section
 * 
 * Représente une section dynamique appartenant à une page (bloc de contenu, composant Vue).
 * Gère l'ordre, le type, les paramètres dynamiques, la visibilité, l'état, les utilisateurs et fichiers associés.
 * Utilisé pour la construction flexible des pages et la gestion fine des droits d'accès.
 * 
 * Relations : page, users, files, createdBy
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\File> $files
 * @property-read int|null $files_count
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
	class Section extends \Eloquent {}
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
 * @property bool $is_system
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsSystem($value)
 */
	class User extends \Eloquent {}
}

