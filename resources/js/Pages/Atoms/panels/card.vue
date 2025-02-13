<script setup>
import { ref, computed, defineProps, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import { getColorFromString } from "@/Utils/Color.js";
import VanillaTilt from 'vanilla-tilt';

const props = defineProps({
    theme: {
        type: String,
        default: "",
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
        default: 10,
    },
    blur: {
        type: String,
        default: "lg",
    },
    shadow: {
        type: String,
        default: "sm",
    },
    rounded: {
        type: String,
        default: "lg",
    },
    width: {
        type: String,
        default: "auto",
    },
    height: {
        type: String,
        default: "auto",
    },
    hovering: {
        type: Boolean,
        default: false,
    },
});

const buildCardClasses = (themeProps, props) => {
    const classes = ["card", "border-glass"];

    // Shadow
    if (props.shadow) {
        classes.push(`border-glass-${props.shadow}`);
    } else if (themeProps.shadow) {
        classes.push(themeProps.shadow);
    }

    // Blur
    if (props.blur) {
        classes.push(`backdrop-blur-${props.blur}`);
    } else if (themeProps.blur) {
        classes.push(themeProps.blur);
    }

    // Background Color
    let bgColor = props.bgColor;
    if (themeProps.colorAuto) {
        bgColor = getColorFromString(props.bgColor);
    } else if (themeProps.color) {
        bgColor = themeProps.color;
    }

    if (props.opacity || themeProps.opacity) {
        classes.push(`bg-${bgColor}/${props.opacity || themeProps.opacity}`);
    } else {
        classes.push(`bg-${bgColor}`);
    }

    // Border Color
    let borderColor = props.borderColor;
    if (themeProps.colorAuto) {
        borderColor = getColorFromString(props.borderColor);
    } else if (themeProps.borderColor) {
        borderColor = themeProps.borderColor;
    }
    classes.push(`border-${borderColor}`);

    // Rounded
    if (props.rounded) {
        classes.push(`rounded-${props.rounded}`);
    } else if (themeProps.rounded) {
        classes.push(themeProps.rounded);
    }

    // Width
    if (props.width !== "auto") {
        if (props.width.includes("[")) {
            classes.push(`w-${props.width}`);
        } else {
            classes.push(`w-${props.width}`);
        }
    } else if (themeProps.width) {
        classes.push(`w-${themeProps.width}`);
    }

    // Height
    if (props.height !== "auto") {
        if (props.height.includes("[")) {
            classes.push(`h-${props.height}`);
        } else {
            classes.push(`h-${props.height}`);
        }
    } else if (themeProps.height) {
        classes.push(`h-${themeProps.height}`);
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const classes = computed(() => buildCardClasses(themeProps.value, props));
const isHovering = computed(() => props.hovering || themeProps.value?.hover !== null);

onMounted(() => {
    VanillaTilt.init(document.querySelectorAll(".card"), {
        max: 1,
        speed: 200,
        glare: true,
        "max-glare": 0.1,
    });
});
</script>

<template>
    <div :class="classes">
        <div class="card-content">
            <slot />
        </div>
        <div v-if="isHovering" class="hover-content">
            <slot name="hover" />
        </div>
    </div>
</template>

<style scoped lang="scss">
.card {
    z-index: 10;
    position: relative;
    isolation: isolate; // Crée un nouveau contexte d'empilement
}

.card-content {
    position: relative;
    z-index: 11;
}

.hover-content {
    position: absolute;
    top: 100%; // Se positionne juste en dessous de la carte
    left: 0;
    width: 100%;
    background: inherit;
    z-index: 11; // Même niveau que card-content
    opacity: 0;
    transform: translateY(-20px); // Commence légèrement plus haut
    transition: all 0.3s ease;
    pointer-events: none;
    border-bottom-left-radius: inherit; // Hérite du border-radius de la carte
    border-bottom-right-radius: inherit;
}

.card:hover {
    .hover-content {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
}
</style>
