<script setup>
import { ref, onMounted, defineExpose, computed, onUnmounted } from "vue";
import { useAttrs } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import useEditableField from '@/Composables/useEditableField';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    placeholder: {
        type: String,
        default: "",
    },
    value: {
        type: [String, Object],
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

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `text-input-${Math.random().toString(36).substr(2, 9)}`);

const editableField = useEditableField(props.value);

const buildInputClasses = (props) => {
    const classes = ["input", "w-full"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Style de bordure
    if (props.bordered) {
        classes.push("input-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildInputClasses(combinedProps.value));

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
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || 'Texte'">
            <template v-if="$slots.inputLabel">
                <slot name="inputLabel" />
            </template>
        </InputLabel>

        <BaseTooltip
            :tooltip="tooltip"
            :tooltip-position="tooltipPosition"
        >
            <div class="relative">
                <input
                    ref="input"
                    type="text"
                    :id="componentId"
                    :class="getClasses"
                    :value="displayValue"
                    @input="updateValue"
                    @blur="handleBlur"
                    :placeholder="placeholder"
                    :maxlength="themeProps.maxLength"
                    :disabled="themeProps.disabled"
                    :readonly="themeProps.readonly"
                    :required="themeProps.required"
                    :autofocus="themeProps.autofocus"
                    :name="themeProps.name"
                />
                <button
                    v-if="useFieldComposable && isFieldModified"
                    @click="handleReset"
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-base-600/80 hover:text-base-600/50"
                >
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </button>
            </div>
            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>

        <InputError v-if="useInputError" :message="errorMessage" class="mt-2" />
    </div>
</template>
