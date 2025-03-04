<script setup>
import { ref, computed } from "vue";
import { useAttrs } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    theme: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "*************",
    },
});

const emit = defineEmits(["update:modelValue"]);
const input = ref(null);
const attrs = useAttrs();
const showPassword = ref(false);

const buildInputClasses = (themeProps) => {
    const classes = ["input", "w-full", "pr-12"];

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
const getClasses = computed(() => buildInputClasses(themeProps.value));

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const updateValue = (event) => {
    emit("update:modelValue", event.target.value);
};
</script>

<template>
    <div class="relative">
        <input
            v-bind="attrs"
            :value="modelValue"
            @input="updateValue"
            ref="input"
            :type="showPassword ? 'text' : 'password'"
            :placeholder="placeholder"
            :class="getClasses"
            :autocomplete="attrs.autocomplete || 'current-password'"
        />
        <div class="absolute right-0 top-1/2 -translate-y-1/2 flex gap-1 px-2">
            <button
                type="button"
                @click.prevent="togglePassword"
                class="text-base-600/80 hover:text-base-600/50"
            >
                <i v-if="showPassword" class="fa-solid fa-eye"></i>
                <i v-else class="fa-solid fa-eye-slash"></i>
            </button>
        </div>
    </div>
</template>
