## Orchestrateur Scrapping

### Objectif
L’orchestrateur est le **point d’entrée unique** du scrapping : il enchaîne les étapes
**collect → conversion → intégration** et expose des méthodes simples pour l’API/CLI/UI.

### Responsabilités (réellement utilisées)
- choisir la stratégie **config-driven** (JSON) ou fallback legacy si nécessaire,
- coordonner les options d’exécution :
  - `skip_cache`, `dry_run`, `validate_only`, `force_update`,
  - `with_images`, `include_relations`,
- gérer l’import des **relations** (avec garde-fous : désactivation récursive, anti-boucle),
- renvoyer un résultat structuré (utile UI/CLI/tests).

### Où c’est implémenté
- `app/Services/Scrapping/Orchestrator/ScrappingOrchestrator.php`

### Où c’est utilisé
- API Laravel : `app/Http/Controllers/Scrapping/ScrappingController.php`
- CLI : `app/Console/Commands/ScrappingCommand.php`
- UI : page `/scrapping`

### Documentation associée
- Référence API : `docs/50-Fonctionnalités/Scrapping/Orchestrateur/API.md`
- Collect / DofusDB : `docs/50-Fonctionnalités/Scrapping/Data-collect/API.md`
- Conversion : `docs/50-Fonctionnalités/Scrapping/Data-conversion/DEFINITIONS.md`
- Intégration : `docs/50-Fonctionnalités/Scrapping/Data-integration/DEFINITIONS.md`

