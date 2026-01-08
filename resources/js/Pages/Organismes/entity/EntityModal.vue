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
import { router } from '@inertiajs/vue3';
import Modal from '@/Pages/Molecules/action/Modal.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityViewLarge from '@/Pages/Molecules/entity/EntityViewLarge.vue';
import EntityViewCompact from '@/Pages/Molecules/entity/EntityViewCompact.vue';
import EntityViewMinimal from '@/Pages/Molecules/entity/EntityViewMinimal.vue';
import EntityViewText from '@/Pages/Molecules/entity/EntityViewText.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useEntityViewFormat } from '@/Composables/store/useEntityViewFormat';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';

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

const emit = defineEmits(['close', 'edit', 'quick-edit', 'expand', 'copy-link', 'download-pdf', 'refresh', 'delete']);

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

// Handlers pour les actions
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf(props.entityType);

const entityTypeKey = computed(() => {
    // Normaliser le type d'entité (ex: 'resources' -> 'resource')
    const type = props.entityType;
    if (type.endsWith('s')) {
        return type.slice(0, -1);
    }
    return type;
});

const handleAction = async (actionKey, entity) => {
    const targetEntity = entity || props.entity;
    const entityId = targetEntity?.id ?? props.entity?.id ?? null;
    if (!entityId) return;

    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${entityTypeKey.value}s`;

    switch (actionKey) {
        case 'quick-edit':
            emit('quick-edit', targetEntity);
            break;

        case 'expand':
            // Expand depuis un modal : redirige vers view (page complète)
            router.visit(route(`entities.${entityTypePlural}.show`, { [entityTypeKey.value]: entityId }));
            emit('expand', targetEntity);
            handleClose(); // Fermer le modal après redirection
            break;

        case 'copy-link': {
            const cfg = getEntityRouteConfig(entityTypeKey.value);
            const url = resolveEntityRouteUrl(entityTypeKey.value, 'show', entityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'entité copié !");
            }
            emit('copy-link', targetEntity);
            break;
        }

        case 'download-pdf':
            await downloadPdf(entityId);
            emit('download-pdf', targetEntity);
            break;

        case 'refresh':
            router.reload({ only: [entityTypePlural] });
            emit('refresh', targetEntity);
            break;

        case 'delete':
            emit('delete', targetEntity);
            break;
    }
};
</script>

<template>
    <Modal 
        :open="open" 
        :size="modalSize"
        placement="middle-center"
        close-on-esc
        @close="handleClose">
        
        <template #header>
            <div class="flex items-center justify-between w-full gap-4">
                <h3 class="text-lg font-bold text-primary-100 flex-1 min-w-0">
                    {{ getEntityName() }}
                </h3>
                <div class="flex-shrink-0">
                    <EntityActions
                        :entity-type="entityTypeKey"
                        :entity="entity"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inModal: true, modalMode: 'view' }"
                        @action="handleAction"
                    />
                </div>
            </div>
        </template>

        <div>
            <EntityViewLarge 
                v-if="currentView === 'large'"
                :entity="entity"
                :entity-type="entityType"
                :show-actions="false" />
            
            <EntityViewCompact 
                v-else-if="currentView === 'compact'"
                :entity="entity"
                :entity-type="entityType"
                :show-actions="false" />
            
            <EntityViewMinimal 
                v-else-if="currentView === 'minimal'"
                :entity="entity"
                :entity-type="entityType"
                :show-actions="false" />
            
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

