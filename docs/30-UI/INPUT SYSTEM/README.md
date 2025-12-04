# 🔧 Système d'Input KrosmozJDR

## 📋 Vue d'ensemble

Le système d'input de KrosmozJDR est un **système unifié et sophistiqué** basé sur l'Atomic Design, utilisant Vue 3 avec Composition API. Il intègre des fonctionnalités avancées de validation, d'actions contextuelles, de gestion d'état et de personnalisation.

### 🎯 **Principes fondamentaux**

- **Atomic Design** : Séparation claire entre Atoms (Core) et Molecules (Field)
- **DRY (Don't Repeat Yourself)** : API centralisée, 0 duplication
- **Validation granulaire** : Règles multiples par champ avec déclencheurs flexibles
- **Transparence** : Le système ne bloque jamais la logique métier des vues
- **Unification** : Une seule API pour tous les types d'input
- **Accessibilité** : Standards WCAG respectés nativement

### 🏗️ **Architecture**

```
InputField (Molecule)
├── InputCore (Atom) - Saisie et styles
├── InputLabel (Atom) - Labels avec positions multiples
├── Validator (Atom) - Messages de validation
├── Helper (Atom) - Textes d'aide
└── Actions contextuelles - Boutons d'action
```

### 🔧 **Composants disponibles**

| Type | Core | Field | Description |
|------|------|-------|-------------|
| **Input** | `InputCore` | `InputField` | Champ de saisie standard |
| **Textarea** | `TextareaCore` | `TextareaField` | Zone de texte multiligne |
| **Select** | `SelectCore` | `SelectField` | Liste déroulante |
| **Checkbox** | `CheckboxCore` | `CheckboxField` | Case à cocher |
| **Radio** | `RadioCore` | `RadioField` | Bouton radio |
| **Toggle** | `ToggleCore` | `ToggleField` | Interrupteur |
| **Range** | `RangeCore` | `RangeField` | Curseur de valeur |
| **Rating** | `RatingCore` | `RatingField` | Système de notation |
| **Filter** | `FilterCore` | `FilterField` | Filtre de recherche |
| **File** | `FileCore` | `FileField` | Upload de fichiers |
| **Color** | `ColorCore` | `ColorField` | Sélecteur de couleur |
| **Date** | `DateCore` | `DateField` | Sélecteur de date |

---

## 🧭 Navigation rapide

### 📚 **Documentation technique**
- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - Architecture technique détaillée
- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants Core et Field
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète de l'API

### 🎨 **Fonctionnalités**
- **[VALIDATION.md](./VALIDATION.md)** - Système de validation unifié
- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles
- **[STYLING.md](./STYLING.md)** - Styles et personnalisation
- **[LABELS.md](./LABELS.md)** - Système de labels complexe
- **[SPECIALIZED_COMPONENTS.md](./SPECIALIZED_COMPONENTS.md)** - Composants Date et Color avec fallback

### 💡 **Pratique**
- **[USAGE_EXAMPLES.md](./USAGE_EXAMPLES.md)** - Exemples d'utilisation pratiques
- **[FILE_UPLOAD.md](./FILE_UPLOAD.md)** - Système complet d'upload de fichiers
- **[INSTALLATION.md](./INSTALLATION.md)** - Guide d'installation des dépendances

---

## 🚀 **Démarrage rapide**

### Installation automatique
Tous les composants sont disponibles automatiquement dans le projet.

### Utilisation basique
```vue
<template>
  <!-- Input simple -->
  <InputField 
    v-model="email" 
    label="Email" 
    type="email" 
  />
  
  <!-- Avec validation granulaire -->
  <InputField 
    v-model="password" 
    label="Mot de passe" 
    type="password"
    :validation-rules="[
      {
        rule: (value) => value && value.length >= 8,
        message: 'Minimum 8 caractères',
        state: 'error',
        trigger: 'blur'
      }
    ]"
  />
  
  <!-- Avec actions -->
  <InputField 
    v-model="search" 
    label="Recherche"
    :actions="['clear', 'copy']"
  />
</template>
```

### API unifiée
Tous les composants Field partagent la même API :
- **Props** : Héritées automatiquement via `getInputPropsDefinition()`
- **Validation** : Règles granulaire via `validationRules` ou validation simple via `validation`
- **Actions** : Actions contextuelles intégrées
- **Styles** : Personnalisation via `variant`, `color`, `size`

---

## 🎯 **Points forts**

### ✅ **Architecture moderne**
- Vue 3 + Composition API
- Composables réutilisables
- Template unifié (FieldTemplate)
- Props dynamiques

### ✅ **Validation granulaire**
- Règles multiples par champ
- Déclencheurs flexibles (auto, manual, blur, change)
- États multiples (error, success, warning, info)
- Intégration notifications
- Contrôle parent et automatique

### ✅ **Actions contextuelles**
- 8 actions intégrées (reset, clear, copy, etc.)
- Actions personnalisées
- Notifications automatiques
- Confirmations

### ✅ **UX excellente**
- Validation temps réel
- Labels flexibles (7 positions)
- Accessibilité native
- Responsive design

---

## 🔗 **Liens utiles**

- **[Documentation UI générale](../README.md)** - Vue d'ensemble UI
- **[Atomic Design](../ATOMIC_DESIGN.md)** - Principes Atomic Design
- **[Bonnes pratiques](../BEST_PRACTICES.md)** - Guide des bonnes pratiques
- **[Système de notifications](../NOTIFICATIONS.md)** - Notifications toast

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Système d'Input KrosmozJDR v2.0*
