<script setup>
/**
 * InputField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ de saisie complet, orchestrant InputCore et InputLabel.
 * - Gère tous les labels (top, bottom, left, right, inline), helper, validator, bouton reset, etc.
 * - API simple : props pour le texte, slots pour le contenu riche.
 * - Utilise InputCore (input pur + édition réactive) et InputLabel (label universel)
 * - Styles DaisyUI, accessibilité, édition réactive, etc.
 * - Gère tous les types d'input : text, email, password, number, url, tel, search, date, etc.
 * - Toggle password automatique (œil) pour type="password" si le navigateur ne l'a pas déjà (utilise les labels inline DaisyUI)
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputField type="text" label="Nom" v-model="name" />
 * <InputField type="password" label="Mot de passe" v-model="password" />
 * <InputField type="password" label="Mot de passe" v-model="password" :showPasswordToggle="false" />
 * <InputField type="email" label="Email" v-model="email" color="primary" />
 *
 * @props {String} label, labelTop, labelBottom, labelLeft, labelRight, inlineLabelLeft, inlineLabelRight
 * @props {String} helper, validator, errorMessage
 * @props {String} color, size, style
 * @props {Boolean} useFieldComposable, showPasswordToggle
 * @slot labelTop, labelBottom, labelLeft, labelRight, inlineLabelLeft, inlineLabelRight, helper, validator
 */
import { computed, ref } from 'vue';
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { styleInputList, variantInputList } from '@/Pages/Atoms/atomMap';
import { getCommonProps, getCustomUtilityProps } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    label: { type: String, default: '' },
    labelTop: { type: String, default: '' },
    labelLeft: { type: String, default: '' },
    labelRight: { type: String, default: '' },
    labelBottom: { type: String, default: '' },
    inlineLabelLeft: { type: String, default: '' },
    inlineLabelRight: { type: String, default: '' },
    helper: { type: String, default: '' },
    validator: { type: [String, Boolean, Object], default: '' },
    errorMessage: { type: String, default: '' },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    // Props à transmettre à InputCore
    style: { type: String, default: 'classic', validator: (v) => styleInputList.includes(v) },
    variant: { type: String, default: '', validator: (v) => variantInputList.includes(v) },
    type: { type: String, default: 'text' },
    color: { type: String, default: '' },
    size: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
    readonly: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    required: { type: Boolean, default: false },
    name: { type: String, default: '' },
    autocomplete: { type: String, default: '' },
    modelValue: { type: [String, Number], default: '' },
    showPasswordToggle: { type: Boolean, default: true },
});

const inputCoreRef = ref(null);

// Props à transmettre à InputCore (exclut les props propres à la molecule)
const coreProps = computed(() => ({
    // Props communes
    id: props.id,
    ariaLabel: typeof props.ariaLabel === 'string' ? props.ariaLabel : '',
    role: props.role,
    tabindex: props.tabindex,
    class: props.class,
    // Props d'input
    type: props.type,
    color: props.color,
    size: props.size,
    style: props.style,
    variant: props.variant,
    placeholder: props.placeholder,
    disabled: props.disabled,
    readonly: props.readonly,
    autofocus: props.autofocus,
    required: props.required,
    name: props.name,
    autocomplete: props.autocomplete,
    modelValue: props.modelValue,
    // Props d'édition réactive
    useFieldComposable: props.useFieldComposable,
    field: props.field,
    debounceTime: props.debounceTime,
    // Props spécifiques
    showPasswordToggle: props.showPasswordToggle,
    // Props pour labels inline
    labelLeft: props.inlineLabelLeft,
    labelRight: props.inlineLabelRight,
}));

const effectiveLabelTop = computed(() => props.labelTop || props.label);
const inputId = computed(
    () => props.id || `inputfield-${Math.random().toString(36).substr(2, 9)}`,
);

// Fonctions pour gérer la validation
function getValidatorState(validator) {
    if (validator === true) return 'success';
    if (validator === 'error' || validator === false) return 'error';
    if (typeof validator === 'string') {
        // Si c'est un message d'erreur (pas un état), on retourne 'error'
        if (validator.includes('requis') || validator.includes('valide') || validator.includes('doit')) {
            return 'error';
        }
        // Sinon on vérifie si c'est un état valide
        if (['error', 'success', 'warning', 'info', ''].includes(validator)) {
            return validator;
        }
        // Par défaut, si c'est une string, c'est probablement un message d'erreur
        return 'error';
    }
    return 'error';
}

function getValidatorMessage(validator) {
    if (props.errorMessage) return props.errorMessage;
    if (typeof validator === 'string' && validator !== 'error' && validator !== 'success' && validator !== 'warning' && validator !== 'info') {
        return validator;
    }
    return '';
}
</script>

<template>
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel
            v-if="effectiveLabelTop || $slots.labelTop"
            :value="effectiveLabelTop"
            position="top"
            :for="inputId"
            :color="props.color"
            :size="props.size"
        >
            <slot name="labelTop" />
        </InputLabel>
        <div class="relative flex items-center w-full">
            <!-- Label left -->
            <InputLabel
                v-if="labelLeft || $slots.labelLeft"
                :value="labelLeft"
                position="left"
                :for="inputId"
                :color="props.color"
                :size="props.size"
                class="mr-2"
            >
                <slot name="labelLeft" />
            </InputLabel>
            
            <!-- Input principal -->
            <InputCore 
                ref="inputCoreRef"
                v-bind="coreProps" 
                v-on="$attrs"
                :aria-invalid="!!errorMessage || validator === 'error'"
            >
                <template v-if="$slots.inlineLabelLeft" #labelLeft>
                    <slot name="inlineLabelLeft" />
                </template>
                <template v-if="$slots.inlineLabelRight" #labelRight>
                    <slot name="inlineLabelRight" />
                </template>
            </InputCore>

            <!-- Label right -->
            <InputLabel
                v-if="labelRight || $slots.labelRight"
                :value="labelRight"
                position="right"
                :for="inputId"
                :color="props.color"
                :size="props.size"
                class="ml-2"
            >
                <slot name="labelRight" />
            </InputLabel>
        </div>
        <!-- Label bottom -->
        <InputLabel
            v-if="labelBottom || $slots.labelBottom"
            :value="labelBottom"
            position="bottom"
            :for="inputId"
            :color="props.color"
            :size="props.size"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        <!-- Validator -->
        <div v-if="validator || $slots.validator" class="mt-1">
            <slot name="validator">
                <Validator
                    v-if="validator"
                    :state="getValidatorState(validator)"
                    :message="getValidatorMessage(validator)"
                />
            </slot>
        </div>
        <!-- Helper -->
        <div v-if="helper || $slots.helper" class="mt-1 text-xs text-base-400">
            <slot name="helper">{{ helper }}</slot>
        </div>
    </div>
</template>

<style scoped></style> 