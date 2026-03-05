# Plan — Tableaux et affichage des entités (alignement Monster / Resource)

**Date** : 2026-03  
**Contexte** : Seuls **Monster** et **Resource** ont un affichage tableau + modal + vues (Large, Compact, Minimal) pleinement aligné. Ce document propose un plan pour étendre le même niveau d’UI/UX à toutes les autres entités.

---

## 1. État actuel (référence : Monster & Resource)

### 1.1 Ce qui est en place pour Monster

| Élément | Détail |
|--------|--------|
| **Page Index** | `EntityTanStackTable` + `serverUrl` (format=entities) + `@loaded` → stockage `tableMeta` |
| **Modal** | `EntityModal` reçoit `:table-meta="tableMeta"` et le transmet aux vues |
| **Vues** | Large, Compact, Minimal : prop `tableMeta`, section « Caractéristiques » avec `CharacteristicsCard` (dense en Compact/Minimal, étendu en Large) |
| **Modèle** | `Monster.toCell()` avec cas dédiés (creature_*, colonnes résumé, `_toSummaryGroupCell` → `CharacteristicsCard`) |
| **Descriptors** | Colonnes résumé (Combat, Résistances, Stats, Dommages, Contrôle) visibles par défaut ; propriétés unitaires masquées |
| **API Table** | Renvoie `meta.characteristics.creature.byDbColumn` pour les libellés/icônes/couleurs |

### 1.2 Ce qui est en place pour Resource

| Élément | Détail |
|--------|--------|
| **Page Index** | Tableau serveur, modal, pas de `tableMeta` (pas de carte caractéristiques dans le modal) |
| **Vues** | Large, Compact, Minimal, Text présents et utilisent descriptors + `toCell()` |
| **Modèle** | `Resource.toCell()` avec formatters (level, rarity, etc.) |
| **Descriptors** | Table + édition + bulk cohérents |

### 1.3 Écart à combler pour les autres entités

- **Pages Index** : ajouter `tableMeta` + `@loaded` avec `meta` et passer `:table-meta="tableMeta"` à `EntityModal` pour les entités qui auront une meta utile (ex. caractéristiques).
- **Vues** : harmoniser structure (header, sections, technique) et, pour les entités « avec caractéristiques », ajouter `CharacteristicsCard` (prop `tableMeta`, `creatureData` / données équivalentes).
- **Backend** : pour les entités avec caractéristiques, faire renvoyer `meta.characteristics.*.byDbColumn` (ou équivalent) par l’API Table quand `format=entities`.
- **Descriptors** : vérifier/ajuster `table.defaultVisible`, colonnes résumé si besoin, cohérence des labels/icônes.

---

## 2. Inventaire des entités

| Entité | Table API | Descriptors | View L/C/M/Text | Index (table + modal) | Carte caractéristiques prévue |
|--------|-----------|-------------|-----------------|------------------------|-------------------------------|
| **resources** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ (pas tableMeta) | Optionnel (resource = objet avec level, etc.) |
| **resource-types** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **items** | ✅ | ✅ | ✅ ✅ ✅ | ✅ | Oui (item = objet avec stats) |
| **spells** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Oui (sort avec effets / caractéristiques) |
| **monsters** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ + tableMeta | ✅ (fait) |
| **creatures** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Oui (créature = même modèle que monster.creature) |
| **npcs** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Oui (NPC peut avoir créature / stats) |
| **breeds** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Optionnel (classe, stats de base) |
| **consumables** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Oui (consommable = objet avec effets) |
| **campaigns** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **scenarios** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **attributes** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **panoplies** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Optionnel (panoplie = ensemble d’items) |
| **capabilities** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **specializations** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |
| **shops** | ✅ | ✅ | ✅ ✅ ✅ ✅ | ✅ | Non |

---

## 3. Plan proposé (phases)

### Phase 1 — Harmonisation tableau + modal (sans caractéristiques) ✅

**Objectif** : Toutes les pages Index qui ont un tableau + modal passent `tableMeta` au modal et stockent la meta au `@loaded`, pour préparer la suite.

**Actions (par page Index) :**

1. Déclarer `tableMeta = ref({})`.
2. Dans `handleTableLoaded`, faire `tableMeta.value = meta || {}`.
3. Sur `EntityModal`, ajouter `:table-meta="tableMeta"`.

**Entités concernées** : resource, resource-type, item, spell, creature, npc, breed, consumable, campaign, scenario, attribute, panoply, capability, specialization, shop.

**Résultat** : Comportement inchangé pour l’utilisateur, mais les vues peuvent déjà recevoir `tableMeta` et l’utiliser plus tard (ex. caractéristiques, options de filtres).

---

### Phase 2 — Harmonisation des vues (Large, Compact, Minimal) ✅ (prop tableMeta réalisée)

**Objectif** : Structure et UX des vues alignées sur Monster/Resource (header, sections, champs techniques, actions).

**Actions (par entité, pour chaque vue Large/Compact/Minimal) :**

1. **Structure**  
   - Large : `EntityViewHeader` (media, title, mainInfos, subtitle, actions) + sections claires (technique, paramètres).  
   - Compact : idem en plus condensé.  
   - Minimal : header + zone étendue au survol (comme Monster).

2. **Données**  
   - Utiliser les descriptors pour labels, icônes, tooltips.  
   - Utiliser `entity.toCell(fieldKey, { size, context })` pour l’affichage des valeurs (via `CellRenderer`).

3. **Props**  
   - Ajouter la prop `tableMeta` (optionnelle) partout pour pouvoir brancher la carte caractéristiques plus tard sans refaire les vues.

**Ordre suggéré** : Resource (référence), puis Item, Spell, Consumable, Creature, Npc, Breed, puis le reste (resource-type, campaign, scenario, attribute, panoply, capability, specialization, shop).

---

### Phase 3 — Backend : meta caractéristiques (API Table)

**Objectif** : Les API Table qui concernent des entités « avec caractéristiques » renvoient une meta exploitable par la carte (ex. `meta.characteristics.<contexte>.byDbColumn`).

**Entités prioritaires** :

| Entité | Contexte meta | Source des définitions (ex.) |
|--------|----------------|------------------------------|
| **creatures** | `meta.characteristics.creature.byDbColumn` | Comme Monster (déjà en BDD / config) |
| **items** | `meta.characteristics.item.byDbColumn` | Caractéristiques d’objet (level, stats, etc.) |
| **spells** | `meta.characteristics.spell.byDbColumn` | Caractéristiques de sort |
| **consumables** | `meta.characteristics.consumable.byDbColumn` ou item | Idem item ou spécifique consommable |
| **npcs** | `meta.characteristics.creature.byDbColumn` (si NPC lié à une créature) | Comme Monster |
| **breeds** | Optionnel | Stats de classe si exposées en caractéristiques |
| **resources** | Optionnel | Level, rarity, etc. si modélisés en caractéristiques |

**Actions (par contrôleur Table) :**

1. Si `format=entities` (ou équivalent), calculer/charger le mapping `byDbColumn` pour le type d’entité (creature, item, spell, …).
2. Exposer-le dans la réponse : `meta.characteristics.<contexte>.byDbColumn`.
3. Documenter la structure (clés, champs attendus : key, name, short_name, icon, color, unit, type, etc.).

**Réutiliser** la logique déjà en place pour Monster (CharacteristicController, seeders, etc.) et l’étendre aux autres contextes (item, spell, consumable).

---

### Phase 4 — Carte caractéristiques dans les vues (entités avec caractéristiques)

**Objectif** : Afficher `CharacteristicsCard` dans les vues Large, Compact, Minimal pour les entités qui ont des caractéristiques et une meta fournie par l’API.

**Prérequis** : Phase 2 (prop `tableMeta` dans les vues) et Phase 3 (meta renvoyée par l’API).

**Actions (par entité concernée) :**

1. **Créer ou réutiliser un builder de groupes**  
   - Sur le modèle de `buildCreatureCharacteristicGroups(creature, byDbColumn)`, avoir une fonction du type `buildXCharacteristicGroups(entityOrSubObject, byDbColumn)` (ex. pour Item, Spell, Consumable, Creature, Npc).

2. **Vues (Large, Compact, Minimal)**  
   - Lire `tableMeta.characteristics.<contexte>.byDbColumn`.  
   - Déduire l’objet « porteur » des valeurs (ex. `entity.creature`, `entity`, `entity.item`).  
   - Calculer les groupes avec le builder.  
   - Afficher une section « Caractéristiques » avec `CharacteristicsCard` (dense en Compact/Minimal, non dense en Large).  
   - Gérer le cas où `tableMeta` ou `byDbColumn` est vide (carte vide ou masquée, pas d’erreur).

3. **Optionnel — Colonnes résumé dans le tableau**  
   - Comme pour Monster : colonnes par groupe (Combat, Stats, Résistances, etc.) avec `CharacteristicsCard` + un groupe par colonne.  
   - Implémenter seulement pour les entités où le tableau serait vraiment amélioré (ex. Creature, Item, Spell).

**Ordre suggéré** : Creature (réutiliser presque tel quel Monster), puis Item, Spell, Consumable, Npc ; Breed et Resource si besoin.

---

### Phase 5 — Tableaux : colonnes résumé et visibilité

**Objectif** : Pour les entités dont les caractéristiques sont riches, proposer des colonnes « résumé » (un groupe par colonne) et masquer les colonnes unitaires par défaut, comme pour Monster.

**Actions (par entité concernée) :**

1. **Modèle**  
   - Dans `toCell()`, cas pour des clés du type `creature_characteristics`, `creature_summary_combat`, etc. (ou équivalent pour item/spell).  
   - Utiliser `_toSummaryGroupCell` (ou équivalent) + `buildXCharacteristicGroups` + `CharacteristicsCard` avec `dense: true`.

2. **Descriptors**  
   - Ajouter les descripteurs pour les colonnes résumé et la colonne « tout ».  
   - `defaultVisible` : colonnes résumé à true (md/lg/xl), colonnes unitaires à false.

3. **Backend**  
   - S’assurer que l’API Table inclut bien les données nécessaires (entité + relation éventuelle + meta.characteristics).

**Entités prioritaires** : Creature, Item, Spell (si beaucoup de colonnes de stats).

---

## 4. Synthèse des priorités

| Priorité | Contenu | Entités |
|----------|---------|--------|
| **P0** | Phase 1 (tableMeta dans toutes les Index + modal) | Toutes |
| **P1** | Phase 2 (structure des vues + prop tableMeta) | Toutes (Item, Spell, Creature, Npc, Consumable en premier) |
| **P2** | Phase 3 (meta caractéristiques API) | Creature, Item, Spell, Consumable, Npc |
| **P3** | Phase 4 (CharacteristicsCard dans les vues) | Creature, Item, Spell, Consumable, Npc |
| **P4** | Phase 5 (colonnes résumé dans le tableau) | Creature, puis Item/Spell si pertinent |

---

## 5. Fichiers à toucher (rappel)

- **Pages Index** : `resources/js/Pages/Pages/entity/<entity>/Index.vue` (tableMeta, handleTableLoaded, EntityModal).
- **Vues** : `resources/js/Pages/Molecules/entity/<entity>/{Entity}ViewLarge.vue`, `ViewCompact.vue`, `ViewMinimal.vue`.
- **Modèles** : `resources/js/Models/Entity/<Entity>.js` (toCell, colonnes résumé si Phase 5).
- **Descriptors** : `resources/js/Entities/<entity>/<entity>-descriptors.js`.
- **Builders** : `resources/js/Utils/Entity/buildCreatureCharacteristicGroups.js` (existant) ; à dupliquer/adapter pour item, spell, consumable (ex. `buildItemCharacteristicGroups.js`).
- **Backend** : `app/Http/Controllers/Api/Table/<Entity>TableController.php` (meta.characteristics.*).

---

## 6. Références

- [ARCHITECTURE_ENTITES_FRONTEND.md](./ARCHITECTURE_ENTITES_FRONTEND.md) — Architecture globale.
- [CHARACTERISTICS_CARD_SCHEMA.md](../30-UI/CHARACTERISTICS_CARD_SCHEMA.md) — Schéma de la carte caractéristiques (atomes, groupe, carte).
- Monster : `MonsterViewLarge.vue`, `Monster.js` (`_toSummaryGroupCell`), `monster-descriptors.js`, `MonsterTableController.php`.

---

## 7. Formatage centralisé des effets / bonus (implémenté)

Pour éviter la duplication de logique entre `Item`, `Resource`, `Consumable`, `Panoply`, `Spell` et `Capability`, le formatage des champs `effect` / `bonus` est désormais centralisé dans un composable unique.

### 7.1 Composable de référence

- **Fichier** : `resources/js/Composables/entity/useCharacteristicEffectFormatter.js`
- **Responsabilités** :
  - parser les payloads JSON (objet / tableau) contenus dans `effect` et `bonus`,
  - extraire des paires `clé/valeur` de caractéristiques,
  - résoudre les métadonnées de caractéristique (`icon`, `color`, `short_name`, etc.) depuis `meta.characteristics.<group>.byDbColumn`,
  - produire une cellule `chips` (`Cell{type,value,params}`) homogène,
  - fallback en texte si la donnée n’est pas une caractéristique (cas fréquent sur certains consommables).

### 7.2 Contrat d’usage côté modèles

Les modèles appellent `buildCharacteristicEffectCell()` en fournissant :

- `rawValues` : valeurs brutes à analyser (ex. `[this.effect, this.bonus]`),
- `sourceGroups` : groupes de méta à utiliser dans l’ordre de priorité (ex. `['item', 'panoply']`),
- `options` : options de cellule (`ctx`, etc.),
- `format`, `size` : contexte d’affichage,
- `chipsLayout` : options de rendu (ex. `{ maxRows: 3 }`).

### 7.3 Groupes de caractéristiques actuellement utilisés

| Entité | Champs formatés | Groupes meta |
|--------|------------------|--------------|
| `Item` | `effect`, `bonus` | `item`, `panoply` |
| `Resource` | `effect` | `resource`, `item` |
| `Consumable` | `effect` | `consumable`, `item` |
| `Panoply` | `bonus` | `panoply`, `item` |
| `Spell` | `effect` | `spell` |
| `Capability` | `effect` | `capability`, `spell` |

### 7.4 Backend requis

Pour que le mapping visuel fonctionne, chaque table API en `format=entities` doit exposer :

- `meta.characteristics.<group>.byDbColumn`

Exemple déjà branché pour la panoplie :

- `PanoplyTableController` expose `meta.characteristics.panoply.byDbColumn` via `CharacteristicMetaByDbColumnService::buildObjectByDbColumn(ENTITY_PANOPLY)`.

### 7.5 Règle de maintenance

Toute évolution du rendu d’effets/bonus (parsing, mapping, fallback, layout chips) doit être faite d’abord dans :

- `useCharacteristicEffectFormatter.js`

et non dupliquée localement dans chaque modèle.
