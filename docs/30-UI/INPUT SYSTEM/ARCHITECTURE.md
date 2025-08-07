# 🏗️ Architecture du Système d'Input

## 📋 Vue d'ensemble technique

Le système d'input de KrosmozJDR repose sur une **architecture modulaire et factorisée** basée sur l'Atomic Design, utilisant Vue 3 avec Composition API et DaisyUI.

---

## 🎯 **Stack Technologique**

### **Frontend**
- **Vue 3** : Framework principal avec Composition API
- **Composition API** : Logique réactive et composables
- **Tailwind CSS** : Utilitaires CSS et responsive design
- **DaisyUI** : Composants pré-stylés et thèmes

### **Architecture**
- **Atomic Design** : Atoms, Molecules, Organisms
- **Composables** : Logique métier centralisée
- **Template unifié** : FieldTemplate pour tous les Fields
- **Props dynamiques** : Système de props flexible

---

## 📁 **Structure des Dossiers**

```
resources/js/Pages/Atoms/data-input/          # Composants Core (Atoms)
├── InputCore.vue                             # Input de base
├── TextareaCore.vue                          # Textarea de base
├── SelectCore.vue                            # Select de base
├── CheckboxCore.vue                          # Checkbox de base
├── RadioCore.vue                             # Radio de base
├── DateCore.vue                              # Date de base
├── FileCore.vue                              # File de base
├── ColorCore.vue                             # Color de base
├── RangeCore.vue                             # Range de base
├── RatingCore.vue                            # Rating de base
├── ToggleCore.vue                            # Toggle de base
├── FilterCore.vue                            # Filter de base
├── InputLabel.vue                            # Label atomique
├── Validator.vue                             # Validateur atomique
├── Helper.vue                                # Helper atomique
└── data-inputMap.js                          # Mapping des types

resources/js/Pages/Molecules/data-input/      # Composants Field (Molecules)
├── InputField.vue                            # Input complet
├── TextareaField.vue                         # Textarea complet
├── SelectField.vue                           # Select complet
├── CheckboxField.vue                         # Checkbox complet
├── RadioField.vue                            # Radio complet
├── DateField.vue                             # Date complet
├── FileField.vue                             # File complet
├── ColorField.vue                            # Color complet
├── RangeField.vue                            # Range complet
├── RatingField.vue                           # Rating complet
├── ToggleField.vue                           # Toggle complet
├── FilterField.vue                           # Filter complet
└── FieldTemplate.vue                         # Template unifié

resources/js/Composables/form/                # Logique métier
├── useInputField.js                          # Composable unifié
├── useInputActions.js                        # Gestion des actions
├── useInputProps.js                          # Gestion des props
├── useInputStyle.js                          # Gestion des styles
└── useValidation.js                          # Système de validation

resources/js/Utils/atomic-design/             # Utilitaires
├── inputHelper.js                            # Helpers pour inputs
├── validationManager.js                      # Gestionnaire de validation
├── labelManager.js                           # Gestionnaire de labels
├── uiHelper.js                               # Helpers UI génériques
└── atomManager.js                            # Gestionnaire d'atoms
```

---

## 🔧 **Composables Principaux**

### **useInputField.js** - Composable unifié
```javascript
// Composable central qui orchestre tous les aspects
export default function useInputField({
  modelValue,
  type = 'input',
  mode = 'field',
  props,
  attrs,
  emit
}) {
  // Gestion du v-model via useInputActions
  const { currentValue, actionsToDisplay, inputRef, focus } = useInputActions({...})
  
  // Gestion des props via useInputProps
  const { inputAttrs, listeners } = useInputProps(props, attrs, emit, type, mode)
  
  // Gestion de la validation via useValidation
  const validation = useValidation({...})
  
  // Gestion du style via useInputStyle
  const styleProperties = getInputStyleProperties({...})
  
  return {
    // API unifiée
    currentValue, actionsToDisplay, inputRef, focus,
    inputAttrs, listeners,
    validationState, validationMessage,
    styleProperties, containerClasses
  }
}
```

### **useInputActions.js** - Gestion des actions
```javascript
// Gestion des actions contextuelles (reset, clear, copy, etc.)
export default function useInputActions({
  modelValue,
  type,
  actions,
  readonly,
  debounce,
  autofocus,
  emit
}) {
  // Actions disponibles
  const reset = () => { /* logique reset */ }
  const clear = () => { /* logique clear */ }
  const copy = () => { /* logique copy */ }
  
  // Actions à afficher selon le type et la configuration
  const actionsToDisplay = computed(() => {
    return parseActions(actions).filter(action => 
      isActionCompatible(action.key, type)
    )
  })
  
  return { reset, clear, copy, actionsToDisplay }
}
```

### **useValidation.js** - Système de validation granulaire
```javascript
// Système de validation granulaire avec règles multiples
export function useValidation({
  value,
  rules = [],
  externalState = null,
  autoValidate = true,
  parentControl = false
}) {
  // États de validation
  const validationState = ref(null)
  const validationMessage = ref(null)
  const hasInteracted = ref(false)
  
  // Fonction de validation granulaire
  const validate = (trigger = 'auto') => {
    // Évaluation des règles selon le déclencheur
    // Tri par priorité et détermination de l'état final
  }
  
  return {
    validationState, validationMessage, hasInteracted,
    validate, validateOnBlur, validateOnChange,
    setInteracted, reset, isEnabled
  }
}
```

---

## 🎨 **Pattern Core → Field**

### **Architecture en couches**
```
Field (Molecule)
├── FieldTemplate (Template unifié)
│   ├── InputLabel (Atom) - Labels
│   ├── Core (Atom) - Input natif
│   ├── Actions contextuelles
│   ├── Validator (Atom) - Validation
│   └── Helper (Atom) - Aide
└── useInputField (Composable) - Logique métier
```

### **Transmission des props**
```javascript
// Dans un composant Field
const {
  inputAttrs,    // Props transmises au Core
  listeners,     // Événements transmis au Core
  labelConfig,   // Configuration des labels
  validationState, // État de validation
  styleProperties // Propriétés de style
} = useInputField({...})

// Transmission au Core via FieldTemplate
<FieldTemplate>
  <template #core="{ inputAttrs, listeners, inputRef }">
    <InputCore v-bind="inputAttrs" v-on="listeners" ref="inputRef" />
  </template>
</FieldTemplate>
```

---

## 🔄 **Flux de Données**

### **1. Initialisation**
```javascript
// 1. Props définies via getInputPropsDefinition()
const props = defineProps(getInputPropsDefinition('input', 'field'))

// 2. Composable initialisé
const inputField = useInputField({
  modelValue: props.modelValue,
  type: 'input',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})
```

### **2. Réactivité**
```javascript
// 3. Valeur réactive
const currentValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// 4. Validation réactive
watch(() => props.validation, (newValidation) => {
  if (newValidation) {
    validation.validate()
  }
})
```

### **3. Rendu**
```javascript
// 5. Template avec transmission des props
<FieldTemplate
  :input-attrs="inputAttrs"
  :listeners="listeners"
  :validation-state="validationState"
>
  <template #core="{ inputAttrs, listeners }">
    <InputCore v-bind="inputAttrs" v-on="listeners" />
  </template>
</FieldTemplate>
```

---

## 🎯 **Principes d'Architecture**

### **1. Séparation des responsabilités**
- **Core (Atom)** : Input natif + styles + accessibilité
- **Field (Molecule)** : Composition + validation + actions
- **Composables** : Logique métier réutilisable
- **Utilitaires** : Helpers et fonctions utilitaires

### **2. Factorisation maximale**
- **Props communes** : Héritées automatiquement
- **Template unifié** : FieldTemplate pour tous les Fields
- **Composable unifié** : useInputField pour toute la logique
- **API cohérente** : Même interface pour tous les composants

### **3. Transparence**
- **v-model** : Fonctionne normalement
- **Événements** : Préservés et transmis
- **Validation** : Ne bloque jamais la logique métier
- **Actions** : Intégrées mais non bloquantes

### **4. Extensibilité**
- **Nouveaux types** : Facile d'ajouter de nouveaux inputs
- **Nouvelles actions** : Système d'actions extensible
- **Nouveaux styles** : Système de styles flexible
- **Nouvelles règles de validation** : Système de validation granulaire extensible

---

## 🔧 **Configuration et Personnalisation**

### **Props dynamiques**
```javascript
// Props héritées automatiquement
const props = defineProps({
  ...getCommonProps(),           // Props communes
  ...getCustomUtilityProps(),    // Utilitaires custom
  ...getInputProps('input', 'field') // Props spécifiques
})
```

### **Styles personnalisés**
```javascript
// Système de styles flexible
const styleProperties = computed(() =>
  getInputStyleProperties(props.type || 'text', {
    variant: props.variant,
    color: props.color,
    size: props.size,
    animation: props.animation,
    ...props.inputStyle
  })
)
```

### **Validation granulaire**
```javascript
// Validation granulaire avec règles multiples
const validation = useValidation({
  value: currentValue,
  rules: props.validationRules,
  externalState: props.validation,
  autoValidate: props.autoValidate,
  parentControl: props.parentControl
})
```

---

## 🚀 **Performance et Optimisation**

### **Computed properties**
- **Bindings optimisés** : Calculés une seule fois
- **Réactivité ciblée** : Seuls les changements nécessaires
- **Mémoisation** : Évite les re-calculs inutiles

### **Lazy loading**
- **Composants Core** : Chargés à la demande
- **Composables** : Importés dynamiquement si nécessaire
- **Styles** : Générés uniquement pour les variants utilisés

### **Tree shaking**
- **Imports ciblés** : Seules les fonctions nécessaires
- **Code splitting** : Séparation logique des modules
- **Purge CSS** : Suppression des styles inutilisés

---

## 🔗 **Liens utiles**

- **[COMPONENTS.md](./COMPONENTS.md)** - Guide des composants
- **[API_REFERENCE.md](./API_REFERENCE.md)** - Référence complète
- **[VALIDATION.md](./VALIDATION.md)** - Système de validation
- **[ACTIONS.md](./ACTIONS.md)** - Actions contextuelles

---

*Documentation générée le : {{ date('Y-m-d H:i:s') }}*
*Architecture du Système d'Input KrosmozJDR v2.0*
