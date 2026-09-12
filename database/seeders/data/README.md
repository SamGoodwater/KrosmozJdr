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

Après l’import, les **ressources déjà liées** aux recettes des items `playable` passent en `playable` (`MarkPlayableItemRecipeResources`, aussi via `ResourceSeeder`). On ne réécrit pas nom ni type. Un prix Dofus à 0 sur une fiche jouable est porté à **1 kama** (`EnsureMinimumPlayableResourcePrice`). Les fiches ressource restent créées par le scrapping.

## Consommables de soin hors combat (`entities/consumables/`)

Un JSON unique `healing-out-of-combat.json` décrit 11 paliers × 4 types (pain, poisson comestible, viande comestible, potion). Soin fixe, **hors combat uniquement**. Recette = 10 unités de la ressource de palier, dont le prix est le barème du consommable / 10.

- **Seed** : `Database\Seeders\Entity\ConsumableSeeder`, après `ResourceSeeder` (`project:seed` / `project:init`).
- Upsert sur `dofusdb_id` (identités Dofus conservées, images incluses) ou `official_id` `jdr:heal:potion:N` pour les potions sans fiche Dofus.
- Les ressources d’ingrédients passent en `playable` avec `auto_update = false` pour geler le prix JDR.
- Sans ressource scrapée, le consommable est tout de même créé avec `price_custom` au barème.

## Parchemins de caractéristique (`entities/consumables/`)

`characteristic-respec-scrolls.json` : 4 paliers × 6 caractéristiques (Force, Intelligence, Chance, Agilité, Vitalité, Sagesse). **Respec** : retire N points déjà investis dans la caractéristique du parchemin pour les replacer ailleurs. **Pas de recette** ; `price_custom` = 1 000 / 3 000 / 5 000 / 10 000.

- Même seeder `ConsumableSeeder`, après l’échelle de soins.
- Upsert sur `dofusdb_id` (Petit / Parchemin / Grand / Puissant). Type Dofus 76 passé en `playable`.
- Recette vidée à l’import. `auto_update = false`.
- Niveaux / rareté : Petit niv. 3 Commun, normal niv. 6 Peu commun, Grand niv. 10 Rare, Puissant niv. 15 Très rare.
- Un consommable `playable` n’est pas recalculé en masse ni via « Actualiser le prix » (le barème custom resterait à 0).

## Consommables utilitaires (`entities/consumables/`)

`utility-playable.json` : Potion de Rappel (800), Antidote (1 500), Bière d'Amakna (200), Café (250), Élixir de Wakfu (3 500), Bonbon de Renaissance du Chanceux (10 000, niv. 12), potions de bouclier et PV temporaires. **Pas de recette** ; `price_custom` = barème.

- Même seeder `ConsumableSeeder`, après les parchemins.
- Upsert sur `dofusdb_id` ou `official_id` `jdr:…`.
- Buffs : durée jusqu’au prochain repos long (8 h max) ; même type d’effet, pas de cumul.

## Classes (`entities/breeds/`)

Un JSON par classe (`feca.json` … `forgelance.json`, 19 fiches, ids Dofus 1–18 et 20) : nom, résumé Dofus, voix élémentaires du §2.3.1. Foggernaut = Steamer (pas une 20ᵉ classe). Upsert sur `dofusdb_id`, `official_id` ou `name`. `auto_update = false`. L’état n’est posé qu’à la création (`draft`). Description tronquée à 255 caractères.

- **Seed** : `Database\Seeders\Entity\ClassBreedSeeder`, **avant** `SpellSeeder`.

## Sorts de classe niveau 1 (`entities/spells/`)

`iop-level-1.json` … `forgelance-level-1.json` : 6 sorts par classe (3 emplacements × 2 variantes), **19 classes**. Upsert sur `dofusdb_id` ou `official_id`. `auto_update = false`, état `playable`. `target_type` optionnel (`direct`, `trap`, `glyph`). Les autres sorts liés à la classe passent hors grille (`character_level` 0, `slot_index` 1).

- **Seed** : `Database\Seeders\Entity\SpellSeeder` (`project:seed` / `project:init` / `DatabaseSeeder`).
- Budget : attaque simple 3 PA 1d6+mod 2×/tour ; sort fort 4–5 PA ; identité 3 PA.

## Panoplies (`entities/panoplies/`)

Un fichier JSON par set relu (bonus de palier + liste des `dofusdb_id` des pièces).

- **Seed** : `Database\Seeders\Entity\PanoplySeeder`, **après** `ItemSeeder` (`project:seed` / `project:init`).
- Les pièces absentes de la base (pas de scrapping, pas d’item JSON) sont simplement omises du `sync`.

## Autres fichiers

Les autres données (types, mappings scrapping, etc.) restent sous forme de fichiers PHP ou JSON selon le seeder concerné ; voir les seeders dans `database/seeders/`.
