## Résumé d’implémentation — Scrapping (config-driven)

### Objectif
Centraliser le scrapping autour d’un pipeline stable (**collect → conversion → intégration**) piloté par des **configs JSON** afin de :
- réduire la duplication,
- rendre la collecte extensible (multi-sources),
- rendre conversion/mapping explicites et testables,
- supporter une UI et une CLI cohérentes.

### Briques principales (référence)
- **HTTP DofusDB** : `app/Services/Scrapping/Http/DofusDbClient.php`
- **Collect config-driven** : `app/Services/Scrapping/DataCollect/ConfigDrivenDofusDbCollector.php`
- **Loader configs** : `app/Services/Scrapping/Config/ScrappingConfigLoader.php`
- **Registry formatters** : `app/Services/Scrapping/Config/FormatterRegistry.php`
- **Converter config-driven** : `app/Services/Scrapping/Config/ConfigDrivenConverter.php`
- **Orchestrateur** : `app/Services/Scrapping/Orchestrator/ScrappingOrchestrator.php`
- **Intégration** : `app/Services/Scrapping/DataIntegration/DataIntegrationService.php`

### Configs JSON (source of truth)
Emplacement :
- `resources/scrapping/sources/dofusdb/source.json`
- `resources/scrapping/sources/dofusdb/entities/*.json`

### Exposition (UI/CLI/API)
- UI : `/scrapping`
- API : `docs/50-Fonctionnalités/Scrapping/Orchestrateur/API.md`
- CLI : `php artisan scrapping`

