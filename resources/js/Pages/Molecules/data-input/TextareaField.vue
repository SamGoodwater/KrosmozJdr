<script setup>
/**
 * TextareaField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ textarea complet, orchestrant TextareaCore (atom), InputLabel, validation, actions contextuelles, etc.
 * - API simplifiée : prop `label` (string ou objet), validation, helper, actions
 * - 6 positions de labels : top, bottom, start, end, inStart, inEnd (top par défaut)
 * - Slots pour chaque position de label et pour overStart/overEnd
 * - Actions contextuelles dynamiques (reset, clear, copy, etc.) via useInputActions
 * - Validation intégrée (locale + notification)
 * - Prêt pour DaisyUI v5.x, Vue 3, Laravel + Inertia
 *
 * @example
 * <TextareaField label="Bio" v-model="bio" :actions="['reset', 'clear', 'copy']" />
 * <TextareaField :label="{ top: 'Bio', inStart: '✍️' }" v-model="bio" />
 *
 * @props {String|Object} label - Label simple (string) ou objet avec positions (top, bottom, start, end, inStart, inEnd)
 * @props {Object|String|Boolean} validation - Configuration de validation
 * @props {String} helper
 * @props {Array|Object|String} actions - Actions contextuelles à activer
 * @props {Boolean} disabled, readonly, required, etc.
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd
 * @slot overStart, overEnd, helper, validator
 */
import { computed, ref, useSlots, inject, watch } from 'vue';
import TextareaCore from '@/Pages/Atoms/data-input/TextareaCore.vue';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import useInputActions from '@/Composables/form/useInputActions';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';
import { validateLabel, processLabelConfig } from '@/Utils/atomic-design/labelManager';
import { processValidation, validateValidationObject } from '@/Utils/atomic-design/validationManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('textarea', 'field'),
});

const slots = useSlots();
const notificationStore = inject('notificationStore', null);
const labelConfig = computed(() => processLabelConfig(props.label, props.defaultLabelPosition));

// --- Utilisation du composable universel pour les actions contextuelles ---
const {
  currentValue,
  actionsToDisplay,
  inputProps,
  focus,
  isModified,
  isReadonly,
  reset,
  back,
  clear,
  copy,
  toggleEdit,
  inputRef,
} = useInputActions({
  modelValue: props.modelValue,
  type: 'textarea',
  actions: props.actions,
  readonly: props.readonly,
  debounce: 500,
  autofocus: props.autofocus,
});

// --- v-model : émettre update:modelValue quand la valeur change ---
const emit = defineEmits(['update:modelValue']);
watch(currentValue, (val) => {
  emit('update:modelValue', val);
});

// --- Validation ---
const notificationStoreInjected = notificationStore;
const processedValidation = computed(() => {
    const validationConfig = props.validation || props.validator;
    if (!validationConfig) {
        return null;
    }
    return processValidation(validationConfig, notificationStoreInjected);
});
const hasValidationState = computed(() => {
    return processedValidation.value !== null || 
           props.errorMessage || 
           slots.validator;
});
const coreProps = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    modelValue: currentValue.value,
    disabled: props.disabled,
    readonly: props.readonly,
    required: props.required,
    ref: inputRef,
    labelInStart: labelConfig.value.inStart || '',
    labelInEnd: labelConfig.value.inEnd || '',
}));
const inputId = computed(
    () => props.id || `textareafield-${Math.random().toString(36).substr(2, 9)}`,
);
const containerClasses = computed(() => 
    mergeClasses(
        'form-control w-full',
        getCustomUtilityClasses(props)
    )
);
function getValidatorState() {
    if (!processedValidation.value) return '';
    return processedValidation.value.state;
}
function getValidatorMessage() {
    if (props.errorMessage) return props.errorMessage;
    if (!processedValidation.value) return '';
    return processedValidation.value.message;
}
</script>

<template>
    <div :class="containerClasses">
        <!-- Label top -->
        <InputLabel
            v-if="labelConfig.top || slots.labelTop"
            :value="labelConfig.top"
            :for="inputId"
            :color="props.color"
            :size="props.size"
        >
            <slot name="labelTop" />
        </InputLabel>
        
        <div class="relative flex items-center w-full">
            <!-- Label start -->
            <InputLabel
                v-if="labelConfig.start || slots.labelStart"
                :value="labelConfig.start"
                :for="inputId"
                :color="props.color"
                :size="props.size"
                class="mr-2"
            >
                <slot name="labelStart" />
            </InputLabel>
            
            <!-- Container relatif pour le textarea et les éléments over -->
            <div class="relative flex-1">
                <!-- Textarea principal -->
                <TextareaCore 
                    v-bind="coreProps" 
                    v-on="$attrs"
                >
                    <template v-if="slots.labelInStart" #labelInStart>
                        <slot name="labelInStart" />
                    </template>
                    <template v-if="slots.labelInEnd" #labelInEnd>
                        <slot name="labelInEnd" />
                    </template>
                    <slot />
                </TextareaCore>

                <!-- Slot overStart (positionné en absolute à gauche) -->
                <div v-if="slots.overStart" class="absolute left-2 top-1/2 transform -translate-y-1/2 z-10 flex gap-1">
                    <slot name="overStart" />
                </div>
                <!-- Slot overEnd (positionné en absolute à droite) + actions contextuelles -->
                <div v-if="slots.overEnd || actionsToDisplay.length" class="absolute right-2 top-1/2 transform -translate-y-1/2 z-10 flex gap-1">
                    <slot name="overEnd" />
                    <Btn
                        v-for="action in actionsToDisplay"
                        :key="action.key"
                        variant="link"
                        circle
                        size="xs"
                        :aria-label="action.ariaLabel"
                        :title="action.tooltip"
                        :disabled="action.disabled"
                        @click.stop="action.onClick"
                    >
                        <i :class="action.icon" class="text-sm"></i>
                    </Btn>
                </div>
            </div>

            <!-- Label end -->
            <InputLabel
                v-if="labelConfig.end || slots.labelEnd"
                :value="labelConfig.end"
                :for="inputId"
                :color="props.color"
                :size="props.size"
                class="ml-2"
            >
                <slot name="labelEnd" />
            </InputLabel>
        </div>
        
        <!-- Label bottom -->
        <InputLabel
            v-if="labelConfig.bottom || slots.labelBottom"
            :value="labelConfig.bottom"
            :for="inputId"
            :color="props.color"
            :size="props.size"
            class="mt-1"
        >
            <slot name="labelBottom" />
        </InputLabel>
        
        <!-- Validator -->
        <div v-if="hasValidationState" class="mt-1">
            <slot name="validator">
                <Validator
                    v-if="processedValidation"
                    :state="getValidatorState()"
                    :message="getValidatorMessage()"
                />
            </slot>
        </div>
        
        <!-- Helper -->
        <div v-if="helper || slots.helper" class="mt-1 text-xs text-base-400">
            <slot name="helper">{{ helper }}</slot>
        </div>
    </div>
</template>

<style scoped></style> 