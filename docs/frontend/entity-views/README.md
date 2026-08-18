# Entity views

Les vues d'entités standardisent l'affichage des fiches JDR.

| Vue | Usage | Composant |
| --- | --- | --- |
| `minimal` | carte / grille | `*ViewMinimal.vue` |
| `line` | ligne dense de table | `*LineRow.vue` |
| `text` | inline + overlay | `*ViewText.vue` |
| `full` | détail page ou modal | `*ViewFull.vue` |
| `edit` | édition | `EntityEditForm`, `*QuickEdit` |

Ne pas créer `ViewLarge` ni `ViewCompact`. Utiliser `resolveEntityViewComponent(type, 'full')`.

## Sorts (usage unifié)

`SpellUsageBlock` factorise méta PA/PO + résolution + chips d’effets pour Minimal,
Line et le bandeau Full. Props `parts` : `meta` | `effects` | `all` (méta sous le
titre, effets en pleine largeur). Full : section Utilisation via la même méta ;
bandeau chips au-dessus du journal ; empty states structurés vs texte libre.

## Parcours d’ouverture

```
minimal compact → survol : overlay déployé → double-clic / quick-view : modal full → agrandir : page full
```

- L’overlay (`EntityMinimalCard`) ne décale pas la grille.
- **Tous** les `*ViewMinimal` (hors `language`) passent par `EntityMinimalCard` : double-clic → modal, whitelist d’actions commune, pin flottant.
- Shell commun : `useEntityMinimalShell` + `EntityMinimalTitle`.
- PNJ : mêmes densités de caractéristiques que les monstres (créature liée).
- La **page** n’est pas l’entrée principale (icône / overflow uniquement).
- En `line` : même logique → modal full (`EntityLineRowActions`).
- Actions du minimal : visibles surtout en déployé (haut-droite), overflow automatique.
- **Favoris** : persistés en BDD (`user_favorites`) pour les comptes connectés. Accès header
  (cœur) → modal sans changer de page ; page `/favoris`. Invité·e : message pour se connecter.
  Icône cœur plein/vide dans les menus d’options. Liste en vue Minimal ; recherche via
  `EntitySearchHitCard` (aperçu Minimal au survol, clic → modal full). Favoris en tête des résultats.

Presets (`ENTITY_ACTION_CONTEXT_PRESETS` dans `entity-actions-config.js`) :

| Preset | Ordre (extrait) |
| --- | --- |
| `minimalLine` | state → pin → quick-view → quick-edit → view-dofusdb → favorite → copy-link → view → edit |
| `modalDetail` | state → favorite → copy-link → view (agrandir) → view-dofusdb → edit → refresh → delete |
| `pageDetail` | state → favorite → copy-link → view-dofusdb → edit → refresh → delete |

## Référence DofusDB

L’action `view-dofusdb` (icône `/images/logos/dofus.png`) apparaît si l’entité a un
`dofusdb_id` : en **minimal déployé**, **modal** et **page**. Le clic ouvre le store Pinia
`dofusDbReference` ; le panneau `DofusDbReferencePanel` (monté dans `Main`) affiche le deep-link
et un bouton `window.open` (pas d’iframe).

## Caractéristiques

Densités `icon` / `labeled` / `spacious` sur `CharacteristicsCard` — voir
[COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
Applicable aux **monstres** et **PNJ** (créature liée) ; sorts/objets gardent leurs effets dédiés.

## Tableau objets

La vue **Line** (`ItemLineRow`) reste courte : description sur 2 lignes, bonus en icônes, recette sur la fiche Full.

La vue **Colonnes** (`item-descriptors.js`) :

| Par défaut | Masqué (sélecteur de colonnes) |
| --- | --- |
| Image, nom, niveau (sm+), type (sm+), rareté (sm+), bonus (md+) | Description, résumé, prix, version Dofus |
| État (sm+) **si** `updateAny` | Métadonnées (id, dates, DofusDB…) |

Les en-têtes affichent `helper` / `general.tooltip` au survol (`TanStackTableHeader.vue`).

## Tableaux d’entités (filtres, tri, recherche)

Index server-side (`items`, `monsters`, `spells`, `resources`, `consumables`, `conditions`) : changer un filtre relance la requête (debounce) ; Réinitialiser vide `filters` et revient à la page 1. Les multi-sélections partent en `filters[key][]`. Le tri envoie `sorts[i][field]` avec l’alias SQL (`item_type` → `item_type_id`, `creature_level` via jointure). La barre de recherche envoie `search=`.

Index client (`npcs`, etc.) : le filtre/tri/recherche portent sur le dataset déjà chargé ; la recherche cible le nom (et les colonnes `searchable`) même si le payload n’a pas de `cells` pré-générées.

### Sort depuis un monstre

Le payload tableau des monstres n’embarque pas l’arbre d’effets. Un clic sur un sort (`MonsterCreatureSpellsList`) hydrate la fiche via `api.tables.spells?whitelist[]=` puis affiche `SpellViewMinimal` en `extended`.
