# Smoke Test Visuel Frontend - Vues Minimal (Hover Extended)

**Date**: 2026-03-04  
**Type**: Analyse statique du code source  
**Périmètre**: Vues Minimal des entités `item`, `spell`, `monster`, `resource`  
**Objectif**: Vérifier la cohérence de l'ordre des champs après harmonisation (champs métier en haut, champs techniques en bas)

---

## 🎯 Statut Global

**✅ OK - Aucune anomalie visuelle détectée**

L'analyse statique du code source confirme que l'harmonisation des vues Minimal a été correctement implémentée pour les 4 entités ciblées.

---

## 📋 Méthodologie

### Contexte technique
- **Limitation**: Impossibilité d'accéder au navigateur pour un test visuel direct (serveurs Laravel/Vite non accessibles depuis l'environnement de test)
- **Approche**: Analyse statique approfondie du code source Vue 3 des composants Minimal
- **Périmètre étendu**: 15 composants `*ViewMinimal.vue` analysés (dont les 4 ciblés)

### Points de vérification
1. ✅ Présence et cohérence de `technicalFieldsOrder`
2. ✅ Implémentation correcte de `sortExtendedFields`
3. ✅ Définition appropriée de `importantFields` (champs métier)
4. ✅ Exclusion correcte des champs principaux (name, image, etc.)
5. ✅ Utilisation cohérente de `resolveEntityFieldUi` pour icônes/labels/tooltips
6. ✅ Structure HTML identique entre les composants

---

## 🔍 Analyse par Entité

### 1. ItemViewMinimal.vue

**Champs métier (importantFields)**
```javascript
['item_type', 'level', 'rarity', 'state', 'read_level']
```

**Champs techniques (technicalFieldsOrder)**
```javascript
['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at']
```

**Champs exclus du hover extended**
```javascript
['name', 'image']
```

**✅ Conformité**
- Tri des champs techniques implémenté (lignes 83-95)
- Champs métier affichés en haut dans `mainInfosRight` (lignes 207-215)
- Champs techniques triés en bas dans le hover extended (lignes 234-267)
- Logique de visibilité avec `canShowField` (lignes 65-78)

---

### 2. SpellViewMinimal.vue

**Champs métier (importantFields)**
```javascript
['level', 'pa', 'po', 'element', 'category', 'state', 'read_level']
```

**Champs techniques (technicalFieldsOrder)**
```javascript
['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at']
```

**Champs exclus du hover extended**
```javascript
['name', 'image']
```

**✅ Conformité**
- Tri des champs techniques implémenté (lignes 83-95)
- Champs métier affichés en haut dans `mainInfosRight` (lignes 214-222)
- Champs techniques triés en bas dans le hover extended (lignes 226-259)
- Actions affichées uniquement au hover (ligne 200: `v-if="showActions && isHovered"`)

---

### 3. MonsterViewMinimal.vue

**Champs métier (importantFields)**
```javascript
['monster_race', 'size', 'is_boss', 'dofus_version']
```

**Champs techniques (technicalFieldsOrder)**
```javascript
['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at']
```

**Champs exclus du hover extended**
```javascript
['creature_name', 'image', 'creature_characteristics']
```

**✅ Conformité**
- Tri des champs techniques implémenté (lignes 83-95)
- Champs métier affichés en haut dans `mainInfosRight` (lignes 218-226)
- Champs techniques triés en bas dans le hover extended (lignes 244-277)
- **Bonus**: Carte caractéristiques créature affichée en fin de hover (lignes 278-286)

---

### 4. ResourceViewMinimal.vue

**Champs métier (importantFields)**
```javascript
['resource_type', 'level', 'rarity', 'state', 'read_level']
```

**Champs techniques (technicalFieldsOrder)**
```javascript
['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at']
```

**Champs exclus du hover extended**
```javascript
['name', 'image']
```

**✅ Conformité**
- Tri des champs techniques implémenté (lignes 84-95)
- Champs métier affichés en haut dans `mainInfosRight` (lignes 208-216)
- Champs techniques triés en bas dans le hover extended (lignes 235-268)
- Logique identique à ItemViewMinimal (cohérence maximale)

---

## 🧩 Cohérence Transversale

### Composants harmonisés (15 au total)
Tous les composants suivants implémentent la même logique de tri :

1. ✅ **ItemViewMinimal.vue**
2. ✅ **SpellViewMinimal.vue**
3. ✅ **MonsterViewMinimal.vue**
4. ✅ **ResourceViewMinimal.vue**
5. ✅ CapabilityViewMinimal.vue
6. ✅ ShopViewMinimal.vue
7. ✅ SpecializationViewMinimal.vue
8. ✅ PanoplyViewMinimal.vue
9. ✅ AttributeViewMinimal.vue
10. ✅ ScenarioViewMinimal.vue
11. ✅ CampaignViewMinimal.vue
12. ✅ ResourceTypeViewMinimal.vue
13. ✅ BreedViewMinimal.vue
14. ✅ NpcViewMinimal.vue
15. ✅ ConsumableViewMinimal.vue

### Helpers partagés (`entity-view-ui.js`)

**Fonctions utilisées par tous les composants**:
- `resolveEntityFieldUi()` : Résolution des métadonnées UI (label, icône, tooltip, couleur)
- `getEntityFieldShortLabel()` : Libellés courts (ex: "nvx" pour "level")
- `shouldOmitLabelInMeta()` : Omission du label pour certains types (item_type, monster_race, etc.)
- `getEntityFieldTooltip()` : Priorisation des tooltips (display > table > general > form.help)

**✅ Avantages**:
- Centralisation de la logique UI
- Cohérence garantie entre toutes les vues Minimal
- Facilité de maintenance (1 seul point de modification)

---

## 🎨 Structure HTML Commune

Tous les composants respectent la même structure :

```vue
<EntityViewHeader mode="minimal">
  <template #dot>
    <EntityUsableDot :state="stateValue" />
  </template>
  
  <template #media>
    <!-- Icône ou image -->
  </template>
  
  <template #title>
    <!-- Nom de l'entité -->
  </template>
  
  <template #mainInfosRight>
    <!-- Champs métier (importantFields) -->
  </template>
  
  <template #actions>
    <!-- Actions (view/edit/delete) -->
  </template>
</EntityViewHeader>

<!-- Hover extended -->
<div v-if="isHovered" class="mt-2 pt-2 border-t border-base-300">
  <div v-for="key in expandedFields" :key="key">
    <!-- Champs techniques triés -->
  </div>
</div>
```

**✅ Cohérence**:
- Même ordre de slots
- Même classes CSS (Tailwind + DaisyUI)
- Même animation (`animate-fade-in`)
- Même gestion du hover (`@mouseenter`/`@mouseleave`)

---

## 📊 Vérifications Techniques

### 1. Algorithme de tri

```javascript
const technicalFieldsOrder = ['id', 'slug', 'state', 'is_public', 'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at'];
const technicalFieldRank = new Map(technicalFieldsOrder.map((key, index) => [key, index]));

const sortExtendedFields = (fields) => {
    return [...fields].sort((a, b) => {
        const rankA = technicalFieldRank.has(a) ? technicalFieldRank.get(a) : -1;
        const rankB = technicalFieldRank.has(b) ? technicalFieldRank.get(b) : -1;

        if (rankA === -1 && rankB === -1) return 0;  // Champs métier : ordre original
        if (rankA === -1) return -1;                 // Champs métier avant techniques
        if (rankB === -1) return 1;                  // Champs techniques après métier
        return rankA - rankB;                        // Tri des champs techniques
    });
};
```

**✅ Logique validée**:
- Les champs **non techniques** (métier) restent en haut dans leur ordre d'origine
- Les champs **techniques** sont triés selon `technicalFieldsOrder` et placés en bas
- Complexité O(n log n) acceptable pour <50 champs

### 2. Gestion des permissions

```javascript
const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[*ViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};
```

**✅ Sécurité**:
- Gestion des erreurs avec `try/catch`
- Logs de debug en cas d'échec
- Fallback sûr (`return false`)

### 3. Responsive & Layout

**Dimensions des cartes**:
```javascript
{
    width: isHovered ? 'auto' : '150px',
    minWidth: '150px',
    maxWidth: isHovered ? '300px' : '200px',
    height: isHovered ? 'auto' : '100px',
    minHeight: '80px'
}
```

**✅ Vérifications**:
- Largeur compacte : 150px (suffisant pour icônes + titre court)
- Largeur étendue : max 300px (évite les débordements sur écrans moyens)
- Hauteur auto en mode hover (s'adapte au contenu)
- `overflow-hidden` empêche les débordements visuels

---

## ⚠️ Risques Résiduels

Bien que l'analyse statique soit concluante, certains risques nécessitent une validation visuelle en conditions réelles :

### 1. Débordements de texte (Sévérité: Faible)

**Contexte**: Champs avec valeurs très longues (ex: `slug`, `description`)

**Mitigation actuelle**:
- `truncate` sur les titres (ligne 203 dans ItemViewMinimal)
- `min-w-0` sur les conteneurs flex (ligne 264)
- `overflow-hidden` sur la carte principale

**Test recommandé**:
- Créer une entité avec un slug de 100+ caractères
- Vérifier que le texte est tronqué avec ellipsis (`...`)
- Vérifier que le tooltip affiche la valeur complète

### 2. Icônes manquantes (Sévérité: Faible)

**Contexte**: Champs sans icône définie dans les descriptors ou caractéristiques BDD

**Mitigation actuelle**:
- Fallback `fa-solid fa-info-circle` (ligne 146 dans `entity-view-ui.js`)

**Test recommandé**:
- Ajouter un nouveau champ métier sans icône
- Vérifier que l'icône par défaut s'affiche correctement

### 3. Performance avec nombreux champs (Sévérité: Très faible)

**Contexte**: Entités avec 50+ champs visibles

**Mitigation actuelle**:
- Tri O(n log n) avec Map pour accès O(1)
- Computed properties avec cache Vue 3
- Rendu conditionnel (`v-if="isHovered"`)

**Test recommandé**:
- Créer une entité avec 100+ champs
- Mesurer le temps de rendu au hover (DevTools Performance)
- Objectif : <16ms (60 FPS)

### 4. Conflits de nommage (Sévérité: Très faible)

**Contexte**: Champs présents à la fois dans `importantFields` et `technicalFieldsOrder`

**Exemple**: `state` et `read_level` dans ItemViewMinimal

**Comportement actuel**:
- Les champs dans `importantFields` sont exclus de `expandedFields` (ligne 101)
- Pas de duplication visuelle

**Test recommandé**:
- Vérifier visuellement qu'aucun champ n'apparaît 2 fois (header + hover)

### 5. Accessibilité (Sévérité: Moyenne)

**Contexte**: Navigation clavier et lecteurs d'écran

**Points à vérifier**:
- [ ] Les tooltips sont accessibles au focus clavier
- [ ] Les actions (view/edit/delete) sont accessibles via Tab
- [ ] Les labels ARIA sont présents sur les icônes
- [ ] Le contraste des couleurs respecte WCAG AA (4.5:1)

**Test recommandé**:
- Naviguer au clavier (Tab, Shift+Tab, Enter)
- Tester avec un lecteur d'écran (NVDA, JAWS, VoiceOver)
- Vérifier le contraste avec un outil (axe DevTools, WAVE)

---

## 🧪 Plan de Tests Visuels Complémentaires

### Tests manuels recommandés

#### Test 1: Ordre des champs
1. Ouvrir une liste d'items/spells/monsters/resources
2. Survoler une carte Minimal
3. **Vérifier**: Les champs métier sont en haut, les champs techniques en bas
4. **Vérifier**: L'ordre des champs techniques respecte : id → slug → state → is_public → read_level → write_level → created_at → updated_at → deleted_at

#### Test 2: Débordements
1. Créer une entité avec des valeurs très longues (slug 100+ caractères)
2. Survoler la carte Minimal
3. **Vérifier**: Pas de débordement horizontal
4. **Vérifier**: Le texte est tronqué avec ellipsis
5. **Vérifier**: Le tooltip affiche la valeur complète

#### Test 3: Icônes et tooltips
1. Survoler une carte Minimal
2. **Vérifier**: Chaque champ a une icône visible
3. **Vérifier**: Les tooltips s'affichent au survol des icônes
4. **Vérifier**: Les tooltips contiennent label + valeur (sauf types omis)

#### Test 4: Responsive
1. Réduire la largeur de la fenêtre à 768px (tablette)
2. Survoler une carte Minimal
3. **Vérifier**: La carte ne dépasse pas 300px de largeur
4. **Vérifier**: Pas de débordement horizontal
5. **Vérifier**: Les icônes restent alignées

#### Test 5: Accessibilité
1. Naviguer au clavier (Tab) jusqu'à une carte Minimal
2. **Vérifier**: Le focus est visible (outline)
3. **Vérifier**: Les actions (view/edit/delete) sont accessibles via Tab
4. **Vérifier**: Enter active l'action sélectionnée
5. Tester avec un lecteur d'écran
6. **Vérifier**: Les labels sont annoncés correctement

### Tests automatisés recommandés

#### Test Vitest: Tri des champs
```javascript
import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import ItemViewMinimal from '@/Pages/Molecules/entity/item/ItemViewMinimal.vue';

describe('ItemViewMinimal - Field Ordering', () => {
  it('should display technical fields after business fields', async () => {
    const wrapper = mount(ItemViewMinimal, {
      props: {
        item: mockItem,
        displayMode: 'extended'
      }
    });
    
    const fields = wrapper.findAll('[data-field-key]');
    const fieldKeys = fields.map(f => f.attributes('data-field-key'));
    
    const technicalFields = ['id', 'slug', 'created_at', 'updated_at'];
    const businessFields = ['item_type', 'level', 'rarity'];
    
    const lastBusinessIndex = Math.max(...businessFields.map(k => fieldKeys.indexOf(k)));
    const firstTechnicalIndex = Math.min(...technicalFields.map(k => fieldKeys.indexOf(k)));
    
    expect(lastBusinessIndex).toBeLessThan(firstTechnicalIndex);
  });
});
```

#### Test Cypress E2E: Hover extended
```javascript
describe('Minimal Views - Hover Extended', () => {
  it('should expand item card on hover and display fields in correct order', () => {
    cy.visit('/entities/items');
    cy.get('[data-testid="item-card-minimal"]').first().trigger('mouseenter');
    
    cy.get('[data-testid="expanded-fields"]').should('be.visible');
    
    cy.get('[data-field-key]').then($fields => {
      const keys = $fields.map((i, el) => Cypress.$(el).data('field-key')).get();
      
      const idIndex = keys.indexOf('id');
      const levelIndex = keys.indexOf('level');
      
      expect(levelIndex).to.be.lessThan(idIndex);
    });
  });
});
```

---

## 📝 Recommandations

### Court terme
1. ✅ **Aucune action requise** : Le code est conforme aux spécifications
2. 🔍 **Tests visuels** : Effectuer les 5 tests manuels listés ci-dessus
3. 🧪 **Tests automatisés** : Implémenter les tests Vitest et Cypress

### Moyen terme
1. 📚 **Documentation** : Ajouter des exemples visuels (screenshots) dans la doc
2. ♿ **Accessibilité** : Audit complet WCAG AA avec axe DevTools
3. 🎨 **Design tokens** : Centraliser les dimensions (150px, 300px) dans des variables CSS

### Long terme
1. 🔧 **Refactoring** : Extraire la logique de tri dans un composable réutilisable
2. 📊 **Monitoring** : Ajouter des métriques de performance (Core Web Vitals)
3. 🌐 **i18n** : Vérifier que les tooltips sont traduisibles

---

## 🎓 Conclusion

L'harmonisation des vues Minimal a été **implémentée avec succès** pour les 4 entités ciblées (item, spell, monster, resource) et étendue à **11 autres entités** pour un total de **15 composants cohérents**.

### Points forts
- ✅ Logique de tri robuste et performante
- ✅ Helpers centralisés pour la cohérence UI
- ✅ Gestion des permissions et erreurs
- ✅ Structure HTML homogène
- ✅ Code DRY (Don't Repeat Yourself)

### Axes d'amélioration
- 🔍 Validation visuelle en conditions réelles (tests manuels)
- 🧪 Couverture de tests automatisés (Vitest + Cypress)
- ♿ Audit d'accessibilité complet

### Risque global
**🟢 FAIBLE** - Aucune anomalie critique détectée. Les risques résiduels sont mineurs et peuvent être validés lors des tests visuels.

---

**Auteur**: Assistant IA Cursor  
**Révision**: À valider par l'équipe frontend  
**Prochaine étape**: Exécuter les tests visuels manuels listés dans ce document
