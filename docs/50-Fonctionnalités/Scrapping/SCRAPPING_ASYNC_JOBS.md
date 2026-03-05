# Scrapping asynchrone (Jobs)

## Objectif
Permettre des imports/simulations de scrapping robustes en arriere-plan avec:
- creation de job,
- suivi de progression (polling),
- annulation explicite,
- reprise de suivi apres navigation.

## Endpoints API

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
- Fallback automatique en mode synchrone (`/api/scrapping/import/batch`) si l'endpoint jobs est indisponible.
- Polling toutes les `1500ms`.
- Sur annulation client (`AbortController`), un appel serveur `/cancel` est envoye.

## Stockage
Table: `scrapping_jobs`
- Identifiant UUID, statut, progression, run_id.
- Payload du job, summary, results.
- Horodatage de debut/fin/annulation.

## Notes de robustesse
- Le worker verifie l'etat du job entre chaque entite.
- Le front peut continuer a naviguer et revenir au scrapping via la notification dynamique.
- Le statut terminal est determine par le serveur, pas uniquement par l'etat local du composant.
