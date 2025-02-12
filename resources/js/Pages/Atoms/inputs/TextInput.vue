<script setup>
import { ref, onMounted, defineExpose, computed } from "vue";
import { useAttrs } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    theme: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
    labelInside: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue"]);
const input = ref(null);
const attrs = useAttrs();

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
    emit("update:modelValue", event.target.value);
};

onMounted(() => {
    if (input.value && themeProps.value.autofocus) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <label
        v-if="labelInside"
        :class="`input border-${themeProps.color || 'primary-500'} text-${themeProps.color || 'primary-500'} input-bordered flex items-center gap-2`"
    >
        <slot v-if="labelInside" name="before" />
        <input
            v-bind="attrs"
            :required="themeProps.required"
            :autofocus="themeProps.autofocus"
            :value="modelValue"
            @input="updateValue"
            ref="input"
            :type="themeProps.type || 'text'"
            :placeholder="placeholder"
            :maxlength="themeProps.maxLength"
            :minlength="themeProps.minLength"
            :pattern="attrs.pattern"
            :data-tip="tooltip"
            :class="getClasses"
        />
        <slot v-if="labelInside" name="after" />
    </label>
    <input
        v-else
        v-bind="attrs"
        :required="themeProps.required"
        :autofocus="themeProps.autofocus"
        :value="modelValue"
        @input="updateValue"
        ref="input"
        :type="themeProps.type || 'text'"
        :placeholder="placeholder"
        :maxlength="themeProps.maxLength"
        :minlength="themeProps.minLength"
        :pattern="attrs.pattern"
        :data-tip="tooltip"
        :class="getClasses"
    />
</template>
