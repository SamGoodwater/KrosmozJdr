# Données des seeders (`database/seeders/data/`)

## Caractéristiques

La source versionnée est **`characteristic-definitions/{creature,object,spell}/*-definition.json`** (une définition par fichier : bloc `characteristic` + `entities` par ligne pivot).

- **Seed** : `CharacteristicSeeder` puis les seeders de groupe (`CreatureCharacteristicSeeder`, etc.) lisent uniquement ces JSON.
- **Export depuis la BDD** (après modification en admin) :  
  `php artisan scrapping:seeders:export --characteristics`  
  réécrit les fichiers JSON sous `characteristic-definitions/`.

Les anciens fichiers PHP (`characteristics.php`, pivots, normes, `characteristic_icons_colors.php`, etc.) ont été retirés au profit de ce référentiel JSON.

### Couleurs « élément / carac » (cohérence des trois groupes)

Pour les notions liées aux éléments Dofus et aux caracs associées, utiliser les **mêmes** noms de palette Tailwind (sans nuance) dans `characteristic.color` et, si présent, dans `value_overrides[].color` :

| Axe | `color` |
|-----|---------|
| Air + Agilité | `cyan` |
| Eau + Chance | `blue` |
| Feu + Intelligence | `orange` |
| Terre + Force | `brown` |
| Neutre | `slate` |
| Vitalité | `emerald` |
| Sagesse | `indigo` |

## Équipements (`entities/items/`)

Un fichier JSON par équipement relu à la main, pour pouvoir recréer le socle d’objets jouables sur n’importe quelle base (notamment avant de faire tourner l’IA). Le gros du catalogue reste produit par le scrapping DofusDB : ces fichiers ne portent que les items validés.

- **Seed** : `Database\Seeders\Entity\ItemSeeder`, appelé par `DatabaseSeeder`, `project:seed` et `project:init`.
- **Base → fichiers** : `php artisan items:seeder-export` (défaut : items `playable` ; `--prune` nettoie les fichiers obsolètes).
- **Fichiers → base** : `php artisan items:seeder-import` (`--dry-run` pour simuler).
- **Boutons admin** : `/admin/content/ia-generation`, section « Étalons d’équipement » (super administrateur).

Structure d’un fichier :

- `key` : `dofusdb_id` en priorité, `official_id` pour un objet sans source Dofus. C’est la clé d’upsert.
- `item` : champs de l’équipement. `effect` et `bonus` sont des **objets JSON éditables** (`{"strength": 3}`), convertis en chaîne à l’écriture en base. Le type est référencé par `item_type_dofus_id` car les identifiants de `item_types` diffèrent d’un environnement à l’autre.
- `relations` : panoplies par `dofusdb_id`, recette par `dofusdb_id` de ressource + quantité. Les références introuvables sont ignorées (une base sans scrapping n’a pas les ressources Dofus).

`image` n’est ni exporté ni importé : la colonne contient une URL absolue liée à l’hôte et à l’identifiant média de l’environnement. Les visuels restent gérés par la média-library.

Un item importé porte `auto_update = false` s’il a été exporté ainsi : le scrapping ne l’écrasera pas.

## Panoplies (`entities/panoplies/`)

Un fichier JSON par set relu (bonus de palier + liste des `dofusdb_id` des pièces).

- **Seed** : `Database\Seeders\Entity\PanoplySeeder`, **après** `ItemSeeder` (`project:seed` / `project:init`).
- Les pièces absentes de la base (pas de scrapping, pas d’item JSON) sont simplement omises du `sync`.

## Autres fichiers

Les autres données (types, mappings scrapping, etc.) restent sous forme de fichiers PHP ou JSON selon le seeder concerné ; voir les seeders dans `database/seeders/`.
