# En-tête de page et boutons d’action

Règles communes pour le titre d’une page, ses actions et l’enregistrement. Objectif : les mêmes termes et le même emplacement partout.

## Portée

- **En-tête `PageHeader`** : toutes les pages **hors CMS** (`Pages/page/*`, `Pages/section/*`) et **hors fiches d’entités** (`Pages/entity/*`). Admin, compte utilisateur, notifications, favoris, retours, pages statiques.
- **Lexique** : partout, y compris entités, CMS, modales et docks d’édition.

## Lexique

Source unique : `resources/js/Utils/atomic-design/actionLabels.js` (`ACTION`, `UNSAVED`).

| Clé | Libellé | Pendant le traitement | Sens |
|-----|---------|-----------------------|------|
| `save` | Enregistrer | Enregistrement… | Action principale d’un formulaire ou d’une page |
| `discard` | Annuler les modifications | — | Revient à l’état enregistré, on reste sur la page |
| `back` | Retour | — | Quitte la page |
| `close` | Fermer | — | Ferme une modale ou un panneau |
| `create` | Créer | Création… | Crée un élément |
| `delete` | Supprimer | — | Supprime (toujours en toutes lettres) |
| `run` | Lancer | Lancement… | Démarre une tâche (sauvegarde, mise à jour…) |
| `confirm` | Confirmer | — | Valide une confirmation (mot de passe, action sensible) |

Termes à ne plus utiliser : « Sauvegarder », « Valider » (pour enregistrer), « Reset », « Réinitialiser » (pour un formulaire), « Suppr. », « Sauvegarde… ». Le contexte vient du titre de la carte ou de la page, pas du bouton (« Enregistrer », pas « Enregistrer les traits »).

« Réinitialiser » reste réservé aux **filtres** d’une liste (remettre les filtres par défaut).

## Emplacement

- **Un seul « Enregistrer » par page**, dans l’en-tête. Il envoie tous les formulaires modifiés.
- Exceptions qui gardent leur propre bouton : **mot de passe** (carte dédiée), **actions par ligne** de tableau, **modales** (pied de modale : « Fermer » + action).
- Fiches d’entités : `EntityEditHeader` + `EntityEditForm` (`layout-profile="sheet"`) ;
  le dock flottant `EditActionDock` reste disponible pour les formulaires qui ne
  masquent pas encore la barre (`hideActionDock`). Lexique inchangé.
- Pages de tâche (sauvegarde, mise à jour, nettoyage) : l’action principale de l’en-tête est « Lancer… » via le slot `primary`.

## Composants

- `Molecules/layout/PageHeader.vue` : titre (`h1`), sous-titre, retour, slots `actions` / `primary` / `meta` / `tabs`. Collant par défaut. Appelle `setPageTitle` et masque le titre du header global (pas de doublon).
- `Molecules/action/PageSaveActions.vue` : badge « Modifications non enregistrées », indicateur « Enregistré » ou « Échec de l’enregistrement » (l’échec reste visible à côté du badge), « Annuler les modifications », « Enregistrer », Ctrl+S / Cmd+S.
- `Composables/form/usePageForms.js` : regroupe plusieurs `useForm`, `saveAll()` (formulaires modifiés seulement, arrêt à la première erreur), `discardAll()`, garde de sortie (navigation Inertia GET vers une autre page + fermeture d’onglet).

```js
const form = useForm({ rules: props.matrix });
const pageForms = usePageForms();
pageForms.register('rules', form, (callbacks) =>
    form.patch(route('admin.entity-display-visibility.update'), {
        preserveScroll: true,
        preserveState: true,
        ...callbacks,
    }),
);
```

```vue
<PageHeader title="Affichage des entités" subtitle="…" :forms="pageForms">
    <template #actions>
        <Btn size="sm" variant="ghost">Actualiser</Btn>
    </template>
</PageHeader>
```

Passer `preserveState: true` : la page n’est pas remontée entre deux envois et Inertia met à jour les valeurs de référence du formulaire après succès (`isDirty` repasse à faux).

## Attention

- `PageHeader` collant utilise `top-0` : le `<main>` défilant porte déjà le padding du header global.
- La garde de sortie ignore le préchargement, les rechargements partiels (`only`) et les envois non GET.
