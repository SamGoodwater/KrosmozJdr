<script setup>
/**
 * EntityModal Organism
 * 
 * @description
 * Modal pour afficher une entité avec les 4 vues possibles
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {String} view - Vue à afficher ('large', 'compact', 'minimal', 'text'), défaut 'large'
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Boolean} useStoredFormat - Utiliser le format stocké dans localStorage (défaut: true)
 * @emit close - Événement émis lors de la fermeture
 */
import { computed } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewLarge from '@/Pages/Molecules/entity/EntityViewLarge.vue';
import EntityViewCompact from '@/Pages/Molecules/entity/EntityViewCompact.vue';
import EntityViewMinimal from '@/Pages/Molecules/entity/EntityViewMinimal.vue';
import EntityViewText from '@/Pages/Molecules/entity/EntityViewText.vue';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    view: {
        type: String,
        default: null,
        validator: (v) => !v || ['large', 'compact', 'minimal', 'text'].includes(v)
    },
    open: {
        type: Boolean,
        default: false
    },
    useStoredFormat: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['close']);

// Utiliser le format stocké si useStoredFormat est true et que view n'est pas fourni
const { viewFormat } = useEntityViewFormat(props.entityType);
const currentView = computed(() => {
    if (props.view) {
        return props.view;
    }
    if (props.useStoredFormat) {
        return viewFormat.value;
    }
    return 'large';
});

const modalSize = computed(() => {
    const sizes = {
        large: 'xl',
        compact: 'lg',
        minimal: 'md',
        text: 'sm'
    };
    return sizes[currentView.value] || 'xl';
});

const handleClose = () => {
    emit('close');
};

/**
 * Récupère le nom de l'entité en gérant les modèles et objets bruts
 */
const getEntityName = () => {
    // Si c'est une instance de modèle, utiliser le getter name
    if (props.entity && typeof props.entity._data !== 'undefined') {
        return props.entity.name || props.entity.title || 'Entité';
    }
    // Sinon, accès direct
    return props.entity?.name || props.entity?.title || 'Entité';
};
</script>

<template>
    <Modal 
        :open="open" 
        :size="modalSize"
        placement="middle"
        animation="fade"
        @close="handleClose">
        
        <template #header>
            <h3 class="text-lg font-bold text-primary-100">
                {{ getEntityName() }}
            </h3>
        </template>

        <div>
            <EntityViewLarge 
                v-if="currentView === 'large'"
                :entity="entity"
                :entity-type="entityType" />
            
            <EntityViewCompact 
                v-else-if="currentView === 'compact'"
                :entity="entity"
                :entity-type="entityType" />
            
            <EntityViewMinimal 
                v-else-if="currentView === 'minimal'"
                :entity="entity"
                :entity-type="entityType" />
            
            <EntityViewText 
                v-else
                :entity="entity"
                :entity-type="entityType" />
        </div>

        <template #actions>
            <Btn @click="handleClose">Fermer</Btn>
        </template>
    </Modal>
</template>

