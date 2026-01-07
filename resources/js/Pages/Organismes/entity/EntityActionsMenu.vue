<script setup>
/**
 * EntityActionsMenu Organism (Legacy Wrapper)
 * 
 * @description
 * Wrapper de compatibilité autour de `EntityActions` pour maintenir l'API legacy.
 * Ce composant convertit les props de permissions explicites en blacklist/whitelist
 * pour le nouveau système d'actions.
 * 
 * @deprecated
 * Utilisez directement `EntityActions` pour les nouveaux composants.
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité (pour générer les routes)
 * @props {Boolean} canView - Permission de voir l'entité
 * @props {Boolean} canUpdate - Permission de modifier l'entité
 * @props {Boolean} canDelete - Permission de supprimer l'entité
 * @props {Boolean} disableQuickActions - Désactiver les actions rapides (si plusieurs entités sélectionnées)
 * @emit view - Événement émis pour ouvrir la page de visualisation
 * @emit quick-view - Événement émis pour ouvrir le modal de visualisation
 * @emit edit - Événement émis pour ouvrir la page d'édition
 * @emit quick-edit - Événement émis pour activer l'édition rapide
 * @emit copy-link - Événement émis pour copier le lien
 * @emit download-pdf - Événement émis pour télécharger le PDF
 * @emit delete - Événement émis pour supprimer l'entité
 * @emit refresh - Événement émis pour rafraîchir les données (admin)
 */
import { computed } from 'vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { normalizeEntityType } from '@/Entities/entity-registry';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    canView: {
        type: Boolean,
        default: false
    },
    canUpdate: {
        type: Boolean,
        default: false
    },
    canDelete: {
        type: Boolean,
        default: false
    },
    disableQuickActions: {
        type: Boolean,
        default: false
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    /**
     * Permission "gestion" (ex: actions d'admin/maintenance).
     * Backward compatible: si null/undefined, fallback sur isAdmin.
     */
    canManage: {
        type: Boolean,
        default: null
    },
    /**
     * Configuration des routes Ziggy pour l'entité (optionnel).
     * Permet de gérer les exceptions de param keys (ex: resourceType).
     * @deprecated Non utilisé dans le nouveau système (géré par entityRouteRegistry)
     */
    routeConfig: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['view', 'quick-view', 'edit', 'quick-edit', 'copy-link', 'download-pdf', 'delete', 'refresh']);

const canManageEffective = computed(() => (props.canManage === null ? props.isAdmin : props.canManage));

// Normaliser le type d'entité
const normalizedEntityType = computed(() => normalizeEntityType(props.entityType));

// Construire la blacklist selon les permissions et disableQuickActions
const blacklist = computed(() => {
    const list = [];
    
    // Actions rapides désactivées
    if (props.disableQuickActions) {
        list.push('quick-view', 'quick-edit');
    }
    
    // Actions nécessitant canView
    if (!props.canView) {
        list.push('view', 'quick-view');
    }
    
    // Actions nécessitant canUpdate
    if (!props.canUpdate) {
        list.push('edit', 'quick-edit');
    }
    
    // Actions nécessitant canDelete
    if (!props.canDelete) {
        list.push('delete');
    }
    
    // Actions nécessitant canManage
    if (!canManageEffective.value) {
        list.push('refresh');
    }
    
    return list;
});

// Gérer les actions et émettre les événements legacy
const handleAction = (actionKey, entity) => {
    const targetEntity = entity || props.entity;
    // Émettre l'événement spécifique pour compatibilité
    emit(actionKey, targetEntity);
};
</script>

<template>
    <EntityActions
        :entity-type="normalizedEntityType"
        :entity="entity"
        format="dropdown"
        display="icon-text"
        size="sm"
        color="primary"
        :blacklist="blacklist"
        :context="{ inPanel: false }"
        @action="handleAction"
    />
</template>

