<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import useEditableField from '@/Composables/useEditableField';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    value: {
        type: [Boolean, Object],
        default: false,
    },
    label: {
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
    id: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(["update:value"]);
const input = ref(null);
const debounceTimeout = ref(null);

const editableField = useEditableField(props.value);

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.value;
});

const buildCheckboxClasses = (props) => {
    const classes = ["checkbox"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildCheckboxClasses(combinedProps.value));

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
    <div class="form-control">
        <BaseTooltip
            :tooltip="tooltip"
            :tooltip-position="tooltipPosition"
        >
            <label class="label cursor-pointer justify-start gap-2">
                <input
                    type="checkbox"
                    :id="props.id"
                    :checked="displayValue"
                    @change="updateValue"
                    @blur="handleBlur"
                    :autofocus="themeProps.autofocus"
                    :required="themeProps.required"
                    ref="input"
                    :class="getClasses"
                />
                <InputLabel v-if="useInputLabel" :for="props.id" :value="inputLabel || label">
                    <template v-if="$slots.inputLabel">
                        <slot name="inputLabel" />
                    </template>
                </InputLabel>
                <button
                    v-if="useFieldComposable && isFieldModified"
                    @click="handleReset"
                    class="ml-auto text-base-600/80 hover:text-base-600/50"
                >
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            </label>
            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>
        <InputError v-if="useInputError" :message="errorMessage" class="mt-2" />
    </div>
</template>

<style scoped>
/* Styles spécifiques au composant Checkbox si nécessaire */
</style>
