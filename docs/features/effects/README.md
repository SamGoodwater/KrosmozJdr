# Effets

Le système d'effets décrit les effets de sorts et d'objets : effets principaux, sous-effets, degrés, usages et mappings DofusDB.

## Backend

- Modèles : `Effect`, `SubEffect`, `EffectDegree`, `EffectUsage`, `ObjectEffect`, `DofusdbEffectMapping`.
- Services : `app/Services/Effect/`, `app/Services/Scrapping/Core/Conversion/SpellEffects/`.
- Admin/API : contrôleurs sous `app/Http/Controllers/Admin/` et `app/Http/Controllers/Api/Effect/`.

## Données

Les mappings DofusDB sont stockés en base (`dofusdb_effect_mappings`) et seedés par `DofusdbEffectMappingSeeder`.

## Frontend

Pages admin effets et composants de configuration sous `resources/js/Pages/Admin/`.
