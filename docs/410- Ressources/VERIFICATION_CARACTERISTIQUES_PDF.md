# Vérification caractéristiques — Conformité aux PDF

Audit réalisé en comparant les seeders `characteristic_creature` et `characteristic_object` avec les PDF de référence.

---

## 1. Caractéristiques.pdf → _creature

### 1.1 Liste PDF (Caractéristiques.pdf)

| Caractéristique PDF | characteristic_key | db_column | Présent |
|---------------------|-------------------|-----------|---------|
| Niveau | level_creature | level | ✓ |
| PA | action_points_creature | pa | ✓ |
| PM | movement_points_creature | pm | ✓ |
| Initiative | initiative_creature | ini | ✓ |
| Portée (PO) | range_creature | po | ✓ |
| Nombre d'invocation | summoning_creature | invocation | ✓ |
| Vitalité | vitality_creature | vitality | ✓ |
| Modificateur Vitalité | modifier_vitality_creature | — (calculé) | ✓ |
| Sagesse | wisdom_creature | sagesse | ✓ |
| Modificateur Sagesse | modifier_wisdom_creature | — (calculé) | ✓ |
| Force | strength_creature | strong | ✓ |
| Modificateur Force | modifier_strength_creature | — (calculé) | ✓ |
| Intelligence | intelligence_creature | intel | ✓ |
| Modificateur Intelligence | modifier_intelligence_creature | — (calculé) | ✓ |
| Chance | chance_creature | chance | ✓ |
| Modificateur Chance | modifier_chance_creature | — (calculé) | ✓ |
| Agilité | agility_creature | agi | ✓ |
| Modificateur Agilité | modifier_agility_creature | — (calculé) | ✓ |
| Bonus jet sauvegarde (6) | save_vitality/wisdom/strength/intelligence/chance/agility_creature | save_*_bonus, save_*_mastery | ✓ |
| Bonus de touche | hit_bonus_creature | touch | ✓ |
| Classe d'armure | armor_class_creature | ca | ✓ |
| Esquive PA | dodge_action_points_creature | dodge_pa | ✓ |
| Esquive PM | dodge_movement_points_creature | dodge_pm | ✓ |
| Tacle | tackle_creature | tacle | ✓ |
| Fuite | dodge_creature | fuite | ✓ |
| Dommage fixe neutre/terre/feu/air/eau | fixed_damage_*_creature | do_fixe_* | ✓ |
| Dommage fixe Multiples | fixed_damage_multiple_creature | — | ✓ |
| Bonus de critiques | critical_hit_creature | critical_hit | ✓ |
| Bonus de Soins | heal_bonus_creature | heal_bonus | ✓ |
| Résistance fixe neutre/terre/feu/air/eau | fixed_resistance_*_creature | res_fixe_* | ✓ |
| Résistance neutre/terre/feu/air/eau % | resistance_*_creature | res_* | ✓ |
| Points de vie | life_points_creature | life | ✓ |
| Dés de vie | hit_dice_creature | — (calculé) | ✓ |
| Réserve de Wakfu | wakfu_reserve_creature | — (calculé) | ✓ |
| Bonus de maîtrise | mastery_bonus_creature | — (calculé) | ✓ |
| Compétences (18) | *_bonus, *_mastery | acrobatie_bonus, etc. | ✓ |
| Compétences passives | — | perspicacite_bonus, etc. | ✓ |

### 1.2 Non présentes dans le PDF équipement (pas de modification équipement)

- **Points de vie temporaire** : pas de colonne dédiée (géré en combat, non stocké)
- **Points de boulier** : pas de colonne dédiée (à vérifier si nécessaire)

### 1.3 Corrections appliquées

- **Entrées 3–6 supprimées** : les doublons `save_agility_creature`, `save_chance_creature`, `save_intelligence_object`, `save_wisdom_object` avec `db_column` inexistants (sav_agi, sav_chance, sav_intel, sav_sagesse) ont été retirés. Les entrées 50–55 (save_vitality_creature, etc.) restent les seules et sont correctes.

---

## 2. Equipements et forgemagie.pdf → _object

### 2.1 Liste PDF (Equipements et forgemagie.pdf)

| Équipement | Caractéristique PDF | characteristic_key | Présent |
|------------|---------------------|-------------------|---------|
| ARMES | Bonus de touche | hit_bonus_object | ✓ |
| ARMES | Dommage fixe neutre/terre/feu/air/eau | fixed_damage_*_object | ✓ |
| ARMES | Dommage fixe multiple | fixed_damage_multiple_object | ✓ |
| CHAPEAUX | Compétences | skills_object | ✓ |
| CHAPEAUX | Points de vie | life_points_max_object | ✓ |
| CHAPEAUX | Vitalité, Sagesse | vitality_object, wisdom_object | ✓ |
| CHAPEAUX | Bonus sauvegarde Vit/Sag | save_vitality_wisdom_object | ✓ |
| CHAPEAUX | Compétences passives | passive_skills_object | ✓ |
| CAPES | Initiative | initiative_object | ✓ |
| CAPES | Compétences | skills_object | ✓ |
| CAPES | Points de vie | life_points_max_object | ✓ |
| CAPES | Force, Intel, Chance, Agi | strength/intelligence/chance/agility_object | ✓ |
| CAPES | Bonus sauvegarde For/Int/Cha/Agi | save_strength_intelligence_chance_agility_object | ✓ |
| AMULETTES | Points de vie | life_points_max_object | ✓ |
| AMULETTES | PA | action_points_object | ✓ |
| AMULETTES | Esquive PA | dodge_action_points_object | ✓ |
| AMULETTES | Bonus de Critiques | critical_hit_object | ✓ |
| BOTTES | Points de vie | life_points_max_object | ✓ |
| BOTTES | PM | movement_points_object | ✓ |
| BOTTES | Esquive PM | dodge_movement_points_object | ✓ |
| BOTTES | Initiative | initiative_object | ✓ |
| ANNEAUX | Nombre d'invocation | summoning_object | ✓ |
| ANNEAUX | PO | range_object | ✓ |
| ANNEAUX | Bonus de soin | heal_bonus_object | ✓ |
| ANNEAUX | Points de vie | life_points_max_object | ✓ |
| CEINTURES | Tacle | tackle_object | ✓ |
| CEINTURES | Fuite | dodge_object | ✓ |
| CEINTURES | Recharge réserve Wakfu | wakfu_recharge_object | ✓ |
| BOUCLIERS | Classe d'armure | armor_class_object | ✓ |
| BOUCLIERS | Résistance fixe * | fixed_resistance_*_object | ✓ |
| BOUCLIERS | Résistance 50% | resistance_50_percent_object | ✓ |
| BOUCLIERS | Invulnérabilité 100% | invulnerability_100_percent_object | ✓ |

### 2.2 Caractéristiques _object en plus du PDF


### 2.3 Meta (hors PDF équipements)

- level_object, rarity_object, price_object, weight_object : nécessaires pour la gestion des objets.

---

## 3. Synthèse

### Creature
- **Toutes** les caractéristiques du PDF Caractéristiques sont présentes dans characteristic_creature.
- **Corrigé** : les 4 entrées doublons (indices 3–6) avec db_column inexistants ont été supprimées.

### Object
- **Toutes** les caractéristiques du PDF Equipements sont présentes dans characteristic_object.
- **Rien en moins** par rapport au PDF.

---

*Document généré pour la vérification conformité PDF — 2026-03.*
