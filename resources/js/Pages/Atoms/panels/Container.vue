<script setup>
import { ref, computed, defineProps, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    bgColor: {
        type: String,
        default: "base-900",
    },
    opacity: {
        type: String,
        default: "40",
    },
    blur: {
        type: String,
        default: "lg",
    },
    rounded: {
        type: String,
        default: "none",
    },
    shadow: {
        type: String,
        default: "sm",
    },
});

const buildContainerClasses = (themeProps, props) => {
    const classes = [
        "container",
        "mx-auto",
        "py-6",
        "max-md:py-6",
        "max-sm:py-4",
        "px-24",
        "max-xl:px-24",
        "max-lg:px-16",
        "max-md:px-6",
        "max-sm:px-2",
        "w-fit-available",
        "h-fit-available",
    ];

    // Blur
    if (props.blur) {
        classes.push(`backdrop-blur-${props.blur}`);
    } else if (themeProps.blur) {
        classes.push(themeProps.blur);
    }

    // Shadow
    if (props.shadow) {
        classes.push(`shadow-${props.shadow}`);
    } else if (themeProps.shadow) {
        classes.push(themeProps.shadow);
    }

    // Rounded
    if (props.rounded) {
        classes.push(`rounded-${props.rounded}`);
    } else if (themeProps.rounded) {
        classes.push(themeProps.rounded);
    }

    // Background Color
    let bgColor = props.bgColor;
    if (themeProps.colorAuto) {
        bgColor = getColorFromString(props.bgColor);
    } else if (themeProps.color) {
        bgColor = themeProps.color;
    }

    // Opacity
    if (props.opacity || themeProps.opacity) {
        classes.push(`bg-${bgColor}/${props.opacity || themeProps.opacity}`);
    } else {
        classes.push(`bg-${bgColor}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildContainerClasses(themeProps.value, props));
</script>

<template>
    <div :class="getClasses">
        <slot />
    </div>
</template>
