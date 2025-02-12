<script setup>
import { computed, ref, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const emit = defineEmits(["update:value"]);

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    value: {
        type: [String, Number],
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
});

const input = ref(null);

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
    emit("update:value", event.target.value);
};

onMounted(() => {
    if (input.value && themeProps.value.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <label :class="getClasses">
        <span>{{ label }}</span>
        <select
            ref="input"
            :value="value"
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
</template>
