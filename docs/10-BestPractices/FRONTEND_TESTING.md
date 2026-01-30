# Tests Frontend - Guide complet

## Vue d'ensemble

Les tests frontend utilisent **Vitest** comme framework de test, qui est l'outil recommandé pour Vue 3 + Vite. Vitest est rapide, compatible avec Vite, et supporte nativement les composants Vue et les composables.

## Installation

Les dépendances nécessaires doivent être installées :

```bash
pnpm add -D vitest @vue/test-utils jsdom @vitest/ui
```

## Configuration

### Vitest Config (`vitest.config.js`)

La configuration utilise la même base que Vite pour la cohérence :
- Environnement `jsdom` pour simuler le DOM
- Alias `@` pour les imports
- Setup file pour les mocks globaux
- Coverage avec v8

### Setup File (`tests/setup.js`)

Le setup file configure :
- Mock de `route()` (Ziggy)
- Mock de `window.location`
- Mock de Inertia router
- Helpers pour créer des données mockées

## Structure des tests

```
tests/
├── setup.js                    # Configuration globale
└── unit/
    ├── composables/
    │   ├── useSectionDefaults.test.js
    │   ├── useSectionUI.test.js
    │   └── useSectionStyles.test.js
    ├── adapters/
    │   └── sectionUIAdapter.test.js
    └── mappers/
        └── sectionMapper.test.js
```

## Exécution des tests

### Commandes disponibles

```bash
# Lancer les tests en mode watch
pnpm test

# Lancer les tests avec l'UI
pnpm test:ui

# Lancer les tests une fois (CI)
pnpm test:run

# Lancer les tests avec coverage
pnpm test:coverage
```

## Types de tests

### 1. Tests unitaires de composables

**Exemple** : `useSectionDefaults.test.js`

```javascript
import { describe, it, expect } from 'vitest';
import { useSectionDefaults } from '@/Pages/Organismes/section/composables/useSectionDefaults';

describe('useSectionDefaults', () => {
  it('devrait retourner les valeurs par défaut pour text', () => {
    const { getDefaults } = useSectionDefaults();
    const defaults = getDefaults('text');
    expect(defaults).toEqual({
      settings: { align: 'left', size: 'md' },
      data: { content: '' },
    });
  });
});
```

### 2. Tests unitaires d'adapters

**Exemple** : `useSectionUI.test.js`

```javascript
import { useSectionUI } from '@/Pages/Organismes/section/composables/useSectionUI';

describe('sectionUIAdapter', () => {
  it('devrait adapter une section avec état playable', () => {
    const section = createMockSection({ state: 'playable' });
    const { uiData } = useSectionUI(section);
    
    expect(uiData.value.color).toBe('success');
    expect(uiData.value.badge.text).toBe('Jouable');
  });
});
```

### 3. Tests unitaires de mappers

**Exemple** : `sectionMapper.test.js`

```javascript
import { mapToSectionModel } from '@/Pages/Organismes/section/mappers/sectionMapper';

describe('sectionMapper', () => {
  it('devrait mapper des données brutes en modèle Section', () => {
    const rawData = createMockSection();
    const sectionModel = mapToSectionModel(rawData);
    
    expect(sectionModel.id).toBe(1);
    expect(sectionModel.template).toBe('text');
  });
});
```

### 4. Tests de composants Vue

**Exemple** : Test d'un composant avec `@vue/test-utils`

```javascript
import { mount } from '@vue/test-utils';
import MyComponent from '@/Pages/Atoms/MyComponent.vue';

describe('MyComponent', () => {
  it('devrait afficher le contenu', () => {
    const wrapper = mount(MyComponent, {
      props: { title: 'Test' },
    });
    
    expect(wrapper.text()).toContain('Test');
  });
});
```

## Helpers de test

### `createMockSection(overrides)`

Crée une section mockée avec des valeurs par défaut :

```javascript
import { createMockSection } from '../../setup.js';

const section = createMockSection({
  template: 'image',
  state: 'draft',
});
```

### `createMockPage(overrides)`

Crée une page mockée avec des valeurs par défaut :

```javascript
import { createMockPage } from '../../setup.js';

const page = createMockPage({
  title: 'Ma page',
  sections: [createMockSection()],
});
```

## Bonnes pratiques

### 1. Tests isolés

Chaque test doit être indépendant et ne pas dépendre d'autres tests.

### 2. Arrange-Act-Assert (AAA)

Structurez vos tests en trois parties :
```javascript
it('devrait faire quelque chose', () => {
  // Arrange : Préparer les données
  const section = createMockSection();
  
  // Act : Exécuter l'action
  const result = useSectionUI(section).uiData.value;
  
  // Assert : Vérifier le résultat
  expect(result.color).toBe('success');
});
```

### 3. Noms descriptifs

Utilisez des noms de test clairs et descriptifs :
```javascript
// ✅ Bon
it('devrait retourner les valeurs par défaut pour text')

// ❌ Mauvais
it('test 1')
```

### 4. Tests rapides

Les tests unitaires doivent être rapides. Évitez les opérations lourdes.

### 5. Coverage

Visez une couverture de code élevée pour les parties critiques :
- Composables
- Adapters
- Mappers
- Services

## Tests E2E

Pour les tests E2E (end-to-end), le projet utilise déjà **Playwright** :

```bash
# Lancer les tests Playwright
npx playwright test
```

Les tests E2E sont dans `playwright/` et testent les workflows complets.

## Intégration CI/CD

### GitHub Actions (exemple)

```yaml
- name: Run tests
  run: |
    pnpm test:run
    pnpm test:coverage
```

## Exemples de tests créés

### ✅ Tests unitaires disponibles

1. **useSectionDefaults.test.js** - Tests des valeurs par défaut
2. **sectionUIAdapter.test.js** - Tests de l'adapter UI
3. **sectionMapper.test.js** - Tests du mapper
4. **useSectionUI.test.js** - Tests du composable UI
5. **useSectionStyles.test.js** - Tests des styles

## Prochaines étapes

### Tests à ajouter

1. **Tests de composants** :
   - `SectionRenderer.test.js`
   - `PageSectionEditor.test.js`
   - Templates Read/Edit

2. **Tests d'intégration** :
   - Création de section complète
   - Réorganisation de sections
   - Auto-save

3. **Tests de services** :
   - `useSectionAPI.test.js`
   - `useSectionSave.test.js`

## Support

Pour plus d'informations :
- [Vitest Documentation](https://vitest.dev/)
- [Vue Test Utils](https://test-utils.vuejs.org/)
- `docs/10-BestPractices/TESTING_PRACTICES.md`

