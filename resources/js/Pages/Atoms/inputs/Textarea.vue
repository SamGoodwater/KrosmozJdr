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
        type: String,
        default: "",
    },
    value: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
});

const input = ref(null);

const buildTextareaClasses = (themeProps, props) => {
    const classes = ["textarea", "w-full", "max-w-xs"];

    // Color
    const color = themeProps.color || 'primary-500';
    classes.push(`text-${color}`);
    classes.push(`border-${color}`);

    // Size
    const size = themeProps.size || 'md';
    classes.push(`textarea-${size}`);

    // Border style
    if (themeProps.bordered) {
        classes.push("textarea-bordered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildTextareaClasses(themeProps.value, props));

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
    <textarea
        ref="input"
        :value="value"
        @input="updateValue"
        :placeholder="placeholder"
        :maxlength="themeProps.maxLength"
        :autofocus="themeProps.autofocus"
        :required="themeProps.required"
        :data-tip="tooltip"
        :class="getClasses"
    ></textarea>
</template>
