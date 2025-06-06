<script setup>
/**
 * Radio Atom (DaisyUI)
 *
 * @description
 * Composant atomique Radio conforme DaisyUI et Atomic Design.
 * - Gère tous les cas d'usage radio (simple, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Props bgOn/bgOff : classes Tailwind appliquées selon l'état coché/décoché
 * - Slots : #labelTop, #labelBottom, #validator, #help, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur checked.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Radio label="Homme" v-model="gender" value="male" color="primary" size="md" bgOn="bg-blue-200 border-blue-600" bgOff="bg-base-200 border-base-300" />
 *
 * @props {String|Boolean|Number} modelValue - Valeur du radio (v-model natif, prioritaire sur checked)
 * @props {String|Boolean|Number} value - Valeur du radio (obligatoire pour groupe)
 * @props {Boolean} checked - Valeur fallback si modelValue n'est pas utilisé
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} bgOn - Classes Tailwind appliquées quand le radio est coché (ex: 'bg-blue-200 border-blue-600')
 * @props {String} bgOff - Classes Tailwind appliquées quand le radio est décoché (ex: 'bg-base-200 border-base-300')
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du radio
 *
 * @note La valeur du radio est contrôlée par modelValue (v-model) si défini, sinon par checked.
 */
import { computed, ref, watch, onMounted, defineExpose } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getInputProps, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps({ exclude: ['type', 'placeholder', 'autocomplete', 'min', 'max', 'step', 'inputmode', 'pattern', 'maxlength', 'minlength'] }),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    value: { type: [String, Boolean, Number], default: '' },
    checked: { type: Boolean, default: false },
    bgOn: { type: String, default: '' },
    bgOff: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const radioRef = ref(null);

const isChecked = computed({
    get() {
        // Pour radio, modelValue === value => checked
        return props.modelValue !== undefined ? props.modelValue === props.value : props.checked;
    },
    set(val) {
        if (val) emit('update:modelValue', props.value);
    }
});

const atomClasses = computed(() => {
    const classes = ['radio'];
    // Couleur DaisyUI
    if (props.color === 'neutral') classes.push('radio-neutral');
    if (props.color === 'primary') classes.push('radio-primary');
    if (props.color === 'secondary') classes.push('radio-secondary');
    if (props.color === 'accent') classes.push('radio-accent');
    if (props.color === 'info') classes.push('radio-info');
    if (props.color === 'success') classes.push('radio-success');
    if (props.color === 'warning') classes.push('radio-warning');
    if (props.color === 'error') classes.push('radio-error');
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('radio-xs');
    if (props.size === 'sm') classes.push('radio-sm');
    if (props.size === 'md') classes.push('radio-md');
    if (props.size === 'lg') classes.push('radio-lg');
    if (props.size === 'xl') classes.push('radio-xl');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    // Erreur
    if (props.errorMessage || props.validator) classes.push('validator radio-error');
    return classes.join(' ');
});

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    name: props.name || undefined,
    required: props.required || undefined,
    readonly: props.readonly || undefined,
    disabled: props.disabled || undefined,
    autofocus: props.autofocus || undefined,
    id: props.id || undefined,
    'aria-checked': isChecked.value,
    value: props.value,
}));

const radioId = computed(() => props.id || `radio-${Math.random().toString(36).substr(2, 9)}`);

function onInput(e) {
    if (e.target.checked) {
        emit('update:modelValue', props.value);
    }
}

onMounted(() => {
    if (radioRef.value && props.autofocus) {
        radioRef.value.focus();
    }
});
defineExpose({ focus: () => radioRef.value && radioRef.value.focus() });
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="radioId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="flex items-center gap-2">
                <input ref="radioRef" type="radio" v-bind="attrs" :id="radioId"
                    :class="[atomClasses, isChecked ? bgOn : bgOff]" :checked="isChecked" @input="onInput"
                    :aria-invalid="!!errorMessage || validator" />
                <slot />
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="radioId" :value="labelBottom" class="mt-1">
                <template v-if="$slots.labelBottom" #default>
                    <slot name="labelBottom" />
                </template>
            </InputLabel>
            <!-- Validator -->
            <div v-if="validator || $slots.validator" class="mt-1">
                <slot name="validator">
                    <Validator v-if="validator"
                        :state="validator === true ? 'success' : validator === 'error' ? 'error' : validator"
                        :message="errorMessage" />
                </slot>
            </div>
            <!-- Help -->
            <div v-if="help || $slots.help" class="mt-1 text-xs text-base-400">
                <slot name="help">{{ help }}</slot>
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
