# Création d’une caractéristique — référence et cas de figure

Ce document complète le fichier exemple [`caracterictis-global.json`](./caracterictis-global.json) (schéma `_schema_version` **2**). Il décrit les champs, le vocabulaire produit, et les combinaisons usuelles à la création ou à l’import d’une caractéristique.

---

## 1. Où vivent les données en base

| Couche | Table | Rôle |
|--------|--------|------|
| Commune | `characteristics` | Identité (`key`), libellés, aide, type, groupe, tri, `value_overrides`, médias |
| Groupe | `characteristic_creature` / `characteristic_object` / `characteristic_spell` | Limites, formules, conversion, normes, mapping (voir § 3) |

Le JSON d’exemple **regroupe** ces deux niveaux en blocs logiques (`general`, `display`, etc.) sans dupliquer le modèle relationnel : à l’import, on répartit vers les tables ci-dessus.

---

## 2. Structure recommandée du JSON (schéma v2)

| Bloc | Contenu |
|------|---------|
| **general** | `key`, `name`, `short_name`, `helper`, `descriptions`, `group`, `type`, `unit`, `sort_order`, `linked_to_characteristic_id` |
| **display** | `icon`, `value_overrides`, `hide_when_empty` (voir § 6) |
| **value_and_norms** | `min`, `max`, `formula`, `formula_display`, `default_value`, `norms_*`, `value_available` (selon groupe) |
| **conversion** | `conversion_formula`, `conversion_function`, `conversion_dofus_sample`, `conversion_krosmoz_sample`, `conversion_sample_rows` |
| **mapping** | `entity`, `dofusdb_characteristic_id`, `db_column`, champs spécifiques (`labels`, `validation`, `item_type_ids`, prix, etc.) |
| **economy_object** | (objets uniquement) `forgemagie_max` — voir § 5 |
| **relations** | Liens **documentaires** entre clés (`characteristic_key`), hors contrainte SQL |

---

## 3. Champs du pivot (FAQ)

### 3.1 `entity`

- **`"*"`** : défaut pour **toutes** les entités du groupe (ex. tous les types de monstres / classes / PNJ pour le pivot créature).
- **Valeur précise** (`monster`, `class`, `npc`, `item`, … selon le modèle) : **surcharge** pour ce type uniquement. Les autres types utilisent la ligne `*` ou, à défaut, les valeurs par défaut métier.
- Ce n’est **pas** un filtre d’affichage joueur : c’est un **périmètre technique** de définition des règles.

### 3.2 `dofusdb_characteristic_id`

Identifiant renvoyé par l’**API DofusDB** (ex. liste des caractéristiques) pour **retrouver la même notion** lors d’un import ou d’un scrapping. Ce n’est **pas** l’ID d’une ligne `characteristics` du projet, ni une « copie » du JSON : c’est un **lien vers le référentiel DofusDB**.

### 3.3 `key` (table `characteristics`) et `db_column` (pivot)

| Champ | Rôle |
|-------|------|
| **key** | Identifiant **stable** et lisible dans toute l’appli (`range_spell`, `action_points_spell`). Unique par ligne `characteristics`. |
| **db_column** | Nom de la **colonne SQL** sur la table métier **quand** la valeur y est stockée (`po`, `pa`, …). Peut être **null** (souvent objets : valeur issue uniquement d’un barème JSON). |

On ne les **fusionne** pas dans la base : ils répondent à deux besoins (logique métier vs stockage). Une surcharge par `entity` peut théoriquement associer des règles différentes ; en pratique `key` + groupe suffisent le plus souvent.

### 3.4 `conversion_sample_rows`

Table optionnelle de lignes du type : niveau / valeur Dofus, niveau / valeur Krosmoz. Sert à :

- **documenter** un barème dans l’interface d’administration ;
- donner des **points de contrôle** visuels ;

la **formule** (`conversion_formula` / `conversion_function`) reste la source de vérité pour le calcul automatique. Voir l’édition dans `Admin/characteristics/Index.vue` (table d’échantillons).

### 3.5 `color_false` et `icon_false`

- **`color_false`** : colonne **supprimée** (migration `2026_04_18_120000_drop_color_false_from_characteristics_table.php`). **Ne plus utiliser** ; utiliser `value_overrides` avec une entrée `value: false` (ou équivalent booléen / chaîne selon résolution).
- **`icon_false`** : peut encore exister en base pour les **booléens** (icône lorsque la valeur est fausse). La priorité d’affichage favorise `value_overrides` ; `icon_false` est un **repli**. Dans les nouveaux contenus, on peut laisser `null` et tout passer par `value_overrides`.

---

## 4. Forgemagie (objets)

En schéma JSON simplifié :

- **`forgemagie_max`** : entier ≥ 0. **`0` ou absence** ⇒ pas de forgemagie pour cette caractéristique.
- Inutile de dupliquer un booléen `forgemagie_allowed` : il est **redondant** avec `forgemagie_max === 0`.

*Note : la base peut encore porter les deux colonnes pour compatibilité ; à l’import on peut dériver `forgemagie_allowed` de `forgemagie_max > 0`.*

---

## 5. Affichage : `hide_when_empty` et surcharges

- **`hide_when_empty`** (convention du JSON exemple) : si `true`, l’UI peut **masquer** la ligne lorsque la valeur est vide / nulle / non pertinente (ex. PO créature à 0 sans besoin de surcharge).
- **`value_overrides`** : icône / couleur / sous-texte **selon la valeur** (voir [SURCHARGES_VISUELLES_PAR_VALEUR.md](../../50-Fonctionnalités/Characteristics-DB/SURCHARGES_VISUELLES_PAR_VALEUR.md)). Pour un PO créature sans besoin visuel particulier : `null` ou tableau vide.

---

## 6. Rédaction des textes (helpers, descriptions)

Pour les textes **visibles ou aidant le joueur** :

- Éviter **« entité »** : nommer ce dont il s’agit (personnage, monstre, sort, objet, effet sur la carte du sort, etc.).
- Éviter **« sous-effet »** : parler d’**« effet du sort »**, d’**« ligne d’effet »**, ou du **libellé métier** (dommages, portée, etc.).

Les textes **techniques** (seeders internes, commentaires outil) peuvent garder un vocabulaire plus précis.

---

## 7. Cas de figure à la création (checklist)

### 7.1 Caractéristique numérique avec colonne SQL (ex. créature)

- `general.type` = `int` (ou adapté).
- `mapping.db_column` renseigné (ex. `po`).
- `conversion` si passage Dofus → Krosmoz.
- `display.hide_when_empty` si la ligne ne doit pas apparaître quand la valeur est vide / zéro.
- Pas de `value_overrides` si aucun besoin visuel par valeur.

### 7.2 Caractéristique objet avec barème de niveau sans colonne SQL

- `mapping.db_column` = `null`.
- `value_and_norms.formula` : JSON (niveau → valeur) ou chaîne JSON en base.
- `mapping.item_type_ids` : types d’objets concernés.
- `economy_object.forgemagie_max` : plafond forgemagie (0 = désactivé).

### 7.3 Caractéristique « effet du sort » (pivot spell)

- `mapping.db_column` peut pointer vers les **paramètres d’effet** (ex. `po` sur la ligne d’effet), pas forcément une colonne de `spells` seule.
- `conversion` souvent plus riche (`round(min(…))`, etc.).
- `display.value_overrides` si plusieurs valeurs doivent changer icône / légende (ex. 0, plafond).

### 7.4 Booléen

- `general.type` = `bool`.
- Préférer `value_overrides` pour vrai / faux (et couleurs) plutôt que `icon_false` seul.
- Ne pas utiliser `color_false`.

### 7.5 Normes (grilles, chartes)

- Renseigner `norms_grid`, `norms_conditions`, `norms_description`, `norms_help_section_id` quand la caractéristique est rattachée à une **charte** ou une **grille de référence** (voir docs Normes / sections CMS).

### 7.6 Surcharge par type d’entité (`entity` ≠ `*`)

- Créer une ligne pivot avec `entity` = `monster` (par ex.) pour des min/max ou formules **spécifiques** ; garder une ligne `*` comme défaut pour le reste.

### 7.7 Même concept sur plusieurs groupes (ex. PA créature vs PA sort)

- Plusieurs lignes dans `characteristics` avec des **`key` différentes** (`action_points_creature`, `action_points_spell`), éventuellement reliées par `linked_to_characteristic_id` ou par convention documentaire dans `relations`.

### 7.8 Documentation d’un barème sans formule unique

- Utiliser `conversion_sample_rows` pour des **exemples** tabulés, tout en gardant une `conversion_formula` ou `conversion_function` si le moteur en a besoin.

### 7.9 Lien scrapping DofusDB

- Renseigner `dofusdb_characteristic_id` dès que la caractéristique est synchronisée avec un ID DofusDB stable.

---

## 8. Fichiers liés

- [SURCHARGES_VISUELLES_PAR_VALEUR.md](../../50-Fonctionnalités/Characteristics-DB/SURCHARGES_VISUELLES_PAR_VALEUR.md)
- [ARCHITECTURE_CARACTERISTIQUES_SPELL.md](../../50-Fonctionnalités/Characteristics-DB/ARCHITECTURE_CARACTERISTIQUES_SPELL.md)
- Schéma SQL : [SCHEMA.md](../../20-Content/SCHEMA.md) (tables `characteristics`, `characteristic_*`)
