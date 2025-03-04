<script setup>
import { computed, ref, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import useEditableField from '@/Composables/useEditableField'; // Import du composable

const emit = defineEmits(["update:value"]);

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
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
    tooltip: {
        type: String,
        default: "",
    },
    useFieldComposable: {
        type: Boolean,
        default: false,
    },
});

const input = ref(null);

const editableField = useEditableField(props.value); // Utilisation du composable

// Computed pour gérer la valeur affichée
const displayValue = computed(() => {
    if (props.useFieldComposable && props.value?.value !== undefined) {
        return props.value.value;
    }
    return props.value;
});

const buildSelectClasses = (themeProps, props) => {
    const classes = ["select", "w-full", "max-w-xs"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);
    classes.push(`border-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`select-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("select-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildSelectClasses(themeProps.value, props));

const updateValue = (event) => {
    if (props.useFieldComposable && props.value?.update) {
        props.value.update(event.target.value);
    } else {
        emit("update:value", event.target.value);
    }
};

onMounted(() => {
    if (input.value && themeProps.value.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <div class="relative">
        <label :class="getClasses">
            <span>{{ label }}</span>
            <select
                ref="input"
                :value="displayValue"
                @change="updateValue"
                :autofocus="themeProps.autofocus"
                :required="themeProps.required"
                :data-tip="tooltip"
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
</template>
