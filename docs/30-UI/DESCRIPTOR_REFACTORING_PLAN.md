# Plan de Refactorisation Complète des Descriptors

**Date de création** : 2026-01-06  
**Statut** : 📋 Planification

---

## 📋 Résumé de la Vision (par l'utilisateur)

### Objectif principal

Créer un système de descriptors **centralisé, modulaire et maintenable** qui :

1. **Centralise le code commun** dans une classe `EntityDescriptor`
2. **Sépare les préoccupations** : vues, tableaux, formulaires, bulk
3. **Simplifie la configuration** : descriptions pures avec fonctions de formatage
4. **Améliore la maintenabilité** : toute la logique au même endroit
5. **Rend le système extensible** : facile d'ajouter de nouvelles entités

### Architecture proposée

```
EntityDescriptor (classe de base)
  ├── Constantes communes (RARITY, VISIBILITY, etc.)
  ├── Fonctions communes (formatage, validation)
  ├── Valeurs par défaut
  └── Validation

ResourceDescriptor extends EntityDescriptor
  ├── Configuration tableau
  ├── Configuration vues (compact, minimal, large)
  ├── Configuration formulaires
  └── Configuration bulk
```

---

## 🎯 Fonctionnalités de la Classe EntityDescriptor

### 1. Centralisation du code commun

- **Constantes partagées** : RARITY, VISIBILITY, etc.
- **Fonctions de formatage** : truncate, capitalize, formatDate, etc.
- **Helpers de validation** : validateField, validateDescriptor, etc.
- **Valeurs par défaut** : format, color, showInCompact, etc.

### 2. Configuration du tableau

Pour chaque colonne :
- ✅ **Affichage par défaut** : fonction de la taille d'écran (xs, sm, md, lg, xl)
- ✅ **Ordre d'affichage** : position dans le header
- ✅ **Permission d'accès** : visible si permission
- ✅ **Icône** : icône FontAwesome
- ✅ **Nom** : libellé de la colonne
- ✅ **Helper/Tooltip** : texte d'aide
- ✅ **Type de donnée** : form, text, badge, number, image, file, icon, etc.
- ✅ **Configuration formulaire** : si type=form, toutes les propriétés
- ✅ **Formatage responsive** : comment formater selon la taille (xs, sm, md, lg, xl)

Configuration globale du tableau :
- ✅ **Permission quickEdit** : peut sélectionner (afficher checkbox)
- ✅ **Menu action** : afficher ou non, quelles actions (selon permissions)

### 3. Configuration des vues (compact, minimal, large)

- **Fichiers séparés** : plus de liberté UX/UI
- **Actions différentes** : chaque format affiche différentes actions (selon permissions)
- **Champs affichés** : liste des champs par vue
- **Ordre d'affichage** : ordre des champs dans la vue

### 4. Configuration des formulaires

- **Champs éditables** : liste des champs avec configuration
- **Groupes** : organisation des champs
- **Validation** : règles de validation
- **Valeurs par défaut** : valeurs initiales

### 5. Configuration bulk (édition en masse)

- **Champs bulk-editables** : liste des champs
- **Agrégation** : comment gérer les valeurs différentes
- **Transformation** : fonctions de build pour chaque champ

### 6. Système de taille responsive

- **Auto par défaut** : s'adapte à la taille d'écran
- **Breakpoints** : xs (smartphone), sm (tablet), md (laptop), lg (desktop), xl (large screen)
- **Adaptation progressive** : fonction pour soustraire une taille
- **Formatage conditionnel** : comment afficher selon la taille

---

## 📐 Structure Proposée des Fichiers

### Structure des dossiers

```
resources/js/Entities/
├── entity/
│   ├── EntityDescriptor.js          # Classe de base
│   ├── EntityDescriptorConstants.js # Constantes communes
│   └── EntityDescriptorHelpers.js   # Helpers de formatage
│
└── resource/
    ├── ResourceDescriptor.js        # Descriptor principal
    ├── ResourceTableConfig.js        # Configuration tableau
    ├── ResourceViewCompact.js       # Vue compacte
    ├── ResourceViewMinimal.js        # Vue minimale
    ├── ResourceViewLarge.js          # Vue large
    ├── ResourceFormConfig.js         # Configuration formulaires
    └── ResourceBulkConfig.js         # Configuration bulk
```

### Structure d'un descriptor (exemple : ResourceDescriptor)

```javascript
class ResourceDescriptor extends EntityDescriptor {
  constructor() {
    super('resource');
    
    // Configuration tableau
    this.tableConfig = new ResourceTableConfig();
    
    // Configurations vues
    this.viewCompact = new ResourceViewCompact();
    this.viewMinimal = new ResourceViewMinimal();
    this.viewLarge = new ResourceViewLarge();
    
    // Configuration formulaires
    this.formConfig = new ResourceFormConfig();
    
    // Configuration bulk
    this.bulkConfig = new ResourceBulkConfig();
  }
  
  // Méthodes communes
  getFieldDescriptor(key) { ... }
  validate() { ... }
  formatValue(key, value, size = 'auto') { ... }
}
```

---

## 🔍 Analyse des Besoins Réels

### 1. Configuration Tableau

**Propriétés nécessaires par colonne :**

```javascript
{
  key: "name",
  label: "Nom",
  icon: "fa-solid fa-font",
  tooltip: "Nom de la ressource",
  type: "text", // form, text, badge, number, image, file, icon, link, date, bool
  permission: "view", // permission requise pour voir la colonne
  defaultVisible: {
    xs: false,  // smartphone : masqué par défaut
    sm: false,  // tablet : masqué par défaut
    md: true,   // laptop : visible par défaut
    lg: true,   // desktop : visible par défaut
    xl: true    // large screen : visible par défaut
  },
  order: 1, // Ordre d'affichage dans le header
  format: {
    xs: "truncate:20",      // smartphone : tronqué à 20 caractères
    sm: "truncate:30",      // tablet : tronqué à 30 caractères
    md: "truncate:44",      // laptop : tronqué à 44 caractères
    lg: "full",             // desktop : complet
    xl: "full"              // large screen : complet
  },
  // Si type = "form", ajouter toutes les propriétés du formulaire
  form: {
    type: "text",
    required: true,
    bulk: { enabled: false }
  }
}
```

**Configuration globale du tableau :**

```javascript
{
  quickEdit: {
    enabled: true,
    permission: "updateAny" // Permission pour sélectionner
  },
  actions: {
    enabled: true,
    permission: "view", // Permission pour voir le menu
    available: ["view", "edit", "delete"], // Actions disponibles (selon permissions)
    defaultVisible: {
      xs: false, // smartphone : masqué
      sm: true,  // tablet : visible
      md: true,  // laptop : visible
      lg: true,  // desktop : visible
      xl: true   // large screen : visible
    }
  }
}
```

### 2. Configuration Vues

**Structure d'une vue (exemple : compact) :**

```javascript
{
  name: "compact",
  label: "Vue compacte",
  fields: ["rarity", "resource_type", "level", "usable"],
  order: ["rarity", "resource_type", "level", "usable"],
  actions: {
    available: ["view", "edit", "quick-edit"], // Actions disponibles
    permission: "view" // Permission pour voir les actions
  },
  layout: {
    // Configuration spécifique à la vue (optionnel)
    columns: 2,
    spacing: "compact"
  }
}
```

### 3. Constantes Communes

```javascript
// RARITY
export const RARITY_OPTIONS = [
  { value: 0, label: "Commun", color: "gray" },
  { value: 1, label: "Peu commun", color: "blue" },
  { value: 2, label: "Rare", color: "green" },
  { value: 3, label: "Très rare", color: "purple" },
  { value: 4, label: "Légendaire", color: "orange" },
  { value: 5, label: "Unique", color: "red" }
];

// VISIBILITY
export const VISIBILITY_OPTIONS = [
  { value: "guest", label: "Invité" },
  { value: "user", label: "Utilisateur" },
  { value: "game_master", label: "Maître de jeu" },
  { value: "admin", label: "Administrateur" }
];

// etc.
```

### 4. Fonctions de Formatage

```javascript
// Dans EntityDescriptorHelpers.js
export function formatRarity(value) { ... }
export function formatVisibility(value) { ... }
export function formatDate(value, size = 'auto') { ... }
export function truncate(value, max) { ... }
export function capitalize(value) { ... }
// etc.
```

### 5. Système de Taille Responsive

```javascript
// Breakpoints
const BREAKPOINTS = {
  xs: 0,    // smartphone
  sm: 640,  // tablet
  md: 1024, // laptop
  lg: 1280, // desktop
  xl: 1536  // large screen
};

// Fonction pour obtenir la taille actuelle
function getCurrentSize() {
  const width = window.innerWidth;
  if (width < BREAKPOINTS.sm) return 'xs';
  if (width < BREAKPOINTS.md) return 'sm';
  if (width < BREAKPOINTS.lg) return 'md';
  if (width < BREAKPOINTS.xl) return 'lg';
  return 'xl';
}

// Fonction pour soustraire une taille (adaptation progressive)
function subtractSize(size, steps = 1) {
  const sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
  const index = sizes.indexOf(size);
  return sizes[Math.max(0, index - steps)];
}
```

---

## 📝 Plan de Refactorisation

### Phase 1 : Création de la classe de base

1. ✅ Créer `EntityDescriptor.js` (classe de base)
2. ✅ Créer `EntityDescriptorConstants.js` (constantes communes)
3. ✅ Créer `EntityDescriptorHelpers.js` (fonctions de formatage)
4. ✅ Implémenter le système de taille responsive

### Phase 2 : Configuration tableau

1. ✅ Créer `TableColumnConfig.js` (classe pour une colonne)
2. ✅ Créer `TableConfig.js` (classe pour la configuration globale)
3. ✅ Implémenter la logique responsive (affichage selon taille)
4. ✅ Implémenter les permissions par colonne
5. ✅ Implémenter le formatage conditionnel

### Phase 3 : Configuration vues

1. ✅ Créer `ViewConfig.js` (classe pour une vue)
2. ✅ Créer les fichiers de vues séparés (compact, minimal, large)
3. ✅ Implémenter la configuration des actions par vue
4. ✅ Implémenter l'ordre d'affichage des champs

### Phase 4 : Configuration formulaires

1. ✅ Créer `FormFieldConfig.js` (classe pour un champ de formulaire)
2. ✅ Créer `FormConfig.js` (classe pour la configuration globale)
3. ✅ Implémenter les groupes de champs
4. ✅ Implémenter la validation

### Phase 5 : Configuration bulk

1. ✅ Créer `BulkFieldConfig.js` (classe pour un champ bulk)
2. ✅ Créer `BulkConfig.js` (classe pour la configuration globale)
3. ✅ Implémenter l'agrégation
4. ✅ Implémenter les fonctions de transformation

### Phase 6 : Migration Resource

1. ✅ Créer `ResourceDescriptor.js`
2. ✅ Créer `ResourceTableConfig.js`
3. ✅ Créer les fichiers de vues (compact, minimal, large)
4. ✅ Créer `ResourceFormConfig.js`
5. ✅ Créer `ResourceBulkConfig.js`
6. ✅ Migrer toutes les configurations
7. ✅ Tester et valider

### Phase 7 : Migration autres entités

1. ✅ Migrer entité par entité
2. ✅ Tester chaque migration
3. ✅ Documenter les changements

---

## ✅ Avantages de cette Approche

1. **Séparation des préoccupations** : Tableau, vues, formulaires, bulk sont séparés
2. **Maintenabilité** : Toute la logique au même endroit
3. **Extensibilité** : Facile d'ajouter de nouvelles entités
4. **Réutilisabilité** : Constantes et fonctions communes
5. **Responsive** : Système de taille adaptatif
6. **Permissions** : Gestion fine des permissions
7. **Flexibilité UX** : Fichiers de vues séparés pour plus de liberté

---

## 🔧 Éléments à Ajouter

### 1. Système de cache

- Cache des descriptors générés
- Cache des configurations de colonnes
- Invalidation du cache

### 2. Système de validation

- Validation des descriptors au chargement
- Messages d'erreur clairs
- Validation des permissions

### 3. Système de migration

- Script de migration depuis l'ancien format
- Validation de la migration
- Rollback si nécessaire

### 4. Documentation

- Guide d'utilisation
- Exemples pour chaque type de configuration
- Guide de migration

### 5. Tests

- Tests unitaires pour chaque classe
- Tests d'intégration pour les descriptors
- Tests de régression

---

## 📊 Comparaison Avant/Après

### Avant (actuel)

- ❌ Code dupliqué entre entités
- ❌ Logique dispersée
- ❌ Pas de système responsive
- ❌ Configuration complexe et verbeuse
- ❌ Difficile à maintenir

### Après (proposé)

- ✅ Code centralisé et réutilisable
- ✅ Logique organisée et modulaire
- ✅ Système responsive intégré
- ✅ Configuration simple et déclarative
- ✅ Facile à maintenir et étendre

---

## 🎯 Prochaines Étapes

1. **Valider l'architecture** : S'assurer que la structure répond aux besoins
2. **Créer les classes de base** : EntityDescriptor, TableConfig, ViewConfig, etc.
3. **Implémenter le système responsive** : Breakpoints et adaptation
4. **Migrer Resource** : Premier exemple complet
5. **Tester et itérer** : Valider que tout fonctionne
6. **Migrer les autres entités** : Progressivement

---

## ❓ Questions à Résoudre

1. **Format des fichiers de vues** : Vue SFC, JSX, ou simple objet JS ?
2. **Gestion des actions** : Comment lier les actions aux vues ?
3. **Cache** : Où et comment cacher les configurations ?
4. **Migration** : Script automatique ou manuel ?
5. **Tests** : Quel niveau de couverture viser ?
