# Cohérence Seeder / Règles – Valeurs et formules

Ce document compare les valeurs min/max, formules et limites des caractéristiques entre les seeders (`database/seeders/data/`) et le livre de règles (`docs/400- Jeu/420- Règles/`).

**Alignement noms ↔ clés BDD** : voir [REFERENCE_CLES_CARACTERISTIQUES.md](REFERENCE_CLES_CARACTERISTIQUES.md).

---

## 1. Ce qui est cohérent

| Élément | Règles | Seeder | Statut |
|--------|--------|--------|--------|
| **PA (créature)** | Base 6, max 12, équip. +6, forgemagie +1 | `action_points_creature` min 6, max 12, formula_display idem | OK |
| **PM (créature)** | Base 3, max 6, équip. +3, forgemagie +1 | `movement_points_creature` min 3, max 6, formula_display idem | OK |
| **PO (créature)** | Base 0, max 6, équip. +6, forgemagie +1 | `range_creature` min 0, max 6, formula_display idem | OK |
| **Scores caractéristiques** | Score 6–31, mod = ⌊(score−10)/2⌋ | `*_creature` (vitality, strength, etc.) min 6, max 31 | OK |
| **Modificateurs** | ⌊(Score − 10) / 2⌋ (2.2.1.2) | `modifier_*_creature` formula_display `floor((score−10)/2) (2.2.1.2)` | OK |
| **PV (créature)** | Vitalité × 10 + dés de vie | `life_points_creature` formula `[vitality_creature]*10+[hit_dice_creature]` | OK |
| **CA (créature)** | 10 + mod. Vitalité + bouclier, max 26 | `armor_class_creature` max 26 | OK |
| **Initiative** | 1d20 + Intelligence + bonus équip. | `initiative_creature` formula_display idem | OK |
| **PA (sorts)** | 0–12 (3.3.2.1) | `characteristic_spell` action_points_spell min 0, max 12 | OK |
| **Niveau créature** | 1–20 (personnages), 1–40 (monstres) | `level_creature` min 1, max 20 (entity *) ; max 40 (monster) | OK |

---

## 2. Convention `max` / `forgemagie_max` (objets)

Dans les JSON `*-object-definition.json` :

- **`max`** = bonus **équipement seul**, **hors** forgemagie (validation via `CharacteristicGetterService::getLimits`).
- **`forgemagie_max`** = plafond **forgemagie** à part.
- **Total** (si forgemagie autorisée) = **`max` + `forgemagie_max`**.

Une clé **`_comment_limits`** (préfixe `_`, ignorée au seed) rappelle cette convention. Détail : [CARACTERISTIQUES_CREATION_REFERENCE.md](../../410-%20Ressources/CARACTERISTIQUES_CREATION_REFERENCE.md) §4.

---

## 3. Alignements récents (objet / règles 2.2.2 et 2.6.1)

| Zone | Règles | Seeder (objet) | Remarque |
|------|--------|----------------|----------|
| **PA** | +6 équip., +1 forgem. (2.2.2) | `action_points_object` `max` 6, `forgemagie_max` 1 | Plafond amulette 2.6.1 cohérent avec +6 équip. |
| **Caracs principales** (chapeau / cape) | +6 équip., +2 forgem. (2.6.1) | `*_object` Force, Int, etc. : `max` 6, `forgemagie_max` 2 ; formule-table plafonnée à +6 | Ancien `max` 8 cumulait visuellement équip. + forgem. |
| **Compétences (actives)** | +5 équip., +3 forgem. (2.2.2) | `acrobatics_object`, etc. : `max` 5, `forgemagie_max` 3 | Ancien `max` 8 = total 5+3. |
| **Compétences passives** | bonus équip. / forgem. distincts | `*_passive_object` : `max` 3, `forgemagie_max` 2 | Inchangé (déjà séparé). |
| **Tacle / Fuite** | +10 équip., +2 forgem. (2.2.2) | `tackle_object`, `dodge_object` : `max` 10, `forgemagie_max` 2 ; formule étendue jusqu’à 20 | Ancien `max` 8 cumulait mal la règle globale. |
| **Esquive PA / PM** | +3 équip., +2 forgem. (2.2.2) | `dodge_action_points_object`, `dodge_movement_points_object` : `max` 3 | Ancien `max` 5 était incohérent. |
| **Résistances fixes** | +10 équip., +3 forgem. (2.2.2) | `fixed_resistance_*_object` : `max` 10, `forgemagie_max` 3 | Bouclier seul : +7 par emplacement (2.6.1) ; cumul global en 2.2.2. |
| **Dommages fixes** | +10 équip., +5 forgem. (2.2.2) | `fixed_damage_*` (éléments) : `max` 10 | Ancien `max` 5 + `forgemagie_max` 5 = total OK mais `max` sous-plafonnait l’équipement. |
| **Bonus de soins** | 7 au total, dont +2 forgem. (2.2.2) | `heal_bonus_object` : `max` 5, `forgemagie_max` 2 | Ancien `max` 7 incluait la forgemagie. |
| **Critique / échec critique** | 0–3, sans forgem. (2.2.2 / fiche) | `critical_hit_object`, `failure_hit_object` : `forgemagie_max` **0** | Ancien `failure_hit_object` avait `forgemagie_max` 1. |

---

## 4. Formules et références

- **Modificateur** : `floor((score−10)/2)` — identique règles (2.2.1.2) et seeders (`formula_display` modifier_*_creature).
- **Plafond modificateur de base** : `⌊Niveau/2⌋ + 1` (règles 2.2.1) — présent uniquement dans les règles (tableau par niveau) ; pas de formule équivalente dans les seeders (normal, c’est une règle de création/perso).
- **PV** : Vitalité×10 + dés de vie — cohérent entre 2.2.2 et `life_points_creature`.

---

## 5. Vérifications recommandées

- [ ] Utilisation de `characteristic_object.max` dans le code : validation côté app (création d’objets, équipement) doit respecter les plafonds **par emplacement** (2.6.1) et, le cas échéant, le **total** `max` + `forgemagie_max` pour la ligne.
- [ ] UI / outils MJ : si un écran doit afficher le plafond « jouable » total, combiner explicitement **`max` + `forgemagie_max`** (et ne pas supposer que `max` est déjà le total).
- [ ] Après modification des seeders : relancer les seeds / tests et mettre à jour ce document.
