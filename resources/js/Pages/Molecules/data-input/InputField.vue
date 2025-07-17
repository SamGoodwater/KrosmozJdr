<script setup>
/**
 * InputField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ de saisie complet, orchestrant InputCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, toggle password, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <InputField label="Nom" v-model="name" />
 * 
 * // Label simple avec position par défaut différente
 * <InputField label="Nom" v-model="name" defaultLabelPosition="top" />
 * 
 * // Label avec positions spécifiques
 * <InputField :label="{ top: 'Nom complet', inStart: 'M.' }" v-model="name" />
 * 
 * // Label complexe avec slots
 * <InputField :label="{ top: 'Email' }" v-model="email">
 *   <template #labelTop>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-envelope"></i>
 *       Email professionnel
 *     </span>
 *   </template>
 * </InputField>
 * 
 * // Avec actions automatiques (toggle password dans overStart, reset dans overEnd si useFieldComposable)
 * <InputField label="Mot de passe" v-model="password" type="password" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <InputField label="Recherche" v-model="search">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-search"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="clearSearch">
 *       <i class="fa-solid fa-times"></i>
 *     </Btn>
 *   </template>
 * </InputField>
 *
 * // Validation locale uniquement
 * <InputField 
 *   label="Email" 
 *   v-model="email"
 *   :validation="{ state: 'error', message: 'Email invalide' }"
 * />
 *
 * // Validation avec notification
 * <InputField 
 *   label="Email" 
 *   v-model="email"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Email valide !',
 *     showNotification: true 
 *   }"
 * />
 *
 * @props {String|Object} label - Label simple (string) ou objet avec positions
 * @props {String} defaultLabelPosition - Position par défaut pour les strings ('floating', 'top', 'bottom', 'start', 'end', 'inStart', 'inEnd')
 * @props {Object|String|Boolean} validation - Configuration de validation (nouvelle API)
 * @props {String} helper, errorMessage
 * @props {String} color, size, style, variant
 * @props {Boolean} useFieldComposable, showPasswordToggle
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (toggle, reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */
import { computed, ref, useSlots, inject, watch } from 'vue';
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import useInputActions from '@/Composables/form/useInputActions';
import { styleInputList, variantInputList } from '@/Pages/Atoms/atomMap';
import { 
    getCommonProps, 
    getCommonAttrs, 
    getCustomUtilityProps, 
    getCustomUtilityClasses,
    mergeClasses 
} from '@/Utils/atomic-design/uiHelper';
import { 
    getInputProps, 
    getInputAttrs, 
    hasValidation
} from '@/Utils/atomic-design/inputHelper';
import { 
    validateLabel,
    processLabelConfig 
} from '@/Utils/atomic-design/labelManager';
import { 
    createValidation,
    validateValidationObject,
    processValidation
} from '@/Utils/atomic-design/validationManager';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('input', 'field'),
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
  togglePassword,
  copy,
  toggleEdit,
  showPassword,
  inputRef,
} = useInputActions({
  modelValue: props.modelValue,
  type: props.type,
  actions: props.actions,
  readonly: props.readonly,
  debounce: props.debounceTime,
  autofocus: props.autofocus,
});

// --- v-model : émettre update:modelValue quand la valeur change ---
const emit = defineEmits(['update:modelValue']);
watch(currentValue, (val) => {
  emit('update:modelValue', val);
});

// --- Validation et autres logiques existantes (inchangées) ---
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
    type: inputProps.value.type,
    color: props.color,
    size: props.size,
    style: props.style,
    variant: props.variant,
    modelValue: currentValue.value,
    readonly: inputProps.value.readonly,
    autofocus: inputProps.value.autofocus,
    // Props pour labels inline
    labelFloating: !!labelConfig.value.floating,
    labelStart: labelConfig.value.inStart || '',
    labelEnd: labelConfig.value.inEnd || '',
    'aria-invalid': hasValidationState.value && processedValidation.value?.state === 'error',
    ref: inputRef,
}));
const inputId = computed(
    () => props.id || `inputfield-${Math.random().toString(36).substr(2, 9)}`,
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
            
            <!-- Container relatif pour l'input et les éléments over -->
            <div class="relative flex-1">
                <!-- Input principal -->
                <InputCore 
                    v-bind="coreProps" 
                    v-on="$attrs"
                >
                    <template v-if="slots.labelInStart" #labelStart>
                        <slot name="labelInStart" />
                    </template>
                    <template v-if="slots.labelInEnd" #labelEnd>
                        <slot name="labelInEnd" />
                    </template>
                    <template v-if="labelConfig.floating && (labelConfig.floating || slots.labelFloating)" #floatingLabel>
                        <slot name="labelFloating">{{ labelConfig.floating }}</slot>
                    </template>
                </InputCore>

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