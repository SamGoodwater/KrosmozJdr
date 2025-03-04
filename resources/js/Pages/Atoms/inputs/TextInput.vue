<script setup>
import { ref, onMounted, defineExpose, computed, onUnmounted } from "vue";
import { useAttrs } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    modelValue: {
        type: [String, Number, Object],
        default: "",
    },
    theme: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    placeholder: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
    labelInside: {
        type: Boolean,
        default: false,
    },
    useFieldComposable: {
        type: Boolean,
        default: false,
    },
    field: {
        type: Object,
        default: null,
    },
    debounceTime: {
        type: Number,
        default: 500,
    },
});

const emit = defineEmits(["update:modelValue"]);
const input = ref(null);
const attrs = useAttrs();
const debounceTimeout = ref(null);

const buildInputClasses = (themeProps, props) => {
    const classes = ["input", "w-full"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);
    classes.push(`border-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`input-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("input-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildInputClasses(themeProps.value, props));

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.modelValue;
});

const updateFieldValue = (newValue) => {
    if (props.useFieldComposable && props.field) {
        props.field.value.value = newValue;
    }
};

const sendUpdate = (newValue) => {
    if (props.useFieldComposable && props.field) {
        if (typeof props.field.update === 'function') {
            props.field.update(newValue);
        }
    } else {
        emit("update:modelValue", newValue);
    }
};

const debouncedUpdate = (newValue) => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }

    updateFieldValue(newValue);

    debounceTimeout.value = setTimeout(() => {
        sendUpdate(newValue);
    }, props.debounceTime);
};

const updateValue = (event) => {
    const newValue = event.target.value;
    debouncedUpdate(newValue);
};

const handleBlur = () => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
        debounceTimeout.value = null;
    }

    if (props.useFieldComposable && props.field) {
        sendUpdate(props.field.value.value);
    }
};

// Ajout d'un gestionnaire pour l'autocomplétion
const handleAutocomplete = (event) => {
    const newValue = event.target.value;
    updateFieldValue(newValue);
    sendUpdate(newValue);
};

// Ajout d'un computed pour vérifier si le champ est modifié
const isFieldModified = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.isModified.value;
    }
    return false;
});

const handleReset = () => {
    if (props.useFieldComposable && props.field) {
        props.field.reset();
        sendUpdate(props.field.value.value);
    }
};

onMounted(() => {
    if (input.value && themeProps.value.autofocus) {
        input.value.focus();
    }

    // Ajouter un écouteur pour l'événement animationstart pour détecter l'autocomplétion
    if (input.value) {
        input.value.addEventListener('animationstart', (e) => {
            if (e.animationName === 'onAutoFillStart') {
                const newValue = input.value.value;
                updateFieldValue(newValue);
                sendUpdate(newValue);
            }
        });
    }
});

// Nettoyer le timeout lors du démontage du composant
onUnmounted(() => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }

    if (input.value) {
        input.value.removeEventListener('animationstart', () => {});
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<style scoped>
@keyframes onAutoFillStart {
    from {/**/}
    to {/**/}
}

input:-webkit-autofill {
    animation-name: onAutoFillStart;
    animation-duration: 1ms;
}
</style>

<template>
    <div class="relative">
        <label
            v-if="labelInside"
            :class="`input border-${themeProps.color || 'primary-500'} text-${themeProps.color || 'primary-500'} input-bordered flex items-center gap-2`"
        >
            <slot v-if="labelInside" name="before" />
            <input
                v-bind="attrs"
                :required="themeProps.required"
                :autofocus="themeProps.autofocus"
                :value="displayValue"
                @input="updateValue"
                @change="handleAutocomplete"
                @blur="handleBlur"
                ref="input"
                :type="props.type"
                :placeholder="placeholder"
                :maxlength="themeProps.maxLength"
                :minlength="themeProps.minLength"
                :pattern="attrs.pattern"
                :data-tip="tooltip"
                :class="[getClasses, { 'pr-8': useFieldComposable && field && isFieldModified }]"
                :autocomplete="attrs.autocomplete || 'off'"
            />
            <slot v-if="labelInside" name="after" />
            <button
                v-if="useFieldComposable && field && isFieldModified"
                @click="handleReset"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
            >
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </button>
        </label>
        <div v-else class="relative">
            <input
                v-bind="attrs"
                :required="themeProps.required"
                :autofocus="themeProps.autofocus"
                :value="displayValue"
                @input="updateValue"
                @change="handleAutocomplete"
                @blur="handleBlur"
                ref="input"
                :type="props.type"
                :placeholder="placeholder"
                :maxlength="themeProps.maxLength"
                :minlength="themeProps.minLength"
                :pattern="attrs.pattern"
                :data-tip="tooltip"
                :class="[getClasses, { 'pr-8': useFieldComposable && field && isFieldModified }]"
                :autocomplete="attrs.autocomplete || 'off'"
            />
            <button
                v-if="useFieldComposable && field && isFieldModified"
                @click="handleReset"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
            >
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </button>
        </div>
    </div>
</template>
