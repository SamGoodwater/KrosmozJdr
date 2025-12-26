# TanStack Table — Architecture & Contrats (Table v2)

## Objectif

Mettre en place un **système de tableau unique, générique et scalable**, basé sur **TanStack Table**, sans duplication de code, et compatible avec :

- **Permissions policy-driven** (Laravel Policies → `usePermissions()`).
- **Hybride “client-first”** :
  - par défaut : tri / recherche / filtres / pagination **côté client** sur un dataset.
  - opt‑in serveur : uniquement si la page fournit un **`serverUrl` complet** (Option A).
- **Données “prêtes à rendre”** :
  - le backend fournit `type + value + params` par cellule (badgeColor, labels, liens, etc.).

## Rôles des composants (Atomic Design)

### `TanStackTable` (Organism — générique)

Responsabilités :

- Rendu de la table (header, rows, cellules).
- État UI : tri, recherche, filtres, pagination, sélection, visibilité colonnes, export CSV.
- Mode hybride “client-first” :
  - utilise toujours le dataset courant pour les opérations client.
  - déclenche le serveur uniquement si `serverUrl` est fourni (opt‑in).

Ne fait **pas** :

- Aucune logique entité (routes Ziggy, permissions métiers, bulk endpoints spécifiques).
- Aucune transformation métier (ex: `rarity=1 → "Commun"`).

### `EntityTanStackTable` (Wrapper — glue entité)

Responsabilités :

- Brancher `usePermissions()` pour activer/masquer features, colonnes et actions.
- Brancher `entityRouteRegistry` pour show/edit/delete/create.
- Gérer les actions entité (view/edit/delete, bulk edit, quick edit).
- Optionnel : fournir `serverUrl` + adapter la réponse serveur vers `TableResponse`.

## Mode hybride “client-first” (règles)

- **Sans `serverUrl`** : aucun fetch serveur. La page fournit un `rows` dataset.
- **Avec `serverUrl`** : `EntityTanStackTable` fetch un dataset “base”, puis `TanStackTable` applique tri/recherche/filtres **côté client** par défaut (sans re-fetch automatique).
- Le serveur n’est utilisé que pour :
  - charger un dataset initial,
  - ou des pages qui veulent des tableaux “personnalisés” en fournissant une URL paramétrée.

## Skeleton (chargement) — cellules & lignes

Objectif : pendant `loading=true`, afficher un **skeleton cohérent colonne par colonne** (évite le layout shift et rend la lecture plus confortable).

### Règles

- Le skeleton doit **respecter la structure des colonnes** (mêmes largeurs approximatives).
- Le skeleton est rendu **par cellule** en fonction de `column.cell.type`.
- Le skeleton doit être **non interactif** (pas de liens/boutons actifs).

### Recommandations par type de cellule

- **`text`**
  - 1 barre (hauteur ~ `h-3`) + largeur variable (ex: 40–80%).
- **`badge`**
  - 1 “pill” (hauteur ~ `h-5`, largeur ~ `w-16`).
- **`icon`**
  - carré/round (ex: `h-5 w-5`).
- **`image`**
  - carré (ex: `h-8 w-8`) avec background neutre.
- **`route`**
  - barre texte + une légère indication de “lien” (optionnel). Mais non cliquable.
- **`custom`**
  - fallback générique (barre texte) sauf si le composant fournit son propre skeleton.

### Paramètres (optionnels)

Le skeleton peut être configuré via `config.ui` :

- `ui.skeletonRows?: number` (nombre de lignes skeleton à afficher)
- `ui.skeletonShowHeader?: boolean` (afficher le header pendant loading)

## Contrat serveur → table (payload)

### `TableResponse`

- **`meta`**
  - `entityType`: string
  - `query`: `{ search, filters, sort, order, page, perPage }` (echo/debug)
  - `pagination?`: `{ total, perPage, currentPage, lastPage }`
  - `capabilities`: `{ viewAny, createAny, updateAny, deleteAny, manageAny }`
  - `filterOptions?`: `{ [filterId]: Array<{ value, label }> }`
- **`rows[]`** : `TableRow[]`

### `TableRow`

- `id`: string|number
- `cells`: `{ [cellId: string]: Cell }`
- `rowParams?`: objet libre
- `actions?`: `Action[]` (optionnel)

### `Cell`

- `type`: `text | badge | icon | image | route | custom`
- `value`: `string | number | boolean | null`
- `params?`: objet libre (piloté par serveur)
  - exemple badge : `{ color: "success" }`
  - exemple route : `{ href: "/entities/resources/12", target?: "_blank" }`
  - exemple tri/filtre : `{ sortValue: 2, filterValue: 2 }`

### Exemple minimal

```json
{
  "meta": {
    "entityType": "resources",
    "query": { "search": "", "filters": {}, "sort": "", "order": "asc", "page": 1, "perPage": 25 },
    "capabilities": { "viewAny": true, "createAny": false, "updateAny": false, "deleteAny": false, "manageAny": false }
  },
  "rows": [
    {
      "id": 12,
      "cells": {
        "image": { "type": "image", "value": "https://…/img.png", "params": { "alt": "Bois de Frêne" } },
        "name": { "type": "route", "value": "Bois de Frêne", "params": { "href": "/entities/resources/12" } },
        "rarity": { "type": "badge", "value": "Commun", "params": { "color": "success", "sortValue": 0, "filterValue": 0 } }
      }
    }
  ]
}
```

## Contrat config (front)

### `TanStackTableConfig` (générique)

- **`id`**: string (unique)
- **`ui`**: `{ variant?, size?, color?, density? }`
  - **`variant`**: `"zebra"|"plain"` (alias `"striped"` → zebra)
  - **`size`**: `"xs"|"sm"|"md"|"lg"` (taille DaisyUI appliquée au tableau + toolbar + pagination)
  - **`color`**: `"primary"|"secondary"|"accent"|"info"|"success"|"warning"|"error"|"neutral"|...`
    - utilisé pour **la bordure** du tableau et **le surlignage** des lignes sélectionnées
  - **`density`**: réservé (alias futur pour `size`)
- **`features`**
  - search: `{ enabled, placeholder?, debounceMs? }`
  - filters: `{ enabled, layout?: "inline"|"drawer", persist?: boolean }`
  - pagination: `{ enabled, perPage: { default, options } }`
  - selection: `{ enabled, checkboxMode: "none"|"always"|"auto", clickToSelect: boolean }`
  - columnVisibility: `{ enabled, persist: boolean, storageKey? }`
  - export: `{ csv?: boolean, filename?: string }`
- **`columns[]`**: `ColumnConfig[]`

### `ColumnConfig`

- `id`: string (stable)
- `label`: string
- `cellId?`: string (par défaut = `id`)
- `isMain?`, `group?`, `responsive?`
- `hideable?`, `defaultHidden?`
  - **Comportement important**: `defaultHidden` est appliqué **uniquement si l’utilisateur n’a pas déjà une préférence persistée** (localStorage) pour cette colonne.
- **Tri / recherche / filtres (custom)**
  - `sort?: { enabled: boolean, mode?: "client"|"server"|"both", sortKey?: string, sortValue?: (row)=>any, sortingFn?: (aRow,bRow,columnId)=>number }`
  - `search?: { enabled: boolean, searchValue?: (row)=>string }`
  - `filter?: { id: string, type: "select"|"multi"|"boolean"|"text"|"range", filterValue?: (row)=>any, filterFn?: (row, columnId, value)=>boolean }`
- **Rendu**
  - `cell`: `{ type: "text"|"badge"|"icon"|"image"|"route"|"custom", component? }`

### `EntityTableConfig` (wrapper entité)

- `entityType`
- `routes` (via registry)
- `permissions` (gating des features/colonnes/actions)
- `server` (opt‑in)
  - `serverUrl?: string` (**Option A** : URL complète fournie par la page)
  - `responseAdapter(payload) -> TableResponse`
- `actions`
  - `rowActions[]`, `bulkActions[]`, `toolbarActions[]` (avec permissions + responsive inline/menu)

## Permissions (source de vérité)

- **Backend = vérité** (Policies/Gates).
- Le front masque/disable via `usePermissions()` et/ou `meta.capabilities`, mais ne remplace jamais l’autorisation backend.

## Conventions “colonnes techniques” (front)

- **`id`**, **`created_by`**, **`created_at`**, **`updated_at`**
  - `hideable: true`
  - `defaultHidden: true`
  - `permissions: { ability: "createAny" }` (visible seulement pour les users pouvant créer l’entité)
- **`dofusdb_*`**, **`auto_update`**
  - `hideable: true`
  - `defaultHidden: true`
  - `permissions: { ability: "updateAny" }` (réservé aux users ayant des droits d’écriture)
- **`usable`**
  - `hideable: true`
  - `defaultHidden: true`
  - pas de `permissions` (visible pour tout le monde si activé)

## Plan de refonte (sans doublons)

### Phase 1 — Spec + skeleton

- Créer `TanStackTable` + molecules (toolbar/filters/header/row/pagination/selection) + atoms cellules.
- Créer `EntityTanStackTable` (permissions/routes/actions + support `serverUrl`).

### Phase 2 — Pilote “resources”

- Créer `resources-table-config` v2 (Approche A).
- Adapter l’endpoint serveur (si nécessaire) pour renvoyer `rows[*].cells` typées.
- Migrer `resources/Index.vue` vers `EntityTanStackTable`.

### Phase 3 — Migration globale

- Migrer entité par entité.
- Supprimer l’ancien système (`EntityTable` v1 et composables associés) dès que non utilisé.

## Checklist fonctionnalités (à ne pas oublier)

- **UI** : loading/empty/error, responsive, accessibilité clavier + `aria-sort`.
- **Colonnes** : hideable/defaultHidden, main column, group, responsive.
- **Search** : debounce, custom `searchValue`, normalisation (case/accents) si besoin.
- **Filtres** : options depuis `meta.filterOptions`, filtres select/multi/boolean/text/range.
- **Tri** : `sortValue`/`sortingFn`, tri stable, gestion des nulls.
- **Pagination** : perPage options, page reset quand filtre/search change.
- **Sélection** : click-to-select, checkboxMode (none/always/auto), bulk actions.
- **Permissions** : gating features/colonnes/actions.
- **Server opt‑in** : `serverUrl` (Option A) + adapter.
- **Persistance** : colonnes visibles, perPage, préférences (si activé).


