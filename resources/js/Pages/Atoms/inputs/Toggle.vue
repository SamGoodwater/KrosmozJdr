<script setup>
import { computed, ref, onMounted, useAttrs } from "vue";
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
    useFieldComposable: {
        type: Boolean,
        default: false,
    },
    field: {
        type: Object,
        default: null,
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
const attrs = useAttrs();

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `toggle-${Math.random().toString(36).substr(2, 9)}`);

const editableField = useEditableField(props.value);

const buildToggleClasses = (props) => {
    const classes = ["toggle"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildToggleClasses(combinedProps.value));

const displayValue = computed(() => {
    if (props.useFieldComposable && props.field) {
        return props.field.value.value;
    }
    return props.value;
});

const updateValue = (event) => {
    const newValue = event.target.checked;
    if (props.useFieldComposable && props.field) {
        props.field.value.value = newValue;
        if (typeof props.field.update === 'function') {
            props.field.update(newValue);
        }
    } else {
        emit("update:value", newValue);
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
        emit("update:value", props.field.value.value);
    }
};
</script>

<template>
    <div class="relative">
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || 'Toggle'">
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
                    type="checkbox"
                    :id="componentId"
                    :class="getClasses"
                    :checked="displayValue"
                    @change="updateValue"
                    :disabled="themeProps.disabled"
                    :required="themeProps.required"
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
