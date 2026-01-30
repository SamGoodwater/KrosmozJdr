<script setup>
/**
 * ResourceEditCompact — Vue d'édition Compact pour Resource
 * 
 * @description
 * Vue d'édition condensée d'une ressource avec les champs essentiels uniquement.
 * Utilise les descriptors pour générer les champs de formulaire.
 * 
 * @props {Object} resource - Instance du modèle Resource
 * @props {Object} fieldsConfig - Configuration des champs depuis les descriptors
 * @props {Object} ctx - Contexte pour les descriptors (options dynamiques, permissions)
 * @props {Boolean} isUpdating - Mode édition (true) ou création (false)
 */
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import EntityFormField from '@/Pages/Molecules/entity/EntityFormField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { createFieldsConfigFromDescriptors } from '@/Utils/entity/descriptor-form';
import { initializeFormFromEntity } from '@/Utils/entity/form-helpers';
import { useEntityFieldHelpers } from '@/Composables/entity/useEntityFieldHelpers';
import { useEntityFormSubmit } from '@/Composables/entity/useEntityFormSubmit';

const props = defineProps({
    resource: {
        type: Object,
        required: true,
    },
    fieldsConfig: {
        type: Object,
        default: () => ({}),
    },
    ctx: {
        type: Object,
        default: () => ({}),
    },
    isUpdating: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['submit', 'cancel']);

// Générer fieldsConfig depuis les descriptors si non fourni
const effectiveFieldsConfig = computed(() => {
    if (Object.keys(props.fieldsConfig || {}).length > 0) {
        return props.fieldsConfig;
    }
    const descriptors = getResourceFieldDescriptors(props.ctx);
    return createFieldsConfigFromDescriptors(descriptors, props.ctx);
});

// Champs essentiels pour la vue compacte
const compactFields = computed(() => [
    'name',
    'resource_type',
    'level',
    'price',
    'rarity',
    'state',
    'read_level',
    'write_level',
].filter(key => effectiveFieldsConfig.value[key]));

// Initialiser le formulaire avec les données de la ressource
const form = useForm(
    initializeFormFromEntity(props.resource, effectiveFieldsConfig.value, compactFields.value)
);

// Utiliser le composable pour les helpers de champs
const { getFieldIcon } = useEntityFieldHelpers(getResourceFieldDescriptors, props.ctx);

// Utiliser le composable pour la soumission
const { handleSubmit } = useEntityFormSubmit({
    form,
    resource: props.resource,
    isUpdating: props.isUpdating,
    entityType: 'resource',
    onSuccess: () => emit('submit'),
});

// Gérer l'annulation
const handleCancel = () => {
    emit('cancel');
};
</script>

<template>
    <div class="space-y-4">
        <form @submit.prevent="handleSubmit" class="space-y-4">
            <EntityFormField
                v-for="key in compactFields"
                :key="key"
                :field-key="key"
                :field-config="{
                    ...effectiveFieldsConfig[key],
                    icon: getFieldIcon(key),
                }"
                v-model="form[key]"
                :error="form.errors[key]"
                size="sm"
            />

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2 pt-3 border-t border-base-300">
                <button
                    type="button"
                    class="btn btn-ghost btn-sm"
                    @click="handleCancel"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="btn btn-primary btn-sm"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">...</span>
                    <span v-else>{{ isUpdating ? 'Mettre à jour' : 'Créer' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
