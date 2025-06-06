<script setup>
/**
 * Toggle Atom (DaisyUI)
 *
 * @description
 * Composant atomique Toggle conforme DaisyUI et Atomic Design.
 * - Gère tous les cas d'usage toggle (switch, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Props communes input via getInputProps()
 * - Props bgOn, bgOff : classes Tailwind complètes pour le fond ON/OFF (pas de classes dynamiques)
 * - Slots : #labelTop, #labelBottom, #validator, #help, #iconOn, #iconOff, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur la prop checked.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Toggle v-model="enabled" label="Activer la fonctionnalité" color="primary" size="lg" bgOn="bg-green-500" bgOff="bg-gray-300">
 *   <template #iconOn><i class="fa-solid fa-check"></i></template>
 *   <template #iconOff><i class="fa-solid fa-xmark"></i></template>
 * </Toggle>
 *
 * @props {Boolean} modelValue - Valeur du toggle (v-model natif, prioritaire sur checked)
 * @props {Boolean} checked - Valeur fallback si modelValue n'est pas utilisé
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {Boolean} indeterminate - Etat indéterminé (optionnel)
 * @props {String} bgOn - Classes Tailwind pour le fond quand activé (ex: 'bg-green-500')
 * @props {String} bgOff - Classes Tailwind pour le fond quand désactivé (ex: 'bg-gray-300')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot iconOn - Icône custom pour l'état activé
 * @slot iconOff - Icône custom pour l'état désactivé
 * @slot default - Slot pour contenu custom à droite du toggle
 *
 * @note La valeur du toggle est contrôlée par modelValue (v-model) si défini, sinon par checked.
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
    bgOn: { type: String, default: '' },
    bgOff: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const toggleRef = ref(null);

const atomClasses = computed(() => {
    const classes = ['toggle'];
    // Couleur DaisyUI
    if (props.color === 'neutral') classes.push('toggle-neutral');
    if (props.color === 'primary') classes.push('toggle-primary');
    if (props.color === 'secondary') classes.push('toggle-secondary');
    if (props.color === 'accent') classes.push('toggle-accent');
    if (props.color === 'info') classes.push('toggle-info');
    if (props.color === 'success') classes.push('toggle-success');
    if (props.color === 'warning') classes.push('toggle-warning');
    if (props.color === 'error') classes.push('toggle-error');
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('toggle-xs');
    if (props.size === 'sm') classes.push('toggle-sm');
    if (props.size === 'md') classes.push('toggle-md');
    if (props.size === 'lg') classes.push('toggle-lg');
    if (props.size === 'xl') classes.push('toggle-xl');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    // Erreur
    if (props.errorMessage || props.validator) classes.push('validator toggle-error');
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

const toggleId = computed(() => props.id || `toggle-${Math.random().toString(36).substr(2, 9)}`);

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
    if (toggleRef.value && props.indeterminate) {
        toggleRef.value.indeterminate = true;
    }
    if (toggleRef.value && props.autofocus) {
        toggleRef.value.focus();
    }
});

watch(() => props.indeterminate, (val) => {
    if (toggleRef.value) {
        toggleRef.value.indeterminate = val;
    }
});

defineExpose({ focus: () => toggleRef.value && toggleRef.value.focus() });
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="toggleId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="flex items-center gap-2">
                <label :class="[isChecked ? bgOn : bgOff, 'toggle', atomClasses]" :for="toggleId">
                    <input ref="toggleRef" type="checkbox" v-bind="attrs" :id="toggleId" :checked="isChecked"
                        @input="onInput" :aria-invalid="!!errorMessage || validator" class="hidden" />
                    <span class="flex items-center justify-center w-full h-full">
                        <template v-if="isChecked">
                            <slot name="iconOn" />
                        </template>
                        <template v-else>
                            <slot name="iconOff" />
                        </template>
                    </span>
                </label>
                <slot />
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="toggleId" :value="labelBottom" class="mt-1">
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
