<script setup>
/**
 * EntityActionsMenu Organism
 * 
 * @description
 * Menu d'actions contextuel pour une entité avec dropdown.
 * Affiche les actions disponibles selon les permissions de l'utilisateur.
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
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
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
    }
});

const emit = defineEmits(['view', 'quick-view', 'edit', 'quick-edit', 'copy-link', 'download-pdf', 'delete', 'refresh']);

const menuOpen = ref(false);
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf, isDownloading } = useDownloadPdf(props.entityType);

/**
 * Génère l'URL de l'entité
 */
const getEntityUrl = () => {
    const entityId = props.entity?.id ?? props.entity?.id ?? null;
    if (!entityId) return '';
    
    const routeName = `entities.${props.entityType}.show`;
    const routeParams = { [props.entityType]: entityId };
    return `${window.location.origin}${route(routeName, routeParams)}`;
};

/**
 * Gère l'ouverture de la page de visualisation
 */
const handleView = () => {
    emit('view');
    menuOpen.value = false;
};

/**
 * Gère l'ouverture rapide (modal)
 */
const handleQuickView = () => {
    emit('quick-view');
    menuOpen.value = false;
};

/**
 * Gère l'ouverture de la page d'édition
 */
const handleEdit = () => {
    emit('edit');
    menuOpen.value = false;
};

/**
 * Gère l'édition rapide
 */
const handleQuickEdit = () => {
    emit('quick-edit');
    menuOpen.value = false;
};

/**
 * Gère la copie du lien
 */
const handleCopyLink = async () => {
    const url = getEntityUrl();
    if (url) {
        await copyToClipboard(url, 'Lien de l\'entité copié !');
        emit('copy-link');
    }
    menuOpen.value = false;
};

/**
 * Gère le téléchargement PDF
 */
const handleDownloadPdf = async () => {
    const entityId = props.entity?.id ?? props.entity?.id ?? null;
    if (entityId) {
        await downloadPdf(entityId);
    }
    emit('download-pdf');
    menuOpen.value = false;
};

/**
 * Gère la suppression
 */
const handleDelete = () => {
    emit('delete');
    menuOpen.value = false;
};

/**
 * Gère le rafraîchissement (admin)
 */
const handleRefresh = () => {
    emit('refresh');
    menuOpen.value = false;
};
</script>

<template>
    <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-sm">
            <Icon source="fa-solid fa-ellipsis-vertical" size="sm" />
        </label>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] w-56 p-2 shadow-lg border border-base-300">
            <!-- Ouverture (page) -->
            <li v-if="canView && !disableQuickActions">
                <button @click="handleView" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-eye" size="sm" />
                    <span>Ouvrir (page)</span>
                </button>
            </li>
            
            <!-- Ouverture rapide (modal) -->
            <li v-if="canView && !disableQuickActions">
                <button @click="handleQuickView" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-window-maximize" size="sm" />
                    <span>Ouvrir rapide</span>
                </button>
            </li>
            
            <!-- Modification (page) -->
            <li v-if="canUpdate && !disableQuickActions">
                <button @click="handleEdit" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-pen" size="sm" />
                    <span>Modifier (page)</span>
                </button>
            </li>
            
            <!-- Modification rapide -->
            <li v-if="canUpdate && !disableQuickActions">
                <button @click="handleQuickEdit" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-bolt" size="sm" />
                    <span>Modifier rapide</span>
                </button>
            </li>
            
            <li><hr class="my-1" /></li>
            
            <!-- Copier le lien -->
            <li>
                <button @click="handleCopyLink" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-link" size="sm" />
                    <span>Copier le lien</span>
                </button>
            </li>
            
            <!-- Télécharger le PDF -->
            <li v-if="!disableQuickActions">
                <button @click="handleDownloadPdf" :disabled="isDownloading" class="flex items-center gap-2" :class="{ 'opacity-50 cursor-not-allowed': isDownloading }">
                    <Icon source="fa-solid fa-file-pdf" size="sm" />
                    <span>{{ isDownloading ? 'Téléchargement...' : 'Télécharger PDF' }}</span>
                </button>
            </li>
            
            <!-- Rafraîchir (admin) -->
            <li v-if="isAdmin">
                <button @click="handleRefresh" class="flex items-center gap-2">
                    <Icon source="fa-solid fa-arrow-rotate-right" size="sm" />
                    <span>Rafraîchir</span>
                </button>
            </li>
            
            <li v-if="canDelete"><hr class="my-1" /></li>
            
            <!-- Supprimer -->
            <li v-if="canDelete">
                <button @click="handleDelete" class="flex items-center gap-2 text-error">
                    <Icon source="fa-solid fa-trash" size="sm" />
                    <span>Supprimer</span>
                </button>
            </li>
        </ul>
    </div>
</template>

