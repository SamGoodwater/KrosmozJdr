## Spécifications — Data-collect (Collect)

### But
Définir les contraintes fonctionnelles/techniques de la phase **Collect** (DofusDB) dans KrosmozJDR.

### Exigences
- **Config-driven** : la liste des entités et leurs endpoints/filtres doivent être déclarés en JSON (`resources/scrapping/...`).
- **Sécurité** :
  - allowlist d’hôtes (`source.json.security.allowedHosts`)
  - validation stricte des filtres (types + bornes) côté backend
- **Pagination Feathers** : support `$limit/$skip` et gestion du **cap fréquent à 50** (avancer le skip avec le `limit` renvoyé).
- **Cache** :
  - cache activé par défaut via le client HTTP,
  - `skip_cache` doit forcer le bypass.
- **Résilience** :
  - retries limités,
  - timeouts,
  - erreurs explicites (sans masquer les causes).

### Références
- API DofusDB : `Data-collect/API.md`
- Vue d’ensemble collect : `Data-collect/README.md`

