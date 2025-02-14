<script setup>
import { ref, computed, defineProps } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import { getColorFromString } from "@/Utils/Color.js";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    modelValue: {
        type: Boolean,
        default: false,
    },
    bgColor: {
        type: String,
        default: "secondary-300",
    },
    borderColor: {
        type: String,
        default: "secondary-100/10",
    },
    opacity: {
        type: String,
        default: "10",
    },
    blur: {
        type: String,
        default: "lg",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', 'full'].includes(value)
    },
});

const emit = defineEmits(['update:modelValue']);

const buildModalClasses = (themeProps, props) => {
    const classes = ["modal-box", "border-glass"];

    // Background Color
    let bgColor = props.bgColor;
    if (themeProps.colorAuto) {
        bgColor = getColorFromString(props.bgColor);
    }
    classes.push(`bg-${bgColor}/${props.opacity}`);

    // Border Color
    let borderColor = props.borderColor;
    if (themeProps.colorAuto) {
        borderColor = getColorFromString(props.borderColor);
    }
    classes.push(`border-${borderColor}`);

    // Size
    if (props.size === 'full') {
        classes.push('w-[90vw] h-[90vh]');
    } else {
        const sizeMap = {
            'xs': 'max-w-xs',
            'sm': 'max-w-sm',
            'md': 'max-w-md',
            'lg': 'max-w-lg',
            'xl': 'max-w-xl',
            '2xl': 'max-w-2xl',
            '3xl': 'max-w-3xl',
        };
        classes.push(sizeMap[props.size]);
    }

    return classes.join(" ");
};

const buildBackdropClasses = (props) => {
    const classes = ["modal-backdrop"];

    if (props.blur) {
        classes.push(`backdrop-blur-${props.blur}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const modalClasses = computed(() => buildModalClasses(themeProps.value, props));
const backdropClasses = computed(() => buildBackdropClasses(props));

const closeModal = () => {
    emit('update:modelValue', false);
};
</script>

<template>
    <dialog :open="modelValue" class="modal">
        <div :class="modalClasses">
            <form method="dialog" @submit.prevent="closeModal">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>

            <div v-if="$slots.title" class="text-lg font-bold mb-4">
                <slot name="title" />
            </div>

            <div class="modal-content">
                <slot />
            </div>
        </div>

        <form method="dialog" :class="backdropClasses" @submit.prevent="closeModal">
            <button>close</button>
        </form>
    </dialog>
</template>

<style scoped lang="scss">
.modal-box {
    position: relative;
    transition: all 0.3s ease;
}

// Animation d'entrée/sortie
.modal[open] {
    animation: modal-pop 0.2s ease-out;
}

@keyframes modal-pop {
    0% {
        opacity: 0;
        transform: scale(0.95);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
