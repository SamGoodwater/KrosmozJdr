# Audit — Service universel d'affichage des caractéristiques

**Date** : 2026-03  
**Statut** : ✅ Refactorisation réalisée (phases 1–3)  
**Objectif** : Centraliser l'affichage des caractéristiques (icône, couleur, nom, description, valeur) dans un service unique, supprimer les duplications et garantir un code propre.

Voir aussi :
- [PROPERTY_DISPLAY_SYSTEM.md](../../30-UI/PROPERTY_DISPLAY_SYSTEM.md)
- [ARCHITECTURE_CARACTERISTIQUES_SPELL.md](./ARCHITECTURE_CARACTERISTIQUES_SPELL.md)
- [PLAN-TABLEAUX-ET-DISPLAY-ENTITES.md](../../00-Project/PLAN-TABLEAUX-ET-DISPLAY-ENTITES.md)

---

## 1. Inventaire des sources de métadonnées

| Source | Rôle |
|--------|------|
| **Backend** `CharacteristicMetaByDbColumnService` | Construit `byDbColumn`, `byCharacteristicKey`, `byDofusdbId`, `byComputedKey` par groupe via `buildAllForFrontend()` |
| **Inertia share** | Partage `characteristics` au démarrage (HandleInertiaRequests) — disponible dans `usePage().props.characteristics` |
| **API** `GET /api/characteristics` | Endpoint alternatif pour fetch explicite (non utilisé par défaut, Inertia share suffit) |
| **Frontend** `useCharacteristicsStore` | Lit Inertia share, expose `getByDbColumn`, `getByCharacteristicKey`, `getByDofusdbId`, `getByComputedKey` |
| **API Table** | Ne renvoie plus `meta.characteristics` — les entités contiennent uniquement les valeurs brutes |

Chaque définition contient : `key`, `db_column`, `name`, `short_name`, `helper`, `descriptions`, `icon`, `color`, `unit`, `type`, `value_available`.

---

## 2. Composants et utilitaires actuels

### 2.1 Composants d'affichage de caractéristiques

| Composant | Usage | Données utilisées |
|-----------|-------|-------------------|
| **CharacteristicFormula** | Valeur + unité (formule, PA, PM, etc.) | def.icon, def.color, def.short_name, def.unit |
| **CharacteristicBoolean** | Oui/non (booléen) | def.icon, def.iconFalse, def.color |
| **CharacteristicBadges** | Liste de badges (ex. taille, propriétés) | def.value_available (label/color par valeur) |
| **CharacteristicGroup** | Orchestre les 3 atomes ci-dessus | — |
| **CharacteristicChip** | Icône + valeur (chips inline) | item.icon, item.color, item.name, item.shortLabel, item.value, item.tooltip |
| **CharacteristicInlineGroup** | Wrappe CharacteristicChip (layout flow) | — |
| **CharacteristicEffectsGrid** | Grille d'effets (icon + label + value) | items avec icon, color, name, shortLabel, value |
| **SpellEffectChips** | Wrappe CharacteristicInlineGroup + filtre degrés | — |
| **PropertyDisplay** | Badge / icône / inline (propriété générique) | property.icon, property.label, property.color, value |
| **CharacteristicsCard** | Conteneur + CharacteristicGroup | — |
| **Badge** | Badge générique (utilisé par CharacteristicBadges) | — |

### 2.2 Utilitaires et composables

| Fichier | Rôle |
|---------|------|
| `useCharacteristicEffectFormatter.js` | Parse JSON effect/bonus → chips avec icon/color via byDbColumn, byCharacteristicKey, byDofusdbId |
| `entity-view-ui.js` | `getEntityCharacteristicsByDbColumn`, `resolveEntityFieldUi`, `resolveEntityBadgeUi` |
| `buildCreatureCharacteristicGroups.js` | Construit groupes creature (Combat, Stats, Modificateurs, etc.) à partir de données brute + byDbColumn |
| `formulaConfig.js` | Décodage formules (non affichage) |
| `SharedConstants.js` | FIELD_ICONS, LEVEL_COLORS, etc. — pas spécifique aux caractéristiques BDD |

---

## 3. Duplications identifiées

### 3.1 hexToRgba — 3 copies identiques

Même fonction dans :
- `CharacteristicFormula.vue`
- `CharacteristicBoolean.vue`
- `CharacteristicBadges.vue`

```js
function hexToRgba(hex, a) {
  if (!hex || typeof hex !== "string") return null;
  let h = hex.replace(/^#/, "");
  if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
  if (h.length !== 6) return null;
  const r = parseInt(h.slice(0, 2), 16);
  const g = parseInt(h.slice(2, 4), 16);
  const b = parseInt(h.slice(4, 6), 16);
  return `rgba(${r},${g},${b},${a})`;
}
```

**Action** : Extraire dans `Utils/color/Color.js` (ou nouveau `hexToRgba.js`).

### 3.2 getColorStyle / iconColorStyle / valueStyle — 4+ occurrences

Logique : hex → `{ color: hex }`, token Tailwind (ex. `blue-600`) → `{ color: 'var(--color-blue-600)' }`.

Présente dans :
- `CharacteristicChip.vue` (`colorStyle`)
- `CharacteristicEffectsGrid.vue` (`getColorStyle`)
- `PropertyDisplay.vue` (`iconColorStyle`)
- `CharacteristicFormula.vue` (`valueStyle` — légèrement différente, utilise directement def.color)

**Action** : Créer `getCharacteristicColorStyle(color)` centralisée.

### 3.3 containerStyle (fond teinté + ombre)

Logique identique dans CharacteristicFormula, CharacteristicBoolean, CharacteristicBadges :
- rgba 8 % pour fond, 15 % pour ombre, 20 % pour bordure

**Action** : Centraliser `getCharacteristicContainerStyle(hex)` dans le service.

### 3.4 Résolution déf (icon, color, name)

- `useCharacteristicEffectFormatter` : collecte from byDbColumn, byCharacteristicKey, byDofusdbId
- `entity-view-ui.resolveEntityFieldUi` : priorise characteristics puis descriptors
- `buildCreatureCharacteristicGroups` : getDef(dbColumn) local

Logiques proches mais pas unifiées (contexte différent : effet JSON vs champ entity vs creature brute).

---

## 4. Flux de données actuels (depuis service global 2026-03)

```
Backend (CharacteristicMetaByDbColumnService::buildAllForFrontend())
    → Inertia share (characteristics) ou GET /api/characteristics
        → useCharacteristicsStore (getByDbColumn, getByCharacteristicKey, etc.)
            → Composants Vue / Models.toCell() / useCharacteristicEffectFormatter / entity-view-ui
```

Les API Table ne renvoient plus `meta.characteristics`. Le frontend résout toutes les métadonnées (icon, color, name, etc.) via le store initialisé au démarrage.

Les composants reçoivent soit :
- une **def** complète (CharacteristicGroup → Formula/Boolean/Badges) via le store
- des **items** préformatés (icon, color, name, value) construits par `buildCharacteristicEffectCell` qui lit le store

---

## 5. Plan de refactorisation

### Phase 1 : Extraction des utilitaires couleur

1. **hexToRgba** — Ajouter dans `Utils/color/Color.js` (ou nouveau module si préférence de séparation)
2. **getCharacteristicColorStyle(color)** — Style inline pour texte/icône (hex ou token Tailwind)
3. **getCharacteristicContainerStyle(color)** — Style pour carte (fond teinté, ombre, bordure)

### Phase 2 : Composable useCharacteristicDisplay

Créer `Composables/entity/useCharacteristicDisplay.js` :

```js
// Résout la définition (icon, color, name, short_name, description) depuis byDbColumn / byCharacteristicKey
resolveDef(key, options)

// Style couleur pour texte/icône
getColorStyle(color)

// Style container (carte teintée)
getContainerStyle(color)

// Valeur affichée selon cas particuliers (booléen, élément, portée 1-1 → cac, etc.)
getDisplayValue(key, value, def)
```

- Réutiliser `hexToRgba`, `getCharacteristicColorStyle`, `getCharacteristicContainerStyle`
- Optionnel : intégrer la logique de `resolveEntityFieldUi` pour unifier

### Phase 3 : Migration des composants

| Composant | Changements |
|-----------|-------------|
| CharacteristicFormula | Importer hexToRgba + getContainerStyle depuis le service ; garder déf en prop |
| CharacteristicBoolean | Idem |
| CharacteristicBadges | Idem |
| CharacteristicChip | Utiliser getColorStyle du service |
| CharacteristicEffectsGrid | Utiliser getColorStyle du service |
| PropertyDisplay | Utiliser getColorStyle du service |

### Phase 4 : Consolidation (optionnel)

- `entity-view-ui.resolveEntityFieldUi` — S'appuyer sur le composable si pertinent
- `useCharacteristicEffectFormatter` — Réutiliser `resolveDef` pour la résolution des clés
- Éviter de dupliquer la logique de collecte byDbColumn / byCharacteristicKey (déjà dans useCharacteristicEffectFormatter)

---

## 6. Cas particuliers à gérer dans le service

| Cas | Règle |
|-----|-------|
| Booléen | icon → icon si true, iconFalse si false |
| Élément | Résolution depuis value_available ou mapping éléments (SharedConstants) |
| Portée 1-1 | Afficher "CAC" au lieu de "1 - 1" |
| Bonus critique 0-3 | 0=Nat 20, 1=Dès 19, 2=Dès 18, 3=Dès 17 |
| Ligne de vue | Présentation variable selon valeur |

Ces règles peuvent être dans une config ou des helpers dédiés (`getDisplayValue`).

---

## 7. Fichiers à modifier

| Fichier | Action |
|---------|--------|
| `Utils/color/Color.js` | Ajouter hexToRgba, getCharacteristicColorStyle, getCharacteristicContainerStyle |
| `Composables/entity/useCharacteristicDisplay.js` | **Créer** — service universel |
| `CharacteristicFormula.vue` | Supprimer hexToRgba, utiliser service |
| `CharacteristicBoolean.vue` | Idem |
| `CharacteristicBadges.vue` | Idem |
| `CharacteristicChip.vue` | Utiliser getColorStyle |
| `CharacteristicEffectsGrid.vue` | Utiliser getColorStyle |
| `PropertyDisplay.vue` | Utiliser getColorStyle |
| `PROPERTY_DISPLAY_SYSTEM.md` | Mettre à jour avec référence au nouveau service |

---

## 8. Non modifié (hors périmètre immédiat)

- `buildCreatureCharacteristicGroups.js` — Logique métier creature (groupes, calculs), structure hardcodée volontaire
- `useCharacteristicEffectFormatter` — Peut réutiliser le service pour résolution, mais le flux effect→chips reste spécifique
- Backend `CharacteristicMetaByDbColumnService` — Source de vérité, inchangé
- Descripteurs entity (spell-descriptors, etc.) — Restent pour champs non caractéristiques
