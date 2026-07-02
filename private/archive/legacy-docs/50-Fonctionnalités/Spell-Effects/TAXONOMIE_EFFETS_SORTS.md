# Taxonomie des effets de sort — Krosmoz JDR

Référence **exhaustive** des types d’effets qu’un sort peut produire. Chaque type est paramétrable en base de données et peut être associé à un ou plusieurs sorts avec des valeurs (min, max, dés, durée, cible, etc.).

---

## 1. Principes

- **Type d’effet** : définition générique (ex. « Dégâts Terre », « Soin », « Vol de PA »). Stocké en BDD dans `spell_effect_types`.
- **Effet d’un sort** : instance liée à un sort précis, avec valeurs et options (ex. « Ce sort inflige 10–20 dégâts Terre »). Stocké dans `spell_effects`.
- **Éléments** : Neutre, Terre, Feu, Eau, Air (et combinaisons si besoin). Un effet peut être **élémental** (lié à un élément) ou **neutre** (sans élément).
- **Valeurs** : selon le type, on stocke `value_min`, `value_max`, et/ou `dice_num` + `dice_side` (ex. 2d6).

---

## 2. Catégories d’effets

| Catégorie | Code | Description |
|-----------|------|-------------|
| **Dégâts** | `damage` | Inflige des dégâts (PV) à la cible. Souvent élémental. |
| **Soin** | `heal` | Restaure des PV. |
| **Bouclier / Absorption** | `shield` | Absorbe un certain nombre de dégâts avant de toucher les PV. |
| **PA (Points d’action)** | `ap` | Modifie les PA (gain, perte, vol). |
| **PM (Points de mouvement)** | `pm` | Modifie les PM (gain, perte, vol). |
| **PO (Portée)** | `range` | Modifie la portée (buff/debuff). |
| **Buff de caractéristique** | `buff_stat` | Augmente temporairement une stat (force, agilité, etc.). |
| **Debuff de caractéristique** | `debuff_stat` | Réduit temporairement une stat. |
| **Buff de dégâts** | `buff_damage` | Augmente les dégâts infligés. |
| **Debuff de dégâts** | `debuff_damage` | Réduit les dégâts infligés. |
| **Résistance** | `resistance` | Augmente ou réduit une résistance (élément). |
| **État / Altération** | `state` | Applique un état (étourdissement, gel, poison, etc.). |
| **Placement** | `placement` | Pousse, tire ou déplace la cible (cases). |
| **Téléportation** | `teleport` | Déplace le lanceur ou la cible vers une case. |
| **Invocation** | `summon` | Invoque une créature (lien vers monstre). |
| **Glyphe / Piège** | `glyph_trap` | Pose un glyphe ou un piège sur une case. |
| **Zone** | `zone` | Crée une zone d’effet (feu, eau, etc.). |
| **Critique** | `critical` | Modifie les dégâts critiques ou applique un critique. |
| **Réflexion** | `reflect` | Renvoie tout ou partie des dégâts à l’attaquant. |
| **Vol (PV, PA, PM)** | `steal` | Vole des PV, PA ou PM à la cible. |
| **Dégâts dans le temps** | `damage_over_time` | Inflige des dégâts chaque tour (poison, brûlure, etc.). |
| **Soin dans le temps** | `heal_over_time` | Soigne chaque tour (régénération). |
| **Blocage** | `lock` | Bloque les déplacements, les lancers de sort ou les actions. |
| **Ligne de vue** | `line_of_sight` | Bloque ou rétablit la ligne de vue. |
| **Invisibilité** | `invisibility` | Rend invisible (totalement ou partiellement). |
| **Prospection / Butin** | `prospecting` | Modifie la prospection (butin). |
| **Autre** | `other` | Effet non classé (description libre). |

---

## 3. Liste exhaustive des types d’effets (définitions en BDD)

Les lignes ci-dessous correspondent aux **types** enregistrables dans `spell_effect_types`. Le champ `category` reprend le code de la table ci-dessus. `slug` est un identifiant unique (ex. `damage_earth`).

### 3.1 Dégâts (damage)

| Slug | Nom | Élément | Description |
|------|-----|---------|-------------|
| `damage_neutral` | Dégâts neutres | Neutre | Dégâts physiques / neutres. |
| `damage_earth` | Dégâts Terre | Terre | Dégâts de type Terre. |
| `damage_fire` | Dégâts Feu | Feu | Dégâts de type Feu. |
| `damage_water` | Dégâts Eau | Eau | Dégâts de type Eau. |
| `damage_air` | Dégâts Air | Air | Dégâts de type Air. |

### 3.2 Soin et bouclier

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `heal` | Soin | heal | Restaure des PV. |
| `heal_over_time` | Soin dans le temps | heal_over_time | Soigne X PV par tour pendant N tours. |
| `shield` | Bouclier | shield | Absorbe X dégâts. |

### 3.3 Ressources de combat (PA, PM, PO)

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `ap_gain` | Gain de PA | ap | Ajoute des PA. |
| `ap_loss` | Perte de PA | ap | Retire des PA. |
| `ap_steal` | Vol de PA | ap | Vole des PA à la cible. |
| `pm_gain` | Gain de PM | pm | Ajoute des PM. |
| `pm_loss` | Perte de PM | pm | Retire des PM. |
| `pm_steal` | Vol de PM | pm | Vole des PM à la cible. |
| `range_buff` | Bonus de portée | range | Augmente la portée. |
| `range_debuff` | Malus de portée | range | Réduit la portée. |

### 3.4 Buffs / Debuffs de caractéristiques

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `buff_strength` | Bonus Force | buff_stat | Augmente la Force. |
| `buff_agility` | Bonus Agilité | buff_stat | Augmente l’Agilité. |
| `buff_intelligence` | Bonus Intelligence | buff_stat | Augmente l’Intelligence. |
| `buff_chance` | Bonus Chance | buff_stat | Augmente la Chance. |
| `buff_wisdom` | Bonus Sagesse | buff_stat | Augmente la Sagesse. |
| `buff_vitality` | Bonus Vitalité | buff_stat | Augmente la Vitalité. |
| `debuff_strength` | Malus Force | debuff_stat | Réduit la Force. |
| `debuff_agility` | Malus Agilité | debuff_stat | Réduit l’Agilité. |
| `debuff_intelligence` | Malus Intelligence | debuff_stat | Réduit l’Intelligence. |
| `debuff_chance` | Malus Chance | debuff_stat | Réduit la Chance. |
| `debuff_wisdom` | Malus Sagesse | debuff_stat | Réduit la Sagesse. |
| `debuff_vitality` | Malus Vitalité | debuff_stat | Réduit la Vitalité. |

### 3.5 Dégâts / Résistances

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `buff_damage` | Bonus dégâts | buff_damage | Augmente les dégâts infligés. |
| `debuff_damage` | Malus dégâts | debuff_damage | Réduit les dégâts infligés. |
| `resistance_percent` | Résistance % | resistance | Modifie une résistance en %. |
| `resistance_fixed` | Résistance fixe | resistance | Modifie une résistance en valeur fixe. |

### 3.6 États / Altérations

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `state_stun` | Étourdissement | state | Cible ne peut pas agir. |
| `state_freeze` | Gel | state | Cible gelée (ne peut pas agir / se déplacer). |
| `state_poison` | Poison | state | Dégâts dans le temps (poison). |
| `state_burn` | Brûlure | state | Dégâts dans le temps (feu). |
| `state_slow` | Ralentissement | state | Réduit PM ou déplacements. |
| `state_silence` | Silence | state | Ne peut pas lancer de sorts. |
| `state_blind` | Cécité | state | Ne peut pas cibler à distance / ligne de vue. |
| `state_curse` | Malédiction | state | Effet négatif générique. |
| `state_other` | Autre état | state | État personnalisé (nom/description). |

### 3.7 Placement et déplacement

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `push` | Pousser | placement | Pousse la cible de X cases. |
| `pull` | Tirer | placement | Tire la cible de X cases. |
| `teleport_self` | Téléportation (soi) | teleport | Téléporte le lanceur. |
| `teleport_target` | Téléportation (cible) | teleport | Téléporte la cible. |

### 3.8 Invocation et zones

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `summon` | Invocation | summon | Invoque une créature (réf. monstre). |
| `glyph` | Glyphe | glyph_trap | Pose un glyphe sur une case. |
| `trap` | Piège | glyph_trap | Pose un piège. |
| `zone_damage` | Zone de dégâts | zone | Zone qui inflige des dégâts. |
| `zone_heal` | Zone de soin | zone | Zone qui soigne. |
| `zone_effect` | Zone d’effet | zone | Zone avec effet personnalisé. |

### 3.9 Critique, réflexion, vol

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `critical_bonus` | Bonus critique | critical | Augmente les dégâts critiques. |
| `critical_hit` | Coup critique | critical | Applique un critique. |
| `reflect_damage` | Renvoi de dégâts | reflect | Renvoie tout ou partie des dégâts. |
| `steal_hp` | Vol de PV | steal | Vole des PV à la cible. |
| `steal_ap` | Vol de PA | steal | Alias vol PA. |
| `steal_pm` | Vol de PM | steal | Alias vol PM. |

### 3.10 Dégâts / Soins dans le temps

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `damage_over_time` | Dégâts dans le temps | damage_over_time | X dégâts par tour, N tours. |
| `heal_over_time` | Soin dans le temps | heal_over_time | X soin par tour, N tours. |

### 3.11 Contrôle et visibilité

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `lock_movement` | Blocage déplacement | lock | Empêche de se déplacer. |
| `lock_cast` | Blocage sort | lock | Empêche de lancer des sorts. |
| `line_of_sight_block` | Bloquer ligne de vue | line_of_sight | Bloque la ligne de vue. |
| `invisibility` | Invisibilité | invisibility | Rend invisible. |

### 3.12 Prospection et autre

| Slug | Nom | Catégorie | Description |
|------|-----|-----------|-------------|
| `prospecting` | Prospection | prospecting | Modifie la prospection (butin). |
| `other` | Autre effet | other | Effet non classé (description libre). |

---

## 4. Schéma de données (résumé)

- **spell_effect_types** : id, name, slug (unique), category, description, value_type (fixed / dice / percent), element (nullable), unit, is_positive, sort_order, dofusdb_effect_id (nullable), created_at, updated_at.
- **spell_effects** : id, spell_id (FK), spell_effect_type_id (FK), value_min, value_max, dice_num, dice_side, duration (tours), target_scope (self / ally / enemy / cell / zone), zone_shape (nullable), dispellable, order, raw_description (nullable), created_at, updated_at. Optionnel : element_id sur l’instance si différent du type.

La liste ci-dessus sert de **référence pour le seeder** des types initiaux et pour l’interface d’administration.
