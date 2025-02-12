<script setup>
import { computed, ref } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const emit = defineEmits(["update:checked"]);

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    checked: {
        type: [String, Boolean],
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
});

const isChecked = ref(props.checked);

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

const updateChecked = (event) => {
    isChecked.value = event.target.checked;
    emit("update:checked", isChecked.value);
};
</script>

<template>
    <div class="form-control">
        <label class="label cursor-pointer">
            <input
                :class="getClasses"
                type="checkbox"
                :checked="isChecked"
                @change="updateChecked"
                :autofocus="themeProps.autofocus"
                :required="themeProps.required"
                :data-tip="tooltip"
            />
            <span class="label-text">{{ label }}</span>
        </label>
    </div>
</template>
