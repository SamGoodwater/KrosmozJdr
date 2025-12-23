<script setup>
/**
 * EntityViewMinimal Molecule
 * 
 * @description
 * Vue minimale d'une entité avec seulement les infos importantes
 * Affichées sous forme d'icônes avec tooltips
 * Peut s'agrandir au hover pour afficher plus de choses
 * Utilisée dans des grilles, petites modals ou hovers
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
 * @props {Array} importantFields - Liste des champs importants à afficher
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @emit edit - Événement émis pour éditer l'entité
 * @emit copy-link - Événement émis pour copier le lien
 * @emit download-pdf - Événement émis pour télécharger le PDF
 * @emit refresh - Événement émis pour rafraîchir les données
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    importantFields: {
        type: Array,
        default: () => ['level', 'rarity', 'usable', 'is_visible']
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh']);

const isHovered = ref(false);
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf, isDownloading } = useDownloadPdf(props.entityType);
const { isAdmin } = usePermissions();

// Permissions
const canUpdate = computed(() => {
    if (props.entity && typeof props.entity.canUpdate !== 'undefined') {
        return props.entity.canUpdate ?? false;
    }
    return props.entity?.can?.update ?? false;
});

const getEntityIcon = (type) => {
    const icons = {
        attribute: 'fa-solid fa-list',
        campaign: 'fa-solid fa-book',
        capability: 'fa-solid fa-star',
        classe: 'fa-solid fa-user',
        consumable: 'fa-solid fa-flask',
        creature: 'fa-solid fa-paw',
        item: 'fa-solid fa-box',
        monster: 'fa-solid fa-dragon',
        npc: 'fa-solid fa-user-tie',
        panoply: 'fa-solid fa-layer-group',
        resource: 'fa-solid fa-gem',
        scenario: 'fa-solid fa-scroll',
        shop: 'fa-solid fa-store',
        specialization: 'fa-solid fa-graduation-cap',
        spell: 'fa-solid fa-wand-magic-sparkles'
    };
    return icons[type] || 'fa-solid fa-circle';
};

const getFieldIcon = (field) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        rarity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        price: 'fa-solid fa-coins',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs'
    };
    return icons[field] || 'fa-solid fa-info-circle';
};

// Génère l'URL de l'entité
const getEntityUrl = () => {
    const entityId = props.entity?.id ?? null;
    if (!entityId) return '';
    
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    const routeName = `entities.${entityTypePlural}.show`;
    const routeParams = { [props.entityType]: entityId };
    return `${window.location.origin}${route(routeName, routeParams)}`;
};

// Handlers
const handleEdit = () => {
    const entityId = props.entity?.id ?? null;
    if (!entityId) return;
    
    const entityTypePlural = props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`;
    router.visit(route(`entities.${entityTypePlural}.edit`, { [props.entityType]: entityId }));
    emit('edit');
};

const handleCopyLink = async () => {
    const url = getEntityUrl();
    if (url) {
        await copyToClipboard(url, 'Lien de l\'entité copié !');
        emit('copy-link');
    }
};

const handleDownloadPdf = async () => {
    const entityId = props.entity?.id ?? null;
    if (entityId) {
        await downloadPdf(entityId);
        emit('download-pdf');
    }
};

const handleRefresh = () => {
    router.reload({ only: [props.entityType === 'panoply' ? 'panoplies' : `${props.entityType}s`] });
    emit('refresh');
};
</script>

<template>
    <div 
        class="relative rounded-lg border border-base-300 transition-all duration-300 overflow-hidden"
        :class="{ 
            'bg-base-200 shadow-lg': isHovered,
            'bg-base-100': !isHovered
        }"
        :style="{ 
            width: isHovered ? 'auto' : '150px',
            minWidth: '150px',
            maxWidth: isHovered ? '300px' : '200px',
            height: isHovered ? 'auto' : '100px',
            minHeight: '80px'
        }"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false">
        
        <div class="p-3">
            <!-- En-tête avec nom, icône et menu -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="sm" class="flex-shrink-0" />
                    <Tooltip :content="entity.name || entity.title || 'Entité'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">{{ entity.name || entity.title }}</span>
                    </Tooltip>
                </div>
                
                <!-- Menu d'actions en haut à droite -->
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <Dropdown placement="bottom-end">
                        <template #trigger>
                            <Btn size="xs" variant="ghost" class="btn-square p-1">
                                <Icon source="fa-solid fa-ellipsis-vertical" size="xs" />
                            </Btn>
                        </template>
                        <template #content>
                            <ul class="menu p-2 shadow bg-base-100 rounded-box w-48 z-[1]">
                                <li v-if="canUpdate">
                                    <button @click="handleEdit" class="flex items-center gap-2">
                                        <Icon source="fa-solid fa-pen" size="xs" />
                                        <span class="text-xs">Modifier</span>
                                    </button>
                                </li>
                                <li>
                                    <button @click="handleCopyLink" class="flex items-center gap-2">
                                        <Icon source="fa-solid fa-link" size="xs" />
                                        <span class="text-xs">Copier le lien</span>
                                    </button>
                                </li>
                                <li>
                                    <button @click="handleDownloadPdf" :disabled="isDownloading" class="flex items-center gap-2" :class="{ 'opacity-50 cursor-not-allowed': isDownloading }">
                                        <Icon source="fa-solid fa-file-pdf" size="xs" :class="{ 'animate-spin': isDownloading }" />
                                        <span class="text-xs">{{ isDownloading ? 'Téléchargement...' : 'PDF' }}</span>
                                    </button>
                                </li>
                                <li v-if="isAdmin">
                                    <button @click="handleRefresh" class="flex items-center gap-2">
                                        <Icon source="fa-solid fa-arrows-rotate" size="xs" />
                                        <span class="text-xs">Rafraîchir</span>
                                    </button>
                                </li>
                            </ul>
                        </template>
                    </Dropdown>
                </div>
            </div>

            <!-- Infos importantes en icônes avec tooltips -->
            <div class="flex gap-2 flex-wrap">
                <template v-for="field in importantFields" :key="field">
                    <Tooltip 
                        v-if="entity[field] !== null && entity[field] !== undefined"
                        :content="`${field}: ${entity[field]}`"
                        placement="top">
                        <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                            <Icon :source="getFieldIcon(field)" :alt="field" size="xs" class="text-primary-400" />
                            <span class="text-xs text-primary-300 font-medium">{{ entity[field] }}</span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <!-- Contenu supplémentaire au hover avec animation -->
            <div 
                v-if="isHovered" 
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <template v-for="(value, key) in entity" :key="key">
                    <div v-if="!importantFields.includes(key) && !['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                         class="flex items-start gap-1">
                        <Tooltip :content="`${key}: ${typeof value === 'object' ? JSON.stringify(value) : value}`" placement="left">
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-primary-400">{{ key }}:</span> 
                                <span v-if="Array.isArray(value)" class="text-primary-200">{{ value.length }} élément(s)</span>
                                <span v-else-if="typeof value === 'object' && value !== null" class="text-primary-200 truncate block">{{ value.name || value.title || 'Objet' }}</span>
                                <span v-else class="text-primary-200 truncate block">{{ value }}</span>
                            </div>
                        </Tooltip>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

