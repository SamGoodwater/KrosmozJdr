<script setup>
import { ref, computed, defineProps, onMounted } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import { getColorFromString } from "@/Utils/Color.js";
import VanillaTilt from "vanilla-tilt";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    bgColor: {
        type: String,
        default: "secondary-700",
    },
    borderColor: {
        type: String,
        default: "secondary-100/10",
    },
    opacity: {
        type: [String, Number],
        default: 80,
    },
    blur: {
        type: String,
        default: "lg",
    },
    shadow: {
        type: String,
        default: "sm",
        validator(value) {
            return [
                "none",
                "xs",
                "sm",
                "md",
                "lg",
                "xl",
                "2xl",
                "3xl",
            ].includes(value);
        },
    },
    rounded: {
        type: String,
        default: "lg",
        validator(value) {
            return [
                "none",
                "xs",
                "sm",
                "md",
                "lg",
                "xl",
                "2xl",
                "3xl",
            ].includes(value);
        },
    },
    width: {
        type: [String, Number],
        default: "auto",
    },
    height: {
        type: [String, Number],
        default: "auto",
    },
});

const width = computed(() => {
    // Width
    if (props.width !== "auto") {
        if (props.width.includes("[")) {
            return `w-${props.width}`;
        } else {
            return `w-${props.width}`;
        }
    } else if (themeProps.width) {
        return `w-${themeProps.width}`;
    }
});

const height = computed(() => {
    // Height
    if (props.height !== "auto") {
        if (props.height.includes("[")) {
            return `h-${props.height}`;
        } else {
            return `h-${props.height}`;
        }
    } else if (themeProps.height) {
        return `h-${themeProps.height}`;
    }
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
    classes.push(width);

    // Height
    classes.push(height);

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const classes = computed(() => buildCardClasses(themeProps.value, props));

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
    <div :class="{ 'card-wrapper': true, width, height }">
        <div :class="classes">
            <div class="card-content">
                <slot />
            </div>
            <div v-if="$slots.hover" class="hover-content">
                <slot name="hover" />
            </div>
        </div>
    </div>
</template>

<style scoped lang="scss">
.card-wrapper {
    position: relative;
    width: 300px;
    min-height: 120px;
    z-index: 10;

    .card {
        position: relative;
        padding-block: 5px;
        padding-inline: 5px;
        backdrop-filter: blur(10px);

        .card-content {
            width: 100%;
            height: 100%;
        }

        .hover-content {
            opacity: 0;
            display: none;
            transition: all 0.3s ease-in-out;
        }

        &:hover {
            position: absolute;
            min-height: 100%;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom: none;
            box-shadow:
                0 -1px 0.375px 0.375px rgba(255, 255, 255, 0.25),
                0 -1px 1px 1.5px rgba(255, 255, 255, 0.05),
                0 -1px 1.75px 2.25px rgba(255, 255, 255, 0.025);

            .hover-content {
                opacity: 1;
                display: block;
            }
        }
    }
}
</style>
