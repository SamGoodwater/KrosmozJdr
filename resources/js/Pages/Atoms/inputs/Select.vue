<script setup>
import { computed, ref, onMounted, useAttrs } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import useEditableField from '@/Composables/useEditableField';
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const emit = defineEmits(["update:value"]);

const props = defineProps({
    ...commonProps,
    value: {
        type: [String, Number, Object],
        default: "",
    },
    options: {
        type: Array,
        required: true,
    },
    label: {
        type: String,
        default: "Sélectionner une option",
    },
    useFieldComposable: {
        type: Boolean,
        default: false,
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
    for: {
        type: String,
        default: '',
    },
});

const input = ref(null);
const select = ref(null);
const attrs = useAttrs();

const editableField = useEditableField(props.value);

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.value?.value !== undefined) {
        return props.value.value;
    }
    return props.value;
});

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `select-${Math.random().toString(36).substr(2, 9)}`);

const buildSelectClasses = (props) => {
    const classes = ["select", "w-full"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Style de bordure
    if (props.bordered) {
        classes.push("select-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildSelectClasses(combinedProps.value));

const updateValue = (event) => {
    const newValue = event.target.value;
    if (props.useFieldComposable && props.value) {
        props.value.value = newValue;
        if (typeof props.value.update === 'function') {
            props.value.update(newValue);
        }
    } else {
        emit("update:value", newValue);
    }
};

onMounted(() => {
    if (select.value && themeProps.value.autofocus) {
        select.value.focus();
    }
});
</script>

<template>
    <div class="relative">
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || 'Sélection'">
            <template v-if="$slots.inputLabel">
                <slot name="inputLabel" />
            </template>
        </InputLabel>

        <BaseTooltip
            :tooltip="tooltip"
            :tooltip-position="tooltipPosition"
        >
            <div class="relative">
                <label :class="getClasses" :for="props.for">
                    <span>{{ label }}</span>
                    <select
                        ref="select"
                        :id="componentId"
                        :value="displayValue"
                        @change="updateValue"
                        :autofocus="themeProps.autofocus"
                        :required="themeProps.required"
                        :class="getClasses"
                    >
                        <option
                            v-for="option in options"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </label>
                <button
                    v-if="useFieldComposable && value?.isModified"
                    @click="value?.reset"
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
