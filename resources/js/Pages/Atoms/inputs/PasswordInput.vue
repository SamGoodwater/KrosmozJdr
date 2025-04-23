<script setup>
import { ref, computed } from "vue";
import { useAttrs } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "*************",
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

const emit = defineEmits(["update:modelValue"]);
const input = ref(null);
const attrs = useAttrs();
const showPassword = ref(false);

const buildInputClasses = (props) => {
    const classes = ["input", "w-full", "pr-12"];

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

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const updateValue = (event) => {
    emit("update:modelValue", event.target.value);
};
</script>

<template>
    <div class="relative">
        <InputLabel v-if="useInputLabel" :for="attrs.id" :value="inputLabel || attrs.id">
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
            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>

        <InputError v-if="useInputError" :message="errorMessage" class="mt-2" />
    </div>
</template>
