<script setup>
/**
 * Toggle Atom (DaisyUI)
 *
 * @description
 * Composant atomique Toggle conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage toggle (switch, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Props communes input via getInputProps()
 * - Props bgOn, bgOff : classes Tailwind complètes pour le fond ON/OFF (pas de classes dynamiques)
 * - Slots : #labelTop, #labelBottom, #validator, #helper, #iconOn, #iconOff, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur la prop checked.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/toggle/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
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
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot iconOn - Icône custom pour l'état activé
 * @slot iconOff - Icône custom pour l'état désactivé
 * @slot default - Slot pour contenu custom à droite du toggle
 *
 * @note La valeur du toggle est contrôlée par modelValue (v-model) si défini, sinon par checked.
 */
import { computed, ref, watch, onMounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputAttrs, getInputProps, hasValidation } from '@/Utils/atomic-design/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps({ exclude: ['type', 'placeholder', 'autocomplete', 'min', 'max', 'step', 'inputmode', 'pattern', 'maxlength', 'minlength'] }),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    checked: { type: Boolean, default: false },
    labelBottom: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const toggleRef = ref(null);

// Détermine si le composant doit afficher un état de validation
const hasValidationState = computed(() => hasValidation(props, useSlots()));

const isChecked = computed({
    get() {
        return props.modelValue !== undefined ? props.modelValue : props.checked;
    },
    set(val) {
        emit('update:modelValue', val);
    }
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'toggle',
            props.color === 'neutral' && 'toggle-neutral',
            props.color === 'primary' && 'toggle-primary',
            props.color === 'secondary' && 'toggle-secondary',
            props.color === 'accent' && 'toggle-accent',
            props.color === 'info' && 'toggle-info',
            props.color === 'success' && 'toggle-success',
            props.color === 'warning' && 'toggle-warning',
            props.color === 'error' && 'toggle-error',
            props.size === 'xs' && 'toggle-xs',
            props.size === 'sm' && 'toggle-sm',
            props.size === 'md' && 'toggle-md',
            props.size === 'lg' && 'toggle-lg',
            props.size === 'xl' && 'toggle-xl',
            hasValidationState.value && 'toggle-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const toggleId = computed(() => props.id || `toggle-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => getCommonAttrs(props));

function onInput(e) {
    isChecked.value = e.target.checked;
}

onMounted(() => {
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
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel v-if="props.label || $slots.labelTop" :for="toggleId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <label :class="[isChecked ? bgOn : bgOff, 'toggle', atomClasses]" :for="toggleId">
                <input ref="toggleRef" type="checkbox" v-bind="attrs" v-on="$attrs" :id="toggleId"
                    :checked="isChecked" @input="onInput" :aria-invalid="hasValidationState"
                    class="hidden" />
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
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="toggleId" :value="props.labelBottom" class="mt-1">
            <template v-if="$slots.labelBottom" #default>
                <slot name="labelBottom" />
            </template>
        </InputLabel>
        <!-- Validator -->
        <div v-if="hasValidationState" class="mt-1">
            <slot name="validator">
                <Validator v-if="props.validator"
                    :state="typeof props.validator === 'string' ? 'error' : 'error'"
                    :message="typeof props.validator === 'string' ? props.validator : props.errorMessage" />
            </slot>
        </div>
        <!-- Helper -->
        <div v-if="props.helper || $slots.helper" class="mt-1 text-xs text-base-400">
            <slot name="helper">{{ props.helper }}</slot>
        </div>
    </div>
</template>

<style scoped></style>
