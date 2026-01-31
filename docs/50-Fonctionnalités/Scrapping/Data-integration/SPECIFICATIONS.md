## Spécifications — Data-integration (Intégration)

### But
Décrire les règles d’écriture en base (KrosmozJDR) pour les données scrappées.

### Exigences
- **Anti-doublon** : privilégier `dofusdb_id` (puis fallback `name`).
- **Options**
  - `dry_run` : simulation sans écriture (actions `would_*`)
  - `force_update` : autoriser l’update si existe
  - `with_images` : activer/désactiver le téléchargement d’images
  - `include_relations` : import en cascade piloté par l’orchestrateur
- **Transactions** : opérations DB atomiques quand il y a écriture (sauf early-return en dry-run / skip).
- **Traçabilité** : renvoyer des actions explicites (`created/updated/skipped` et variantes `would_*`).

### Références
- API intégration : `Data-integration/API.md`
- Définitions intégration : `Data-integration/DEFINITIONS.md`

