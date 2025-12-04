<script setup>
/**
 * Composant de sélection du type d'entité avec affichage des limites
 */
import { computed, watch } from 'vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

const props = defineProps({
    modelValue: {
        type: String,
        required: true,
    },
    entityTypes: {
        type: Array,
        required: true,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

// Variable pour le mode développement
const isDev = import.meta.env.DEV;

const selectedEntity = computed(() => {
    if (!props.entityTypes || props.entityTypes.length === 0) {
        return null;
    }
    const found = props.entityTypes.find(e => e.value === props.modelValue);
    if (isDev && found) {
        console.log('EntityTypeSelector - selectedEntity found:', found);
    } else if (isDev && !found) {
        console.log('EntityTypeSelector - selectedEntity NOT found for:', props.modelValue, 'Available:', props.entityTypes.map(e => e.value));
    }
    return found;
});

const entityTypeOptions = computed(() => {
    if (!props.entityTypes || props.entityTypes.length === 0) {
        return [];
    }
    return props.entityTypes.map(e => ({
        value: e.value,
        label: e.label,
    }));
});

// Debug: surveiller les changements (à retirer en production)
if (isDev) {
    watch(() => props.entityTypes, (newVal) => {
        console.log('EntityTypeSelector - entityTypes changed:', newVal);
    }, { deep: true });

    watch(() => entityTypeOptions.value, (newVal) => {
        console.log('EntityTypeSelector - entityTypeOptions:', newVal);
    }, { deep: true });
}
</script>

<template>
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
        <div class="flex-1 min-w-0">
            <label class="label text-primary-100 font-semibold mb-1 text-sm">
                Type d'entité
            </label>
            <SelectField
                :model-value="modelValue"
                @update:model-value="emit('update:modelValue', $event)"
                :options="entityTypeOptions"
                :disabled="loading || entityTypeOptions.length === 0"
                placeholder="Sélectionner un type"
                size="sm"
            />
            <!-- Debug info -->
            <div v-if="entityTypeOptions.length === 0" class="text-xs text-warning mt-1">
                Aucune option disponible
            </div>
        </div>

        <div v-if="selectedEntity" class="flex items-center gap-2 text-sm flex-shrink-0">
            <Icon :source="`fa-solid ${selectedEntity.icon}`" :alt="selectedEntity.label" pack="solid" class="text-primary-300" />
            <span class="text-primary-200 font-medium">{{ selectedEntity.label }}</span>
            <Badge color="info" size="sm" :content="`${selectedEntity.maxId} max`" />
        </div>
    </div>
</template>

