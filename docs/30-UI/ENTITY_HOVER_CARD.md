# Entity Overlay Card — Aperçu d'entité (service overlay unifié)

## Vue d'ensemble

Le système **Entity Overlay Card** affiche un aperçu minimal d'une entité depuis une référence (lien, nom) en s'appuyant sur le service overlay unifié. Le déclenchement est **uniformisé en click-first** pour les vues texte d'entités, avec conservation du focus/souris tant que l'overlay reste interactif.

## Terminologie

- **Overlay** : terme de reference pour tout panneau flottant (riche/interactif ou non) gere par le service unifie.
- **Tooltip** : cas simple d'overlay, en general court et non interactif (aide contextuelle).
- **Popover** : synonyme historique ; dans la documentation UI, preferer le terme **overlay**.

## Composants et composables (actuels)

| Élément | Rôle |
|--------|------|
| `OverlayTrigger` | Déclencheur unifié (click/hover/auto), rendu overlay, interactions clavier/souris, lazy/cache. |
| `useOverlay` | Façade du service (trigger, position, dismiss, accessibilité, contenu). |
| `useOverlayContentResolver` | Résolution de contenu (`text/html/component/loader`) + cache TTL + dedup. |

## Comportement

1. **Click-first uniforme** sur les vues texte d'entités.
2. **Lazy loading** : le contenu est résolu à l'ouverture.
3. **Cache en mémoire** : TTL + taille max, avec déduplication des requêtes concurrentes.
4. **Interactif** : reste ouvert tant que focus/souris sont dans le trigger/panel.
5. **Positionnement** : Floating UI (`flip`, `shift`, `offset`) et Teleport modal-aware.
6. **Accessibilité** : ARIA (`expanded`, `controls`, `describedby`), fermeture `Escape`, restauration focus.
7. **Indicateur de chargement** : skeleton/spinner pendant la résolution async.

## Utilisation

```vue
<OverlayTrigger
  :content="{
    key: `resource:${id}`,
    loader: async ({ signal }) => {
      const res = await fetch(`/api/table/resources?format=entities&filters[id]=${id}&limit=1`, { signal });
      const json = await res.json();
      return { component: ResourceViewMinimal, props: { resource: json.entities?.[0], displayMode: 'extended' } };
    }
  }"
  trigger="click"
  placement="top"
  max-width="md"
  :interactive="true"
>
  <a :href="route('entities.resources.show', { resource: id })">Bois</a>
</OverlayTrigger>
```

**Props principales (`OverlayTrigger`)** :
- `content` : `string`, `html`, `component`, ou `loader async`
- `trigger` : `click | hover | auto`
- `placement` : `top|right|bottom|left|...` (Floating UI)
- `maxWidth` : `xs|sm|md|lg|xl|auto`
- `interactive` : maintient ouvert sur focus/souris
- `lazy`, `cache`, `closeOnOutside`, `closeOnEscape`, `focusTrap`

## Intégration actuelle

- **ResourceIngredientsList** : chaque ingrédient est enveloppé par `OverlayTrigger` (loader async + cache).
- **Vues texte d'entités** : passage par `EntityViewTextLink`, lui-même branché sur `OverlayTrigger`.

## Extensibilité

Pour ajouter d'autres types d'entités (monster, spell, npc, etc.) :
1. Ajouter un `loader` adapté dans le `content` du trigger (ou factory dédiée).
2. Retourner un composant minimal + props (`{ component, props }`).
3. Vérifier l'API `api.tables.{entityType}` avec `filters[id]` et `format=entities`.

## Dépendances

Le projet repose sur **@floating-ui/vue** (déjà présent) ; aucune librairie supplémentaire n'est nécessaire.

## Fichiers liés

- `resources/js/Pages/Molecules/overlay/OverlayTrigger.vue`
- `resources/js/Composables/overlay/useOverlay.js`
- `resources/js/Composables/overlay/useOverlayContentResolver.js`
- `resources/js/Composables/overlay/useOverlayService.js`
- `resources/js/Pages/Molecules/data-display/ResourceIngredientsList.vue`
