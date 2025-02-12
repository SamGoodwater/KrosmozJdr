<script setup>
import { computed, ref, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const emit = defineEmits(["update:value"]);

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
        type: Number,
        default: 0,
    },
    tooltip: {
        type: String,
        default: "",
    },
});

const input = ref(null);

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

const updateValue = (event) => {
    emit("update:value", Number(event.target.value));
};

onMounted(() => {
    if (input.value && themeProps.value.autofocus) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        ref="input"
        type="number"
        :value="value"
        @input="updateValue"
        :placeholder="placeholder"
        :max="themeProps.maxLength"
        :min="themeProps.minLength"
        :step="attrs?.step || 1"
        :required="themeProps.required"
        :autofocus="themeProps.autofocus"
        :data-tip="tooltip"
        :class="getClasses"
    />
</template>
