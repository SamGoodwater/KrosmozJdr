# Caractéristiques

Les caractéristiques décrivent les valeurs numériques et règles de lecture des créatures, objets et sorts : niveaux, dégâts, PA/PM/PO, limites, formules et normes.

## Backend

- Définitions et lecture : `app/Services/Characteristics/`, `app/Support/Characteristics/`.
- Limites : `app/Services/Characteristic/Limit/CharacteristicLimitService.php`.
- Formules : services sous `app/Services/Characteristic/`.
- Seeders : `CharacteristicSeeder`, `CreatureCharacteristicSeeder`, `ObjectCharacteristicSeeder`, `SpellCharacteristicSeeder`.

## Frontend

- Store Pinia : `resources/js/Composables/store/useCharacteristicsPiniaStore.js`.
- Affichages : composants `Characteristic*` dans les Atoms/Molecules.
- Admin : pages `resources/js/Pages/Admin/characteristics/`.

## Lien avec d'autres features

- Scrapping : conversion DofusDB → caractéristiques Krosmoz.
- Effets : effets de sorts/objets liés à des caractéristiques.
