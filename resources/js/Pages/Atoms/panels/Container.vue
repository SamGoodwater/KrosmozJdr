<script setup>
import { ref, computed, defineProps } from "vue";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    bgColor: {
        type: String,
        default: "base-900",
    },
    bgOpacity: {
        type: String,
        default: "40",
    },
    blur: {
        type: String,
        default: "lg",
        validator: (value) =>
            ["non", "xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    rounded: {
        type: String,
        default: "none",
        validator: (value) =>
            ["none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(
                value,
            ),
    },
    shadow: {
        type: String,
        default: "sm",
        validator: (value) =>
            ["none", "xs", "sm", "md", "lg", "xl", "2xl", "3xl"].includes(
                value,
            ),
    },
});

const getClasses = computed(() => {
    let classes = [
        "container",
        "mx-auto",
        "py-6",
        "max-md:py-6",
        "max-sm:py-4",
        "px-24",
        "max-xl:px-32",
        "max-lg:px-24",
        "max-md:px-10",
        "max-sm:px-2",
        "w-fit-available",
        "h-fit-available",
    ];
    let match;
    let color;

    // BLUR
    if (props.theme) {
        const regexBlur =
            /(?:^|\s)(?<capture>blur-(none|xs|sm|md|lg|xl|2xl))(?:\s|$)/;
        match = regexBlur.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`backdrop-blur-${match.groups.capture}`);
        } else {
            classes.push(`backdrop-blur-${props.blur}`);
        }
    } else {
        classes.push(`backdrop-blur-${props.blur}`);
    }

    // SHADOW
    if (props.theme) {
        const regexShadow =
            /(?:^|\s)(?<capture>shadow-(none|xs|sm|md|lg|xl|2xl|3xl))(?:\s|$)/;
        match = regexShadow.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`shadow-${match.groups.capture}`);
        } else {
            classes.push(`shadow-${props.shadow}`);
        }
    } else {
        classes.push(`shadow-${props.shadow}`);
    }

    // ROUNDED
    if (props.theme) {
        const regexRounded =
            /(?:^|\s)(?<capture>rounded-(none|sm|md|lg|xl|2xl|3xl|full))(?:\s|$)/;
        match = regexRounded.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`rounded-${match.groups.capture}`);
        } else {
            classes.push(`rounded-${props.rounded}`);
        }
    } else {
        classes.push(`rounded-${props.rounded}`);
    }

    // BG COLOR
    if (props.theme) {
        const regexColorAuto = /(?:^|\s)(?<capture>color-auto)(?:\s|$)/;
        match = regexColorAuto.exec(props.theme);
        if (match && match?.groups?.capture) {
            color = getColorFromString(props.bgColor);
        }

        const regexColor =
            /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            color = match.groups.capture;
        }
    } else if (props.bgColor) {
        color = props.bgColor;
    } else {
        color = "secondary-300";
    }
    if (props.bgOpacity) {
        classes.push(`bg-${color}/${props.bgOpacity}`);
    } else {
        classes.push(`bg-${color}`);
    }

    return classes.join(" ");
});
</script>

<template>
    <div :class="getClasses">
        <slot />
    </div>
</template>

<style scoped></style>
