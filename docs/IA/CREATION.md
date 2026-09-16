# Fiches de création (prompt de conversion)

Source unique des guides MJ **et** du texte à injecter dans l’IA de conversion : `resources/ia/creation-guides/`.

Pas d’appel LLM. Laravel lira ce pack plus tard dans l’assembleur de contexte (couche « tâche par type »).

## Types couverts

Sorts, monstres, équipements, consommables, capacités, traits, ressources.

Chaque guide a cinq blocs : **philosophie**, **points importants**, **limites et propriétés**, **conseils**, **exemples** (fiches `playable` : Pression, Piou Vert, Pain d’Incarnam, Fureur, etc.).

Classes, spés, PNJ, panoplies, états restent sur l’atelier CMS seulement (`database/seeders/data/creation-pages.php`) : pas encore dans ce pack de conversion.

## Comment s’en servir

- Atelier MJ : `CreationPagesSeeder` charge les mêmes fichiers.
- Prompt : `CreationGuideCatalog::promptFor('spell')` ou `promptBundle()`.
- CLI : `php artisan ia:creation-guides` / `ia:creation-guides spell --json=…`

Le HTML CMS (krefs) est aplati en texte : `[[kref:…|PA]]` → `PA`.

## Suite

Quand le pipeline de conversion sera branché, coller le prompt du type dans la couche 2 (tâche + JSON Schema), avec les étalons `playable` et les clés `writable` de [CHAMPS.md](./CHAMPS.md).
