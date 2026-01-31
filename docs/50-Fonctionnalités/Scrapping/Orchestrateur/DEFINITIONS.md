## Définitions — Orchestrateur (Scrapping)

### Objectif
Définir les notions manipulées par l’orchestrateur (résultats, options, relations).

---

## Options d’exécution (communes)
- `skip_cache` : bypass du cache HTTP (collect)
- `dry_run` : simulation (pas d’écriture DB)
- `validate_only` : stoppe après conversion (retourne raw/converted)
- `force_update` : autorise l’update si l’entité existe déjà
- `with_images` (défaut `true`)
- `include_relations` (défaut `true`)

---

## Résultat (forme générale)
Les méthodes de l’orchestrateur renvoient typiquement un tableau associatif contenant :
- `success` (bool)
- `message` (string)
- `data` (mixed) : entité(s) importée(s) / actions / IDs internes
- `related` (optionnel) : résumé des relations importées

---

## Preview
La preview renvoie généralement :
- `raw` : données collectées
- `converted` : données après conversion config-driven/legacy
- `existing` (optionnel) : entité déjà présente en DB (si trouvée)

> La forme exacte dépend du type d’entité et du fallback legacy.

