# État d'implémentation — Système d'Actions pour les Entités

**Date** : 2026-01-06  
**Statut** : ✅ **TERMINÉ** (Toutes les phases complétées)

---

## ✅ Phase 1 : Structure de base — TERMINÉE

### Fichiers créés

1. ✅ **`entity-actions-config.js`** — Configuration centralisée des actions
   - Actions communes définies (view, quick-view, edit, quick-edit, copy-link, download-pdf, refresh, minimize, delete)
   - Support des groupes d'actions pour séparateurs
   - Action `minimize` prévue (fonctionnalité future)

2. ✅ **`useEntityActions.js`** — Composable pour la logique métier
   - Filtrage selon permissions (via `usePermissions`)
   - Support whitelist/blacklist
   - Support contexte (ex: `inPanel` pour minimize)
   - Groupement des actions par groupe

3. ✅ **`EntityActionButton.vue`** (Atom) — Bouton d'action unique
   - Support `icon-only` et `icon-text`
   - Gestion des variants (error pour delete)
   - Taille et couleur configurables

4. ✅ **`EntityActionsList.vue`** (Molecule) — Liste horizontale de boutons
   - Utilisé pour les vues entités (Compact, Minimal, Large)

5. ✅ **`EntityActionsDropdown.vue`** (Molecule) — Menu dropdown
   - Réutilise le composant `Dropdown` existant
   - Support des groupes avec séparateurs
   - Utilisé pour la colonne Actions dans les tableaux

6. ✅ **`EntityActions.vue`** (Organism) — Composant principal flexible
   - Support 3 formats : `buttons`, `dropdown`, `context`
   - Support 2 modes d'affichage : `icon-only`, `icon-text`
   - Filtrage whitelist/blacklist
   - Menu contextuel (clic droit) avec position fixe

---

## ✅ Phase 2 : Intégration vues entités — TERMINÉE

### Fichiers modifiés

1. ✅ **`EntityViewCompact.vue`**
   - Intégré `<EntityActions format="buttons" display="icon-only" />`
   - Remplacement du menu d'actions existant
   - Handler `handleAction` pour toutes les actions

2. ✅ **`EntityViewMinimal.vue`**
   - Intégré `<EntityActions format="buttons" display="icon-only" />`
   - Affiché uniquement au hover (`isHovered`)
   - Handler `handleAction` pour toutes les actions

3. ✅ **`EntityViewLarge.vue`**
   - Intégré `<EntityActions format="buttons" display="icon-text" />`
   - Remplacement des boutons individuels
   - Handler `handleAction` pour toutes les actions

---

## ✅ Phase 3 : Intégration tableaux — TERMINÉE

### Fichiers modifiés

1. ✅ **`EntityTanStackTable.vue`**
   - Ajout automatique de la colonne "Actions" dans la config
   - Transmission de `entityType` et `showActionsColumn` à `TanStackTable`
   - Gestion de l'événement `action`

2. ✅ **`TanStackTable.vue`**
   - Props `entityType` et `showActionsColumn`
   - Transmission des props à `TanStackTableRow`
   - Émission de l'événement `action`
   - Correction du colspan pour la colonne Actions

3. ✅ **`TanStackTableRow.vue`**
   - Colonne Actions avec dropdown
   - Menu contextuel au clic droit (via `Teleport`)
   - Gestion de la fermeture du menu contextuel
   - Props `entityType` et `showActionsColumn`

4. ✅ **`TanStackTableHeader.vue`**
   - Support de la colonne Actions (sans label)
   - Colonne vide affichée si `showActionsColumn` est true

---

## ✅ Refactorisation — TERMINÉE

### Fichiers modifiés

1. ✅ **`EntityActionsMenu.vue`**
   - Refactorisé pour utiliser `EntityActions` en interne
   - Wrapper de compatibilité pour l'API legacy
   - Conversion des props de permissions en blacklist
   - Émission des événements legacy pour compatibilité

---

## ✅ Phase 4 : Tests et documentation — TERMINÉE

1. ✅ Tests unitaires pour `useEntityActions`
   - Fichier : `tests/unit/composables/useEntityActions.test.js`
   - Tests de filtrage par permissions
   - Tests de filtrage whitelist/blacklist
   - Tests de contexte (inPanel pour minimize)
   - Tests de groupement des actions
   - Tests d'actions nécessitant une entité

2. ✅ Documentation d'utilisation
   - Fichier : `docs/30-UI/ENTITY_ACTIONS_GUIDE.md`
   - Guide complet avec exemples
   - API du composant
   - Actions disponibles
   - Permissions
   - Personnalisation
   - Dépannage
   - Migration depuis EntityActionsMenu

3. ⏳ Tests d'intégration pour les composants (optionnel)
   - Tests E2E pourraient être ajoutés plus tard si nécessaire

---

## 🎯 Prochaines étapes

1. ✅ **Intégrer dans les vues entités** (Phase 2) — TERMINÉ
2. ✅ **Intégrer dans les tableaux** (Phase 3) — TERMINÉ
3. ✅ **Refactoriser EntityActionsMenu** — TERMINÉ
4. ✅ **Tester et documenter** (Phase 4) — TERMINÉ

## 📚 Documentation

- **Guide utilisateur** : `docs/30-UI/ENTITY_ACTIONS_GUIDE.md`
- **Tests unitaires** : `tests/unit/composables/useEntityActions.test.js`
- **Proposition initiale** : `docs/100- Done/ENTITY_ACTIONS_SYSTEM_PROPOSAL.md`

---

## 📝 Notes importantes

### Action "minimize"
- ✅ Bouton et icône prévus dans la config
- ⏳ Fonctionnalité à implémenter plus tard
- ✅ Filtrage automatique : seulement visible si `context.inPanel === true`

### Menu contextuel
- ✅ Réutilise le même système que le dropdown (pas de composant séparé)
- ✅ Position fixe avec `position: fixed` et coordonnées x, y
- ✅ Gestion des clics pour fermer le menu

### Permissions
- ✅ Utilise `usePermissions` existant
- ✅ Support des permissions globales (`canViewAny`, `canUpdateAny`, etc.)
- ⏳ TODO: Implémenter permissions par instance si nécessaire

---

## 📚 Références

- **Proposition** : `docs/100- Done/ENTITY_ACTIONS_SYSTEM_PROPOSAL.md`
- **Configuration** : `resources/js/Entities/entity-actions-config.js`
- **Composable** : `resources/js/Composables/entity/useEntityActions.js`
- **Composants** : `resources/js/Pages/Organismes/entity/EntityActions.vue`

