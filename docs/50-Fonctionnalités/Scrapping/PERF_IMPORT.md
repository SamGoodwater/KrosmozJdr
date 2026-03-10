# Performance de l'import scrapping

## Origines du temps d'exécution

Pour un import de **42 monstres avec relations** (sorts + drops), le pipeline exécute environ :

| Phase | Opérations | Goulot principal |
|-------|------------|------------------|
| **1. Collecte** | 42 × fetch monster + N × fetch spell + M × fetch item | Requêtes HTTP vers DofusDB (avec cache si `skip_cache=false`) |
| **2. Conversion** | Formules, mapping, effets (sorts) | CPU, requêtes BDD (catalogues, types) |
| **3. Validation** | Clamp + validate par entité | CPU |
| **4. Intégration** | Écriture creatures, monsters, etc. | BDD |
| **5. Drain relations** | Pour chaque spell/item non en BDD : runOne complet | **Principal goulot** |

### Drain des relations

Après l’intégration d’un monstre, les sorts et drops sont mis sur une pile. Le **drain** exécute pour chaque élément :

- Spell déjà en BDD → simple lookup (rapide)
- Item/ressource/consommable déjà en BDD → simple lookup (rapide depuis l’optimisation)
- Nouveau spell/item → **runOne complet** (fetch API + conversion + validation + intégration)

Chaque `runOne` pour un nouvel item = ~1–3 s (API + conversion + BDD). Avec beaucoup de drops uniques, le temps total augmente vite.

## Optimisations appliquées

1. **Cache DofusDB** (`DofusDbClient`) : TTL 3600 s par défaut. Les mêmes ressources/sorts réutilisés bénéficient du cache.
2. **`resolveExistingRelationImportState` pour les items** : si une ressource/item/consommable existe déjà en BDD, on évite `runOne` et on récupère l’ID. Avant : on refaisait le pipeline pour chaque occurrence.
3. **`skip_existing` (défaut `true`)** : avant chaque `runOne`, vérification en BDD si l'entité existe et si on ne la remplacerait pas. Si oui → skip complet : pas d'appel API, pas de conversion. Accélère fortement les imports en masse.

## Pistes d’amélioration

| Piste | Complexité | Gain estimé |
|-------|------------|-------------|
| Importer d’abord tous les sorts/items communs avant les monstres | Moyenne | Réduction des runOne en phase drain |
| Traitement par lots (batch) pour la BDD | Élevée | Moins d’allers-retours |
| Parallélisation (jobs séparés pour chaque monstre) | Élevée | Temps ≈ temps du monstre le plus long |
| Logging réduit en production | Faible | Quelques % |
| Préchargement des catalogues (types, races) | Moyenne | Moins de requêtes BDD par entité |

## Recommandations

- **Cache** : garder `skip_cache=false` pour les imports répétés.
- **Ordre des imports** : importer d’abord ressources, sorts et équipements courants, puis les monstres.
- **Timeout job** : `ProcessScrappingJob` a un timeout de 600 s ; s’assurer que le worker (`php artisan queue:work`) ne le réduit pas.

- **Worker** : lancer avec \`php artisan queue:work --timeout=660\` (defaut 60 s).
- **skip_existing** : laisser activé pour les imports en masse (défaut).
- **Taille batchs** : privilégier 30-50 entités par job.
