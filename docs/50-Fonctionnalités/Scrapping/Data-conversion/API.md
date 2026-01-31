## API — Conversion / Prévisualisation (Scrapping)

### Objectif
La conversion n’est pas exposée comme une API “standalone”. Elle est consommée via :
- la **prévisualisation** (collect + conversion, sans écriture),
- l’**import** (collect + conversion + intégration) avec options.

### Prévisualiser une entité (collect + conversion)
Endpoint :

```http
GET /api/scrapping/preview/{type}/{id}
```

Contraintes :
- `{type}` ∈ `class|monster|item|resource|consumable|spell|panoply`
- `{id}` borné par les limites connues (fallback) et/ou la config.

Réponse :
- `success=true`
- `data` contient l’objet renvoyé par l’orchestrateur (généralement : raw + converted + existing si présent)

Notes :
- Ce endpoint est celui utilisé pour une UX “aperçu / comparaison”.
- La conversion est **config-driven** quand une config JSON existe (sinon fallback legacy).

### Importer une entité (avec options)
Les endpoints d’import acceptent des options qui impactent conversion/intégration :
- `dry_run` : exécute la chaîne sans persister (simulation)
- `validate_only` : s’arrête après conversion (retourne raw/converted sans intégration)
- `force_update` : autorise l’écrasement si l’entité existe déjà
- `with_images` : télécharge/stocke les images (par défaut `true`)
- `include_relations` : importe aussi les relations (par défaut `true`)

Voir la liste complète dans `Orchestrateur/API.md`.

