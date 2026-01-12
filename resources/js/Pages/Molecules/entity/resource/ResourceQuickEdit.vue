<script setup>
/**
 * ResourceQuickEdit — Vue QuickEdit pour Resource
 * 
 * @description
 * Vue d'édition rapide pour une ou plusieurs ressources.
 * Utilise les descriptors pour générer les champs et useBulkEditPanel pour gérer
 * les valeurs différentes et les états indéterminés.
 * 
 * @props {Array} selectedEntities - Entités sélectionnées
 * @props {Boolean} isAdmin - L'utilisateur a les droits d'édition
 * @props {Object} extraCtx - Contexte additionnel pour les descriptors
 * @props {Array} fields - Liste optionnelle de champs à afficher
 */
import { computed, toRef } from 'vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue';
import TextareaCore from '@/Pages/Atoms/data-input/TextareaCore.vue';
import SelectCore from '@/Pages/Atoms/data-input/SelectCore.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import { useBulkEditPanel } from '@/Composables/entity/useBulkEditPanel';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { createFieldsConfigFromDescriptors, createBulkFieldMetaFromDescriptors } from '@/Utils/entity/descriptor-form';
import { useEntityFieldHelpers } from '@/Composables/entity/useEntityFieldHelpers';
import { useEntityFieldFilter } from '@/Composables/entity/useEntityFieldFilter';
import { getMapperForEntityType } from '@/Utils/Entity/MapperRegistry';

const props = defineProps({
    selectedEntities: {
        type: Array,
        default: () => [],
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
    extraCtx: {
        type: Object,
        default: () => ({}),
    },
    fields: {
        type: Array,
        default: null,
    },
});

// Note: Les événements 'change' et 'clear-field' sont définis mais non utilisés actuellement
// Ils sont conservés pour compatibilité future si nécessaire
const emit = defineEmits(['change', 'clear-field']);

// Contexte pour les descriptors
const ctx = computed(() => ({
    ...(props.extraCtx || {}),
    capabilities: { updateAny: props.isAdmin },
    meta: {
        ...(props.extraCtx || {}),
        capabilities: { updateAny: props.isAdmin },
    },
}));

// Obtenir les descriptors
const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));

// Générer les configs depuis les descriptors
const fieldsConfigAll = computed(() => createFieldsConfigFromDescriptors(descriptors.value, ctx.value));
const fieldMetaAll = computed(() => createBulkFieldMetaFromDescriptors(descriptors.value, ctx.value));

// Déterminer les champs à afficher
const fieldKeys = computed(() => {
    if (Array.isArray(props.fields) && props.fields.length) return props.fields;
    // Par défaut, tous les champs bulk-enabled
    return Object.keys(fieldMetaAll.value || {});
});

// Utiliser le composable pour filtrer les champs
const { isFieldVisible: isFieldVisibleForConfig, filterFields: filterFieldsForConfig } = useEntityFieldFilter(
    descriptors,
    ctx,
    { checkVisibility: true, checkEditability: false, isAdmin: false }
);

const { isFieldVisible: isFieldVisibleForMeta, filterFields: filterFieldsForMeta } = useEntityFieldFilter(
    descriptors,
    ctx,
    { checkVisibility: true, checkEditability: true, isAdmin: props.isAdmin }
);

// Filtrer les champs visibles pour fieldsConfig
const fieldsConfig = computed(() => {
    const visibleKeys = filterFieldsForConfig(fieldKeys.value || []);
    const out = {};
    for (const k of visibleKeys) {
        if (!fieldsConfigAll.value?.[k]) continue;
        out[k] = fieldsConfigAll.value[k];
    }
    return out;
});

// Filtrer les champs visibles et éditables pour fieldMeta
const fieldMeta = computed(() => {
    const visibleKeys = filterFieldsForMeta(fieldKeys.value || []);
    const out = {};
    for (const k of visibleKeys) {
        if (!fieldMetaAll.value?.[k]) continue;
        out[k] = fieldMetaAll.value[k];
    }
    return out;
});

// Utiliser useBulkEditPanel pour la logique d'agrégation et de gestion d'état
const {
    aggregate,
    form,
    dirty,
    placeholder: getPlaceholder,
    onChange,
    resetFromSelection,
} = useBulkEditPanel({
    selectedEntities: toRef(props, 'selectedEntities'),
    isAdmin: props.isAdmin,
    fieldMeta,
    mode: 'client',
    filteredIds: computed(() => []),
    entityType: 'resources',
});

// Utiliser le composable pour les helpers de champs
const { getFieldIcon: getFieldIconHelper, getFieldGroup, groupFieldsByGroup } = useEntityFieldHelpers(getResourceFieldDescriptors, ctx);

// Grouper les champs par groupe
const groupedFieldKeys = computed(() => {
    return groupFieldsByGroup(Object.keys(fieldsConfig.value || {}), 'Champs');
});

// Fonctions utilitaires pour les booléens
const getBoolChecked = (key) => {
    if (dirty?.[key]) {
        const v = form?.[key];
        return v === true || v === 1 || String(v) === '1';
    }
    const v = aggregate.value?.[key]?.value;
    return v === true || v === 1 || String(v) === '1';
};

const getBoolIndeterminate = (key) => {
    return !dirty?.[key] && aggregate.value?.[key]?.same === false;
};

// Vérifier si un champ est nullable
const isNullableBulkField = (key) => Boolean(fieldMeta.value?.[key]?.nullable);

// Vérifier si les options ont une valeur vide
const hasEmptyOption = (options) => {
    const arr = Array.isArray(options) ? options : [];
    return arr.some((o) => String(o?.value ?? '') === '');
};

// Utiliser getFieldIcon du composable (déjà défini ci-dessus)
const getFieldIcon = getFieldIconHelper;

// Gérer le changement d'un champ
const handleChange = (key, value) => {
    onChange(key, value);
    // Note: Événement 'change' émis pour compatibilité future (non utilisé actuellement)
    emit('change', { key, value });
};

// Réinitialiser un champ
const handleClearField = (key) => {
    dirty[key] = false;
    form[key] = '';
    // Note: Événement 'clear-field' émis pour compatibilité future (non utilisé actuellement)
    emit('clear-field', key);
};

// Nombre de champs modifiés (dirty)
const modifiedFieldsCount = computed(() => {
    return Object.values(dirty || {}).filter(Boolean).length;
});

// Construire le payload en utilisant le mapper
const buildPayload = () => {
    const mapper = getMapperForEntityType('resources');
    const payload = { ids: [] }; // Les IDs seront ajustés par le parent
    
    if (mapper && typeof mapper.fromBulkForm === 'function') {
        const bulkFormData = {};
        for (const key of Object.keys(dirty || {})) {
            if (dirty[key]) {
                bulkFormData[key] = form[key];
            }
        }
        const mappedData = mapper.fromBulkForm(bulkFormData);
        Object.assign(payload, mappedData);
    } else {
        // Fallback : utiliser directement les valeurs du form
        for (const key of Object.keys(dirty || {})) {
            if (dirty[key]) {
                payload[key] = form[key];
            }
        }
    }
    
    return payload;
};

// Exposer les valeurs nécessaires pour le parent (EntityQuickEditPanel)
defineExpose({
    resetFromSelection,
    modifiedFieldsCount,
    dirty,
    form,
    aggregate,
    buildPayload,
});
</script>

<template>
    <div class="space-y-5">
        <div v-if="!isAdmin" class="text-sm text-warning">
            Tu dois avoir les droits pour modifier.
        </div>

        <div v-for="group in groupedFieldKeys" :key="group.title" class="space-y-3">
            <div
                v-if="groupedFieldKeys.length > 1"
                class="divider my-0 text-base-content/60 font-semibold text-sm uppercase tracking-wide"
            >
                {{ group.title }}
            </div>

            <div
                v-for="key in group.keys"
                :key="key"
                class="form-control transition-all duration-200"
                :class="{ 'ring-2 ring-primary/30 rounded-md p-2 -m-2': dirty?.[key] }"
            >
                <label class="label">
                    <span class="label-text flex items-center gap-2">
                        <Icon
                            :source="getFieldIcon(key)"
                            size="xs"
                            class="text-primary-400"
                        />
                        <span :class="{ 'font-semibold text-primary': dirty?.[key] }">
                            {{ fieldsConfig[key]?.label }}
                        </span>
                        <transition
                            enter-active-class="transition-all duration-200 ease-out"
                            enter-from-class="opacity-0 scale-75"
                            enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition-all duration-150 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-75"
                        >
                            <Icon
                                v-if="dirty?.[key]"
                                source="fa-solid fa-circle-check"
                                alt="Modifié"
                                size="xs"
                                class="text-primary"
                            />
                        </transition>
                        <Tooltip
                            v-if="fieldsConfig[key]?.help"
                            :content="fieldsConfig[key]?.help"
                            placement="top"
                            color="neutral"
                        >
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs px-1"
                                :aria-label="`Info: ${fieldsConfig[key]?.label || ''}`"
                            >
                                <Icon source="fa-solid fa-circle-info" alt="Info" size="xs" />
                            </button>
                        </Tooltip>
                    </span>
                </label>

                <div v-if="fieldsConfig[key]?.help" class="text-xs opacity-70 mb-1">
                    {{ fieldsConfig[key]?.help }}
                </div>

                <!-- Checkbox (booléen) -->
                <div v-if="fieldsConfig[key]?.type === 'checkbox'" class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <ToggleCore
                            variant="glass"
                            size="sm"
                            color="primary"
                            :disabled="!isAdmin"
                            :model-value="getBoolChecked(key)"
                            :indeterminate="getBoolIndeterminate(key)"
                            @update:model-value="(v) => handleChange(key, v ? '1' : '0')"
                        />
                        <span class="text-xs opacity-70">
                            <template v-if="getBoolIndeterminate(key)">Valeurs différentes</template>
                            <template v-else>{{ getBoolChecked(key) ? 'Oui' : 'Non' }}</template>
                        </span>
                    </div>

                    <button
                        v-if="dirty?.[key]"
                        type="button"
                        class="btn btn-ghost btn-xs"
                        :disabled="!isAdmin"
                        title="Annuler la modification de ce champ"
                        @click="handleClearField(key)"
                    >
                        <Icon source="fa-solid fa-rotate-left" alt="Réinitialiser" size="xs" />
                    </button>
                </div>

                <!-- Select avec recherche -->
                <SelectSearchField
                    v-else-if="fieldsConfig[key]?.type === 'select' && fieldsConfig[key]?.searchable"
                    :label="''"
                    :options="fieldsConfig[key]?.options || []"
                    :placeholder="fieldsConfig[key]?.placeholder || getPlaceholder(aggregate[key]?.same)"
                    :required="fieldsConfig[key]?.required"
                    :disabled="!isAdmin"
                    :size="'sm'"
                    :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
                    @update:model-value="(v) => handleChange(key, v)"
                />

                <!-- Select standard -->
                <SelectCore
                    v-else-if="fieldsConfig[key]?.type === 'select'"
                    class="w-full"
                    variant="glass"
                    size="sm"
                    color="primary"
                    :disabled="!isAdmin"
                    :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
                    @update:model-value="(v) => handleChange(key, v)"
                >
                    <option value="" disabled hidden>
                        {{ getPlaceholder(aggregate[key]?.same) }}
                    </option>
                    <option
                        v-if="isNullableBulkField(key) && !hasEmptyOption(fieldsConfig[key]?.options)"
                        value=""
                    >
                        —
                    </option>
                    <option
                        v-for="opt in fieldsConfig[key]?.options || []"
                        :key="String(opt.value)"
                        :value="String(opt.value)"
                    >
                        {{ opt.label }}
                    </option>
                </SelectCore>

                <!-- Textarea -->
                <TextareaCore
                    v-else-if="fieldsConfig[key]?.type === 'textarea'"
                    class="w-full"
                    variant="glass"
                    size="sm"
                    color="primary"
                    :disabled="!isAdmin"
                    :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
                    @update:model-value="(v) => handleChange(key, v)"
                    :placeholder="fieldsConfig[key]?.placeholder || getPlaceholder(aggregate[key]?.same)"
                    :rows="fieldsConfig[key]?.rows"
                />

                <!-- Number/Text -->
                <InputCore
                    v-else-if="fieldsConfig[key]?.type !== 'file'"
                    class="w-full"
                    :type="fieldsConfig[key]?.type === 'number' ? 'number' : 'text'"
                    variant="glass"
                    size="sm"
                    color="primary"
                    :disabled="!isAdmin"
                    :model-value="dirty[key] ? form[key] : (aggregate[key]?.same ? String(aggregate[key]?.value ?? '') : '')"
                    @update:model-value="(v) => handleChange(key, v)"
                    :placeholder="fieldsConfig[key]?.placeholder || getPlaceholder(aggregate[key]?.same)"
                    :step="fieldsConfig[key]?.step"
                    :min="fieldsConfig[key]?.min"
                    :max="fieldsConfig[key]?.max"
                />

                <!-- File (non supporté en bulk) -->
                <InputCore
                    v-else
                    class="w-full"
                    type="file"
                    variant="glass"
                    size="sm"
                    color="primary"
                    :disabled="true"
                    title="Upload de fichier non supporté en bulk (payload JSON). Utilise un champ URL ou l'édition unitaire."
                />
            </div>
        </div>
    </div>
</template>
