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
 * @emit close - Événement émis lors de la fermeture
 */
import { computed } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewLarge from '@/Pages/Molecules/entity/EntityViewLarge.vue';
import EntityViewCompact from '@/Pages/Molecules/entity/EntityViewCompact.vue';
import EntityViewMinimal from '@/Pages/Molecules/entity/EntityViewMinimal.vue';
import EntityViewText from '@/Pages/Molecules/entity/EntityViewText.vue';

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
        default: 'large',
        validator: (v) => ['large', 'compact', 'minimal', 'text'].includes(v)
    },
    open: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const modalSize = computed(() => {
    const sizes = {
        large: 'xl',
        compact: 'lg',
        minimal: 'md',
        text: 'sm'
    };
    return sizes[props.view] || 'xl';
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
                v-if="view === 'large'"
                :entity="entity"
                :entity-type="entityType" />
            
            <EntityViewCompact 
                v-else-if="view === 'compact'"
                :entity="entity"
                :entity-type="entityType" />
            
            <EntityViewMinimal 
                v-else-if="view === 'minimal'"
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

