<script setup>
import { ref, computed, useSlots, useAttrs } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";
import { imageExists, formatSizeToMB, validateFile, formatFileType } from "@/Utils/file/File";
import InputLabel from '@/Pages/Atoms/inputs/InputLabel.vue';
import InputError from '@/Pages/Atoms/inputs/InputError.vue';
import BaseTooltip from '@/Pages/Atoms/feedback/BaseTooltip.vue';

const props = defineProps({
    ...commonProps,
    modelValue: {
        type: [File, Array],
        default: null,
    },
    label: {
        type: String,
        default: "",
    },
    styled: {
        type: String,
        default: "",
        validator: (value) => ["", "ghost", "outline", "link"].includes(value),
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
    useInputLabel: {
        type: Boolean,
        default: true,
    },
    useInputError: {
        type: Boolean,
        default: true,
    },
    inputLabel: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
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
const attrs = useAttrs();
const slots = useSlots();

// Générer un ID unique pour le composant
const componentId = computed(() => attrs.id || `file-input-${Math.random().toString(36).substr(2, 9)}`);

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));

const buildInputClasses = (props) => {
    const classes = ["file-input", "w-full", "transition-all", "duration-300"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // État d'erreur
    if (props.error) {
        classes.push("file-input-error");
    }

    return classes.join(" ");
};

const getClasses = computed(() => buildInputClasses(combinedProps.value));

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

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleChange = (e) => {
    handleFiles(e.target.files);
};

const handleDelete = (e) => {
    e.stopPropagation();
    emit('delete');
};

const shouldShowOverlay = computed(() => {
    return isHovering.value && slots.default;
});

const shouldShowDeleteButton = computed(() => {
    return props.showDeleteButton && props.currentFile && slots.default;
});

const tooltipMessage = computed(() => {
    return props.tooltip || "Cliquez ou déposez un fichier ici";
});

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
        <InputLabel v-if="useInputLabel" :for="componentId" :value="inputLabel || label || 'Fichier'">
            <template v-if="$slots.inputLabel">
                <slot name="inputLabel" />
            </template>
        </InputLabel>

        <BaseTooltip
            :tooltip="tooltip"
            :tooltip-position="tooltipPosition"
        >
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
                            <span class="text-content-dark text-shadow-lg">
                                {{ currentFile ? 'Modifier le fichier' : 'Ajouter un fichier' }}
                            </span>

                            <!-- Bouton de suppression -->
                            <button
                                v-if="shouldShowDeleteButton"
                                @click.stop="handleDelete"
                                class="text-error-800/80 text-xl text-shadow-lg hover:text-error-600 transition-colors duration-300"
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
                    :id="componentId"
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

            <template v-if="typeof tooltip === 'object'" #tooltip>
                <slot name="tooltip" />
            </template>
        </BaseTooltip>

        <!-- Helper text -->
        <div v-if="helperMessage || slots.helper" class="mt-2 text-sm text-base-500">
            <slot name="helper">
                {{ helperMessage }}
            </slot>
        </div>

        <!-- Message d'erreur -->
        <InputError v-if="useInputError" :message="errorMessage || error" class="mt-2" />
    </div>
</template>

<style scoped>
.scale-102 {
    transform: scale(1.02);
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
