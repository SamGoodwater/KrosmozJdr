<script setup>
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
 * - Slots : #labelTop, #labelBottom, #validator, #helper, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur checked.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
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
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} bgOn - Classes Tailwind appliquées quand le radio est coché (ex: 'bg-blue-200 border-blue-600')
 * @props {String} bgOff - Classes Tailwind appliquées quand le radio est décoché (ex: 'bg-base-200 border-base-300')
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du radio
 *
 * @note La valeur du radio est contrôlée par modelValue (v-model) si défini, sinon par checked.
 */
import { computed, ref, watch, onMounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from "@/Utils/atomic-design/uiHelper";
import InputLabel from "@/Pages/Atoms/data-input/InputLabel.vue";
import { getInputProps, getInputAttrs } from "@/Utils/atomic-design/inputHelper";

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps('radio', 'core'),
    ...getCustomUtilityProps(),
});

const emit = defineEmits(["update:modelValue"]);
const radioRef = ref(null);

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
            "radio",
            props.color === "neutral" && "radio-neutral",
            props.color === "primary" && "radio-primary",
            props.color === "secondary" && "radio-secondary",
            props.color === "accent" && "radio-accent",
            props.color === "info" && "radio-info",
            props.color === "success" && "radio-success",
            props.color === "warning" && "radio-warning",
            props.color === "error" && "radio-error",
            props.size === "xs" && "radio-xs",
            props.size === "sm" && "radio-sm",
            props.size === "md" && "radio-md",
            props.size === "lg" && "radio-lg",
            props.size === "xl" && "radio-xl",
            hasValidationState.value && "radio-error",
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class,
    ),
);

const radioId = computed(
    () => props.id || `radio-${Math.random().toString(36).substr(2, 9)}`,
);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs('radio', 'core'),
}));

function onInput(e) {
    isChecked.value = e.target.checked;
}

onMounted(() => {
    if (radioRef.value && props.autofocus) {
        radioRef.value.focus();
    }
});
defineExpose({ focus: () => radioRef.value && radioRef.value.focus() });
</script>

<template>
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel
            v-if="props.label || $slots.labelTop"
            :for="radioId"
            :value="props.label"
        >
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <input
                ref="radioRef"
                type="radio"
                v-bind="attrs"
                v-on="$attrs"
                :id="radioId"
                :class="[atomClasses, isChecked ? bgOn : bgOff]"
                :checked="isChecked"
                @input="onInput"
                :aria-invalid="hasValidationState"
            />
            <slot />
        </div>
        <!-- Label bottom -->
        <InputLabel
            v-if="props.labelBottom || $slots.labelBottom"
            :for="radioId"
            :value="props.labelBottom"
            class="mt-1"
        >
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
