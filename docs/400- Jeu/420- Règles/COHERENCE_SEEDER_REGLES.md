# Cohérence Seeder / Règles – Valeurs et formules

Ce document compare les valeurs min/max, formules et limites des caractéristiques entre les seeders (`database/seeders/data/`) et le livre de règles (`docs/400- Jeu/420- Règles/`).

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

## 2. Incohérences corrigées (objet / règles 2.6.1)

Les règles 2.6.1 (Équipements de base) fixent le **maximum par type d’objet**. Le seeder `characteristic_object` définissait des `max` par caractéristique qui dépassaient ces plafonds.

| Caractéristique | Règles (2.6.1) | Seeder (avant) | Correction |
|-----------------|----------------|----------------|------------|
| **PM (bottes)** | PM : +3 maximum | `movement_points_object` max 5 | max 5 → **3** |
| **PA (amulettes)** | PA : +6 maximum | `action_points_object` max 5 | max 5 → **6** |
| **PO (anneaux)** | Portée (PO) : +6 maximum | `range_object` max 5 | max 5 → **6** |

---

## 3. Point à trancher (bonus par objet)

**Règles 2.6.1** : Chapeaux (Vitalité, Sagesse) et Capes (Force, Intelligence, Chance, Agilité) : **+4 maximum** par objet.

**Seeder** : `vitality_object`, `strength_object`, `intelligence_object`, etc. ont `max` = **8** et une formule `[level]*(8/20)` (à niveau 20, bonus théorique 8).

- Soit le **max 8** sert à la conversion Dofus / génération et le plafond « règle » +4 est appliqué ailleurs (validation, affichage) → à documenter.
- Soit le seeder doit refléter la règle : **max 4** pour ces caractéristiques d’objet.

À décider : garder 8 pour la génération et documenter, ou passer les `max` à 4 dans `characteristic_object` pour ces carac.

---

## 4. Formules et références

- **Modificateur** : `floor((score−10)/2)` — identique règles (2.2.1.2) et seeders (`formula_display` modifier_*_creature).
- **Plafond modificateur de base** : `⌊Niveau/2⌋ + 1` (règles 2.2.1) — présent uniquement dans les règles (tableau par niveau) ; pas de formule équivalente dans les seeders (normal, c’est une règle de création/perso).
- **PV** : Vitalité×10 + dés de vie — cohérent entre 2.2.2 et `life_points_creature`.

---

## 5. Vérifications recommandées

- [ ] Utilisation de `characteristic_object.max` dans le code : validation côté app (création d’objets, équipement) doit respecter les plafonds des règles (2.6.1).
- [ ] Si des objets « générés » peuvent dépasser +4 (Force, etc.) ou +3 (PM), prévoir un plafond explicite (min(seeder_max, rules_max)) ou aligner le seeder sur les règles.
- [ ] Après modification des seeders : relancer les seeds / tests et mettre à jour ce document.
