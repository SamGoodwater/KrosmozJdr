# Caractéristiques

Les caractéristiques décrivent les valeurs numériques et règles de lecture des créatures, objets et sorts : niveaux, dégâts, PA/PM/PO, limites, formules et normes.

## Backend

- Définitions et lecture : `app/Services/Characteristics/`, `app/Support/Characteristics/`.
- Limites : `app/Services/Characteristic/Limit/CharacteristicLimitService.php`.
- Formules : services sous `app/Services/Characteristic/`.
- Seeders : `CharacteristicSeeder`, `CreatureCharacteristicSeeder`, `ObjectCharacteristicSeeder`, `SpellCharacteristicSeeder`.
- Qualité des définitions : `app/Services/Characteristics/CharacteristicDefinitionQualityService.php` vérifie notamment les `norms_grid`, formules de conversion et restrictions d'équipement.

## Normes

Les définitions JSON dans `database/seeders/data/characteristic-definitions/` portent les grilles de normes 5 puissances × 20 niveaux (`norms_grid`). L'audit 2026-07 ne laisse aucun `norms_grid` manquant sur les vraies définitions `creature`, `object` et `spell`; seuls les fichiers `_templates` restent volontairement vides.

Pour les objets, les caractéristiques dont l'aide cible un type d'équipement portent aussi `item_type_dofus_ids` :
- amulettes : `[1]`
- armes : `[2,3,4,5,6,7,8]`
- chapeaux/capes : `[9,10]`

## Frontend

- Store Pinia : `resources/js/Composables/store/useCharacteristicsPiniaStore.js`.
- Affichages : composants `Characteristic*` dans les Atoms/Molecules.
- Admin : pages `resources/js/Pages/Admin/characteristics/`.

## Lien avec d'autres features

- Scrapping : conversion DofusDB → caractéristiques Krosmoz.
- Effets : effets de sorts/objets liés à des caractéristiques.
