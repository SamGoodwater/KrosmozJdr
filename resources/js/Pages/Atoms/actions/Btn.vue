<script setup>
import { computed } from "vue";
import { adjustIntensityColor } from "@/Utils/Color";
import Tooltip from "../feedback/tooltips.vue";

const props = defineProps({
    theme: {
        type: String,
        default: "button",
    },
    label: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "button",
        validator: (value) =>
            ["", "button", "submit", "reset", "radio", "checkbox"].includes(
                value,
            ),
    },
    face: {
        type: String,
        default: "",
        validator: (value) =>
            ["", "block", "wide", "square", "circle"].includes(value),
    },
    styled: {
        type: String,
        default: "",
        validator: (value) =>
            ["", "glass", "outline", "link", "ghost"].includes(value),
    },
    color: {
        type: String,
        default: "primary",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["", "xs", "sm", "md", "lg"].includes(value),
    },
    tooltip: {
        type: String,
        default: "",
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) =>
            ["", "top", "right", "bottom", "left"].includes(value),
    },
});

const getClasses = computed(() => {
    let classes = ["btn"];
    let match;
    if (props.theme) {
        // STYLE
        const regexStyled =
            /(?:^|\s)(?<capture>|outline|ghost|link|glass)(?:\s|$)/;
        match = regexStyled.exec(props.theme);
        if (match && match?.groups?.capture) {
            if (match.groups.capture === "glass") {
                classes.push("glass");
            } else {
                classes.push("btn-" + match.groups.capture);
            }
        }

        //FACE
        const regexFace =
            /(?:^|\s)(?<capture>|wide|block|square|circle)(?:\s|$)/;
        match = regexFace.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push("btn-" + match.groups.capture);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`btn-${match.groups.capture}`);
        } else {
            classes.push(`btn-md`);
        }

        // COLOR
        const regexColor =
            /(?:^|\s)(?<capture>primary|secondary|success|error|simple)(?:\s|$)/;
        match = regexColor.exec(props.theme);
        if (match && match?.groups?.capture) {
            classes.push(`btn-custom-${match.groups.capture}`);
        }
    }

    if (
        !["glass", "outline", "link", "ghost"].some((word) =>
            props.theme.includes(word),
        )
    ) {
        if (props.styled && props.styled !== "glass") {
            classes.push(`${props.styled}`);
        }
        if (props.styled === "glass") {
            classes.push(`glass`);
        }
    }

    if (
        !["block", "wide", "square", "circle"].some((word) =>
            props.theme.includes(word),
        )
    ) {
        if (props.face) {
            classes.push(`${props.face}`);
        }
    }

    if (!["xs", "sm", "md", "lg"].some((word) => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`btn-${props.size}`);
        }
    }
    // if (['primary', 'secondary', 'success', 'error', "simple"].some(word => props.theme.includes(word)) === false) {
    if (props.color) {
        if (props.styled == "outline" || props.theme.includes("outline")) {
            classes.push(`text-${props.color}`);
            classes.push(`border-${props.color}`);
            classes.push(`hover:text-${adjustIntensityColor(props.color, 2)}`);
            classes.push(
                `hover:border-${adjustIntensityColor(props.color, 2)}`,
            );
        } else if (props.styled == "link" || props.theme.includes("link")) {
            classes.push(`text-${props.color}`);
            classes.push(`hover:text-${adjustIntensityColor(props.color, 2)}`);
        } else {
            classes.push(`bg-${props.color}`);
            classes.push(`hover:bg-${adjustIntensityColor(props.color, 2)}`);
        }
    }
    // }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(" ");
});
</script>

<template>
    <Tooltip v-if="tooltip" :placement="tooltipPosition">
        <template #reference>
            <button :type="type" :class="`${getClasses}`">
                <span v-if="label">{{ label }}</span>
                <slot v-else name="label" />
            </button>
        </template>
        <template #content>
            <span>{{ tooltip }}</span>
        </template>
    </Tooltip>
    <button v-else :type="type" :class="`${getClasses}`">
        <span v-if="label">{{ label }}</span>
        <slot v-else name="label" />
    </button>
</template>

<style scoped lang="scss">
.btn-link {
    background-color: transparent;
    text-decoration: none;
    margin: 0;
    padding: 0;
    height: auto;
    min-height: auto;
    width: auto;
    min-width: auto;

    &.btn-xs {
        font-size: 0.75rem;
    }
    &.btn-sm {
        font-size: 0.875rem;
    }
    &.btn-md {
        font-size: 1rem;
    }
    &.btn-lg {
        font-size: 1.25rem;
    }
}

.btn-custom {
    border: 0px solid transparent;

    &-primary {
        background-color: var(--color-primary-800);

        &:hover {
            background-color: var(--color-primary-600);
        }

        &.btn-outline {
            background-color: transparent;
            color: var(--color-primary-600);
            border-color: var(--color-primary-600);

            &:hover {
                color: var(--color-primary-400);
                border-color: var(--color-primary-400);
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            color: var(--color-primary-600);

            &:hover {
                color: var(--color-primary-400);
            }
        }
    }

    &-secondary {
        background-color: var(--color-secondary-400);
        color: var(--color-secondary-700);

        &:hover {
            background-color: var(--color-secondary-600);
            color: var(--color-secondary-200);
        }

        &.btn-outline {
            background-color: transparent;
            color: var(--color-secondary-400);
            border-color: var(--color-secondary-400);

            &:hover {
                color: var(--color-secondary-200);
                border-color: var(--color-secondary-200);
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            color: var(--color-secondary-400);

            &:hover {
                color: var(--color-secondary-200);
            }
        }
    }

    &-success {
        background-color: var(--color-success-800);

        &:hover {
            background-color: var(--color-success-600);
        }

        &.btn-outline {
            background-color: transparent;
            color: var(--color-success-600);
            border-color: var(--color-success-600);

            &:hover {
                color: var(--color-success-400);
                border-color: var(--color-success-400);
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            color: var(--color-success-600);

            &:hover {
                color: var(--color-success-400);
            }
        }
    }

    &-error {
        background-color: var(--color-error-800);

        &:hover {
            background-color: var(--color-error-600);
        }

        &.btn-outline {
            background-color: transparent;
            color: var(--color-error-600);
            border-color: var(--color-error-600);

            &:hover {
                color: var(--color-error-400);
                border-color: var(--color-error-400);
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            color: var(--color-error-600);

            &:hover {
                color: var(--color-error-400);
            }
        }
    }

    &-simple {
        background-color: var(--color-gray-400);
        color: var(--color-gray-700);

        &:hover {
            background-color: var(--color-gray-600);
            color: var(--color-gray-200);
        }

        &.btn-outline {
            background-color: transparent;
            color: var(--color-gray-400);
            border-color: var(--color-gray-400);

            &:hover {
                color: var(--color-gray-200);
                border-color: var (--color-gray-200);
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            color: var(--color-gray-400);

            &:hover {
                color: var(--color-gray-200);
            }
        }
    }
}
</style>
