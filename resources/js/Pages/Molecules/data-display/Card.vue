<script setup>
import { ref, computed, defineProps, onMounted } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { getColorFromString } from "@/Utils/color/Color.js";
import VanillaTilt from "vanilla-tilt";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    title: {
        type: String,
        default: "",
    },
    subtitle: {
        type: String,
        default: "",
    },
    bordered: {
        type: Boolean,
        default: true,
    },
    shadow: {
        type: Boolean,
        default: true,
    },
    hover: {
        type: Boolean,
        default: false,
    },
    clickable: {
        type: Boolean,
        default: false,
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

const emit = defineEmits(["click"]);

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

const buildCardClasses = (props) => {
    const classes = ["card"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Style de bordure
    if (props.bordered) {
        classes.push("card-bordered");
    }

    // Ombre
    if (props.shadow) {
        classes.push("shadow-md");
    }

    // Effet de survol
    if (props.hover) {
        classes.push("hover:shadow-lg transition-shadow duration-300");
    }

    // Curseur cliquable
    if (props.clickable) {
        classes.push("cursor-pointer");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildCardClasses(combinedProps.value));

onMounted(() => {
    VanillaTilt.init(document.querySelectorAll(".card"), {
        max: 1,
        speed: 200,
        glare: true,
        "max-glare": 0.1,
    });
});

const handleClick = () => {
    if (props.clickable) {
        emit("click");
    }
};
</script>

<template>
    <BaseTooltip :tooltip="tooltip" :tooltip-position="tooltipPosition">
        <div :class="getClasses" @click="handleClick">
            <div v-if="title || subtitle || $slots.header" class="card-header">
                <slot name="header">
                    <h3 v-if="title" class="card-title">{{ title }}</h3>
                    <p v-if="subtitle" class="card-subtitle">{{ subtitle }}</p>
                </slot>
            </div>

            <div class="card-body">
                <slot />
            </div>

            <div v-if="$slots.footer" class="card-footer">
                <slot name="footer" />
            </div>
        </div>
        <template v-if="typeof tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </BaseTooltip>
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
