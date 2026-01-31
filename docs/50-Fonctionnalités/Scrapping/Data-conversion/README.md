# Service Data-conversion (Conversion)

### Objectif
La couche **Data-conversion** transforme les données brutes DofusDB en un format attendu par KrosmozJDR, en appliquant :
- un **mapping** (source → modèles/champs Krosmoz),
- une suite de **formatters** whitelistés (ex: `pickLang`, `toInt`, `mapDofusdbItemType`, …).

### Conversion “config-driven” (source de vérité)
Le mapping + la conversion sont pilotés par :
- `resources/scrapping/sources/dofusdb/entities/*.json` (mapping champs + formatters)
- `resources/scrapping/formatters/registry.json` (liste blanche + schéma des args)

Le backend charge/valide ces fichiers via :
- `app/Services/Scrapping/Config/ScrappingConfigLoader.php`
- `app/Services/Scrapping/Config/FormatterRegistry.php`
- `app/Services/Scrapping/Config/ConfigDrivenConverter.php`

### Compatibilité legacy
Quand une entité n’est pas encore migrée en JSON, l’orchestrateur peut “fallback” sur la logique existante.
L’objectif de la refonte est de **réduire** ces fallbacks au profit des configs JSON.

### Exposition via l’API interne
La conversion est visible via :
- `GET /api/scrapping/preview/{type}/{id}` (collect + conversion, sans écriture)
- et indirectement via les endpoints d’import (qui déclenchent la chaîne complète).

Voir :
- `Data-conversion/API.md`

