# Plan : Commandes d’initialisation et de mise à jour du projet

## Objectif

Créer deux commandes Artisan :

1. **`project:init`** — Initialisation complète du projet (base vide → données complètes)
2. **`project:update`** — Mise à jour des données en `auto_update` (à exécuter périodiquement)

---

## 1. Commande `project:init`

### Rôle

Transformer une base vide (après migrations) en un projet fonctionnel avec :
- Utilisateurs et pages critiques
- Structure des types (races, item-types, spell-types)
- Caractéristiques et mappings DofusDB
- Données scrappées depuis DofusDB
- Capabilities (si source disponible)
- Données métier optionnelles (shops, scénarios, etc.)

### Ordre d’exécution

| Phase | Étape | Commande / action |
|-------|-------|-------------------|
| 1 | Migrations | `php artisan migrate --force` |
| 2 | Socle scrapping | `php artisan scrapping:setup` (ou exécuter les seeders manuellement) |
| 3 | Pages / menu / utilisateur | `php artisan db:seed --class=UserSeeder` etc. (ou `DatabaseSeeder` partiel) |
| 4 | Types item (ressources, consommables, équipements) | `php artisan scrapping:types:extract` puis `scrapping:types:seed` |
| 5 | Races monstres | `php artisan db:seed --class=MonsterRaceSeeder` |
| 6 | Scrapping entités (ordre de dépendances) | Voir tableau ci-dessous |
| 7 | Capabilities | `php artisan capabilities:import-legacy database/seeders/data/capability.json` (si fichier présent) |
| 8 | Données optionnelles | Shops, scénarios, campagnes (seeders si disponibles) |

### Ordre scrapping (dépendances)

Les entités ont des dépendances implicites :

| Ordre | Entité | Options CLI | Notes |
|-------|--------|-------------|-------|
| 1 | `class` (breeds) | `--entity=class --max-items=0` | Classes/base pour sorts |
| 2 | `spell` | `--entity=spell --max-items=0` | Sorts (dépend des breeds) |
| 3 | `monster-race` | (catalogue) | Races monstres DofusDB → BDD |
| 4 | `monster` | `--entity=monster --max-items=0` | Par tranches niveau possible |
| 5 | `resource` | `--entity=resource --resource-types=allowed --max-items=0` | Types depuis BDD |
| 6 | `consumable` | `--entity=consumable --max-items=0` | Idem |
| 7 | `item` (equipment) | `--entity=item --max-items=0` | Équipements |
| 8 | `panoply` | `--entity=panoply --max-items=0` | Sets (après items) |

### Options utiles pour init

- `--noimage` : désactiver les images (plus rapide, rattrapage possible plus tard)
- `--skip-cache` : forcer la fraîcheur des données
- `--max-items=0` : tout importer (ou une limite pour tester)
- `--simulate` : optionnel, pour valider sans écrire (dry-run partiel)

### Gestion des tranches de niveau (monstres)

Pour éviter les timeouts, importer les monstres par tranches :

```bash
# Exemple : tranches de 50 niveaux
php artisan scrapping:run --entity=monster --levelMin=1 --levelMax=50 --max-items=0
php artisan scrapping:run --entity=monster --levelMin=51 --levelMax=100 --max-items=0
# etc.
```

La commande `project:init` peut intégrer cette logique en boucle (ex. 1–50, 51–100, …, 201–250).

### Structure proposée de la commande

```
project:init
  {--fresh : migrate:fresh --force avant tout}
  {--skip-migrate : Ne pas lancer les migrations}
  {--skip-seeders : Ne pas exécuter les seeders (socle déjà fait)}
  {--skip-scrapping : Ne pas scraper (pour tests rapides)}
  {--skip-capabilities : Ne pas importer les capabilities}
  {--noimage : Désactiver le téléchargement des images}
  {--entity= : Limiter à une entité (ex: monster,resource)}
  {--max-items=5000 : Limite par entité (0=illimité)}
  {--simulate : Ne pas écrire en base (validation seule)}
```

---

## 2. Commande `project:update`

### Rôle

Mettre à jour les entités déjà en base dont `auto_update = true`.  
Ne pas créer de nouvelles entités, ni écraser celles avec `auto_update = false`.

### Stratégie

1. Récupérer les IDs DofusDB des entités en base avec `auto_update = true`
2. Pour chaque entité (monster, spell, item, resource, consumable, breed, panoply) :
   - Requêter l’API DofusDB uniquement pour ces IDs
   - Appliquer `update_mode = auto_update` (respect de `auto_update`)
   - Option `skip_existing` : ne pas appeler l’API pour les entités qu’on n’écraserait pas

### Prérequis côté `scrapping:run`

La commande `scrapping:run` actuelle ne gère pas :
- `--update-mode=auto_update`
- `--skip-existing`

**Action** : étendre `scrapping:run` (ou créer un wrapper) avec ces options, puis les utiliser dans `project:update`.

### Structure proposée de la commande

```
project:update
  {--entity= : Limiter à une entité (défaut: toutes)}
  {--noimage : Ne pas télécharger les images}
  {--skip-cache : Ignorer le cache HTTP}
  {--dry-run : Simuler sans écrire}
```

### Entités concernées

| Entité | Table / modèle | Champ auto_update |
|--------|----------------|-------------------|
| monster | monsters (creatures) | monsters.auto_update |
| spell | spells | spells.auto_update |
| breed | breeds (classes) | breeds.auto_update |
| item | items | items.auto_update |
| resource | resources | resources.auto_update |
| consumable | consumables | consumables.auto_update |
| panoply | panoplies | (à vérifier) |

### Algorithme proposé

```
Pour chaque entité supportée :
  1. SELECT dofusdb_id FROM ... WHERE auto_update = 1
  2. Si liste vide → skip
  3. scrapping:run --entity=X --ids=1,2,3,... --update-mode=auto_update --skip-existing
     (ou appel direct Orchestrator avec ces options)
```

---

## 3. Tâches techniques détaillées

### 3.1 Extension de `scrapping:run`

| Tâche | Description |
|-------|-------------|
| Ajouter `--update-mode` | Valeurs : `ignore`, `draft_raw_auto_update`, `auto_update`, `force` (aligné UI) |
| Ajouter `--skip-existing` | Ne pas appeler l’API pour les entités déjà en base qu’on n’écraserait pas |
| Passer ces options à `buildImportOptions` | Puis à l’Orchestrator / IntegrationService |

### 3.2 Commande `project:init`

| Tâche | Description |
|-------|-------------|
| Créer `ProjectInitCommand` | Signature + options |
| Phase migrations | `migrate:fresh` ou `migrate` |
| Phase seeders | Appeler `scrapping:setup` ou seeders ciblés |
| Phase types | `scrapping:types:extract`, `scrapping:types:seed` |
| Phase races | `MonsterRaceSeeder` (ou synchro catalogue DofusDB si disponible) |
| Phase scrapping | Boucle sur entités dans l’ordre de dépendances |
| Phase capabilities | `capabilities:import-legacy` si fichier existant |
| Gestion tranches niveau | Pour monster : boucle levelMin/levelMax par tranches de 50 |
| Logs / progression | Barre de progression ou lignes info par phase |

### 3.3 Commande `project:update`

| Tâche | Description |
|-------|-------------|
| Créer `ProjectUpdateCommand` | Signature + options |
| Récupérer IDs auto_update | Par entité, depuis les tables |
| Appeler scrapping par lot | Limiter la taille des listes d’IDs (ex. 100 par requête) |
| Options orchestrator | `update_mode=auto_update`, `skip_existing=true` |
| Rapport | Nombre mis à jour / ignorés / erreurs |

### 3.4 Planification (scheduler)

| Tâche | Description |
|-------|-------------|
| Variable d’environnement | `PROJECT_UPDATE_SCHEDULED=true` (désactivé par défaut) |
| Créneau | Ex. `dailyAt('04:00')` pour `project:update` |
| Documentation | `.env.example` + doc déploiement |

---

## 4. Fichiers à créer / modifier

| Fichier | Action |
|---------|--------|
| `app/Console/Commands/ProjectInitCommand.php` | Créer |
| `app/Console/Commands/ProjectUpdateCommand.php` | Créer |
| `app/Console/Commands/ScrappingRunCommand.php` | Modifier (--update-mode, --skip-existing) |
| `app/Console/Kernel.php` | Modifier (schedule project:update si env) |
| `docs/50-Fonctionnalités/Scrapping/PLAN_COMMANDES_INIT_ET_UPDATE.md` | Ce document |
| `docs/100- Done/README.md` | Ajouter entrée après livraison |

---

## 5. Estimation et priorisation

| Phase | Priorité | Effort estimé |
|-------|----------|---------------|
| Extension scrapping:run (update-mode, skip-existing) | Haute | 1–2 h |
| project:init (sans tranches niveau) | Haute | 2–3 h |
| project:init (tranches niveau monstres) | Moyenne | 0,5 h |
| project:update | Haute | 2 h |
| Planification scheduler | Basse | 0,5 h |
| Documentation | Moyenne | 0,5 h |

---

## 6. Références

- `scrapping:setup` : bootstrap socle scrapping
- `scrapping:run` : collecte + import
- `scrapping:types:extract` / `scrapping:types:seed` : types item
- `capabilities:import-legacy` : capacités depuis export JSON
- [Orchestrateur/API.md](./Orchestrateur/API.md) : options d’import
- [PLAN_TYPES_ITEM_BDD_SEEDER.md](./PLAN_TYPES_ITEM_BDD_SEEDER.md) : workflow types
