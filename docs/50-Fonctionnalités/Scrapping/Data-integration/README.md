# Service Data-integration (Intégration)

### Objectif
La couche **Data-integration** applique les données converties en base KrosmozJDR :
- création / mise à jour,
- gestion des conflits,
- gestion des relations,
- gestion des images (optionnelle).

Elle est déclenchée par l’orchestrateur (UI/CLI/API) et **ne doit pas dépendre** de l’UI.

### Conventions clés
- **Référence externe** : on privilégie `dofusdb_id` pour retrouver une entité existante (puis fallback par `name`).
- **Simulation** : `dry_run=true` renvoie des actions `would_*` sans écrire.
- **Écrasement** : `force_update=true` autorise la mise à jour quand l’entité existe.
- **Images** : `with_images` active/désactive le téléchargement/stockage local.
- **Relations** : `include_relations` pilote l’import des relations (avec garde-fous anti-boucle côté orchestrateur).

### Exposition (API interne)
L’intégration est déclenchée via :
- `POST /api/scrapping/import/<type>/{id}`
- `POST /api/scrapping/import/batch`
- `POST /api/scrapping/import/range`
- `POST /api/scrapping/import/all`

Voir :
- `Data-integration/API.md`
- `Orchestrateur/API.md`

### Lien avec le code
Implémentation principale :
- `app/Services/Scrapping/DataIntegration/DataIntegrationService.php`

