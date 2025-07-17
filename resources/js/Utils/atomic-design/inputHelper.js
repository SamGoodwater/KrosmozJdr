// inputHelper.js — Version DRY ultra-factorisée (props/attrs génériques, exclusion des commons possible)

import { maskList } from '@/Pages/Atoms/data-input/data-inputMap';
import { colorList, sizeList, variantList } from '@/Pages/Atoms/atomMap';
import { validateLabel } from './labelManager';
import { validateValidationObject } from './validationManager';

// --- PROPS COMMUNES ---
const coreCommons = {
        modelValue: { type: [String, Number, Boolean, Array, Object], default: '' },
        name: { type: String, default: '' },
        required: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
        autocomplete: { type: String, default: '' },
        autofocus: { type: Boolean, default: false },
  color: { type: String, default: '', validator: v => colorList.includes(v) },
  size: { type: String, default: '', validator: v => sizeList.includes(v) },
  style: {
    type: [String, Object],
    default: 'glass',
    validator: v => (typeof v === 'string' ? variantList.includes(v) : true),
  },
        ariaLabel: { type: String, default: '' },
        'aria-invalid': { type: [Boolean, String], default: undefined },
    };
const fieldCommons = {
  ...coreCommons,
  label: { type: [String, Object], default: '', validator: validateLabel },
  helper: { type: String, default: '' },
  validator: { type: [String, Boolean, Object], default: '' },
  errorMessage: { type: String, default: '' },
  validation: { type: [String, Boolean, Object], default: '', validator: validateValidationObject },
};

// --- PROPS SPÉCIFIQUES PAR TYPE ---
const typeProps = {
  input: {
    core: {
      labelFloating: { type: Boolean, default: false },
      labelEnd: { type: String, default: '' },
      labelStart: { type: String, default: '' },
    },
    field: {
      label: { type: [String, Object], default: '', validator: validateLabel },
      defaultLabelPosition: { type: String, default: 'top', validator: v => ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd', 'floating'].includes(v) },
      helper: { type: String, default: '' },
      validation: { type: [String, Boolean, Object], default: '', validator: validateValidationObject },
      validator: { type: [String, Boolean, Object], default: '' },
      errorMessage: { type: String, default: '' },
      useFieldComposable: { type: Boolean, default: false },
      field: { type: Object, default: null },
      debounceTime: { type: Number, default: 500 },
      showPasswordToggle: { type: Boolean, default: true },
      actions: { type: [Array, Object, String], default: undefined },
    },
  },
  select: {
    core: {
      labelInStart: { type: String, default: '' },
      labelInEnd: { type: String, default: '' },
    },
    field: {
      label: { type: [String, Object], default: '', validator: validateLabel },
      defaultLabelPosition: { type: String, default: 'top', validator: v => ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd'].includes(v) },
      helper: { type: String, default: '' },
      validation: { type: [String, Boolean, Object], default: '', validator: validateValidationObject },
      validator: { type: [String, Boolean, Object], default: '' },
      errorMessage: { type: String, default: '' },
      actions: { type: [Array, Object, String], default: undefined },
      options: { type: Array, required: true },
      multiple: { type: Boolean, default: false },
      disabled: { type: Boolean, default: false },
      readonly: { type: Boolean, default: false },
        required: { type: Boolean, default: false },
    },
  },
  textarea: {
    core: {
      labelInStart: { type: String, default: '' },
      labelInEnd: { type: String, default: '' },
    },
    field: {
      label: { type: [String, Object], default: '', validator: validateLabel },
      defaultLabelPosition: { type: String, default: 'top', validator: v => ['top', 'bottom', 'start', 'end', 'inStart', 'inEnd'].includes(v) },
      helper: { type: String, default: '' },
      validation: { type: [String, Boolean, Object], default: '', validator: validateValidationObject },
      validator: { type: [String, Boolean, Object], default: '' },
      errorMessage: { type: String, default: '' },
      actions: { type: [Array, Object, String], default: undefined },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
      required: { type: Boolean, default: false },
    },
  },
  radio: {
    core: {
      labelBottom: { type: String, default: '' },
      value: { type: [String, Number, Boolean], default: '' },
      checked: { type: Boolean, default: false },
    },
    field: {
      labelBottom: { type: String, default: '' },
      value: { type: [String, Number, Boolean], default: '' },
      checked: { type: Boolean, default: false },
    },
  },
  range: {
    core: {
      labelBottom: { type: String, default: '' },
      useFieldComposable: { type: Boolean, default: false },
      field: { type: Object, default: null },
      debounceTime: { type: Number, default: 500 },
      min: { type: [String, Number], default: '' },
      max: { type: [String, Number], default: '' },
      step: { type: [String, Number], default: '' },
    },
    field: {
      labelBottom: { type: String, default: '' },
      useFieldComposable: { type: Boolean, default: false },
      field: { type: Object, default: null },
      debounceTime: { type: Number, default: 500 },
        min: { type: [String, Number], default: '' },
        max: { type: [String, Number], default: '' },
        step: { type: [String, Number], default: '' },
    },
  },
  rating: {
    core: {
      labelBottom: { type: String, default: '' },
      useFieldComposable: { type: Boolean, default: false },
      field: { type: Object, default: null },
      debounceTime: { type: Number, default: 500 },
      number: { type: Number, default: 5 },
      mask: { type: String, default: 'mask-star', validator: v => maskList.includes(v) },
      items: { type: Array, default: null },
    },
    field: {
      labelBottom: { type: String, default: '' },
      useFieldComposable: { type: Boolean, default: false },
      field: { type: Object, default: null },
      debounceTime: { type: Number, default: 500 },
      number: { type: Number, default: 5 },
      mask: { type: String, default: 'mask-star', validator: v => maskList.includes(v) },
      items: { type: Array, default: null },
    },
  },
  toggle: {
    core: {
      labelBottom: { type: String, default: '' },
      checked: { type: Boolean, default: false },
      indeterminate: { type: Boolean, default: false },
      bgOn: { type: String, default: '' },
      bgOff: { type: String, default: '' },
    },
    field: {
      labelBottom: { type: String, default: '' },
      checked: { type: Boolean, default: false },
      indeterminate: { type: Boolean, default: false },
      bgOn: { type: String, default: '' },
      bgOff: { type: String, default: '' },
    },
  },
  filter: {
    core: {
      labelBottom: { type: String, default: '' },
    },
    field: {
      labelBottom: { type: String, default: '' },
    },
  },
  fileinput: {
    core: {
      labelBottom: { type: String, default: '' },
      accept: { type: String, default: '' },
      multiple: { type: Boolean, default: false },
      capture: { type: String, default: '' },
    },
    field: {
      label: { type: String, default: '' },
      helper: { type: String, default: '' },
      multiple: { type: Boolean, default: false },
      accept: { type: String, default: '' },
      maxSize: { type: Number, default: 0 },
      error: { type: String, default: '' },
      progress: { type: Number, default: null },
      disabled: { type: Boolean, default: false },
    },
  },
  checkbox: {
    core: {
      indeterminate: { type: Boolean, default: false },
      bgOn: { type: String, default: '' },
      bgOff: { type: String, default: '' },
      value: { type: [String, Number, Boolean], default: '' },
    },
    field: {
      label: { type: [String, Object], default: '', validator: validateLabel },
      defaultLabelPosition: { type: String, default: 'top', validator: v => ['top', 'bottom', 'start', 'end'].includes(v) },
      helper: { type: String, default: '' },
      validation: { type: [String, Boolean, Object], default: '', validator: validateValidationObject },
      validator: { type: [String, Boolean, Object], default: '' },
      errorMessage: { type: String, default: '' },
      actions: { type: [Array, Object, String], default: undefined },
        disabled: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        required: { type: Boolean, default: false },
    },
  },
};

/**
 * getInputProps — Récupère les props factorisées pour un type d'input et un variant donné
 * @param {string} type - Le type d'input (input, checkbox, radio, ...)
 * @param {string} [variant='core'] - Le variant ('core' ou 'field')
 * @param {Array} [exclude=[]] - Props à exclure ("common" pour exclure tous les props communs)
 * @returns {object} - Objet props prêt à injecter dans defineProps
 */
export function getInputProps(type, variant = 'core', exclude = []) {
  const commons = variant === 'field' ? fieldCommons : coreCommons;
  const specifics = typeProps[type]?.[variant] || {};
  let base = { ...commons, ...specifics };
  let excludeList = [...exclude];
  if (exclude.includes('common')) {
    excludeList = excludeList.filter(e => e !== 'common');
    excludeList = [...excludeList, ...Object.keys(commons)];
  }
  return Object.fromEntries(
    Object.entries(base).filter(([key]) => !excludeList.includes(key))
  );
}

// --- ATTRS ---
const attrsExclusions = ['label', 'helper', 'validator', 'errorMessage', 'validation'];

/**
 * getInputAttrs — Récupère les attrs HTML pour un type d'input et un variant donné
 * @param {string} type - Le type d'input (input, checkbox, ...)
 * @param {string} [variant='core'] - Le variant ('core' ou 'field')
 * @param {Array} [exclude=[]] - Attributs à exclure ("common" pour exclure tous les attrs communs)
 * @returns {object} - Objet attrs prêt à injecter dans v-bind
 */
export function getInputAttrs(type, variant = 'core', exclude = []) {
  const commons = variant === 'field' ? fieldCommons : coreCommons;
  const specifics = typeProps[type]?.[variant] || {};
  let base = { ...commons, ...specifics };
  let excludeList = [...attrsExclusions, ...exclude];
  if (exclude.includes('common')) {
    excludeList = excludeList.filter(e => e !== 'common');
    excludeList = [...excludeList, ...Object.keys(commons)];
  }
  return Object.fromEntries(
    Object.entries(base).filter(([key]) => !excludeList.includes(key))
  );
}
