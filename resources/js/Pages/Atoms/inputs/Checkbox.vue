<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import useEditableField from '@/Composables/useEditableField'; // Import du composable

const emit = defineEmits(["update:value"]);

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
});

const input = ref(null);
const debounceTimeout = ref(null);

const editableField = useEditableField(props.value); // Utilisation du composable

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.value;
});

const buildCheckboxClasses = (themeProps, props) => {
    const classes = ["checkbox"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`checkbox-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("checkbox-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildCheckboxClasses(themeProps.value, props));

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
    <div class="relative form-control">
        <label class="label cursor-pointer">
            <input
                :class="getClasses"
                type="checkbox"
                :checked="displayValue"
                @change="updateValue"
                @blur="handleBlur"
                :autofocus="themeProps.autofocus"
                :required="themeProps.required"
                :data-tip="tooltip"
                ref="input"
            />
            <span class="label-text">{{ label }}</span>
        </label>
        <button
            v-if="useFieldComposable && isFieldModified"
            @click="handleReset"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
        >
            <i class="fa-solid fa-arrow-rotate-left"></i>
        </button>
    </div>
</template>
