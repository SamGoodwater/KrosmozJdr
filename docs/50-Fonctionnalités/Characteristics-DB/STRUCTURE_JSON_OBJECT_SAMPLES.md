# Structure du JSON `characteristics_object_samples.json`

Ce fichier est produit par la commande `php artisan characteristics:extract-object-samples`. Il contient des **échantillons niveau → valeur** issus des **équipements** DofusDB, avec un **lien formel** entre les caractéristiques DofusDB et les clés Krosmoz.

---

## Notation formalisée

Pour utiliser le JSON de façon cohérente, les termes suivants sont définis dans la racine du fichier (`notation`) et dans le fichier de mapping DofusDB → Krosmoz.

| Terme | Définition | Source |
|-------|------------|--------|
| **dofusdb_characteristic_id** | Identifiant numérique d’une caractéristique dans l’API DofusDB. Présent dans `GET /characteristics` (champ `id`) et dans chaque effet d’item : `item.effects[].characteristic`. | [API DofusDB /characteristics](https://api.dofusdb.fr/characteristics?$skip=0&visible=true&$limit=50) |
| **characteristic_key** | Clé Krosmoz du seeder (groupe object), ex. `pa_object`, `strong_object`, `pm_object`. | `database/seeders/data/characteristic_object.php` |
| **conversion_dofus_sample** | Objet `{ niveau_dofus: valeur_moyenne }` : pour chaque tranche de niveau Dofus (1, 10, 20, …, 200), la valeur moyenne observée sur les équipements. | Calculée par la commande d'extraction |
| **conversion_krosmoz_sample** | Objet `{ niveau_krosmoz: valeur_cible }` (niveaux 1, 4, 8, 12, 16, 20). | Fichier `object_krosmoz_samples.json` (règles 2.2.1/2.2.2) |


Le **lien** entre les deux mondes est stocké dans :

- **Fichier de mapping** (source de vérité) : `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json`  
  → `mapping` : `dofusdb_characteristic_id` (string) → `characteristic_key`
- **Dans le JSON produit** : la section `dofusdb_characteristic_id_to_characteristic_key` reprend ce mapping pour que le fichier soit autoportant.

**Référence complète (toutes les caractéristiques DofusDB)** : [DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md](./DOFUSDB_CHARACTERISTIC_ID_REFERENCE.md) — liste exhaustive id ↔ keyword, nom (fr), characteristic_key Krosmoz (ou « — » si non utilisée).

Exemple de correspondance (issue de l’API /characteristics) :

| dofusdb_characteristic_id | keyword (API) | characteristic_key |
|---------------------------|----------------|----------------------|
| 0 | hitPoints | pv_max_object |
| 1 | actionPoints | pa_object |
| 5 | level | level_object |
| 10 | strength | strong_object |
| 11 | vitality | vitality_object |
| 23 | movementPoints | pm_object |
| 27 | DodgeApLostProbability | esquive_pa_object |
| 28 | DodgeMpLostProbability | esquive_pm_object |
| 40 | weight | weight_object |
| 44 | initiative | ini_object |

---

## Vue d’ensemble du JSON

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  characteristics_object_samples.json                                          │
├─────────────────────────────────────────────────────────────────────────────┤
│  source, entity, scope              →  D’où viennent les données              │
│  meta                              →  description, reference_levels,          │
│                                        characteristic_keys_without_samples    │
│  notation                          →  Définition des termes                  │
│  dofusdb_characteristic_id_to_characteristic_key  →  Lien id → clé Krosmoz   │
│  characteristic_key_metadata       →  label_fr par clé (affichage)           │
│  by_characteristic_key             →  conversion_dofus_sample +               │
│                                        conversion_dofus_sample_reference +     │
│                                        conversion_krosmoz_sample +             │
│                                        conversion_krosmoz_sample_reference     │
│  by_dofusdb_characteristic_id      →  Par id DofusDB (brut)                  │
│  by_effect_id                      →  Par effectId (type d’effet)            │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 1. Racine : `source`, `entity`, `scope`

| Clé      | Valeur         | Signification |
|----------|----------------|----------------|
| `source` | `"dofusdb"`    | Données issues de l’API DofusDB |
| `entity` | `"item"`       | Entité API = items (objets) |
| `scope`  | `"equipment"`  | Uniquement les **équipements** (armes, armures, etc.) |

---

## 2. `meta` — Contexte de l’extraction

| Clé | Signification |
|-----|----------------|
| `description` | Résumé d’usage : utiliser `conversion_dofus_sample_reference` pour pré-remplir l’admin. |
| `extracted_at` | Date/heure de l’extraction (ISO 8601) |
| `equipment_super_type_ids` | IDs des catégories DofusDB considérées comme équipement |
| `equipment_type_ids_count` | Nombre de typeIds équipement utilisés |
| `excluded_type_ids` | typeIds exclus (apparat, obsolètes, etc.) |
| `item_count` | Nombre d’équipements analysés |
| `level_buckets` | Tranches de niveau (1, 10, 20, …, 200) |
| `reference_levels` | Niveaux Dofus utilisés par l’admin : **1, 40, 80, 120, 160, 200** |
| `level_object_sample_levels` | Niveaux pour lesquels on a un échantillon pour `level_object` |
| `characteristic_keys_without_samples` | Liste des `characteristic_key` mappées mais **jamais observées** sur les équipements (données manquantes) |

---

## 3. `notation`

Définition des termes pour lire le reste du fichier :

- **dofusdb_characteristic_id** : id de `GET /characteristics` et de `item.effects[].characteristic`
- **characteristic_key** : clé Krosmoz (ex. `pa_object`, `strong_object`)
- **conversion_dofus_sample** : objet `{ niveau_dofus: valeur_moyenne }` par tranche
- **conversion_dofus_sample_reference** : sous-ensemble aux niveaux **reference_levels** (1, 40, 80, 120, 160, 200), avec interpolation si besoin ; **prêt pour pré-remplir l’admin** (même niveaux que le tableau de conversion par défaut)

---

## 4. `characteristic_key_metadata`

Pour chaque `characteristic_key` présente dans `by_characteristic_key`, un libellé d’affichage :

- **label_fr** : libellé français (ex. « PA », « Points de vie », « Force »).

Utile pour afficher les noms des caractéristiques sans dépendre du seeder.

---

## 5. `dofusdb_characteristic_id_to_characteristic_key`

Mapping **id DofusDB → clé Krosmoz** utilisé pour construire `by_characteristic_key`.  
Alimenté depuis `resources/scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz.json` (champ `mapping`).

Exemple : `"1": "pa_object"`, `"10": "strong_object"`, `"23": "pm_object"`.

---

## 6. `by_characteristic_key` — Par caractéristique Krosmoz (prêt à l’emploi)

Données **déjà nommées** en clés Krosmoz, avec **moyennes par tranche de niveau**.

- **level_object** : issu du niveau des équipements ; contient aussi `raw_level_counts` (effectif par tranche).
- **Autres clés** (pa_object, strong_object, pm_object, etc.) : issus des effets des items (`effect.characteristic`), agrégés par tranche puis fusionnés via le mapping.

Chaque entrée contient au moins :

- **conversion_dofus_sample** : `{ "10": 5, "20": 7, … }` (niveau → valeur moyenne)
- **conversion_dofus_sample_reference** : `{ "1": …, "40": …, "80": …, "120": …, "160": …, "200": … }` — **à utiliser pour pré-remplir l’admin** (niveaux alignés sur le tableau de conversion par défaut ; interpolation si une tranche manque)
- **item_count** : nombre d’effets agrégés (pour les clés dérivées des effets)
- **level_buckets** : tranches où la caractéristique est présente

Pour `level_object` uniquement : **raw_level_counts** (effectif d’équipements par tranche).

---

## 7. `by_dofusdb_characteristic_id` — Par id DofusDB (brut)

Même structure que par clé, mais indexé par **dofusdb_characteristic_id** (string).  
Utile pour vérifier les données avant application du mapping ou pour des ids non mappés.

---

## 8. `by_effect_id` — Par effectId (type d’effet)

Données groupées par **effectId** (type d’effet DofusDB), sans lien direct avec une caractéristique.  
Conservé pour analyse ou pour un mapping effectId → characteristic_key ultérieur si besoin.

---

## Récapitulatif du flux

1. **API DofusDB** : `/characteristics` donne **id** + **keyword** (hitPoints, actionPoints, strength, …).
2. **Effets des items** : chaque effet a **characteristic** (= id de l’API) et **from** / **to** (valeur).
3. **Mapping** : `dofusdb_characteristic_to_krosmoz.json` associe **id** → **characteristic_key**.
4. **Extraction** : la commande agrège par tranche de niveau (par **characteristic**), applique le mapping, remplit **by_characteristic_key**, ajoute **conversion_dofus_sample_reference** (niveaux 1, 40, 80, 120, 160, 200) et **characteristic_keys_without_samples**.
5. **Utilisation** : pour pré-remplir l’admin caractéristiques (object), utiliser **by_characteristic_key[clé].conversion_dofus_sample_reference** ; pour un libellé, **characteristic_key_metadata[clé].label_fr**.

---

## Qualité des données et bonnes pratiques

- **Données correctes** : les valeurs sont des **moyennes** (from+to)/2 par tranche de niveau sur les équipements collectés. Elles reflètent le jeu Dofus (bonus typiques par niveau).
- **Référence admin** : `conversion_dofus_sample_reference` est calculé aux niveaux **1, 40, 80, 120, 160, 200** (avec interpolation si une tranche n’a pas d’équipement). C’est le format attendu par le tableau de conversion de l’admin.
- **Caractéristiques sans samples** : `meta.characteristic_keys_without_samples` liste les clés mappées mais jamais observées sur les équipements (ex. esquive_pa_object, weight_object si aucun item avec cet effet dans l’échantillon). Augmenter `--max-items` ou vérifier le mapping si besoin.
- **Valeurs atypiques** : certaines caractéristiques (ex. pv_max_object) peuvent avoir des valeurs élevées ou des malus (négatifs) selon les effets Dofus ; à interpréter comme bonus bruts, pas comme PV finaux.
- **Granularité** : `conversion_dofus_sample` garde toutes les tranches (1, 10, 20, …) pour analyse ; `conversion_dofus_sample_reference` ne garde que les 6 niveaux de référence pour l’import admin.
