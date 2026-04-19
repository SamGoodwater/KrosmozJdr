# Données des seeders (`database/seeders/data/`)

## Caractéristiques

La source versionnée est **`characteristic-definitions/{creature,object,spell}/*-definition.json`** (une définition par fichier : bloc `characteristic` + `entities` par ligne pivot).

- **Seed** : `CharacteristicSeeder` puis les seeders de groupe (`CreatureCharacteristicSeeder`, etc.) lisent uniquement ces JSON.
- **Export depuis la BDD** (après modification en admin) :  
  `php artisan scrapping:seeders:export --characteristics`  
  (alias : `db:export-seeder-data`) réécrit les fichiers JSON sous `characteristic-definitions/`.

Les anciens fichiers PHP (`characteristics.php`, pivots, normes, `characteristic_icons_colors.php`, etc.) ont été retirés au profit de ce référentiel JSON.

## Autres fichiers

Les autres données (types, mappings scrapping, etc.) restent sous forme de fichiers PHP ou JSON selon le seeder concerné ; voir les seeders dans `database/seeders/`.
