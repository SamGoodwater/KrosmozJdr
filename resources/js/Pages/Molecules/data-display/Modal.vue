<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "",
    },
    subtitle: {
        type: String,
        default: "",
    },
    closeButton: {
        type: Boolean,
        default: true,
    },
    backdrop: {
        type: Boolean,
        default: true,
    },
    centered: {
        type: Boolean,
        default: true,
    },
    size: {
        type: String,
        default: "md",
        validator(value) {
            return ["sm", "md", "lg", "xl", "2xl", "3xl", "4xl", "5xl", "6xl", "7xl", "full"].includes(value);
        },
    },
});

const emit = defineEmits(["close", "update:show"]);

const modalRef = ref(null);

const buildModalClasses = (props) => {
    const classes = ["modal"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Taille
    classes.push(`modal-${props.size}`);

    // Centrage
    if (props.centered) {
        classes.push("modal-centered");
    }

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildModalClasses(combinedProps.value));

const handleClose = () => {
    emit("close");
    emit("update:show", false);
};

const handleBackdropClick = (event) => {
    if (event.target === modalRef.value) {
        handleClose();
    }
};

const handleEscape = (event) => {
    if (event.key === "Escape") {
        handleClose();
    }
};

onMounted(() => {
    document.addEventListener("keydown", handleEscape);
});

onUnmounted(() => {
    document.removeEventListener("keydown", handleEscape);
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            ref="modalRef"
            class="modal-backdrop"
            :class="{ 'modal-backdrop-blur': backdrop }"
            @click="handleBackdropClick"
        >
            <BaseTooltip
                :tooltip="tooltip"
                :tooltip-position="tooltipPosition"
            >
                <div :class="getClasses">
                    <div class="modal-content">
                        <div v-if="title || subtitle || $slots.header || closeButton" class="modal-header">
                            <slot name="header">
                                <div class="modal-title">
                                    <h3 v-if="title">{{ title }}</h3>
                                    <p v-if="subtitle">{{ subtitle }}</p>
                                </div>
                            </slot>
                            <button
                                v-if="closeButton"
                                class="modal-close"
                                @click="handleClose"
                            >
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="modal-body">
                            <slot />
                        </div>

                        <div v-if="$slots.footer" class="modal-footer">
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
                <template v-if="typeof tooltip === 'object'" #tooltip>
                    <slot name="tooltip" />
                </template>
            </BaseTooltip>
        </div>
    </Teleport>
</template>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-backdrop-blur {
    backdrop-filter: blur(5px);
}

.modal {
    position: relative;
    background-color: var(--color-base-900);
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    max-width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-centered {
    margin: auto;
}

.modal-sm {
    width: 24rem;
}

.modal-md {
    width: 28rem;
}

.modal-lg {
    width: 32rem;
}

.modal-xl {
    width: 36rem;
}

.modal-2xl {
    width: 42rem;
}

.modal-3xl {
    width: 48rem;
}

.modal-4xl {
    width: 56rem;
}

.modal-5xl {
    width: 64rem;
}

.modal-6xl {
    width: 72rem;
}

.modal-7xl {
    width: 80rem;
}

.modal-full {
    width: 100%;
    height: 100%;
}

.modal-content {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--color-base-800);
}

.modal-title {
    flex: 1;
}

.modal-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.modal-title p {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: var(--color-base-400);
}

.modal-close {
    background: none;
    border: none;
    padding: 0.5rem;
    cursor: pointer;
    color: var(--color-base-400);
    transition: color 0.2s;
}

.modal-close:hover {
    color: var(--color-base-200);
}

.modal-body {
    padding: 1rem;
    flex: 1;
    overflow-y: auto;
}

.modal-footer {
    padding: 1rem;
    border-top: 1px solid var(--color-base-800);
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}
</style>
