<script setup>
/**
 * CreateEntityModal Organism
 * 
 * @description
 * Modal générique pour créer une nouvelle entité.
 * Utilise EntityEditForm avec isUpdating: false pour la création.
 * 
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {Object} fieldsConfig - Configuration des champs à afficher (optionnel)
 * @props {Object} defaultEntity - Entité par défaut avec valeurs initiales (optionnel)
 * @emit close - Événement émis lors de la fermeture
 * @emit created - Événement émis après création réussie
 */
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import EntityEditForm from './EntityEditForm.vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    entityType: {
        type: String,
        required: true
    },
    fieldsConfig: {
        type: Object,
        default: () => ({})
    },
    defaultEntity: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close', 'created']);

const notificationStore = useNotificationStore();
const viewMode = ref('large');

// Entité vide pour la création
const emptyEntity = computed(() => {
    return {
        id: null,
        ...props.defaultEntity
    };
});

// Nom de l'entité pour l'affichage
const entityTypeLabel = computed(() => {
    const labels = {
        item: 'objet',
        spell: 'sort',
        monster: 'monstre',
        npc: 'PNJ',
        classe: 'classe',
        panoply: 'panoplie',
        campaign: 'campagne',
        scenario: 'scénario',
        creature: 'créature',
        resource: 'ressource',
        consumable: 'consommable',
        attribute: 'attribut',
        capability: 'capacité',
        specialization: 'spécialisation',
        shop: 'boutique'
    };
    return labels[props.entityType] || props.entityType;
});

// Gestion de la fermeture
const handleClose = () => {
    emit('close');
};

// Gestion de la soumission
const handleSubmit = () => {
    // Recharger la page pour afficher la nouvelle entité
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    router.reload({
        only: [entityTypePlural],
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            emit('created');
            handleClose();
        }
    });
};

// Gestion de l'annulation
const handleCancel = () => {
    handleClose();
};

// Réinitialiser le formulaire quand le modal s'ouvre
watch(() => props.open, (isOpen) => {
    if (isOpen) {
        viewMode.value = 'large';
    }
});
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
                Créer un{{ entityTypeLabel.match(/^[aeiou]/i) ? 'e' : '' }} {{ entityTypeLabel }}
            </h3>
        </template>

        <div class="max-h-[70vh] overflow-y-auto pr-2">
            <EntityEditForm
                :entity="emptyEntity"
                :entity-type="entityType"
                :view-mode="viewMode"
                :fields-config="fieldsConfig"
                :is-updating="false"
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

