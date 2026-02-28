# Characteristic and property naming reference (English, no abbreviations)

**Rule** : All characteristic keys, property names, and related identifiers use **English** and **full words** (no abbreviations). This applies everywhere: seeders, migrations, config files, function/method names, characteristic keys, and mapping configs.

**Scope** : Any identifier that represents a game stat, element, resource, or effect property (e.g. in `characteristics.key`, `db_column`, `effect_sub_effects`, DofusDB mapping, formatters).

---

## 1. Elements (damage / resistance types)

| Do not use | Use |
|------------|-----|
| eau | water |
| terre | earth |
| feu | fire |
| air | air |
| neutre | neutral |

**Examples** : `fixed_damage_earth_object`, `resistance_water_creature`, `fixed_resistance_fire_object`.

---

## 2. Core stats (ability scores, main characteristics)

| Do not use | Use |
|------------|-----|
| agi, agilité | agility |
| intel | intelligence |
| vita | vitality |
| strong, force | strength |
| sagesse | wisdom |
| chance | chance *(already English; keep as-is)* |

**Examples** : `agility_creature`, `intelligence_object`, `strength_creature`, `modifier_wisdom_creature`.

---

## 3. Save / bonus save (sauvegarde)

| Do not use | Use |
|------------|-----|
| objet_sav, objet_save | object_save |
| sav (alone) | save |
| save_vit_sag | save_vitality_wisdom |
| save_force_int_cha_agi | save_strength_intelligence_chance_agility |

**Note** : Prefer a single, consistent prefix: `object_save_*` for object context, `save_*_creature` for creature context. Always spell **save** in full (never `sav`).

---

## 4. Resources (action points, movement, range)

| Do not use | Use |
|------------|-----|
| pa | action_points |
| pm | movement_points |
| po | range |

**Examples** : `action_points_creature`, `action_points_spell`, `movement_points_object`, `range_creature`, `range_spell`, `range_editable_spell`, `spell_range_min_spell`, `spell_range_max_spell`.

---

## 5. Life, damage, resistance

| Do not use | Use |
|------------|-----|
| pv | life_points |
| pv_max | life_points_max |
| do_fixe | fixed_damage |
| res_fixe | fixed_resistance |
| res (for %) | resistance |
| res_50 | resistance_50_percent |
| invuln_100 | invulnerability_100_percent |
| bouclier | shield *(points)* |

**Examples** : `life_points_creature`, `life_points_max_object`, `fixed_damage_earth_object`, `fixed_resistance_neutral_creature`, `resistance_fire_creature`.

---

## 6. Combat and positioning

| Do not use | Use |
|------------|-----|
| ini | initiative |
| ca | armor_class |
| touch | hit_bonus |
| fuite | dodge *(or escape; same term for “tacle” opposite)* |
| tacle | tackle |
| esquive | dodge *(avoid “esquive” in keys)* |

**Examples** : `initiative_creature`, `armor_class_creature`, `hit_bonus_object`, `dodge_action_points_creature`, `dodge_movement_points_creature`, `tackle_creature`.

---

## 7. Spells and abilities

| Do not use | Use |
|------------|-----|
| invocation | summoning *(or keep invocation if used as game term)* |
| competences | skills |
| competences_passives | passive_skills |
| powerful | power *(for spell power index)* |
| de_vie | hit_dice |
| bonus_maitrise | mastery_bonus |
| reserve_wakfu | wakfu_reserve |
| wakfu_recharge | wakfu_recharge *(already English)* |

**Examples** : `summoning_creature`, `summoning_object`, `skills_object`, `cast_per_turn_spell`, `cast_per_target_spell`, `hit_dice_creature`, `mastery_bonus_creature`.

---

## 8. Modifiers and derived stats

| Do not use | Use |
|------------|-----|
| modificateur | modifier |

**Examples** : `modifier_vitality_creature`, `modifier_agility_creature`, `modifier_intelligence_creature`.

---

## 9. Context suffix (unchanged)

Keep the context suffix as-is: `_creature`, `_object`, `_spell`. They indicate the characteristic group (creature, object, spell).

**Pattern** : `{property}_{context}`  
Example: `action_points_creature`, `fixed_damage_earth_object`, `range_spell`.

---

## 10. Where this applies

- **Database** : `characteristics.key`, `characteristic_creature.db_column`, `characteristic_object.db_column`, `characteristic_spell.db_column`
- **Seeders** : `database/seeders/data/characteristics.php`, `characteristic_creature.php`, `characteristic_object.php`, `characteristic_spell.php`, `characteristic_icons_colors.php`
- **Config** : `config/effect_sub_effects.php` (keys and labels used for machine identifiers), scrapping mapping configs, DofusDB → Krosmoz mapping JSON
- **Migrations** : Column names that store these concepts (if any new columns are added)
- **Services** : Method names, variable names, and mapping keys that refer to characteristics (e.g. `DofusDbEffectMapping::ELEMENT_ID_TO_KEY`, formatter context)
- **Frontend** : Only when exposing or comparing characteristic keys (e.g. admin mapping UI); display labels can stay localized (French) for users

---

## 11. Effect sub-effects config (short keys)

For `config/effect_sub_effects.php`, the same rule applies: use full English, no abbreviations. The “short” keys used there (without `_creature` / `_object` / `_spell`) should still be full words, e.g.:

- `action_points`, `movement_points`, `range`
- `agility`, `strength`, `intelligence`, `chance`, `wisdom`, `vitality`
- `life_points`, `shield`
- `earth`, `fire`, `water`, `air`, `neutral`

This keeps the sub-effect namespace aligned with the rest of the naming reference and avoids confusion when mapping to full characteristic keys.

---

## 12. Reference: old key → new key (migration checklist)

When migrating existing data and code, use this mapping. Each row is “old identifier (French or abbrev) → new canonical form”.

| Old | New |
|-----|-----|
| objet_sav_agi_creature | object_save_agility_creature |
| objet_sav_intel_object | object_save_intelligence_object |
| objet_sav_sagesse_object | object_save_wisdom_object |
| objet_save_chance_creature | object_save_chance_creature *(only fix prefix: objet → object)* |
| life_creature | life_points_creature *(if pv was ever used)* |
| pa_creature, pa_spell, pa_object | action_points_creature, action_points_spell, action_points_object |
| pm_creature, pm_object | movement_points_creature, movement_points_object |
| po_creature, po_spell, po_object | range_creature, range_spell, range_object |
| ini_creature, ini_object | initiative_creature, initiative_object |
| ca_creature, ca_object | armor_class_creature, armor_class_object |
| touch_creature, touch_object | hit_bonus_creature, hit_bonus_object |
| agi_creature, agi_object | agility_creature, agility_object |
| intel_creature, intel_object | intelligence_creature, intelligence_object |
| strong_creature, strong_object | strength_creature, strength_object |
| sagesse_creature, sagesse_object | wisdom_creature, wisdom_object |
| do_fixe_* | fixed_damage_* (e.g. fixed_damage_earth_object) |
| res_fixe_* | fixed_resistance_* |
| res_neutre_creature, res_terre_creature, … | resistance_neutral_creature, resistance_earth_creature, … |
| esquive_pa_*, esquive_pm_* | dodge_action_points_*, dodge_movement_points_* |
| fuite_creature, fuite_object | dodge_creature, dodge_object *(or escape_* if you prefer)* |
| tacle_creature, tacle_object | tackle_creature, tackle_object |
| save_vit_sag_object | save_vitality_wisdom_object |
| save_force_int_cha_agi_object | save_strength_intelligence_chance_agility_object |
| modificateur_* | modifier_* (e.g. modifier_vitality_creature) |
| de_vie_creature | hit_dice_creature |
| bonus_maitrise_creature | mastery_bonus_creature |
| pv_max_object | life_points_max_object |
| competences_object | skills_object |
| competences_passives_object | passive_skills_object |
| res_50_object | resistance_50_percent_object |
| invuln_100_object | invulnerability_100_percent_object |
| po_editable_spell, spell_po_min_spell, spell_po_max_spell | range_editable_spell, spell_range_min_spell, spell_range_max_spell |

Elements in compound keys: **terre** → **earth**, **feu** → **fire**, **eau** → **water**, **air** → **air**, **neutre** → **neutral**.

---

*This document is the single source of truth for characteristic and property naming. When adding or renaming such identifiers, follow it and update seeders, configs, mappings, and code accordingly. See also [NAMING_CONVENTIONS.md](./NAMING_CONVENTIONS.md) and [ANALYSE_ZONES_RISQUE_NAMMING.md](../50-Fonctionnalités/Scrapping/ANALYSE_ZONES_RISQUE_NAMMING.md).*
