import { variantList, sizeList, colorList } from '@/Pages/Atoms/atomMap';
import { getCommonProps } from '@/Utils/atomic-design/uiHelper';

// --- VALIDATEURS ---

function validateVariant(value) {
    return variantList.includes(value);
}

function validateSize(value) {
    return sizeList.includes(value);
}

function validateColor(value) {
    return colorList.includes(value) || value.startsWith('color-');
}

function validateLabel(value) {
    if (typeof value === 'string') return true;
    if (typeof value === 'object' && value !== null) {
        return value.top || value.bottom || value.start || value.end || value.inStart || value.inEnd || value.floating;
    }
    return false;
}

function validateHelper(value) {
    if (typeof value === 'string') return true;
    if (typeof value === 'object' && value !== null) {
        return value.message || value.icon || value.style;
    }
    return false;
}

// --- PROPS COMMUNES ---

/**
 * Props communes à TOUS les inputs (core + field)
 */
export const COMMON_PROPS = [
    // Props communes de uiHelper.js
    ...Object.entries(getCommonProps()).map(([key, config]) => ({
        key,
        type: config.type,
        default: config.default
    })),
    // Props spécifiques aux inputs
    { key: 'modelValue', type: [String, Number, Boolean, Array, Object], default: '' },
    { key: 'name', type: String, default: '' },
    { key: 'placeholder', type: String, default: '' },
    { key: 'required', type: Boolean, default: false },
    { key: 'readonly', type: Boolean, default: false },
    { key: 'autocomplete', type: String, default: '' },
    { key: 'autofocus', type: Boolean, default: false },
    { key: 'inputStyle', type: Object, default: null },
    { key: 'variant', type: String, default: 'glass', validator: validateVariant },
    { key: 'size', type: String, default: 'md', validator: validateSize },
    { key: 'color', type: String, default: 'primary', validator: validateColor },
    { key: 'animation', type: [String, Boolean], default: true },
    { key: 'aria-invalid', type: [Boolean, String], default: undefined },
    { key: 'field', type: Object, default: null },
    { key: 'shadow', type: String, default: '' },
    { key: 'backdrop', type: String, default: '' },
    { key: 'opacity', type: String, default: '' },
    { key: 'rounded', type: String, default: '' },
];

/**
 * Props communes aux FIELDS uniquement
 */
export const COMMON_FIELD_PROPS = [
    { key: 'label', type: [String, Object], default: '', validator: validateLabel },
    { key: 'helper', type: [String, Object], default: '', validator: validateHelper },
    { key: 'defaultLabelPosition', type: String, default: 'top', validator: v => ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd', 'floating'].includes(v) },
    { key: 'validation', type: [String, Boolean, Object, Number], default: undefined },
    { key: 'actions', type: [Array, Object, String], default: undefined },
    { key: 'debounceTime', type: Number, default: 500 },    
];

/**
 * Événements communs à tous les inputs
 */
export const COMMON_EVENTS = ['onFocus', 'onBlur'];

// --- PROPS SPÉCIFIQUES PAR TYPE ---

/**
 * Props spécifiques par type d'input
 */
export const SPECIFIC_PROPS = {
    input: {
        core: [
            { key: 'labelFloating', type: Boolean, default: false },
            { key: 'labelInStart', type: String, default: '' },
            { key: 'labelInEnd', type: String, default: '' },
            { key: 'type', type: String, default: 'text' },
            { key: 'maxlength', type: [String, Number], default: undefined },
            { key: 'minlength', type: [String, Number], default: undefined },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onInput', 'onChange', 'onKeydown', 'onKeyup'],
    },
    select: {
        core: [
            { key: 'multiple', type: Boolean, default: false },
            { key: 'options', type: Array, default: () => [] },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    textarea: {
        core: [
            { key: 'labelFloating', type: Boolean, default: false },
            { key: 'labelInStart', type: String, default: '' },
            { key: 'labelInEnd', type: String, default: '' },
            { key: 'rows', type: Number, default: 3 },
            { key: 'cols', type: Number, default: 50 },
            { key: 'maxlength', type: [String, Number], default: undefined },
            { key: 'minlength', type: [String, Number], default: undefined },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onInput', 'onChange', 'onKeydown', 'onKeyup'],
    },
    radio: {
        core: [
            { key: 'value', type: [String, Number, Boolean], default: '' },
            { key: 'checked', type: Boolean, default: false },
            { key: 'type', type: String, default: 'radio' },
            { key: 'options', type: Array, default: () => [] },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    range: {
        core: [
            { key: 'min', type: [String, Number], default: 0 },
            { key: 'max', type: [String, Number], default: 100 },
            { key: 'step', type: [String, Number], default: 1 },
            { key: 'type', type: String, default: 'range' },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onInput', 'onChange'],
    },
    rating: {
        core: [
            { key: 'min', type: [String, Number], default: 0 },
            { key: 'max', type: [String, Number], default: 5 },
            { key: 'number', type: Number, default: 5 },
            { key: 'numberChecked', type: Number, default: 0 },
            { key: 'defaultMask', type: String, default: 'mask-star', validator: v => maskList.includes(v) },
            { key: 'items', type: Array, default: null },
            { key: 'type', type: String, default: 'radio' },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    toggle: {
        core: [
            { key: 'checked', type: Boolean, default: false },
            { key: 'indeterminate', type: Boolean, default: false },
            { key: 'styleState', type: Object, default: null },
            { key: 'type', type: String, default: 'checkbox' },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    filter: {
        core: [
            { key: 'value', type: [String, Number, Boolean], default: '' },
            { key: 'checked', type: Boolean, default: false },
            { key: 'type', type: String, default: 'radio' },
            { key: 'options', type: Array, default: () => [] },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onInput', 'onChange'],
    },
    file: {
        core: [
            { key: 'labelFloating', type: Boolean, default: false },
            { key: 'labelInStart', type: String, default: '' },
            { key: 'labelInEnd', type: String, default: '' },
            { key: 'accept', type: String, default: '' },
            { key: 'multiple', type: Boolean, default: false },
            { key: 'capture', type: String, default: '' },
            { key: 'maxSize', type: Number, default: 0 },
            { key: 'useProgress', type: Number, default: null },
            { key: 'showPreview', type: Boolean, default: true },
            { key: 'type', type: String, default: 'file' },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    checkbox: {
        core: [
            { key: 'indeterminate', type: Boolean, default: false },
            { key: 'styleState', type: Object, default: null },
            { key: 'value', type: [String, Number, Boolean], default: '' },
            { key: 'options', type: Array, default: () => [] },
        ],
        field: [],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    color: {
        core: [
            { key: 'type', type: String, default: 'color' },
            { key: 'format', type: String, default: 'hex', validator: v => ['hex', 'rgb', 'rgba', 'hsl', 'hsla'].includes(v) },
            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },
            { key: 'colorsDefault', type: Array, default: () => [
                '#000000', '#FFFFFF', '#FF1900', '#F47365', '#FFB243', '#FFE623',
                '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF', '#5D61FF', '#FF89CF',
                '#FC3CAD', '#BF3DCE', '#8E00A7', 'rgba(0,0,0,0)'
            ]},
            { key: 'colorsHistoryKey', type: String, default: 'vue-colorpicker-history' },
            { key: 'suckerHide', type: Boolean, default: true },
            { key: 'showValue', type: Boolean, default: true },
            { key: 'showPreview', type: Boolean, default: true },
            { key: 'showFormat', type: Boolean, default: true },
            { key: 'showRandom', type: Boolean, default: true },
            { key: 'showClear', type: Boolean, default: true },
            { key: 'colorPicker', type: Boolean, default: true },
        ],
        field: [
            { key: 'format', type: String, default: 'hex', validator: v => ['hex', 'rgb', 'rgba', 'hsl', 'hsla'].includes(v) },
            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },
            { key: 'colorsDefault', type: Array, default: () => [
                '#000000', '#FFFFFF', '#FF1900', '#F47365', '#FFB243', '#FFE623',
                '#6EFF2A', '#1BC7B1', '#00BEFF', '#2E81FF', '#5D61FF', '#FF89CF',
                '#FC3CAD', '#BF3DCE', '#8E00A7', 'rgba(0,0,0,0)'
            ]},
            { key: 'colorsHistoryKey', type: String, default: 'vue-colorpicker-history' },
            { key: 'suckerHide', type: Boolean, default: true },
            { key: 'showValue', type: Boolean, default: true },
            { key: 'showPreview', type: Boolean, default: true },
            { key: 'showFormat', type: Boolean, default: true },
            { key: 'showRandom', type: Boolean, default: true },
            { key: 'showClear', type: Boolean, default: true },
            { key: 'colorPicker', type: Boolean, default: true },
        ],
        events: [...COMMON_EVENTS, 'onChange'],
    },
    date: {
        core: [
            { key: 'min', type: [Date, String], default: null },
            { key: 'max', type: [Date, String], default: null },
            { key: 'format', type: String, default: 'YYYY-MM-DD' },
            { key: 'locale', type: String, default: 'fr' },
            { key: 'value', type: [Date, String], default: null },
            { key: 'placeholder', type: String, default: 'Sélectionner une date' },
            { key: 'clearable', type: Boolean, default: true },
            { key: 'weekStart', type: Number, default: 1 },
            { key: 'firstDayOfWeek', type: Number, default: 1 },
            { key: 'showWeekNumbers', type: Boolean, default: false },
            { key: 'showToday', type: Boolean, default: true },
            { key: 'todayLabel', type: String, default: 'Aujourd\'hui' },
            { key: 'clearLabel', type: String, default: 'Effacer' },
            { key: 'previousLabel', type: String, default: 'Précédent' },
            { key: 'nextLabel', type: String, default: 'Suivant' },
            { key: 'monthLabel', type: String, default: 'Mois' },
            { key: 'yearLabel', type: String, default: 'Année' },
            { key: 'disabledDates', type: Array, default: () => [] },
            { key: 'enabledDates', type: Array, default: () => [] },
            { key: 'range', type: Boolean, default: false },
            { key: 'multiple', type: Boolean, default: false },
            { key: 'autoClose', type: Boolean, default: true },
            { key: 'position', type: String, default: 'bottom', validator: v => ['top', 'bottom', 'left', 'right'].includes(v) },
            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },
        ],
        field: [
            { key: 'format', type: String, default: 'YYYY-MM-DD' },
            { key: 'locale', type: String, default: 'fr' },
            { key: 'placeholder', type: String, default: 'Sélectionner une date' },
            { key: 'clearable', type: Boolean, default: true },
            { key: 'weekStart', type: Number, default: 1 },
            { key: 'firstDayOfWeek', type: Number, default: 1 },
            { key: 'showWeekNumbers', type: Boolean, default: false },
            { key: 'showToday', type: Boolean, default: true },
            { key: 'todayLabel', type: String, default: 'Aujourd\'hui' },
            { key: 'clearLabel', type: String, default: 'Effacer' },
            { key: 'previousLabel', type: String, default: 'Précédent' },
            { key: 'nextLabel', type: String, default: 'Suivant' },
            { key: 'monthLabel', type: String, default: 'Mois' },
            { key: 'yearLabel', type: String, default: 'Année' },
            { key: 'disabledDates', type: Array, default: () => [] },
            { key: 'enabledDates', type: Array, default: () => [] },
            { key: 'range', type: Boolean, default: false },
            { key: 'multiple', type: Boolean, default: false },
            { key: 'autoClose', type: Boolean, default: true },
            { key: 'position', type: String, default: 'bottom', validator: v => ['top', 'bottom', 'left', 'right'].includes(v) },
            { key: 'theme', type: String, default: 'dark', validator: v => ['light', 'dark'].includes(v) },
        ],
        events: [...COMMON_EVENTS, 'onChange', 'onSelect', 'onClear', 'onOpen', 'onClose'],
    },
};

// --- UTILITAIRES ---

export function getInputPropsDefinition(type = 'input', mode = 'core') {
  const baseProps = [...COMMON_PROPS]
  if (mode === 'field') baseProps.push(...COMMON_FIELD_PROPS)

  const specificProps = SPECIFIC_PROPS[type]?.[mode] || []
  const all = [...baseProps, ...specificProps]

  return all.reduce((acc, prop) => {
    acc[prop.key] = {
      type: prop.type,
      default: prop.default,
      validator: prop.validator,
    }
    return acc
  }, {})
}

export function getValidPropKeys(type = 'input', mode = 'core') {
  const baseKeys = COMMON_PROPS.map(p => p.key)
  const fieldKeys = mode === 'field' ? COMMON_FIELD_PROPS.map(p => p.key) : []
  const specificKeys = (SPECIFIC_PROPS[type]?.[mode] || []).map(p => p.key)

  return [...baseKeys, ...fieldKeys, ...specificKeys]
}
