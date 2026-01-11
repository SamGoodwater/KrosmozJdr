<script setup>
/**
 * EntityQuickEditModal Organism
 * 
 * @description
 * Modal pour l'édition rapide d'une entité unique dans un modal.
 * Utilise EntityEditForm pour l'édition.
 * 
 * @props {Object} entity - Données de l'entité à éditer
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {Object} fieldsConfig - Configuration des champs à afficher
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @emit close - Événement émis lors de la fermeture
 * @emit submit - Événement émis lors de la soumission du formulaire
 */
import { ref, watch } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import { getEntityRouteConfig } from '@/Composables/entity/entityRouteRegistry';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    fieldsConfig: {
        type: Object,
        required: true
    },
    open: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close', 'submit']);

const viewMode = ref('large');

// Réinitialiser le mode d'affichage quand le modal s'ouvre
watch(() => props.open, (isOpen) => {
    if (isOpen) {
        viewMode.value = 'large';
    }
});

const handleClose = () => {
    emit('close');
};

const handleSubmit = () => {
    emit('submit');
    emit('close');
};

const handleCancel = () => {
    emit('close');
};

// Obtenir la configuration de route pour l'entité
const routeConfig = getEntityRouteConfig(props.entityType);
const routeNameBase = routeConfig?.show?.name?.replace(/\.show$/, '') || `entities.${props.entityType}s`;
const routeParamKey = routeConfig?.show?.paramKey || props.entityType;

// Obtenir le label de l'entité
const getEntityName = () => {
    return props.entity?.name || props.entity?.title || props.entity?.id || 'Entité';
};
</script>

<template>
    <Modal 
        :open="open" 
        size="xl" 
        placement="middle-center"
        close-on-esc
        @close="handleClose"
    >
        <template #header>
            <h3 class="text-2xl font-bold text-primary-100">
                Modifier {{ getEntityName() }}
            </h3>
        </template>

        <div class="max-h-[70vh] overflow-y-auto pr-2" v-if="entity">
            <EntityEditForm
                :entity="entity"
                :entity-type="entityType"
                :view-mode="viewMode"
                :fields-config="fieldsConfig"
                :is-updating="true"
                :route-name-base="routeNameBase"
                :route-param-key="routeParamKey"
                @submit="handleSubmit"
                @cancel="handleCancel"
                @update:view-mode="viewMode = $event"
            />
        </div>

        <template #actions>
            <!-- Les actions sont gérées par EntityEditForm -->
        </template>
    </Modal>
</template>
