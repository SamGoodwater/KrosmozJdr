<script setup>
/**
 * ResourceEditLarge — Vue d'édition Large pour Resource
 * 
 * @description
 * Vue d'édition complète d'une ressource avec tous les champs.
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

// Initialiser le formulaire avec les données de la ressource
const form = useForm(
    initializeFormFromEntity(props.resource, effectiveFieldsConfig.value)
);

// Utiliser le composable pour les helpers de champs
const { getFieldIcon, getFieldGroup, groupFieldsByGroup } = useEntityFieldHelpers(getResourceFieldDescriptors, props.ctx);

// Grouper les champs par groupe
const groupedFields = computed(() => {
    return groupFieldsByGroup(Object.keys(effectiveFieldsConfig.value));
});

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
    <div class="space-y-6">
        <form @submit.prevent="handleSubmit" class="space-y-6">
            <div v-for="group in groupedFields" :key="group.title" class="space-y-4">
                <div
                    v-if="groupedFields.length > 1"
                    class="divider my-0 text-base-content/60 font-semibold text-sm uppercase tracking-wide"
                >
                    {{ group.title }}
                </div>

                <EntityFormField
                    v-for="key in group.keys"
                    :key="key"
                    :field-key="key"
                    :field-config="{
                        ...effectiveFieldsConfig[key],
                        icon: getFieldIcon(key),
                    }"
                    v-model="form[key]"
                    :error="form.errors[key]"
                />
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-base-300">
                <button
                    type="button"
                    class="btn btn-ghost"
                    @click="handleCancel"
                >
                    Annuler
                </button>
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Enregistrement...</span>
                    <span v-else>{{ isUpdating ? 'Mettre à jour' : 'Créer' }}</span>
                </button>
            </div>
        </form>
    </div>
</template>
