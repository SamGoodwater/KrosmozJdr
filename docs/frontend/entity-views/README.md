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

## Référence DofusDB

Sur les surfaces **Full** et **édition** (page / modal), l’action `view-dofusdb` (icône
`/images/logos/dofus.png`) apparaît si l’entité a un `dofusdb_id`. Le clic ouvre le store Pinia
`dofusDbReference` ; le panneau `DofusDbReferencePanel` (monté dans `Main`) affiche le deep-link
et un bouton `window.open` (pas d’iframe).
