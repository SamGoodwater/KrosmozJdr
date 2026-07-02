# Scrapping asynchrone (Jobs)

## Objectif
Permettre des imports/simulations de scrapping robustes en arriere-plan avec:
- creation de job,
- suivi de progression (polling),
- annulation explicite,
- reprise de suivi apres navigation.

## Endpoints API

### Lister les jobs
- `GET /api/scrapping/jobs`
- Reponse : `{ active: [...], recent_finished: [...] }` — jobs en attente/en cours + derniers terminés.

### Creer un job
- `POST /api/scrapping/jobs`
- Payload:
  - `kind`: `import_batch`
  - `entities`: `[{ type, id }]`
  - options import classiques (`dry_run`, `include_relations`, `replace_mode`, etc.)
- Reponse: `202 Accepted` avec `data.job_id`.

### Lire le statut d'un job
- `GET /api/scrapping/jobs/{jobId}`
- Reponse:
  - `data.status`: `queued|running|succeeded|failed|cancelled`
  - `data.progress.done|total`
  - `data.summary` (`total/success/errors/cancelled`)
  - `data.results` (resultats detail par entite)
  - `data.run_id`

### Annuler un job
- `POST /api/scrapping/jobs/{jobId}/cancel`
- Met le job en `cancelled`.

## Comportement frontend
- `useScrappingBatch` utilise les jobs asynchrones par defaut.
- Fallback automatique en mode synchrone (`/api/scrapping/import/batch`) si:
  - l'endpoint jobs est indisponible ou retourne une erreur;
  - la création du job prend plus de 15 s (ex: `QUEUE_CONNECTION=sync`);
  - le job reste en statut `queued` plus de 60 s (worker non lancé).
- Polling toutes les `1500ms`.
- Sur annulation client (`AbortController`), un appel serveur `/cancel` est envoye.
- Protection contre les imports multiples (guard sur `importing`).

## Stockage
Table: `scrapping_jobs`
- Identifiant UUID, statut, progression, run_id.
- Payload du job, summary, results.
- Horodatage de debut/fin/annulation.

## Timeout et pivots incomplets
Si un job échoue après ~1 min ("FAIL" dans le worker) alors que les données semblent enregistrées : c’est probablement un **timeout** (60 s par défaut). Le monstre/créature est sauvegardé, mais la **phase de drainage** des relations (import des sorts et items des drops, puis mise à jour des pivots `creature_spell`, `creature_resource`, etc.) peut être interrompue. Le job `ProcessScrappingJob` utilise un timeout de 10 min ; si le worker est lancé avec un timeout plus court (`--timeout=60`), c’est ce dernier qui s’applique. Solution : `php artisan queue:work --timeout=660`. Pour compléter les pivots : réimporter le monstre (les relations déjà écrites seront conservées, le drainage reprendra le reste).

## Résilience (2026-03)
- **DB::reconnect()** au début de chaque job : évite "MySQL server has gone away".
- **Retries** : 2 tentatives, backoff 30 s (erreurs transitoires).
- **SaveProgress** : sauvegarde tous les 5 entités (réduit écritures BDD).

## Notes de robustesse
- Le worker verifie l'etat du job entre chaque entite.
- Le front peut continuer a naviguer et revenir au scrapping via la notification dynamique.
- Le statut terminal est determine par le serveur, pas uniquement par l'etat local du composant.

## Prérequis
- `QUEUE_CONNECTION=database` (ou `redis`) dans `.env` pour un traitement asynchrone.
- Lancer `php artisan queue:work --timeout=660` pour traiter les jobs. Sinon, le front basculera en mode synchrone après 60 s de polling sur un job bloqué en `queued`.
- Pour les imports avec relations (monstres, classes) : le job a un timeout de 10 min. Si `DB_QUEUE_RETRY_AFTER` est défini, le mettre à au moins 600 pour éviter qu’un job long soit réattribué trop tôt.
