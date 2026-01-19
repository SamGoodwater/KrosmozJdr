<script setup>
/**
 * EntityFormField — Composant Molecule pour le rendu d'un champ de formulaire d'entité
 * 
 * @description
 * Composant réutilisable qui encapsule la logique de rendu des différents types de champs
 * (text, number, textarea, select, checkbox, file) pour les formulaires d'édition d'entités.
 * 
 * @props {String} fieldKey - Clé unique du champ
 * @props {Object} fieldConfig - Configuration du champ depuis les descriptors
 * @props {any} modelValue - Valeur du champ (v-model)
 * @props {String} [size] - Taille du champ ('sm', 'md', 'lg'), défaut 'md'
 * @props {String} [error] - Message d'erreur de validation
 * 
 * @emit update:modelValue - Événement émis lors du changement de valeur
 * 
 * @example
 * <EntityFormField
 *   field-key="name"
 *   :field-config="fieldsConfig.name"
 *   v-model="form.name"
 *   :error="form.errors.name"
 * />
 */
import { computed, ref } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import TextareaField from '@/Pages/Molecules/data-input/TextareaField.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import FileField from '@/Pages/Molecules/data-input/FileField.vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
import LevelBadge from '@/Pages/Molecules/data-display/LevelBadge.vue';

const props = defineProps({
    fieldKey: {
        type: String,
        required: true,
    },
    fieldConfig: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, Number, Boolean, Array, File, FileList],
        default: null,
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    error: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['update:modelValue']);

// Valeur locale pour v-model
const localValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

// Déterminer le type de champ
const fieldType = computed(() => props.fieldConfig?.type || 'text');

// Vérifier si c'est un champ de type number
const isNumberField = computed(() => fieldType.value === 'number');

// Afficher un badge de preview pour les champs "niveau"
const isLevelLikeField = computed(() => {
    const k = String(props.fieldKey || '');
    return k === 'level' || k === 'min_level' || k === 'max_level';
});

// Vérifier si c'est un champ de type text
const isTextField = computed(() => fieldType.value === 'text' || (!['textarea', 'select', 'file', 'number', 'checkbox'].includes(fieldType.value)));

// Vérifier si c'est un champ de type checkbox
const isCheckboxField = computed(() => fieldType.value === 'checkbox');

// Vérifier si c'est un champ de type textarea
const isTextareaField = computed(() => fieldType.value === 'textarea');

// Vérifier si c'est un champ de type select
const isSelectField = computed(() => fieldType.value === 'select');

// Vérifier si c'est un champ de type file
const isFileField = computed(() => fieldType.value === 'file');

// Upload (optionnel) pour les FileFields: si fieldConfig.uploadUrl est fourni, on upload immédiatement
const uploading = ref(false);
const uploadError = ref(null);

const currentFilePath = computed(() => {
    const v = props.modelValue;
    return (typeof v === 'string' && v) ? v : null;
});

/**
 * Upload un fichier vers un endpoint qui retourne { success:true, url }.
 *
 * @param {File} file
 * @param {string} url
 * @returns {Promise<string|null>} URL de fichier ou null
 */
const uploadFile = async (file, url) => {
    const endpoint = String(url || '').trim();
    if (!endpoint) return null;
    if (!file || typeof file !== 'object') return null;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;
    if (!csrf) {
        uploadError.value = "Token CSRF introuvable. Recharge la page.";
        return null;
    }

    try {
        uploading.value = true;
        uploadError.value = null;
        const formData = new FormData();
        formData.append('file', file);

        const res = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                Accept: 'application/json',
            },
            body: formData,
        });

        const data = await res.json().catch(() => null);
        if (!res.ok || !data?.success || !data?.url) {
            uploadError.value = data?.message || "Upload: erreur.";
            return null;
        }

        return String(data.url);
    } catch (e) {
        uploadError.value = "Upload: " + (e?.message || "unknown");
        return null;
    } finally {
        uploading.value = false;
    }
};

const handleFileUpdate = async (v) => {
    // suppression explicite
    if (v === null || typeof v === 'undefined' || v === '') {
        localValue.value = '';
        return;
    }

    // Extraction du File (FileField peut renvoyer FileList)
    const file = (v instanceof File)
        ? v
        : (v && typeof v === 'object' && 'length' in v ? (Array.from(v)[0] || null) : null);

    if (!file) {
        localValue.value = v;
        return;
    }

    const uploadUrl = props.fieldConfig?.uploadUrl;
    if (uploadUrl) {
        const url = await uploadFile(file, uploadUrl);
        if (url) localValue.value = url;
        return;
    }

    // Fallback: laisser passer le File tel quel (utile dans certains formulaires non-bulk)
    localValue.value = file;
};
</script>

<template>
    <div class="form-control">

        <!-- Text -->
        <InputField
            v-if="isTextField"
            v-model="localValue"
            :label="fieldConfig?.label"
            type="text"
            :placeholder="fieldConfig?.placeholder"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :validation="error ? { state: 'error', message: error } : null"
            :size="size"
        />

        <!-- Number -->
        <InputField
            v-else-if="isNumberField"
            v-model="localValue"
            :label="fieldConfig?.label"
            type="number"
            :placeholder="fieldConfig?.placeholder"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :step="fieldConfig?.step"
            :min="fieldConfig?.min"
            :max="fieldConfig?.max"
            :validation="error ? { state: 'error', message: error } : null"
            :size="size"
        >
            <template v-if="isLevelLikeField" #overEnd>
                <div class="pointer-events-none">
                    <LevelBadge :level="localValue" size="sm" variant="soft" :glassy="true" />
                </div>
            </template>
        </InputField>

        <!-- Textarea -->
        <TextareaField
            v-else-if="isTextareaField"
            v-model="localValue"
            :label="fieldConfig?.label"
            :placeholder="fieldConfig?.placeholder"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :rows="fieldConfig?.rows"
            :validation="error ? { state: 'error', message: error } : null"
            :size="size"
        />

        <!-- Select -->
        <SelectField
            v-else-if="isSelectField && !fieldConfig?.searchable"
            v-model="localValue"
            :label="fieldConfig?.label"
            :options="fieldConfig?.options || []"
            :placeholder="fieldConfig?.placeholder"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :validation="error ? { state: 'error', message: error } : null"
            :size="size"
            variant="glass"
        />

        <!-- Select avec recherche -->
        <SelectSearchField
            v-else-if="isSelectField && fieldConfig?.searchable"
            v-model="localValue"
            :label="fieldConfig?.label"
            :options="fieldConfig?.options || []"
            :placeholder="fieldConfig?.placeholder"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :validation="error ? { state: 'error', message: error } : null"
            :size="size"
        />

        <!-- Checkbox -->
        <div v-else-if="isCheckboxField" class="flex items-center gap-2">
            <ToggleCore
                :model-value="Boolean(localValue)"
                :disabled="disabled || fieldConfig?.disabled"
                :size="size"
                @update:model-value="localValue = $event"
            />
            <span :class="size === 'sm' ? 'text-sm' : 'text-base'">{{ fieldConfig?.label }}</span>
        </div>

        <!-- File -->
        <FileField
            v-else-if="isFileField"
            :model-value="localValue"
            :label="fieldConfig?.label"
            :accept="fieldConfig?.accept"
            :multiple="fieldConfig?.multiple"
            :required="fieldConfig?.required"
            :disabled="disabled || fieldConfig?.disabled"
            :validation="error ? { state: 'error', message: error } : null"
            :current-path="currentFilePath"
            @update:model-value="handleFileUpdate"
        />

        <div v-if="isFileField && uploading" class="mt-1 text-xs opacity-70">
            Upload en cours…
        </div>
        <div v-if="isFileField && uploadError" class="mt-1 text-xs text-error">
            {{ uploadError }}
        </div>

        <!-- Help text -->
        <div v-if="fieldConfig?.help" class="label">
            <span class="label-text-alt text-xs opacity-70">
                {{ fieldConfig.help }}
            </span>
        </div>

        <!-- Error message -->
        <div v-if="error" class="label">
            <span class="label-text-alt text-error">
                {{ error }}
            </span>
        </div>
    </div>
</template>
