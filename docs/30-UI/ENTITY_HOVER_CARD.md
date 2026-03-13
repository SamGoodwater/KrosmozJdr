# Entity Hover Card — Aperçu d'entité au survol (style Wikipédia)

## Vue d'ensemble

Le système **Entity Hover Card** affiche un aperçu minimal d'une entité au survol d'une référence (lien, nom) — un peu comme les tooltips de Wikipédia lorsqu'une page référence une autre. Conçu pour être réutilisable partout où une entité est citée dans du texte ou une liste.

## Composants et composables

| Élément | Rôle |
|--------|------|
| `EntityMinimalTooltip` | Composant wrapper — entoure le déclencheur (slot par défaut), affiche la carte Minimal en popover au survol. |
| `useEntityHoverCard` | Composable — chargement, cache, annulation des requêtes. |

## Comportement

1. **Lazy loading** : l'entité n'est chargée qu'au survol (délai 250 ms).
2. **Cache en mémoire** : une entité déjà chargée n'est pas re-fetchée (Map globale `entityType:id`).
3. **Interactif** : le survol de la carte la garde ouverte — l'utilisateur peut cliquer sur les liens.
4. **Positionnement** : Floating UI (`flip`, `shift`, `offset`) — la carte évite les bords du viewport.
5. **Indicateur de chargement** : spinner + « Chargement… » pendant le fetch.
6. **Annulation** : `AbortController` annule les requêtes obsolètes (survol rapide sur plusieurs références).

## Utilisation

```vue
<EntityMinimalTooltip entity-type="resources" :entity-id="42">
  <a :href="route('entities.resources.show', { resource: 42 })">Bois</a>
</EntityMinimalTooltip>
```

**Props** :
- `entityType` (requis) : `resources`, `items`, `consumables`
- `entityId` (requis si pas `entity`) : ID de l'entité
- `entity` (optionnel) : entité déjà chargée — évite le fetch
- `tableMeta` (optionnel) : meta du tableau (characteristics) pour les effets
- `placement` (optionnel) : `top`, `bottom`, `left`, `right` — défaut `top`

## Intégration actuelle

- **ResourceIngredientsList** : chaque ingrédient (ressource) est enveloppé par `EntityMinimalTooltip`.
- **Vues Line/Minimal** (Item, Resource, Consumable) : noms d'entités principaux avec tooltip via Route + EntityMinimalTooltip.

## Extensibilité

Pour ajouter d'autres types d'entités (monsters, spells, npcs, etc.) :
1. Étendre `API_ROUTE_BY_TYPE` et `propNameByType` dans `useEntityHoverCard` et `EntityMinimalTooltip`.
2. Ajouter un composant ViewMinimal pour le type (ou réutiliser un existant).
3. S'assurer que l'API `api.tables.{entityType}` supporte `filters[id]` et `format=entities`.

## Pas de librairie externe supplémentaire

Le projet utilise **@floating-ui/vue** (déjà présent) pour le positionnement. Pas besoin de Tippy.js ou autre — le comportement est implémenté en custom (délais, interactivité, cache).

## Fichiers liés

- `resources/js/Pages/Molecules/entity/shared/EntityMinimalTooltip.vue`
- `resources/js/Composables/entity/useEntityHoverCard.js`
- `resources/js/Pages/Molecules/data-display/ResourceIngredientsList.vue`
