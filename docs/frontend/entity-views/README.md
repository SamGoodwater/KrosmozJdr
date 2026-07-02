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
