// /Composables/form/useInputProps.js
import { computed } from 'vue'
import { getValidPropKeys } from '@/Utils/atomic-design/inputHelper'

// Attributs HTML classiques autorisés
const HTML_ATTRS = [
  'name', 'type', 'placeholder', 'maxlength', 'minlength',
  'readonly', 'required', 'autocomplete', 'autofocus', 'multiple',
  'disabled', 'tabindex', 'accept', 'step', 'min', 'max', 'capture',
  'aria-label', 'aria-invalid', 'id', 'value'
]

// Props qui ne sont PAS des attributs HTML (à exclure)
const NON_HTML_PROPS = [
  'size', 'color', 'variant', 'animation', 'inputStyle',
  'shadow', 'backdrop', 'opacity', 'rounded',
  'labelFloating', 'labelInStart', 'labelInEnd',
  'modelValue', 'field', 'label', 'helper', 'defaultLabelPosition',
  'validation', 'actions', 'debounceTime', 'options', 'checked',
  'value', 'rows', 'cols', 'number', 'items', 'half', 'format',
  'theme', 'colorsDefault', 'colorsHistoryKey', 'suckerHide',
  'showValue', 'showPreview', 'showFormat', 'showRandom', 'showClear',
  'indeterminate', 'multiple'
]

// Éléments qui n'ont pas d'attribut 'type'
const ELEMENTS_WITHOUT_TYPE = ['select', 'textarea']

export default function useInputProps(props, attrs, emit, type = 'input', mode = 'core') {
  const propKeys = getValidPropKeys(type, mode)

  const inputAttrs = computed(() => {
    const result = {}

    // Attributs HTML autorisés depuis les props (en excluant les props non-HTML)
    propKeys.forEach(key => {
      const attrKey = key === 'ariaLabel' ? 'aria-label' : key
      if (props[key] !== undefined && !NON_HTML_PROPS.includes(key)) {
        // Exclure 'type' pour les éléments qui n'en ont pas
        if (key === 'type' && ELEMENTS_WITHOUT_TYPE.includes(type)) {
          return
        }
        // Exclure les attributs vides ou null (sauf pour certains attributs booléens)
        if (props[key] !== null && props[key] !== '' && props[key] !== undefined) {
          result[attrKey] = props[key]
        }
      }
    })

    // Attributs HTML passés via $attrs
    Object.entries(attrs).forEach(([key, val]) => {
      if (!key.startsWith('on') && HTML_ATTRS.includes(key)) {
        // Exclure 'type' pour les éléments qui n'en ont pas
        if (key === 'type' && ELEMENTS_WITHOUT_TYPE.includes(type)) {
          return
        }
        // Exclure les attributs vides ou null (sauf pour certains attributs booléens)
        if (val !== null && val !== '' && val !== undefined) {
          result[key] = val
        }
      }
    })

    // NE PAS gérer la valeur ici - laisser useInputActions s'en charger
    // if (props.modelValue !== undefined && props.modelValue !== null) {
    //   result.value = props.modelValue
    // }
    
    return result
  })

  const listeners = {}
  for (const key in attrs) {
    if (key.startsWith('on') && typeof attrs[key] === 'function') {
      const eventName = key.slice(2).toLowerCase()
      listeners[eventName] = attrs[key]
    }
  }

  return {
    inputAttrs,
    listeners
  }
}
