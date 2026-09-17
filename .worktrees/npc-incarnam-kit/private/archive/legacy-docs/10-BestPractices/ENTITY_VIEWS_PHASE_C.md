# Vues entité — référence release 1.3.2

Modèle officiel : **minimal**, **line**, **texte**, **full**, **edit**.

**Voir aussi** : [ENTITY_VIEWS.md](../30-UI/ENTITY_VIEWS.md) · [DECISIONS Q7–Q9](../110-%20To%20Do/DECISIONS-OUVERTES-release-1.3.2.md)

## Glossaire (ne pas confondre)

| Terme | Signification |
| --- | --- |
| **`ViewFull`** | Vue détail entité (page ou modal) |
| **`PROPERTY_DISPLAY_MODES.compact`** | Libellés **courts** des caractéristiques (chips), pas une vue entité |
| **`displayMode` Minimal** | `hover` / `compact` / `extended` sur les **cartes** Minimal |
| **`EntityViewHeader mode="compact"`** | Densité du **header** en modal full |

## Contrat `ViewFull`

| Prop | Rôle |
| --- | --- |
| `showActions` | Barre `EntityActions` |
| `inModal` | `true` en `EntityModal` → header dense, `titleTag` souvent `h2` |
| `titleTag` | `h1` sur page Show, `h2` en modal |
| `tableMeta` | Métadonnées tableau (ex. caractéristiques créature) |

`EntityViewHeader` : `:mode="inModal ? 'compact' : 'full'"` (layout header), ou `mode="minimal"` sur les vues Minimal.

## Résolution dynamique

[`resolveEntityViewComponent.js`](../../resources/js/Utils/entity/resolveEntityViewComponent.js) : clé **`full`** → `*ViewFull.vue`.

Préférences modal : [`useEntityViewFormat.js`](../../resources/js/Composables/store/useEntityViewFormat.js) — formats **`full`**, **`minimal`**, **`text`**.

## Raccourcis tableau (Q8)

| Interaction | Comportement |
| --- | --- |
| Clic | Sélection |
| Double-clic | Modal **full** |
| Ctrl / Cmd + clic | Page Show |
| Alt + clic | Édition si droit ; sinon notification |
| Clic droit | Menu actions |

Composable : [`useEntityIndexTableIntents.js`](../../resources/js/Composables/entity/useEntityIndexTableIntents.js).

## Création (Q9)

Après `CreateEntityModal` : redirection ou ouverture **édition complète** (`entities.*.edit` ou modal d’édition dédiée).

- Frontend : `EntityEditForm` envoie `redirect_after_create=edit` ; champs optionnels par type via [`entity-create-config.js`](../../resources/js/Utils/entity/entity-create-config.js) (`getEntityCreateAllowFieldKeys`).
- Backend : trait `RedirectsAfterEntityCreate` sur les contrôleurs `store` concernés.

## Recette manuelle

1. Tableau → mode **Ligne** / **Minimal** / **Colonne** (sans bandeau densité).
2. Double-clic → modal **full** ; Ctrl+clic → page Show.
3. Alt+clic sans droit édition → notification uniquement.
4. Page Show → `ViewFull` + bouton retour (`EntityViewFullWrapper`).
5. Créer une entité → édition complète après enregistrement.
