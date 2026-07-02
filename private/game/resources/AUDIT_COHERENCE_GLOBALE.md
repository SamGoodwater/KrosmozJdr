# Audit de cohérence — Règles, BDD, fiches ressources

Ce document vérifie la cohérence entre :
- **Règles** : `private/game/rules/` (2.2.1, 2.2.2, 2.6.1, etc.)
- **Caractéristiques BDD** : `database/seeders/data/characteristic_creature.php`, `characteristic_object.php`, `characteristics.php`
- **Fiches ressources** : `private/game/resources/` (fiche_perso, Equipements et forgemagie, CaractéristiquesAvecLien avec Dofus)

---

## 1. Synthèse

| Zone | Statut | Détails |
|------|--------|---------|
| **Règles ↔ BDD** | ✓ Cohérent | Formules, min/max, plafonds alignés (sauf point 3) |
| **Règles ↔ Fiche personnage** | ✓ Cohérent | Fiche 0.1.3.8 corrigée, alignée avec règles |
| **BDD ↔ Fiche personnage** | ✓ Cohérent | Voir [COHERENCE_FICHE_PERSO_VS_PROJET.md](COHERENCE_FICHE_PERSO_VS_PROJET.md) |
| **BDD ↔ Fiches ressources (PDF)** | ✓ Cohérent | Formules objet/creature alignées sur PDF Équipements et Caractéristiques |

**Point à trancher** : Bonus Vitalité/Force/etc. par objet — règles 2.6.1 disent +4 max, seeder/PDF Équipements indiquent jusqu’à +8 (voir section 4).

---

## 2. Règles ↔ Caractéristiques BDD

### 2.1 Caractéristiques principales (2.2.1)

| Règle | BDD | Statut |
|-------|-----|--------|
| Modificateur = ⌊(Score−10)/2⌋ | `modifier_*_creature` formula | ✓ |
| Plafond mod = ⌊Niveau/2⌋+1 | Intégré dans formule modifier | ✓ |
| Scores 6–31 | `*_creature` min 6, max 31 | ✓ |
| Équipements +4 max (2.6.1) | Voir section 4 | À trancher |

### 2.2 Caractéristiques secondaires (2.2.2)

| Règle | BDD | Statut |
|-------|-----|--------|
| PA base 6, max 12 | `action_points_creature` | ✓ |
| PM base 3, max 6 | `movement_points_creature` | ✓ |
| PO base 0, max 6 | `range_creature` | ✓ |
| PV = Vitalité×10 + dés de vie | `life_points_creature` | ✓ |
| CA = 10 + mod. Vitalité + bouclier | `armor_class_creature` | ✓ |
| Initiative = 1d20 + mod. Intel | `initiative_creature` | ✓ |
| Esquive PA/PM = 8 + mod. Sagesse | `dodge_action_points_creature`, `dodge_movement_points_creature` | ✓ |
| Fuite = 1d20 + mod. Agi | `dodge_creature` | ✓ |
| Tacle = 1d20 + mod. Chance | `tackle_creature` | ✓ |
| Dés de vie = floor(niveau/2), max 10 | `hit_dice_creature` | ✓ |
| Bonus maîtrise = 1+floor(niveau/4), max 6 | `mastery_bonus_creature` | ✓ |
| Sauvegardes = mod. + bonus maîtrise (si maîtrisé) | `save_*_creature` | ✓ |
| Réserve Wakfu = bonus maîtrise + équip. | `wakfu_reserve_creature` | ✓ |

### 2.3 Équipements (2.6.1)

| Règle | BDD (characteristic_object) | Statut |
|-------|----------------------------|--------|
| Chapeaux : Vit/Sag +4 max | vitality_object, wisdom_object max 8 | Voir section 4 |
| Capes : For/Int/Cha/Agi +4 max | idem max 8 | Voir section 4 |
| PA amulettes +6 max | action_points_object max 6 | ✓ |
| PM bottes +3 max | movement_points_object max 3 | ✓ |
| PO anneaux +6 max | range_object max 6 | ✓ |
| Invocations anneaux +5 max | summoning_object max 5 | ✓ |
| Boucliers CA +5 max | armor_class_object max 5 | ✓ |
| Esquive PA amulettes +5 max | dodge_action_points_object | ✓ |
| Esquive PM bottes +5 max | dodge_movement_points_object | ✓ |
| Tacle/Fuite ceintures +10 max | tackle_object, dodge_object max 10 | ✓ |

---

## 3. Fiches ressources ↔ BDD

### 3.1 Fiche personnage (fiche_perso 0.1.3.8)

Alignée avec le projet. Voir [COHERENCE_FICHE_PERSO_VS_PROJET.md](COHERENCE_FICHE_PERSO_VS_PROJET.md).

### 3.2 PDF Équipements et forgemagie

| PDF | BDD | Statut |
|-----|-----|--------|
| Tables niveau→bonus (hit_bonus, dommages, etc.) | Formules-table dans characteristic_object | ✓ Appliquées |
| Prix de base, forgemagie | base_price_per_unit, rune_price_per_unit | ✓ |
| Limites forgemagie | forgemagie_max | ✓ |

### 3.3 PDF CaractéristiquesAvecLien avec Dofus

| PDF | BDD | Statut |
|-----|-----|--------|
| Modificateur (carac−10)/2, limites niv 1-3 | modifier_*_creature formula | ✓ |
| Esquive PA/PM = 8 + mod. Sagesse | dodge_action_points_creature, dodge_movement_points_creature | ✓ |
| Dés de vie = floor(niveau/2), max 10 | hit_dice_creature | ✓ |
| Bonus de maîtrise = 1+floor(niveau/4), max 6 | mastery_bonus_creature | ✓ |
| Jets de sauvegarde = mod. + bonus maîtrise | save_*_creature | ✓ |

---

## 4. Point à trancher : bonus Vitalité/Force/etc. par objet

| Source | Valeur |
|--------|--------|
| **Règles 2.6.1** | Chapeaux (Vit, Sag) et Capes (For, Int, Cha, Agi) : **+4 maximum** par objet |
| **PDF Équipements et forgemagie** | Table niveau 19-20 : bonus **8** par palier, forgemagie +2 |
| **Seeder characteristic_object** | max 8, formules paliers jusqu’à 8 |

**Options** :
1. **Garder max 8** dans le seeder pour la conversion Dofus et documenter que la règle 2.6.1 (+4) est une limite de **création manuelle** ou de **validation** à appliquer côté UI.
2. **Aligner sur 2.6.1** : passer max à 4 pour ces caractéristiques et adapter les formules.

---

## 5. Fichiers de référence

| Fichier | Rôle |
|---------|------|
| `COHERENCE_FICHE_PERSO_VS_PROJET.md` | Fiche personnage ↔ BDD |
| `COHERENCE_SEEDER_REGLES.md` | Règles ↔ seeders (dans 420-Règles) |
| `PROPOSITIONS_FORMULES_ET_PROPRIETES.md` | Propositions de formules (objet, creature) |

---

## 6. Actions recommandées

- [ ] Décider : max 4 ou 8 pour Vitalité/Force/etc. par objet (section 4)
- [ ] Documenter la décision dans COHERENCE_SEEDER_REGLES
- [ ] Vérifier que la validation côté app respecte les plafonds des règles 2.6.1

---

*Document généré à partir des règles 2.2.1, 2.2.2, 2.6.1, COHERENCE_SEEDER_REGLES, fiches PDF et seeders.*
