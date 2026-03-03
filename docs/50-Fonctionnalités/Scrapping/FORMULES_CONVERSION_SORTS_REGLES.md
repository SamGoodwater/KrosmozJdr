# Formules de conversion des sorts — Référence règles

**Objectif** : Lier les **formules de conversion** du groupe spell (`characteristic_spell`) aux **barèmes et règles** du jeu (docs 400- Jeu/420- Règles) pour que les valeurs Dofus (dés, fixes) soient converties en échelle Krosmoz.

**Références** : [PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md](./PLAN_IMPLEMENTATION_PHASE3_CONVERSION_VALEURS_EFFETS.md), [PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md](../Characteristics-DB/PROPRIETES_CONVERSION_DOFUS_KROSMOZ.md), règles 5.3.1, 5.2.3, 3.3.2.

---

## 1. Sources règles

| Document | Contenu utilisé |
|----------|-----------------|
| **5.3.1 Résistances et dégâts** | Barèmes dégâts/soins par niveau (1d6+mod à 5d6+mod), PV moyens, résistances. |
| **5.2.3 Sorts et aptitudes** | Coûts PA (3–4 simple, 5 fort, 2–3 bonus), bornes dégâts/soins par niveau. |
| **3.3.2 Mécaniques de lancement** | Coûts 3–4 PA / 5 PA / 2–3 PA, portée en cases, ligne de vue 0/1. |

---

## 2. Variable d’entrée `d`

Pour les effets de sort, la conversion reçoit une seule variable **`d`** :

- **Dés** : `d = diceNum × (diceSide + 1) / 2` (moyenne).
- **Valeur fixe** : `d = value`.

Ex. 2d6 → d = 7 ; 13d18 → d ≈ 123,5.

---

## 3. Formules par caractéristique (groupe spell)

### 3.1 power_spell (dégâts / soins)

**Règles (5.3.1, 5.2.3)** :
- Dégâts par sort : niv 1–3 → 1d6+mod (~5–10), niv 4–7 → 2d6+mod, …, niv 16–20 → 5d6+mod (~40–50).
- Soins : 1d4+mod à 5d4+mod selon niveau.
- Échelle Krosmoz : valeur « typique » par sort environ **5 à 25** (ordre de grandeur des moyennes de dés + mod).

**Échelle Dofus** : `d` peut aller de ~3 (1d6) à 200+ (sorts haut niveau). Il faut **compresser** vers 1–25.

**Formule retenue** :
```text
round(min(30, max(1, 3 + 19 * pow(max(1,[d])/200, 0.5))))
```
- `d = 5` → ~5
- `d = 20` → ~9
- `d = 50` → ~13
- `d = 100` → ~17
- `d = 200` → ~22

**Limites** : min 0, max 30 (cohérent avec 5.3.1).

**Échantillons (optionnel)** :
- `conversion_dofus_sample` : ex. `{"1":5,"50":50,"100":120,"200":220}` (ordre de grandeur d par niveau Dofus).
- `conversion_krosmoz_sample` : ex. `{"1":5,"10":12,"15":17,"20":22}` (cibles Krosmoz par niveau 1–20).

---

### 3.2 action_points_spell (coût en PA)

**Règles (3.3.2.1, 5.2.3)** : 3–4 PA simple, 5 PA fort, 2–3 PA bonus ; max 12 PA.

**Dofus** : coût souvent 1–8. Même ordre de grandeur que Krosmoz.

**Formule** : `round([d])` ou `[d]` (pass-through + clamp 0–12 par limites BDD).

---

### 3.3 range_spell, area_spell, spell_range_min_spell, spell_range_max_spell

**Règles (3.3.2.2)** : portée en cases (PO), zone en cases.

**Formule** : `round([d])` ou `[d]` (entiers, clamp si min/max définis).

---

### 3.4 sight_line_spell, is_magic_spell, range_editable_spell, number_between_two_cast_editable_spell

**Règles** : champs 0/1 (oui/non).

**Formule** : `min(1, max(0, round([d])))` (déjà en place).

---

### 3.5 cast_per_turn_spell, cast_per_target_spell, number_between_two_cast_spell

**Règles** : entiers (nombre de lancers, délai en tours).

**Formule** : `round([d])` ou `[d]`.

---

## 4. Synthèse des formules à mettre en BDD

| characteristic_key | conversion_formula | min | max | Note |
|--------------------|--------------------|-----|-----|------|
| power_spell | `round(min(30, max(1, 3 + 19 * pow(max(1,[d])/200, 0.5))))` | 0 | 30 | Dégâts/soins (5.3.1, 5.2.3) |
| action_points_spell | `[d]` | 0 | 12 | Coût PA (3.3.2.1) |
| range_spell | `round([d])` | 0 | — | Portée cases |
| area_spell | `round([d])` | 0 | — | Zone cases |
| spell_range_min_spell | `round([d])` | 0 | — | Portée min |
| spell_range_max_spell | `round([d])` | 0 | — | Portée max |
| cast_per_turn_spell | `round([d])` | 0 | — | Lancers/tour |
| cast_per_target_spell | `round([d])` | 0 | — | Lancers/cible/tour |
| number_between_two_cast_spell | `round([d])` | 0 | — | Délai tours |
| category_spell, element_spell | `[d]` | selon BDD | — | Pass-through + clamp |
| sight_line_spell, is_magic_spell, … (0/1) | `min(1, max(0, round([d])))` | 0 | 1 | Déjà en place |

---

## 5. Fichiers impactés

- **Données** : `database/seeders/data/characteristic_spell.php` (ou mise à jour via export BDD + ré-import).
- **Doc** : [CARACTERISTIQUES_EFFETS_PAR_ACTION.md](./CARACTERISTIQUES_EFFETS_PAR_ACTION.md) (§ 2.5), présent document.

---

## 6. Ajustements possibles

- **power_spell** : si les retours terrain montrent des valeurs trop hautes ou basses, modifier les constantes (3, 19, 200, 0.5) ou la borne 30 ; les barèmes 5.3.1 restent la référence.
- **Portée / zone** : si Dofus utilise une échelle différente (ex. cases × 2), ajouter un facteur dans la formule (ex. `round([d]/2)`).
- **Échantillons** : remplir `conversion_dofus_sample` et `conversion_krosmoz_sample` pour power_spell permet d’utiliser l’outil de suggestion de formules (admin) et de documenter la courbe.
