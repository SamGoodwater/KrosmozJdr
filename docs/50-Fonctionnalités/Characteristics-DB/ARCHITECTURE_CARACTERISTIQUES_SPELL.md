# Architecture des caractéristiques spell — Type 1 et Type 2

Document de référence pour comprendre et faire évoluer les caractéristiques du groupe **spell**.

---

## 1. Vue d'ensemble

Les caractéristiques spell se divisent en **deux types distincts** :

| Type | Description | Stockage | Conversion Dofus |
|------|-------------|----------|------------------|
| **Type 1** | Caractéristiques qui **décrivent le sort** lui-même | Table `spells` ou `effects` | Oui |
| **Type 2** | Caractéristiques **influençables par les sous-effets** | `effect_sub_effect.params` (characteristic, value_converted) | Oui |

---

## 2. Type 1 — Descripteurs du sort

### 2.1 Liste et stockage

| characteristic_key | db_column | Table | Rôle |
|-------------------|-----------|-------|------|
| action_points_spell | pa | spells | Coût PA |
| area_spell | area | effects | Zone d'impact |
| element_spell | element | spells | Élément |
| power_spell | powerful | spells | Indice de puissance (niveau) |
| cast_per_turn_spell | cast_per_turn | spells | Lancements max/tour |
| cast_per_target_spell | cast_per_target | spells | Lancements max/cible |
| sight_line_spell | sight_line | spells | Ligne de vue |
| number_between_two_cast_spell | number_between_two_cast | spells | Délai entre lancers |
| number_between_two_cast_editable_spell | number_between_two_cast_editable | spells | Délai éditable |
| category_spell | category | spells | Type de sort |
| is_magic_spell | is_magic | spells | Magique vs physique |
| movement_points_spell | pm | spells | Var. PM (rare au niveau sort) |
| range_spell | po | spells | Portée (po_min/po_max) |
| range_editable_spell | po_editable | spells | Portée éditable |
| spell_range_min_spell | spell_po_min | spells | Portée min |
| spell_range_max_spell | spell_po_max | spells | Portée max |
| casting_time_spell | casting_time | effect ? | Temps d'incantation |
| duration_spell | duration | effect | Durée de l'effet |
| time_before_use_again_spell | time_before_use_again | effect ? | Délai avant réutilisation |
| ritual_available_spell | ritual_available | spells | Utilisable en rituel |

**Note** : `casting_time`, `duration`, `time_before_use_again` — à vérifier dans les migrations (spells vs effects).

### 2.2 Flux

- DofusDB → config `spell.json` → IntegrationService → colonnes `spells` / `effects`
- Les formules de conversion (si présentes) sont dans `characteristic_spell`

---

## 3. Type 2 — Influenceables par les sous-effets

### 3.1 Type 2 créature

Toutes les caractéristiques de **characteristic_creature** peuvent être ciblées par les actions **booster**, **retirer**, **voler-caracteristiques** :

- PA, PM, Ini, PO, Invocations
- 6 stats (Vitalité, Sagesse, Force, Intel, Chance, Agi)
- Esquive PA/PM, Fuite, Tacle, CA
- Dégâts fixes (5 éléments + multiple)
- Bonus critique, Bonus soin
- Résistances fixes et %

Le paramètre `params.characteristic` du sous-effet référence une **characteristic_key** (ex. `pa`, `strong`, `res_terre`). Le `SpellEffectConversionFormulaResolver` normalise vers `*_spell` (ex. `pa` → `action_points_spell`).

**Important** : les caractéristiques spell Type 2 creature ne sont **pas liées** aux caractéristiques creature (`linked_to_key` = NULL). Les formules de conversion diffèrent : les valeurs spell concernent des effets **boost** ou **retrait** appliqués par des sorts, donc des amplitudes généralement plus faibles que les valeurs de base d'une créature.

### 3.2 Type 2 action (implémenté)

Actions dont la valeur à convertir **ne dépend pas** d'une caractéristique créature, mais du **type d'action** :

| Action (sub_effect_slug) | Type 2 action | characteristic_key conversion | Remarque |
|--------------------------|----------------|-------------------------------|----------|
| **frapper** | Dommages | `dommages_spell` | Élément = type (feu, eau, etc.) ; valeur = montant dégâts |
| **soigner** | Soin | `soin_spell` | Valeur = montant soin |
| **voler-vie** | Vol de vie | `vol_vie_spell` | Valeur = montant PV volés |
| **protéger** | Bouclier / PV temporaires | `bouclier_spell` | Valeur = absorption |

**Implémenté** : les 4 caractéristiques sont définies dans `characteristics.php`, `characteristic_spell.php` (formule type power_spell) et `SpellEffectConversionFormulaResolver` (mapping action → characteristic_key).

---

## 4. Classification actuelle dans characteristic_spell.php

### Type 1 (descripteurs sort/effet)

- action_points_spell, area_spell, element_spell, power_spell
- cast_per_turn_spell, cast_per_target_spell
- sight_line_spell, number_between_two_cast_spell
- category_spell, is_magic_spell
- range_spell, range_editable_spell
- spell_range_min_spell, spell_range_max_spell
- casting_time_spell, duration_spell, time_before_use_again_spell
- ritual_available_spell

### Type 2 créature (conversion par characteristic)

- strong_spell, vitality_spell, sagesse_spell, chance_spell, agi_spell, intel_spell
- movement_points_spell (ressource)
- dodge_spell, tackle_spell
- dodge_action_points_spell, dodge_movement_points_spell
- critical_spell
- res_air_spell, res_eau_spell, res_feu_spell, res_neutre_spell, res_terre_spell
- do_fixe_multiple_spell
- fixed_resistance_*_spell
- critical_damage_reduction_spell, push_damage_reduction_spell
### Type 2 action (caractéristiques dédiées)

- frapper → **dommages_spell**
- soigner → **soin_spell**
- voler-vie → **vol_vie_spell**
- protéger → **bouclier_spell**

---

## 5. Flux de conversion (sous-effets)

1. **SpellEffectsConversionService** reçoit un sous-effet (sub_effect_slug, params).
2. **SpellEffectConversionFormulaResolver** résout la characteristic_key :
   - `frapper` → `dommages_spell`, `soigner` → `soin_spell`, `voler-vie` → `vol_vie_spell`, `protéger` → `bouclier_spell`
   - `booster`/`retirer`/`voler-caracteristiques` → `params.characteristic` normalisé (ex. `po` → `range_spell`)
3. **CharacteristicGetterService** fournit min, max, conversion_formula pour cette characteristic_key (groupe spell).
4. **DofusConversionService** applique la formule : `value_converted = f(value_dofus)`.
5. Résultat stocké dans `params.value_converted`.

---

## 6. Mapping DofusDB

**dofusdb_characteristic_to_krosmoz_spell.json** : mappe les IDs Dofus (champ `effect.characteristic`) vers les clés courtes utilisées dans params.characteristic.

Exemples : 1→pa, 10→strong, 27→esquive_pa, 78→fuite, 79→tacle, etc.

Les Type 2 action (frapper, soigner, voler-vie, protéger) n'utilisent pas ce mapping pour la **valeur** : l'élément (characteristic) vient de `elementId` Dofus, la valeur est convertie via dommages_spell / soin_spell / vol_vie_spell / bouclier_spell selon l'action.

---

## 7. Ajustements à prévoir

### 7.1 Seeder characteristic_spell

1. **Séparer clairement Type 1 et Type 2**
   - Ajouter un champ `type` ou `storage` (spell_column | effect_params) pour documenter.
   - Type 1 : db_column pointe vers une colonne réelle (spells / effects).
   - Type 2 : db_column décrit le paramètre dans effect_sub_effect.params (ex. value_converted pour la valeur, characteristic pour la cible).

2. **Répertorier explicitement les Type 2 action** ✅ *Implémenté*
   - `dommages_spell`, `soin_spell`, `vol_vie_spell`, `bouclier_spell` créés dans characteristics.php, characteristic_spell.php, characteristic_icons_colors.php.
   - `SpellEffectConversionFormulaResolver` mappe frapper→dommages_spell, soigner→soin_spell, voler-vie→vol_vie_spell, protéger→bouclier_spell.

3. **Aligner Type 2 créature avec characteristic_creature** ✅ *Exhaustif depuis 2026-03*
   - Toutes les characteristic_creature boostables ont une entrée *_spell pour la conversion.
   - Couvert : pa, pm, po, strong, vitality, sagesse, chance, agi, intel, fuite, tacle, esquive_pa, esquive_pm, res_*, do_fixe_multiple, critical, fixed_resistance_*, push, critiques.
   - **Ajoutés (Type 2 creature complet)** : initiative_spell, armor_class_spell, hit_bonus_spell, summoning_spell, heal_bonus_spell, fixed_damage_neutral_spell, fixed_damage_earth_spell, fixed_damage_fire_spell, fixed_damage_air_spell, fixed_damage_water_spell, save_vitality_spell, save_wisdom_spell, save_strength_spell, save_intelligence_spell, save_chance_spell, save_agility_spell, wakfu_reserve_spell, mastery_bonus_spell.
   - Retirés : echec_critique_spell, magic_find_spell (prospection).

### 7.2 Config effect_sub_effects.php

- La liste `characteristics` (stat, resource, element) est utilisée par l'admin pour filtrer les choix.
- Aligner les `key` avec les clés courtes attendues par le resolver (pa, pm, po, agi, strong, etc.) ou prévoir une couche de mapping key↔characteristic_key.

### 7.3 Colonnes spells / effects

- Vérifier si `casting_time`, `duration`, `time_before_use_again` existent dans spells ou effects.
- Si absentes : les ajouter ou retirer les entrées characteristic_spell correspondantes.

---

## 8. Références

- [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](../Scrapping/CARACTERISTIQUES_EFFETS_PAR_ACTION.md) — classification par action
- [TAXONOMIE_SOUS_EFFETS.md](../Spell-Effects/TAXONOMIE_SOUS_EFFETS.md) — pattern action → caractéristique → valeur
- config/effect_sub_effects.php — liste characteristics (stat, resource, element) pour l'admin
