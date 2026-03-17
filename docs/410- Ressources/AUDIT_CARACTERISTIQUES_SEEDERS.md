# Audit caractéristiques — Seeders objet et créature

Audit réalisé pour aligner les seeders avec les règles KrosmozJDR (2.2.2, 2.6.1, 4.2.2). Les caractéristiques non utilisées dans le JDR mais envoyées par l'API DofusDB doivent être retirées.

---

## 1. Caractéristiques OBJET — À retirer (non dans 2.6.1)

Ces caractéristiques proviennent de DofusDB mais ne sont **pas définies** dans les règles KrosmozJDR (section 2.6.1 Équipements de base).

| characteristic_key | DofusDB id | Raison |
|--------------------|------------|--------|
| `power_object` | 25 | Puissance % — pas dans 2.6.1 |
| `magic_find_object` | 48 | Prospection — pas dans 2.6.1 |
| `reflect_damage_object` | 50 | Renvoi — pas dans 2.6.1 |
| `push_damage_bonus_object` | 84 | Poussée — pas dans 2.6.1 |
| `push_damage_reduction_object` | 85 | Poussée (fixe) — pas dans 2.6.1 |
| `critical_damage_bonus_object` | 86 | Bonus dégâts critique — pas dans 2.6.1 |
| `critical_damage_reduction_object` | 87 | Réduction dégâts critique — pas dans 2.6.1 |
| `received_damage_multiplier_distance_object` | 121 | Distance % (résistance) — pas dans 2.6.1 |
| `all_damage_bonus_object` | 16 | Dommages génériques — 2.6.1 utilise dommages fixes par élément (fixed_damage_*) |

**Conserver** : `invulnerability_100_percent_object` (résistance 100 % = immunité, définie en 2.2.2).

---

## 2. Caractéristiques CRÉATURE — À retirer (surnuméraires ou inexistantes)

### 2.1 Entrées supprimées (2026-03)

| characteristic_key | Raison |
|--------------------|--------|
| `name_object` | Le nom n'est pas une caractéristique — c'est un attribut de l'entité. Erreur de groupe (object dans creature). |
| `save_agility_creature`, `save_chance_creature`, `save_intelligence_object`, `save_wisdom_object` (indices 3–6) | Colonnes `sav_agi`, `sav_chance`, `sav_intel`, `sav_sagesse` **n'existent pas** dans la table `creatures`. Doublons des save_*_creature (indices 50–55) qui ont les bonnes formules. **Supprimés.** |

### 2.2 Caractéristiques créature conservées (conformes 4.2.2)

- Niveau, PV, 6 stats, PA, PM, PO, Initiative
- CA, bonus touche, Esquive PA/PM, Fuite, Tacle
- Résistances (fixe + 50 %), dommages fixes
- Modificateurs et sauvegardes (calculés)
- Dés de vie, réserve Wakfu, bonus maîtrise (calculés)
- Bonus critique, bonus soin

---

## 3. Fichiers impactés et modifications réalisées

| Fichier | Modifications |
|---------|---------------|
| `characteristic_object.php` | Suppression des 11 blocs objet non-Krosmoz |
| `characteristic_creature.php` | Suppression des 4 `object_save_*` et de `name_object` |
| `characteristics.php` | Suppression des 11 entrées objet + 4 entrées `object_save_*` |
| `characteristic_icons_colors.php` | Aucune modification (les caractéristiques retirées n'y figuraient pas) |
| `dofusdb_characteristic_to_krosmoz.json` | Suppression des mappings pour IDs 16, 25, 48, 50, 82, 83, 84, 85, 86, 87, 121 |
| `DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md` | Marqué ces IDs comme « — » (non utilisés) |

**Note** : `name_object` est conservé dans `characteristics.php` car référencé par `scrapping_entity_mappings.php` pour le mapping des noms d'entités.

---

## 4. Vérification complémentaire (2026-03)

### 4.1 Cohérence characteristics.php / characteristic_object.php

Une vérification a confirmé que les **11 caractéristiques objet** retirées de `characteristic_object.php` étaient encore présentes comme définitions orphelines dans `characteristics.php`. Elles ont été supprimées de `characteristics.php` pour assurer la cohérence.

### 4.2 Modificateurs (créature uniquement)

Les modificateurs (`modifier_vitality_creature`, `modifier_wisdom_creature`, etc.) sont **uniquement pour les créatures** : ils sont calculés à partir des stats, non stockés en base, et ne figurent **pas** dans `characteristic_object.php`. Aucune caractéristique modificateur n'est appliquée aux objets.

### 4.3 Caractéristiques objet conformes à 2.6.1

La liste actuelle de `characteristic_object.php` couvre toutes les caractéristiques définies dans les règles KrosmozJDR 2.6.1 (Équipements de base) : niveau, rareté, prix, poids, 6 stats, bonus de sauvegarde, compétences, PA/PM, esquives PA/PM, tacle/fuite, invocations, portée, CA, résistances, dommages fixes, bonus critique, bonus soin, recharge Wakfu.

---

*Document généré dans le cadre de l'audit caractéristiques seeders — 2026-03.*
