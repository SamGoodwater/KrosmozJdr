<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import useEditableField from '@/Composables/useEditableField'; // Import du composable

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    placeholder: {
        type: Number,
        default: 0,
    },
    value: {
        type: [Number, Object],
        default: 0,
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
});

const emit = defineEmits(["update:value"]);
const input = ref(null);
const debounceTimeout = ref(null);

const editableField = useEditableField(props.value); // Utilisation du composable

const buildInputClasses = (themeProps, props) => {
    const classes = ["input", "w-full", "max-w-xs"];

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
    const newValue = Number(event.target.value);
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
        <input
            ref="input"
            type="number"
            :value="displayValue"
            @input="updateValue"
            @blur="handleBlur"
            :placeholder="placeholder"
            :max="themeProps.maxLength"
            :min="themeProps.minLength"
            :step="attrs?.step || 1"
            :required="themeProps.required"
            :autofocus="themeProps.autofocus"
            :data-tip="tooltip"
            :class="getClasses"
        />
        <button
            v-if="useFieldComposable && isFieldModified"
            @click="handleReset"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
        >
            <i class="fa-solid fa-arrow-rotate-left"></i>
        </button>
    </div>
</template>
