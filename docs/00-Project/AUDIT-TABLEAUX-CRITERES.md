# Audit — Critères des tableaux d'entités

**Date** : 2026-03  
**Objectif** : Vérifier que tous les tableaux respectent les 6 critères suivants.

---

## Critères

| # | Critère | Détail |
|---|---------|--------|
| 1 | **Propriétés principales affichées par défaut** | Colonnes « métier » (nom, niveau, état, type, etc.) avec `table.defaultVisible: { md: true, lg: true, xl: true }` (au moins). |
| 2 | **Résumés de caractéristiques** | Si l'entité a beaucoup de champs de stats, une colonne dédiée (type `chips` + `CharacteristicsCard` par groupe) pour limiter le nombre de colonnes. Ex. : Monster (Combat, Résistances, Stats, Dommages, Contrôle). |
| 3 | **Filtres principaux visibles** | Filtres level, état (state), type, etc. avec `filterable: { id, type, defaultVisible: true }`. |
| 4 | **Filtres secondaires masqués par défaut** | Autres filtres avec `filterable: { ..., defaultVisible: false }` (affichés dans « Autres filtres »). |
| 5 | **Tri sur la plupart des colonnes** | Colonnes avec valeurs comparables : `table.sortable: true`. Aligné avec `allowedSort` du contrôleur API Table. |
| 6 | **QuickEdit fonctionnel** | `_tableConfig.quickEdit.enabled: true`, `_quickeditConfig.fields` rempli, et backend bulk OK. |

---

## État par entité

### Référence : Resource ✅

- **Colonnes principales** : name, level, rarity, state visibles md/lg/xl ; description, image, etc. selon taille.
- **Résumés** : Non (pas de colonnes caractéristiques type Monster).
- **Filtres** : level, state, resource_type_id, rarity en principal (defaultVisible true) ; read_level, write_level, id en secondaire (defaultVisible false).
- **Tri** : sortable sur name, level, rarity, state, price, weight, etc.
- **QuickEdit** : activé, champs alignés backend.

### Référence : Monster ✅

- **Colonnes principales** : creature_name, monster_race, size, is_boss visibles ; colonnes résumé (Combat, Résistances, Stats, Dommages, Contrôle) visibles md/lg/xl.
- **Résumés** : Oui (creature_summary_combat, creature_summary_resistance, etc.) ; colonnes unitaires creature_* masquées par défaut.
- **Filtres** : size, is_boss, monster_race_id, creature_level, creature_hostility, creature_state en principal ; reste en secondaire.
- **Tri** : sortable sur les colonnes pertinentes.
- **QuickEdit** : activé.

### Item ✅ (aligné 2026-03)

- **Colonnes** : `table` avec defaultVisible (name, level, rarity, state, item_type principaux ; id, description, auto_update, dates masqués par défaut).
- **Résumés** : Non (pas de colonnes caractéristiques item).
- **Filtres** : level, state, item_type_id, rarity avec filterable (principaux defaultVisible true).
- **Tri** : sortable sur id, name, level, rarity, state, item_type, dofusdb_id, created_at, updated_at.
- **QuickEdit** : activé, _quickeditConfig.fields renseigné.

### Spell ✅ (aligné 2026-03)

- **Colonnes** : `table` avec defaultVisible (name, level, pa, state principaux ; element, category, description, spell_types, etc.).
- **Filtres** : level, state, pa, element, category avec filterable (level, state en principal).
- **Tri** : sortable sur id, name, level, pa, po, area, dofusdb_id, state, created_at, updated_at.
- **QuickEdit** : activé.

### Consumable ✅ (aligné 2026-03)

- **Colonnes** : `table` avec defaultVisible (name, level, rarity, state, consumable_type principaux).
- **Filtres** : level, state, rarity, consumable_type_id (principaux defaultVisible true).
- **Tri** : sortable sur id, name, level, rarity, state, consumable_type, dofusdb_id, created_at, updated_at.
- **QuickEdit** : activé.

### Npc ✅ (filtres 2026-03)

- **Filtres** : breed_id, specialization_id, creature_level, creature_state ; backend NpcTableController + filterOptions ; toCell creature_level/creature_state dans Npc.js.

### Breed, Campaign, Scenario, Attribute, Panoply, Capability, Specialization, Shop, Resource-type

- **Structure** : la plupart n’ont que `display` (pas de `table` détaillé) → colonnes toutes visibles, colonnes visibles selon fallback. QuickEdit vérifié : tous ont _tableConfig.quickEdit.enabled: true et _quickeditConfig.fields rempli. Colonnes principales définies dans les descriptors (name, state, etc.). Action optionnelle : ajouter table.sortable / filterable / defaultVisible.
- **QuickEdit** : tous ont _tableConfig.quickEdit et _quickeditConfig.fields.
- **Action** : optionnel — ajouter `table.sortable` et `table.filterable` + `defaultVisible` pour les colonnes principales et les filtres level/state quand l’API les supporte.

---

## Synthèse des actions

| Entité | 1 Principales | 2 Résumés | 3–4 Filtres | 5 Tri | 6 QuickEdit |
|--------|----------------|-----------|-------------|-------|-------------|
| Resource | ✅ | — | ✅ | ✅ | ✅ |
| Monster | ✅ | ✅ | ✅ | ✅ | ✅ |
| Item | ✅ | — | ✅ | ✅ | ✅ |
| Spell | ✅ | — | ✅ | ✅ | ✅ |
| Consumable | ✅ | — | ✅ | ✅ | ✅ |
| Npc | ✅ | — | ✅ | ✅ | ✅ |
| Attribute | ✅ | — | ✅ | ✅ | ✅ |
| Capability | ✅ | — | ✅ | ✅ | ✅ |
| Campaign | ✅ | — | ✅ | ✅ | ✅ |
| Scenario | ✅ | — | ✅ | ✅ | ✅ |
| Specialization | ✅ | — | ✅ | ✅ | ✅ |
| Panoply | ✅ | — | ✅ | ✅ | ✅ |
| Shop | ✅ | — | ✅ | ✅ | ✅ |
| Breed | ✅ | — | ✅ | ✅ | ✅ |
| Resource-type | ✅ | — | ✅ | ✅ | ✅ |

---

## Fichiers concernés

- **Descriptors** : `resources/js/Entities/<entity>/<entity>-descriptors.js` (ajout de `table` avec sortable, filterable, defaultVisible).
- **Backend** : `app/Http/Controllers/Api/Table/<Entity>TableController.php` (allowedSort, filtres déjà en place pour la plupart).
- **Filtres UI** : `TanStackTableFilters.vue` utilise `filter.defaultVisible !== false` pour « Filtres principaux » et `=== false` pour « Autres filtres ».
