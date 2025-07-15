<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * InputCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs input, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : type, v-model, placeholder, disabled, readonly, color, size, style, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, etc.
 * - Édition réactive via useEditableField (optionnel)
 * - Utilise getInputClasses pour les classes DaisyUI/Tailwind
 * - Slot par défaut : input natif
 * - Gère tous les types d'input : text, email, password, number, url, tel, search, date, etc.
 * - Toggle password automatique (œil) pour type="password" si le navigateur ne l'a pas déjà
 * - Utilise le système de labels inline de DaisyUI pour éviter les divs englobantes
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputCore type="text" v-model="name" placeholder="Nom" />
 * <InputCore type="password" v-model="password" placeholder="Mot de passe" />
 * <InputCore type="password" v-model="password" :showPasswordToggle="false" />
 * <InputCore type="email" v-model="email" color="primary" size="lg" />
 *
 * @props {String} type - Type d'input (text, email, password, number, url, tel, search, date, etc.)
 * @props {String} modelValue - v-model
 * @props {String} placeholder
 * @props {Boolean} disabled, readonly
 * @props {String} color, size, style
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean} useFieldComposable - active l'édition réactive
 * @props {Object} field - objet field externe (optionnel)
 * @props {Number} debounceTime - délai debounce (ms)
 * @props {Boolean} showPasswordToggle - affiche le toggle œil pour les passwords (défaut true)
 * @props {String} labelLeft - Label inline à gauche (dans la balise label)
 * @props {String} labelRight - Label inline à droite (dans la balise label)
 * @slot labelLeft - Slot pour label inline à gauche
 * @slot labelRight - Slot pour label inline à droite
 * @slot default - input natif (optionnel)
 */
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { getCommonProps, getCommonAttrs, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputAttrs, getInputProps } from '@/Utils/atomic-design/atomManager';
import { getInputClasses } from '@/Composables/form/useInputStyle';
import useEditableField from '@/Composables/form/useEditableField';
import usePasswordToggle from '@/Composables/form/usePasswordToggle';
import { styleInputList, variantInputList } from '@/Pages/Atoms/atomMap';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps(),
    color: { type: String, default: '' },
    size: { type: String, default: '' },
    style: { 
        type: [String, Object], 
        default: 'classic', 
        validator: (v) => {
            if (typeof v === 'string') {
                return styleInputList.includes(v);
            }
            return true; // Accepte les objets pour compatibilité
        }
    },
    variant: { type: String, default: '', validator: (v) => variantInputList.includes(v) },
    type: { type: String, default: 'text' },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    showPasswordToggle: { type: Boolean, default: true },
    labelRight: { type: String, default: '' },
    labelLeft: { type: String, default: '' },
    // Override ariaLabel pour s'assurer qu'elle soit une string
    ariaLabel: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const inputRef = ref(null);

// Utilise le composable pour la gestion du toggle password
const passwordToggle = usePasswordToggle({
    type: computed(() => props.type),
    showToggle: computed(() => props.showPasswordToggle)
});

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

const isFieldModified = computed(() =>
    props.useFieldComposable && editableField.value
        ? editableField.value.isModified.value
        : false,
);

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
    if (
        props.useFieldComposable &&
        editableField.value &&
        typeof editableField.value.reset === 'function'
    ) {
        editableField.value.reset();
        editableField.value.onBlur();
    }
}

onMounted(() => {
    if (inputRef.value && props.autofocus) {
        inputRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ 
    focus: () => inputRef.value && inputRef.value.focus(),
    isFieldModified,
    handleReset,
});

const atomClasses = computed(() => {
    // Extrait la valeur de style (string ou objet)
    const styleValue = typeof props.style === 'string' ? props.style : 'classic';
    
    return getInputClasses({
        style: styleValue,
        color: props.color,
        size: props.size,
        variant: props.variant,
        error: false,
    });
});

const attrs = computed(() => {
    const finalType = props.type === 'password' ? passwordToggle.effectiveType.value : props.type;
    return {
        ...getCommonAttrs(props),
        ...getInputAttrs(props),
        type: finalType,
        'aria-label': typeof props.ariaLabel === 'string' ? props.ariaLabel : undefined,
    };
});
</script>

<template>
    <label :class="atomClasses" v-if="labelRight || $slots.labelRight || labelLeft || $slots.labelLeft || passwordToggle.shouldShowToggle || (props.useFieldComposable && isFieldModified)">
        <span v-if="labelLeft || $slots.labelLeft" class="label-text">
            <slot name="labelLeft">{{ labelLeft }}</slot>
        </span>
        <input
            ref="inputRef"
            v-bind="attrs"
            v-on="$attrs"
            :value="displayValue"
            @input="onInput"
            @blur="onBlur"
        />
        <span v-if="labelRight || $slots.labelRight || passwordToggle.shouldShowToggle || (props.useFieldComposable && isFieldModified)" class="label-text">
            <slot name="labelRight">{{ labelRight }}</slot>
            <Btn
                v-if="passwordToggle.shouldShowToggle" 
                variant="link"
                circle
                size="xs"
                class="text-base-content/60 hover:text-base-content/80 transition-colors"
                @click.stop="passwordToggle.togglePassword"
                :aria-label="typeof passwordToggle.ariaLabel === 'string' ? passwordToggle.ariaLabel : ''"
            >
                <i 
                    :class="passwordToggle.iconClass"
                    class="text-sm"
                ></i>
            </Btn>
            <Btn
                v-if="props.useFieldComposable && isFieldModified"
                variant="link"
                circle
                size="xs"
                @click.stop="handleReset"
                :aria-label="'Réinitialiser'"
            >
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </Btn>
        </span>
    </label>
    <input
        v-else
        ref="inputRef"
        v-bind="attrs"
        v-on="$attrs"
        :class="atomClasses"
        :value="displayValue"
        @input="onInput"
        @blur="onBlur"
    />
</template>

<style scoped></style> 