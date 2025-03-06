<script setup>
import { ref, computed } from "vue";
import { extractTheme } from "@/Utils/extractTheme";
import Tooltip from "../feedback/Tooltip.vue";

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
        default: "",
        validator: (value) => ["", "xs", "sm", "md", "lg", "xl"].includes(value),
    },
    styled: {
        type: String,
        default: "",
        validator: (value) => ["", "ghost", "outline", "link"].includes(value),
    },
    color: {
        type: String,
        default: "",
    },
    bordered: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: String,
        default: "",
        validator: (value) => ["", "none", "sm", "md", "lg", "xl", "2xl", "3xl", "full"].includes(value),
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
    label: {
        type: String,
        default: "",
    },
    helperText: {
        type: String,
        default: "",
    },
    tooltip: {
        type: String,
        default: "",
    },
    tooltipPosition: {
        type: String,
        default: "bottom",
        validator: (value) => ["top", "right", "bottom", "left"].includes(value),
    },
    error: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue", "error"]);
const fileInput = ref(null);
const isDragging = ref(false);
const dragCounter = ref(0);

// Construction des classes CSS
const buildInputClasses = (themeProps, props) => {
    const classes = ["file-input", "w-full", "transition-all", "duration-200"];

    // Taille
    const size = props.size || themeProps.size || "md";
    classes.push(`file-input-${size}`);

    // Style (ghost, outline, etc.)
    const styled = props.styled || themeProps.styled;
    if (styled) {
        if (styled === "ghost") {
            classes.push("bg-transparent");
            classes.push("border-transparent");
            classes.push("hover:bg-base-100/10");
        } else if (styled === "outline") {
            classes.push("bg-base-100/10");
        } else {
            classes.push(`file-input-${styled}`);
        }
    }

    // Bordure
    const bordered = props.bordered ?? themeProps.bordered ?? false;
    if (bordered) {
        classes.push("file-input-bordered");
    }

    // Arrondi
    const rounded = props.rounded || themeProps.rounded;
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    // Couleur
    const color = props.color || themeProps.color || "primary";
    classes.push(`file-input-${color}`);

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
        "transition-all",
        "duration-300",
        "ease-in-out",
    ];

    // Arrondi
    const rounded = props.rounded || themeProps.value.rounded;
    if (rounded && rounded !== "none") {
        classes.push(`rounded-${rounded}`);
    }

    // Style pendant le drag
    if (isDragging.value) {
        const color = props.color || themeProps.value.color || "primary";
        classes.push(
            "ring-2",
            `ring-${color}`,
            "ring-opacity-50",
            `bg-${color}/5`
        );
    }

    return classes.join(" ");
});

const themeProps = computed(() => extractTheme(props.theme));
const getClasses = computed(() => buildInputClasses(themeProps.value, props));

// Gestion des fichiers
const validateFile = (file) => {
    if (props.maxSize && file.size > props.maxSize) {
        emit("error", `Le fichier ${file.name} dépasse la taille maximale autorisée`);
        return false;
    }

    if (props.accept) {
        const acceptedTypes = props.accept.split(",").map(type => type.trim());
        const fileType = file.type;
        const fileExtension = `.${file.name.split(".").pop()}`;

        if (!acceptedTypes.some(type =>
            type === fileType ||
            type === fileExtension ||
            (type.includes("/*") && fileType.startsWith(type.replace("/*", "")))
        )) {
            emit("error", `Le format du fichier ${file.name} n'est pas accepté`);
            return false;
        }
    }

    return true;
};

const handleFiles = (files) => {
    const validFiles = Array.from(files).filter(validateFile);

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

// Gestion du clic sur l'image/avatar
const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleChange = (e) => {
    handleFiles(e.target.files);
};
</script>

<template>
    <div>
        <!-- Fieldset avec label si présent -->
        <fieldset v-if="label" class="p-4 rounded-lg space-y-2 border">
            <legend class="px-2 text-base-600">{{ label }}</legend>

            <!-- Zone de drop avec Tooltip -->
            <Tooltip v-if="tooltip" :placement="tooltipPosition">
                <div
                    :class="buildDropZoneClasses"
                    @dragenter="handleDragEnter"
                    @dragleave="handleDragLeave"
                    @dragover.prevent
                    @drop="handleDrop"
                >
                    <!-- Slot pour image/avatar -->
                    <div v-if="$slots.default" @click="triggerFileInput" class="cursor-pointer">
                        <slot />
                    </div>

                    <!-- Input file standard si pas de slot -->
                    <input
                        v-show="!$slots.default"
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
                        class="absolute inset-0 flex items-center justify-center bg-primary/10 backdrop-blur-sm rounded-lg in-drag"
                    >
                        <span class="text-secondary-200 text-shadow-md font-medium">
                            Déposez vos fichiers ici
                        </span>
                    </div>
                </div>

                <template #content>
                    {{ tooltip }}
                </template>
            </Tooltip>

            <!-- Version sans tooltip -->
            <div
                v-else
                :class="buildDropZoneClasses"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div v-if="$slots.default" @click="triggerFileInput" class="cursor-pointer">
                    <slot />
                </div>

                <input
                    v-show="!$slots.default"
                    ref="fileInput"
                    type="file"
                    :class="getClasses"
                    :multiple="multiple"
                    :accept="accept"
                    @change="handleChange"
                />

                <div
                    v-if="isDragging"
                    class="absolute inset-0 flex items-center justify-center bg-primary/10 backdrop-blur-sm rounded-lg in-drag  "
                >
                    <span class="text-secondary-200 text-shadow-md font-medium">
                        Déposez vos fichiers ici
                    </span>
                </div>
            </div>

            <!-- Texte d'aide ou d'erreur -->
            <label v-if="helperText" class="text-sm text-base-500">{{ helperText }}</label>
            <label v-if="error" class="text-sm text-error">{{ error }}</label>
        </fieldset>

        <!-- Version sans fieldset -->
        <div v-else>
            <!-- Même contenu que précédemment, sans le fieldset -->
            <Tooltip v-if="tooltip" :placement="tooltipPosition">
                <div
                    :class="buildDropZoneClasses"
                    @dragenter="handleDragEnter"
                    @dragleave="handleDragLeave"
                    @dragover.prevent
                    @drop="handleDrop"
                >
                    <div v-if="$slots.default" @click="triggerFileInput" class="cursor-pointer">
                        <slot />
                    </div>

                    <input
                        v-show="!$slots.default"
                        ref="fileInput"
                        type="file"
                        :class="getClasses"
                        :multiple="multiple"
                        :accept="accept"
                        @change="handleChange"
                    />

                    <div
                        v-if="isDragging"
                        class="absolute inset-0 flex items-center justify-center bg-primary/10 backdrop-blur-sm rounded-lg in-drag"
                    >
                        <span class="text-secondary-200 text-shadow-md font-medium">
                            Déposez vos fichiers ici
                        </span>
                    </div>
                </div>

                <template #content>
                    {{ tooltip }}
                </template>
            </Tooltip>

            <div
                v-else
                :class="buildDropZoneClasses"
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                @dragover.prevent
                @drop="handleDrop"
            >
                <div v-if="$slots.default" @click="triggerFileInput" class="cursor-pointer">
                    <slot />
                </div>

                <input
                    v-show="!$slots.default"
                    ref="fileInput"
                    type="file"
                    :class="getClasses"
                    :multiple="multiple"
                    :accept="accept"
                    @change="handleChange"
                />

                <div
                    v-if="isDragging"
                    class="absolute inset-0 flex items-center justify-center bg-primary/10 backdrop-blur-sm rounded-lg in-drag"
                >
                    <span class="text-secondary-200 text-shadow-md font-medium">
                        Déposez vos fichiers ici
                    </span>
                </div>
            </div>

            <!-- Texte d'aide ou d'erreur -->
            <div v-if="helperText" class="text-sm text-base-500 mt-1">{{ helperText }}</div>
            <div v-if="error" class="text-sm text-error mt-1">{{ error }}</div>
        </div>
    </div>
</template>

<style scoped>
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
</style>
