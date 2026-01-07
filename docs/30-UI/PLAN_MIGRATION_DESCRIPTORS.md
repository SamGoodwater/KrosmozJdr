# Plan de migration — Système Entity Field Descriptors

**Date de création** : 2025-01-27  
**Date de finalisation** : 2026-01-06  
**Statut** : ✅ **100% Complété**  
**Entités migrées** : 16/16

---

## 📊 État actuel

### ✅ Entités migrées (16/16)

1. ✅ `resource` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
2. ✅ `resource_type` — Complète (descriptors + adapter + Index.vue + tests)
3. ✅ `item` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
4. ✅ `spell` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
5. ✅ `monster` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
6. ✅ `creature` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
7. ✅ `npc` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
8. ✅ `classe` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
9. ✅ `consumable` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
10. ✅ `campaign` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
11. ✅ `scenario` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
12. ✅ `attribute` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
13. ✅ `panoply` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
14. ✅ `capability` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
15. ✅ `specialization` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)
16. ✅ `shop` — Complète (descriptors + adapter + Index.vue + bulk controller + tests)

### 📊 Résultats

- **15 contrôleurs bulk** créés et testés
- **16 contrôleurs table** supportent `?format=entities`
- **16 adapters frontend** créés
- **165 tests passent** (966 assertions) — Voir [TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md](../100-%20Done/TESTS_ENTITY_DESCRIPTORS_IMPLEMENTATION.md)

---

## 🎯 Phase 1 : Migrations prioritaires (spell, monster, creature)

### Objectif
Migrer les 3 entités les plus utilisées vers le système descriptor.

### Étapes par entité

#### 1.1 Spell
- [ ] Créer `resources/js/Entities/spell/spell-descriptors.js`
  - [ ] Définir tous les `FieldDescriptor` (id, name, level, description, etc.)
  - [ ] Configurer `display.views` (table, minimal, text, compact, extended)
  - [ ] Configurer `display.sizes` (small, normal, large)
  - [ ] Configurer `edit.form` (type, required, options, bulk, group, help, tooltip)
  - [ ] Définir `SPELL_VIEW_FIELDS` (compact, extended, quickEdit)
- [ ] Créer `resources/js/Entities/spell/spell-adapter.js`
  - [ ] Implémenter `buildSpellCell(entity, fieldKey, opts)`
  - [ ] Implémenter `adaptSpellEntitiesTableResponse({ meta, entities })`
  - [ ] Gérer les relations (si nécessaire)
- [ ] Mettre à jour `app/Http/Controllers/Api/Table/SpellTableController.php`
  - [ ] Ajouter support `?format=entities`
  - [ ] Retourner `entities[]` au lieu de `rows[]` avec `cells`
- [ ] Mettre à jour `resources/js/Pages/Pages/entity/spell/Index.vue`
  - [ ] Ajouter `?format=entities` à `serverUrl`
  - [ ] Ajouter `:response-adapter="adaptSpellEntitiesTableResponse"`
  - [ ] Migrer `fieldsConfig` et `defaultEntity` vers `createFieldsConfigFromDescriptors` / `createDefaultEntityFromDescriptors`
  - [ ] Remplacer le bulk edit panel par `EntityQuickEditPanel`
  - [ ] Utiliser `useBulkRequest` pour les appels bulk
  - [ ] Ajuster le layout grid (`xl:grid-cols-[minmax(0,1fr)_380px]`)
- [ ] Mettre à jour `resources/js/Entities/entity-registry.js`
  - [ ] Ajouter le cas `spells` avec `getSpellFieldDescriptors`, `buildSpellCell`, `SPELL_VIEW_FIELDS`, `adaptSpellEntitiesTableResponse`
- [ ] Créer `app/Http/Controllers/Api/SpellBulkController.php` (si nécessaire)
  - [ ] Implémenter `bulkUpdate` avec validation
- [ ] Ajouter route `PATCH /api/entities/spells/bulk` dans `routes/api.php` (si nécessaire)

#### 1.2 Monster
- [ ] Même processus que `spell` (descriptors → adapter → backend → Index.vue → registry → bulk controller)

#### 1.3 Creature
- [ ] Même processus que `spell` (descriptors → adapter → backend → Index.vue → registry → bulk controller)

### Critères de validation
- ✅ Le tableau affiche correctement toutes les colonnes
- ✅ Les filtres fonctionnent
- ✅ Le tri fonctionne
- ✅ Les vues (minimal, compact, extended, text) fonctionnent
- ✅ Le quick edit panel fonctionne
- ✅ L'édition unique fonctionne
- ✅ La création fonctionne
- ✅ Les permissions sont respectées

---

## 🔍 Phase 2 : Tests et validation

### Objectif
Valider le système descriptor sur les 6 entités migrées (3 existantes + 3 nouvelles).

### Tests fonctionnels
- [ ] **Tableaux** : Vérifier que tous les tableaux affichent correctement les données
- [ ] **Filtres** : Tester tous les types de filtres (text, select, multi-select, range, date)
- [ ] **Tri** : Vérifier le tri sur toutes les colonnes
- [ ] **Vues** : Tester minimal, compact, extended, text pour chaque entité
- [ ] **Quick edit** : Tester l'édition multiple avec différents types de champs
- [ ] **Édition unique** : Tester l'édition d'une seule entité
- [ ] **Création** : Tester la création d'entités
- [ ] **Permissions** : Vérifier que les permissions sont respectées (affichage + édition)

### Tests de performance
- [ ] **Génération de cellules** : Mesurer le temps de génération pour 1000+ entités
- [ ] **Rendu** : Vérifier que le rendu reste fluide avec beaucoup de données
- [ ] **Mémoire** : Vérifier qu'il n'y a pas de fuites mémoire

### Tests de cohérence
- [ ] **Couleurs badges** : Vérifier que les couleurs auto sont cohérentes
- [ ] **Truncation** : Vérifier que la truncation fonctionne partout
- [ ] **Tooltips** : Vérifier que les tooltips s'affichent correctement
- [ ] **Responsive** : Tester sur différentes tailles d'écran

---

## 🚀 Phase 3 : Migrations restantes (npc, panoply, classe, etc.)

### Objectif
Migrer les 10 entités restantes vers le système descriptor.

### Approche
- Migrer par ordre de priorité (npc → panoply → classe → ...)
- Réutiliser les patterns établis dans Phase 1
- Valider chaque entité avant de passer à la suivante

### Liste complète
1. `npc` (priorité moyenne)
2. `panoply` (priorité moyenne)
3. `classe` (priorité moyenne)
4. `capability` (priorité basse)
5. `attribute` (priorité basse)
6. `specialization` (priorité basse)
7. `shop` (priorité basse)
8. `scenario` (priorité basse)
9. `campaign` (priorité basse)
10. `consumable` (priorité basse)

---

## ✨ Phase 4 : Améliorations UX

### Objectif
Améliorer l'expérience utilisateur du système d'édition.

### Améliorations prévues
- [ ] **Indicateur "X champs modifiés"** dans le header du modal d'édition (multi-edit)
  - Afficher le nombre de champs modifiés dans le titre du modal
  - Mettre à jour en temps réel
- [ ] **Amélioration du quick edit panel**
  - [ ] Afficher un indicateur visuel pour les champs modifiés
  - [ ] Ajouter un bouton "Tout réinitialiser" (reset tous les champs)
  - [ ] Améliorer l'affichage des sections (groupes)
- [ ] **Amélioration de l'EntityEditForm**
  - [ ] Améliorer l'affichage des champs "valeurs différentes" en multi-edit
  - [ ] Ajouter des raccourcis clavier (Ctrl+S pour sauvegarder, Esc pour annuler)
- [ ] **Amélioration des vues**
  - [ ] Ajouter des animations de transition entre les vues
  - [ ] Améliorer l'affichage des tooltips (position, timing)
- [ ] **Amélioration des badges**
  - [ ] Ajouter plus de schémas de couleurs (`autoScheme`)
  - [ ] Améliorer l'effet glassmorphism

---

## 📚 Phase 5 : Documentation et nettoyage

### Objectif
Finaliser la documentation et nettoyer le code legacy.

### Documentation
- [ ] **Mettre à jour `ENTITY_FIELD_DESCRIPTORS.md`**
  - [ ] Ajouter des exemples pour chaque type d'entité migrée
  - [ ] Documenter les patterns récurrents
  - [ ] Ajouter un guide de migration pas-à-pas
- [ ] **Créer un guide de migration**
  - [ ] Template pour créer un nouveau descriptor
  - [ ] Checklist de migration
  - [ ] Exemples de code
- [ ] **Mettre à jour la documentation générale**
  - [ ] Mettre à jour `docs.index.json`
  - [ ] Ajouter des liens vers la nouvelle documentation
- [ ] **Documenter les bonnes pratiques**
  - [ ] Quand utiliser `autoScheme` vs `color` fixe
  - [ ] Comment choisir les tailles (small, normal, large)
  - [ ] Comment organiser les groupes dans `edit.form.group`

### Nettoyage
- [ ] **Supprimer le code legacy**
  - [ ] Vérifier qu'il n'y a plus de références aux anciens `*-field-schema.js`
  - [ ] Supprimer les anciens bulk edit panels spécifiques (si encore présents)
  - [ ] Nettoyer les imports inutilisés
- [ ] **Optimiser le code**
  - [ ] Vérifier les performances des adapters
  - [ ] Optimiser les fonctions de génération de cellules
  - [ ] Réduire la duplication de code entre adapters

---

## 🎨 Phase 6 : Optimisations et polish

### Objectif
Optimiser les performances et améliorer la qualité du code.

### Optimisations
- [ ] **Lazy loading des descriptors**
  - [ ] Charger les descriptors uniquement quand nécessaire
  - [ ] Mettre en cache les descriptors générés
- [ ] **Optimisation des adapters**
  - [ ] Mettre en cache les cellules générées (si possible)
  - [ ] Optimiser les boucles de génération
- [ ] **Optimisation du rendu**
  - [ ] Utiliser `v-memo` pour les cellules qui ne changent pas
  - [ ] Optimiser les re-renders

### Polish
- [ ] **Améliorer les erreurs**
  - [ ] Ajouter des messages d'erreur clairs
  - [ ] Gérer les cas d'erreur (descriptor manquant, adapter manquant, etc.)
- [ ] **Améliorer les types TypeScript/JSDoc**
  - [ ] Ajouter des types plus précis pour les descriptors
  - [ ] Améliorer l'autocomplétion
- [ ] **Tests unitaires**
  - [ ] Tester les fonctions de génération de cellules
  - [ ] Tester les adapters
  - [ ] Tester les utilitaires (descriptor-form, color, etc.)

---

## 📋 Checklist globale

### Avant de commencer une migration
- [ ] Identifier tous les champs de l'entité
- [ ] Identifier les relations nécessaires
- [ ] Identifier les permissions nécessaires
- [ ] Identifier les filtres et tris nécessaires

### Pendant la migration
- [ ] Créer le descriptor avec tous les champs
- [ ] Créer l'adapter avec la logique de génération de cellules
- [ ] Mettre à jour le backend pour supporter `?format=entities`
- [ ] Mettre à jour l'Index.vue pour utiliser le nouveau système
- [ ] Mettre à jour le registry
- [ ] Créer le bulk controller (si nécessaire)

### Après la migration
- [ ] Tester tous les cas d'usage
- [ ] Vérifier les permissions
- [ ] Vérifier les performances
- [ ] Mettre à jour la documentation

---

## 🎯 Priorités et estimations

### Priorité haute (Phase 1)
- **Spell** : ~2-3h
- **Monster** : ~2-3h
- **Creature** : ~2-3h
- **Total** : ~6-9h

### Priorité moyenne (Phase 3 - partie 1)
- **Npc** : ~2h
- **Panoply** : ~2h
- **Classe** : ~2h
- **Total** : ~6h

### Priorité basse (Phase 3 - partie 2)
- **7 entités restantes** : ~1-2h chacune
- **Total** : ~7-14h

### Tests et validation (Phase 2)
- **Tests fonctionnels** : ~3-4h
- **Tests de performance** : ~2h
- **Total** : ~5-6h

### Améliorations UX (Phase 4)
- **Indicateur "X champs modifiés"** : ~1h
- **Améliorations quick edit** : ~2h
- **Améliorations EntityEditForm** : ~2h
- **Total** : ~5h

### Documentation (Phase 5)
- **Mise à jour documentation** : ~2-3h
- **Guide de migration** : ~2h
- **Nettoyage code** : ~1-2h
- **Total** : ~5-7h

### Optimisations (Phase 6)
- **Lazy loading** : ~2h
- **Optimisation adapters** : ~2h
- **Polish** : ~2h
- **Total** : ~6h

**Estimation totale** : ~40-50h

---

## 📝 Notes

- Les estimations sont approximatives et peuvent varier selon la complexité de chaque entité.
- Il est recommandé de valider chaque phase avant de passer à la suivante.
- Les améliorations UX peuvent être faites en parallèle des migrations.
- La documentation doit être mise à jour au fur et à mesure des migrations.

---

## 🔄 Mise à jour

Ce plan sera mis à jour régulièrement pour refléter l'avancement réel du projet.

**Dernière mise à jour** : 2025-01-27

