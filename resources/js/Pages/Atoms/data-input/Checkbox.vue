<script setup>
/**
 * Checkbox Atom (DaisyUI)
 *
 * @description
 * Composant atomique Checkbox conforme DaisyUI et Atomic Design.
 * - Gère tous les cas d'usage checkbox (simple, indéterminé, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #labelTop, #labelBottom, #validator, #help, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur la prop checked.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Checkbox label="Se souvenir de moi" v-model="checked" color="primary" size="md" :validator="form.errors.remember" help="Cochez pour rester connecté" />
 *
 * @props {Boolean} modelValue - Valeur de la checkbox (v-model natif, prioritaire sur checked)
 * @props {Boolean} checked - Valeur fallback si modelValue n'est pas utilisé
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {Boolean} indeterminate - Etat indéterminé (optionnel)
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} bgOn - Classes Tailwind appliquées quand la checkbox est cochée (ex: 'bg-green-500 border-green-600')
 * @props {String} bgOff - Classes Tailwind appliquées quand la checkbox est décochée (ex: 'bg-base-200 border-base-300')
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite de la checkbox
 *
 * @note La valeur de la checkbox est contrôlée par modelValue (v-model) si défini, sinon par checked.
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
    checked: { type: Boolean, default: false },
    indeterminate: { type: Boolean, default: false },
    /**
     * Classes Tailwind appliquées selon l'état (coché/décoché)
     * bgOn: classes si coché, bgOff: classes si décoché
     */
    bgOn: { type: String, default: '' },
    bgOff: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const checkboxRef = ref(null);

const atomClasses = computed(() => {
    const classes = ['checkbox'];
    // Couleur DaisyUI
    if (props.color === 'neutral') classes.push('checkbox-neutral');
    if (props.color === 'primary') classes.push('checkbox-primary');
    if (props.color === 'secondary') classes.push('checkbox-secondary');
    if (props.color === 'accent') classes.push('checkbox-accent');
    if (props.color === 'info') classes.push('checkbox-info');
    if (props.color === 'success') classes.push('checkbox-success');
    if (props.color === 'warning') classes.push('checkbox-warning');
    if (props.color === 'error') classes.push('checkbox-error');
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('checkbox-xs');
    if (props.size === 'sm') classes.push('checkbox-sm');
    if (props.size === 'md') classes.push('checkbox-md');
    if (props.size === 'lg') classes.push('checkbox-lg');
    if (props.size === 'xl') classes.push('checkbox-xl');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    // Erreur
    if (props.errorMessage || props.validator) classes.push('validator checkbox-error');
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
}));

const checkboxId = computed(() => props.id || `checkbox-${Math.random().toString(36).substr(2, 9)}`);

const isChecked = computed({
    get() {
        return props.modelValue !== undefined ? props.modelValue : props.checked;
    },
    set(val) {
        emit('update:modelValue', val);
    }
});

function onInput(e) {
    isChecked.value = e.target.checked;
}

onMounted(() => {
    if (checkboxRef.value && props.indeterminate) {
        checkboxRef.value.indeterminate = true;
    }
    if (checkboxRef.value && props.autofocus) {
        checkboxRef.value.focus();
    }
});

watch(() => props.indeterminate, (val) => {
    if (checkboxRef.value) {
        checkboxRef.value.indeterminate = val;
    }
});

defineExpose({ focus: () => checkboxRef.value && checkboxRef.value.focus() });
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="checkboxId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="flex items-center gap-2">
                <input ref="checkboxRef" type="checkbox" v-bind="attrs" :id="checkboxId"
                    :class="[atomClasses, isChecked ? bgOn : bgOff]" :checked="isChecked" @input="onInput"
                    :aria-invalid="!!errorMessage || validator" />
                <slot />
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="checkboxId" :value="labelBottom" class="mt-1">
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
