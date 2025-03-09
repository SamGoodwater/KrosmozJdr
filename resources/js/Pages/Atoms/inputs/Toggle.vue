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
    value: {
        type: [Boolean, Object],
        default: false,
    },
    label: {
        type: String,
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
const attrs = useAttrs();
const debounceTimeout = ref(null);

const editableField = useEditableField(props.value);

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `toggle-${Math.random().toString(36).substr(2, 9)}`);

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.value;
});

const buildToggleClasses = (themeProps, props) => {
    const classes = ["toggle"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`toggle-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("toggle-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildToggleClasses(themeProps.value, props));

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
    const newValue = event.target.checked;
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
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || 'Interrupteur'">
            <template v-if="$slots.inputLabel">
                <slot name="inputLabel" />
            </template>
        </InputLabel>

        <input
            ref="input"
            :id="componentId"
            type="checkbox"
            :class="getClasses"
            :checked="displayValue"
            @change="updateValue"
            @blur="handleBlur"
            :required="themeProps.required"
            :autofocus="themeProps.autofocus"
            :name="themeProps.name"
            :title="tooltip"
        />
        <span>{{ label }}</span>
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
