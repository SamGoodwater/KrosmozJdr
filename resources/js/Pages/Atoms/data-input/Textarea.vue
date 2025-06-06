<script setup>
/**
 * Textarea Atom (DaisyUI + Custom Utility + Edition réactive)
 *
 * @description
 * Composant atomique Textarea conforme DaisyUI et Atomic Design.
 * - Gère tous les cas d'usage textarea (multiligne, aide, validation, etc.)
 * - Props DaisyUI : color, size, variant
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #labelTop, #labelBottom, #validator, #help, default
 * - v-model natif
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
 *
 * @example
 * <Textarea label="Bio" v-model="bio" color="primary" size="md" :validator="form.errors.bio" help="Quelques lignes sur vous" useFieldComposable :debounceTime="300" />
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost', 'bordered')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @props {Number|String} rows - Nombre de lignes (optionnel, défaut 3)
 * @props {Number|String} cols - Nombre de colonnes (optionnel)
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Contenu custom dans le textarea
 *
 * @note Pas de floating-label ni de label left/right sur textarea.
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, onMounted, onUnmounted, defineExpose } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getInputProps, getInputAttrs, getCustomUtilityProps, getCustomUtilityClasses } from '@/Utils/atom/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import useEditableField from '@/Composables/form/useEditableField';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps(),
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
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'ghost', 'bordered'].includes(v),
    },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    rows: { type: [Number, String], default: 5 },
    cols: { type: [Number, String], default: '' },
});

const emit = defineEmits(['update:modelValue']);
const textareaRef = ref(null);

// Gestion editableField (optionnel)
const editableField = computed(() => {
    if (props.useFieldComposable) {
        return useEditableField(props.modelValue, {
            field: props.field,
            debounce: props.debounceTime,
            onUpdate: (val) => emit('update:modelValue', val),
        });
    }
    return null;
});

const isFieldModified = computed(() => props.useFieldComposable && editableField.value ? editableField.value.isModified.value : false);
const displayValue = computed(() => {
    if (props.useFieldComposable && editableField.value) {
        return editableField.value.value.value;
    }
    return props.modelValue;
});

function onInput(e) {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onInput(e);
    } else {
        emit('update:modelValue', e.target.value);
    }
}
function onBlur() {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onBlur();
    }
}
function handleReset() {
    if (props.useFieldComposable && editableField.value && typeof editableField.value.reset === 'function') {
        editableField.value.reset();
        editableField.value.onBlur();
    }
}

onMounted(() => {
    if (textareaRef.value && props.autofocus) {
        textareaRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ focus: () => textareaRef.value && textareaRef.value.focus() });

function getAtomClasses(props) {
    const classes = ['textarea'];
    // Couleur DaisyUI
    if (props.color === 'neutral') classes.push('textarea-neutral');
    if (props.color === 'primary') classes.push('textarea-primary');
    if (props.color === 'secondary') classes.push('textarea-secondary');
    if (props.color === 'accent') classes.push('textarea-accent');
    if (props.color === 'info') classes.push('textarea-info');
    if (props.color === 'success') classes.push('textarea-success');
    if (props.color === 'warning') classes.push('textarea-warning');
    if (props.color === 'error') classes.push('textarea-error');
    // Taille DaisyUI
    if (props.size === 'xs') classes.push('textarea-xs');
    if (props.size === 'sm') classes.push('textarea-sm');
    if (props.size === 'md') classes.push('textarea-md');
    if (props.size === 'lg') classes.push('textarea-lg');
    if (props.size === 'xl') classes.push('textarea-xl');
    // Variant DaisyUI
    if (props.variant === 'ghost') classes.push('textarea-ghost');
    if (props.variant === 'bordered') classes.push('textarea-bordered');
    // Utilitaires custom
    classes.push(...getCustomUtilityClasses(props));
    // Erreur
    if (props.errorMessage || props.validator) classes.push('validator textarea-error');
    return classes.join(' ');
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
}));

const textareaId = computed(() => props.id || `textarea-${Math.random().toString(36).substr(2, 9)}`);
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="textareaId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="relative w-full">
                <textarea ref="textareaRef" v-bind="attrs" :id="textareaId" :class="atomClasses" :value="displayValue"
                    @input="onInput" @blur="onBlur" :aria-invalid="!!errorMessage || validator === 'error'" :rows="rows"
                    :cols="cols" />
                <!-- Bouton reset -->
                <Btn v-if="props.useFieldComposable && isFieldModified" class="absolute right-2 top-2 z-20" size="xs"
                    variant="ghost" circle @click="handleReset" :aria-label="'Réinitialiser'">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </Btn>
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="textareaId" :value="labelBottom" class="mt-1">
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
