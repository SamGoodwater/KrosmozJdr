<script setup>
import { computed, ref, onMounted, onUnmounted, useAttrs } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import useEditableField from '@/Composables/useEditableField';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    value: {
        type: [String, Object],
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
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
    useInputLabel: {
        type: Boolean,
        default: true,
    },
    useInputError: {
        type: Boolean,
        default: true,
    },
    inputLabel: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(["update:value"]);
const input = ref(null);
const debounceTimeout = ref(null);
const attrs = useAttrs();

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `textarea-${Math.random().toString(36).substr(2, 9)}`);

const editableField = useEditableField(props.value);

const buildTextareaClasses = (themeProps, props) => {
    const classes = ["textarea", "w-full", "max-w-xs"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);
    classes.push(`border-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`textarea-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("textarea-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildTextareaClasses(themeProps.value, props));

const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.value;
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
        emit("update:value", newValue);
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
});

onUnmounted(() => {
    if (debounceTimeout.value) {
        clearTimeout(debounceTimeout.value);
    }
});
</script>

<template>
    <div class="relative">
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || 'Texte'">
            <template v-if="$slots.inputLabel">
                <slot name="inputLabel" />
            </template>
        </InputLabel>

        <textarea
            ref="input"
            :id="componentId"
            :class="getClasses"
            :value="displayValue"
            @input="updateValue"
            @blur="handleBlur"
            :placeholder="placeholder"
            :maxlength="themeProps.maxLength"
            :rows="themeProps.rows"
            :cols="themeProps.cols"
            :disabled="themeProps.disabled"
            :readonly="themeProps.readonly"
            :required="themeProps.required"
            :autofocus="themeProps.autofocus"
            :name="themeProps.name"
            :title="tooltip"
            autocomplete="off"
        ></textarea>
        <button
            v-if="useFieldComposable && isFieldModified"
            @click="handleReset"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
        >
            <i class="fa-solid fa-arrow-rotate-left"></i>
        </button>
        <InputError v-if="useInputError" :message="errorMessage" class="mt-2" />
    </div>
</template>
