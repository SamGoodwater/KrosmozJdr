<script setup>
import { ref, computed, useSlots } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import Tooltip from "../feedback/Tooltip.vue";
import { imageExists, formatSizeToMB, validateFile, formatFileType } from "@/Utils/files";

const props = defineProps({
    modelValue: {
        type: [File, Array],
        default: null,
    },
    theme: {
        type: String,
        default: "",
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl"].includes(value),
    },
    color: {
        type: String,
        default: "secondary-800",
    },
    rounded: {
        type: String,
        default: "lg",
        validator: (value) => ["", "none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(value),
    },
    blur: {
        type: String,
        default: "lg",
        validator: (value) => ["", "none", "xs", "sm", "md", "lg", "xl", "2xl"].includes(value),
    },
    'box-shadow': {
        type: String,
        default: "md",
        validator: (value) => ["", "none", "xs", "sm", "md", "lg", "xl", "2xl", "3xl", "4xl"].includes(value),
    },
    opacity: {
        type: String,
        default: "",
    },
    bgColor: {
        type: String,
        default: "",
    },
    textColor: {
        type: String,
        default: "",
    },
    styled: {
        type: String,
        default: "",
        validator: (value) => ["", "ghost", "outline", "link"].includes(value),
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) => ["top", "right", "bottom", "left", "top-start", "top-end", "right-start", "right-end", "bottom-start", "bottom-end", "left-start", "left-end"].includes(value),
    },
    tooltip: {
        type: String,
        default: "Cliquez ou déposez un fichier ici",
    },
    label: {
        type: String,
        default: "",
    },
    helper: {
        type: String,
        default: "auto",
        validator: (value) => value === null || value === "" || value === "auto" || typeof value === "string",
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    accept: {
        type: String,
        default: "",
    },
    maxSize: {
        type: Number,
        default: 5242880, // 5MB par défaut
    },
    currentFile: {
        type: String,
        default: null,
    },
    showDeleteButton: {
        type: Boolean,
        default: true
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits([
    "update:modelValue",
    "error",
    "delete"
]);

const fileInput = ref(null);
const isDragging = ref(false);
const dragCounter = ref(0);
const isHovering = ref(false);

const slots = useSlots();

// Construction des classes CSS
const buildInputClasses = (themeProps, props) => {
    const classes = ["file-input", "w-full", "transition-all", "duration-300"];

    // Taille
    const size = props.size || themeProps.size || "md";
    classes.push(`file-input-${size}`);

    // Style (ghost, outline, etc.)
    const styled = props.styled || themeProps.styled;
    if (styled) {
        if (styled === "ghost") {
            classes.push("file-input-ghost");
        } else if (styled === "outline") {
            classes.push("bg-base-100/10");
        } else {
            classes.push(`bg-transparent border-transparent hover:bg-base-100/10`);
        }
    }

    // Arrondi
    const rounded = props.rounded || themeProps.rounded || "lg";
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    // Couleur
    const color = props.color || themeProps.color || "secondary-800";
    classes.push(`file-input-${color}`);

    // Box Shadow
    const boxShadow = props['box-shadow'] || themeProps['box-shadow'] || "md";
    if (boxShadow && boxShadow !== "none") {
        classes.push(`box-shadow-${boxShadow}`);
    }

    // Blur
    const blur = props.blur || themeProps.blur || "lg";
    if (blur && blur !== "none") {
        classes.push(`backdrop-blur-${blur}`);
    }

    // Opacité
    const opacity = props.opacity || themeProps.opacity;
    if (opacity) {
        classes.push(`opacity-${opacity}`);
    }

    // Couleur de fond
    const bgColor = props.bgColor || themeProps.bgColor;
    if (bgColor) {
        classes.push(`bg-${bgColor}`);
    }

    // Couleur du texte
    const textColor = props.textColor || themeProps.textColor;
    if (textColor) {
        classes.push(`text-${textColor}`);
    }

    // État d'erreur
    if (props.error) {
        classes.push("file-input-error");
    }

    return classes.join(" ");
};

const buildDropZoneClasses = computed(() => {
    const classes = [
        "relative",
        "w-full",
        "flex",
        "items-center",
        "justify-center",
        "transition-all",
        "duration-300",
        "ease-in-out",
    ];

    // Arrondi
    const rounded = props.rounded || themeProps.value.rounded || "lg";
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    // Style pendant le drag
    if (isDragging.value) {
        const color = props.color || themeProps.value.color || "secondary-800";
        classes.push(
            `box-shadow-lg`,
            `backdrop-blur-lg`,
            `bg-${color}/5`,
            "scale-102",
            "animate-pulse"
        );
    }

    return classes.join(" ");
});

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildInputClasses(themeProps.value, props));

// Gestion des fichiers
const handleFiles = (files) => {
    const validFiles = Array.from(files).filter(file => {
        const validation = validateFile(file, {
            maxSize: props.maxSize,
            accept: props.accept
        });

        if (!validation.isValid) {
            emit("error", validation.error);
            return false;
        }
        return true;
    });

    if (props.multiple) {
        emit("update:modelValue", validFiles);
    } else if (validFiles.length > 0) {
        console.log('Emitting file:', validFiles[0]);
        emit("update:modelValue", validFiles[0]);
    }
};

// Gestion du drag & drop
const handleDragEnter = (e) => {
    e.preventDefault();
    dragCounter.value++;
    isDragging.value = true;
};

const handleDragLeave = (e) => {
    e.preventDefault();
    dragCounter.value--;
    if (dragCounter.value === 0) {
        isDragging.value = false;
    }
};

const handleDrop = (e) => {
    e.preventDefault();
    isDragging.value = false;
    dragCounter.value = 0;
    handleFiles(e.dataTransfer.files);
};

// Gestion du clic sur l'image/avatar
const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleChange = (e) => {
    handleFiles(e.target.files);
};

// Fonction pour gérer la suppression
const handleDelete = (e) => {
    e.stopPropagation();
    emit('delete');
};

// Fonction pour vérifier si on doit afficher l'overlay
const shouldShowOverlay = computed(() => {
    return isHovering.value && slots.default;
});

// Fonction pour vérifier si on doit afficher le bouton de suppression
const shouldShowDeleteButton = computed(() => {
    return props.showDeleteButton && props.currentFile && slots.default;
});

// Nouveau computed pour le message du tooltip
const tooltipMessage = computed(() => {
    return props.tooltip || "Cliquez ou déposez un fichier ici";
});

// Nouveau computed pour les classes de l'overlay
const overlayClasses = computed(() => {
    return [
        "absolute",
        "inset-0",
        "flex",
        "items-center",
        "justify-center",
        "rounded-lg",
        "bg-gradient-to-t",
        "from-base-800/20",
        "to-transparent",
        "backdrop-blur-xs",
        "hover:backdrop-blur-sm",
        "transition-all",
        "duration-500",
        "opacity-0",
        "hover:opacity-100"
    ].join(" ");
});

// Computed pour le message d'aide automatique
const helperMessage = computed(() => {
    if (props.helper === null || props.helper === "") return null;
    if (props.helper !== "auto") return props.helper;

    const parts = [];

    if (props.accept) {
        const formats = props.accept.split(",")
            .map(formatFileType)
            .join(", ");
        parts.push(`Formats acceptés : ${formats}`);
    }

    if (props.maxSize) {
        const maxSizeMB = formatSizeToMB(props.maxSize);
        parts.push(`Taille maximale : ${maxSizeMB} Mo`);
    }

    return parts.length > 0 ? parts.join(" | ") : null;
});
</script>

<template>
    <div class="w-full">
        <!-- Label (avec support du slot) -->
        <div v-if="label || slots.label" class="mb-2 text-center">
            <slot name="label">
                <span class="text-base-content dark:text-base-content-dark">{{ label }}</span>
            </slot>
        </div>

        <!-- Zone de drop avec Tooltip -->
        <Tooltip :text="tooltipMessage" :placement="tooltipPosition">
            <div
                :class="buildDropZoneClasses"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                @dragover.prevent
                @drop="handleDrop"
                @mouseenter="isHovering = true"
                @mouseleave="isHovering = false"
            >
                <!-- Slot pour contenu personnalisé (image/avatar) -->
                <div v-if="slots.default" class="relative cursor-pointer w-full flex justify-center" @click="triggerFileInput">
                    <slot />

                    <!-- Overlay au survol -->
                    <div v-show="isHovering" :class="overlayClasses">
                        <div class="flex flex-col items-center gap-3">
                            <span class="text-content-dark text-shadow-md">
                                {{ currentFile ? 'Modifier le fichier' : 'Ajouter un fichier' }}
                            </span>

                            <!-- Bouton de suppression -->
                            <button
                                v-if="shouldShowDeleteButton"
                                @click.stop="handleDelete"
                                class="text-error-800/80 hover:text-error-600 transition-colors duration-300"
                                title="Supprimer le fichier"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Input file standard si pas de slot -->
                <input
                    v-show="!slots.default"
                    ref="fileInput"
                    type="file"
                    :class="getClasses"
                    :multiple="multiple"
                    :accept="accept"
                    @change="handleChange"
                />

                <!-- Overlay pendant le drag -->
                <div
                    v-if="isDragging"
                    class="absolute inset-0 flex items-center justify-center bg-gradient-to-t from-base-800/20 to-transparent backdrop-blur-lg rounded-lg animate-pulse"
                >
                    <span class="text-content-dark text-shadow-md font-medium">
                        Déposez votre fichier ici
                    </span>
                </div>
            </div>

            <template #content>
                {{ tooltipMessage }}
            </template>
        </Tooltip>

        <!-- Helper text (avec support du slot) -->
        <div v-if="helperMessage || slots.helper" class="mt-2 text-sm text-base-500">
            <slot name="helper">
                {{ helperMessage }}
            </slot>
        </div>

        <!-- Message d'erreur -->
        <div v-if="error" class="mt-2 text-sm text-error">
            {{ error }}
        </div>
    </div>
</template>

<style scoped>
.scale-102 {
    transform: scale(1.02);
}

.text-shadow-md {
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.file-input-bordered {
    border: 1px solid var(--color-secondary-200);
}
.file-input-bordered:hover {
    border: 1px solid var(--color-secondary-300);
}
.file-input-bordered:focus {
    border: 1px solid var(--color-secondary-400);
}
.file-input-bordered:focus-within {
    border: 1px solid var(--color-secondary-400);
}
.file-input-bordered:active {
    border: 1px solid var(--color-secondary-500);
}
.file-input-bordered:disabled {
    border: 1px solid var(--color-secondary-200);
}
.in-drag {
    box-shadow:
    0 0 1px 1px rgba(255, 255, 255, 0.50),
    0 0 3px 4px rgba(255, 255, 255, 0.10),
    0 0 5px 6px rgba(255, 255, 255, 0.05),
    inset 0 0 3px 4px rgba(255, 255, 255, 0.10),
    inset 0 0 5px 6px rgba(255, 255, 255, 0.05);
}
.hover-overlay {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}
.hover-overlay:hover {
    opacity: 1;
}
</style>
