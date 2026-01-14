<script setup>
/**
 * EntityQuickEditModal Organism
 * 
 * @description
 * Modal pour l'édition rapide d'une entité unique dans un modal.
 * Utilise EntityQuickEdit.vue pour l'édition générique basée sur les descriptors.
 * 
 * @props {Object} entity - Données de l'entité à éditer
 * @props {String} entityType - Type d'entité (ex: 'attributes', 'resources', 'items')
 * @props {Boolean} open - Contrôle l'ouverture du modal
 * @props {Boolean} isAdmin - L'utilisateur a les droits d'édition
 * @props {Object} extraCtx - Contexte additionnel pour les descriptors
 * @props {Array} fields - Liste optionnelle de champs à afficher
 * @emit close - Événement émis lors de la fermeture
 * @emit submit - Événement émis lors de la soumission du formulaire avec le payload
 */
import { ref, watch, computed } from 'vue';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { resolveEntityViewComponentSync } from '@/Utils/entity/resolveEntityViewComponent';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    open: {
        type: Boolean,
        default: false
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    extraCtx: {
        type: Object,
        default: () => ({})
    },
    fields: {
        type: Array,
        default: null
    }
});

const emit = defineEmits(['close', 'submit']);

// Référence au composant QuickEdit pour accéder aux valeurs exposées
const quickEditRef = ref(null);

// Convertir l'entité unique en tableau pour EntityQuickEdit
const selectedEntities = computed(() => {
    return props.entity ? [props.entity] : [];
});

// Résoudre le composant QuickEdit pour cette entité (synchrone)
const QuickEditComponent = computed(() => {
    return resolveEntityViewComponentSync(props.entityType, 'quickedit');
});

// Réinitialiser quand le modal s'ouvre
watch(() => props.open, (isOpen) => {
    if (isOpen && quickEditRef.value?.resetFromSelection) {
        // Petit délai pour s'assurer que le composant est monté
        setTimeout(() => {
            quickEditRef.value?.resetFromSelection();
        }, 100);
    }
});

const handleClose = () => {
    emit('close');
};

const handleSubmit = () => {
    if (!quickEditRef.value) {
        emit('close');
        return;
    }
    
    // Utiliser buildPayload exposé par EntityQuickEdit
    const payload = quickEditRef.value.buildPayload();
    // Ajouter l'ID de l'entité unique
    payload.ids = [props.entity?.id].filter(Boolean);
    
    emit('submit', payload);
    emit('close');
};

const handleCancel = () => {
    emit('close');
};

// Obtenir le label de l'entité
const getEntityName = () => {
    return props.entity?.name || props.entity?.title || props.entity?.id || 'Entité';
};

// Vérifier si des champs ont été modifiés
const canSubmit = computed(() => {
    return quickEditRef.value?.modifiedFieldsCount > 0;
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
                Édition rapide : {{ getEntityName() }}
            </h3>
        </template>

        <div class="max-h-[70vh] overflow-y-auto pr-2" v-if="entity">
            <component
                v-if="QuickEditComponent"
                :is="QuickEditComponent"
                ref="quickEditRef"
                :entityType="entityType"
                :selectedEntities="selectedEntities"
                :isAdmin="isAdmin"
                :extraCtx="extraCtx"
                :fields="fields"
            />
            <div v-else class="text-sm text-error p-4 border border-error rounded">
                <p><strong>Composant QuickEdit non trouvé</strong></p>
                <p>EntityType: {{ entityType }}</p>
                <p>QuickEditComponent: {{ QuickEditComponent }}</p>
            </div>
        </div>

        <template #actions>
            <Btn variant="ghost" @click="handleCancel">
                Annuler
            </Btn>
            <Btn 
                variant="primary" 
                :disabled="!canSubmit"
                @click="handleSubmit"
            >
                <Icon source="fa-solid fa-check" class="mr-2" />
                Appliquer
            </Btn>
        </template>
    </Modal>
</template>
