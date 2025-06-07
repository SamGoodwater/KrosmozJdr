<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Radio Atom (DaisyUI)
 *
 * @description
 * Composant atomique Radio conforme DaisyUI (v5.x) et Atomic Design.
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
 * @see https://daisyui.com/components/radio/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
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
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';

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

const atomClasses = computed(() =>
    mergeClasses(
        [
            'radio',
            props.color === 'neutral' && 'radio-neutral',
            props.color === 'primary' && 'radio-primary',
            props.color === 'secondary' && 'radio-secondary',
            props.color === 'accent' && 'radio-accent',
            props.color === 'info' && 'radio-info',
            props.color === 'success' && 'radio-success',
            props.color === 'warning' && 'radio-warning',
            props.color === 'error' && 'radio-error',
            props.size === 'xs' && 'radio-xs',
            props.size === 'sm' && 'radio-sm',
            props.size === 'md' && 'radio-md',
            props.size === 'lg' && 'radio-lg',
            props.size === 'xl' && 'radio-xl',
            (props.errorMessage || props.validator) && 'validator radio-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    'aria-checked': isChecked.value,
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
                <input ref="radioRef" type="radio" v-bind="attrs" v-on="$attrs" :id="radioId"
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
