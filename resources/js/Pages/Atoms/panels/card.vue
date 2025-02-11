<script setup>
import { ref, defineProps, computed } from "vue";

const props = defineProps({
    theme: {
        type: String,
        default: "",
    },
    bgColor: {
        type: String,
        default: "bg-secondary-500",
    },
    opacity: {
        type: String,
        default: "15",
    },
    blur: {
        type: String,
        default: "lg",
        validator: (value) =>
            ["xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    shadow: {
        type: String,
        default: "sm",
        validator: (value) =>
            ["xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    rounded: {
        type: String,
        default: "lg",
        validator: (value) =>
            ["none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(
                value,
            ),
    },
    tiny: {
        type: Boolean,
        default: false,
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

const isHovering = ref(false);

const getClasses = computed(() => {
    let classes = ["card"];
    let match;

    // SHADOW
    if (props.shadow) {
        classes.push("shadow" + props.shadow);
    } else if (props.theme) {
        const regex =
            /(?:^|\s)(?<capture>shadow-(none|xs|sm|md|lg|xl|2xl))(?:\s|$)/;
        match = regex.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`${match.groups.capture}`);
        }
    }

    //BLUR
    if (props.theme) {
        const regexBlur =
            /(?:^|\s)(?<capture>blur-(none|xs|sm|md|lg|xl|2xl))(?:\s|$)/;
        match = regexBlur.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`backdrop-${match.groups.capture}`);
        } else {
            classes.push(`backdrop-blur-${props.blur}`);
        }
    } else {
        classes.push(`backdrop-blur-${props.blur}`);
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

    // HOVERING
    if (props.theme) {
        const regexHover = /(?:^|\s)(?<capture>hover|hovering)(?:\s|$)/;
        match = regexHover.exec(props.theme);
        if (match && match?.groups?.capture) {
            isHovering.value = true;
        }
    } else if (props.hovering) {
        isHovering.value = props.hovering;
    }

    // WIDTH
    if (props.theme) {
        const regexWidth =
            /(?:^|\s)(?<capture>w-(auto|full|screen|\[?\d+\/\d+\]?))(?:\s|$)/;
        match = regexWidth.exec(props.theme);
        if (match && match?.groups?.capture) {
            if (match.groups.capture === "auto") {
                classes.push(`w-auto`);
            } else if (match.groups.capture === "full") {
                classes.push(`w-full`);
            } else if (match.groups.capture === "screen") {
                classes.push(`w-screen`);
            } else if (match.groups.capture.includes("[")) {
                classes.push(`w-[${match.groups.capture}]`);
            } else {
                classes.push(`w-${match.groups.capture}`);
            }
        }
    } else if (props.width) {
        if (props.width === "auto") {
            classes.push(`w-auto`);
        } else if (props.width === "full") {
            classes.push(`w-full`);
        } else if (props.width === "screen") {
            classes.push(`w-screen`);
        } else if (props.width.includes("[")) {
            classes.push(`w-[${props.width}]`);
        } else {
            classes.push(`w-${props.width}`);
        }
    }

    // HEIGHT
    if (props.theme) {
        const regexHeight =
            /(?:^|\s)(?<capture>h-(auto|full|screen|\[?\d+\/\d+\]?))(?:\s|$)/;
        match = regexHeight.exec(props.theme);
        if (match && match?.groups?.capture) {
            if (match.groups.capture === "auto") {
                classes.push(`h-auto`);
            } else if (match.groups.capture === "full") {
                classes.push(`h-full`);
            } else if (match.groups.capture === "screen") {
                classes.push(`h-screen`);
            } else if (match.groups.capture.includes("[")) {
                classes.push(`h-[${match.groups.capture}]`);
            } else {
                classes.push(`h-${match.groups.capture}`);
            }
        }
    } else if (props.height) {
        if (props.height === "auto") {
            classes.push(`h-auto`);
        } else if (props.height === "full") {
            classes.push(`h-full`);
        } else if (props.height === "screen") {
            classes.push(`h-screen`);
        } else if (props.height.includes("[")) {
            classes.push(`h-[${props.height}]`);
        } else {
            classes.push(`h-${props.height}`);
        }
    }

    return classes.join(" ");
});
</script>

<template>
    <div :class="getClasses">
        <div>
            <slot />
        </div>
        <div v-if(isHovering.value) class="hover">
            <slot name="hover" />
        </div>
    </div>
</template>

<style scoped lang="scss">
.hover {
    display: none;

    &:hover {
        display: block;
    }
}
</style>
