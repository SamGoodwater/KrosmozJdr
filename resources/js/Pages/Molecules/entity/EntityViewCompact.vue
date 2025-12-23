<script setup>
/**
 * EntityViewCompact Molecule
 * 
 * @description
 * Vue compacte d'une entité avec toutes les infos mais dans un format condensé
 * Utilise tooltips et scroll pour les grands textes
 * 
 * @props {Object} entity - Données de l'entité
 * @props {String} entityType - Type d'entité
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
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf, isDownloading } = useDownloadPdf(props.entityType);
const { isAdmin } = usePermissions();

// État pour la troncature
const expandedFields = ref(new Set());

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

const getFieldIcon = (key) => {
    const icons = {
        level: 'fa-solid fa-level-up-alt',
        rarity: 'fa-solid fa-star',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        price: 'fa-solid fa-coins',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-bolt',
        po: 'fa-solid fa-crosshairs',
        description: 'fa-solid fa-align-left',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock'
    };
    return icons[key] || 'fa-solid fa-info-circle';
};

const truncate = (text, maxLength = 30) => {
    if (!text || text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const toggleField = (key) => {
    if (expandedFields.value.has(key)) {
        expandedFields.value.delete(key);
    } else {
        expandedFields.value.add(key);
    }
};

const isFieldExpanded = (key) => {
    return expandedFields.value.has(key);
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
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact avec menu -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Icon :source="getEntityIcon(entityType)" :alt="entity.name" size="md" class="flex-shrink-0" />
                <h3 class="text-lg font-semibold text-primary-100 truncate">{{ entity.name || entity.title }}</h3>
            </div>
            
            <!-- Menu d'actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <Dropdown placement="bottom-end">
                    <template #trigger>
                        <Btn size="sm" variant="ghost" class="btn-square">
                            <Icon source="fa-solid fa-ellipsis-vertical" size="sm" />
                        </Btn>
                    </template>
                    <template #content>
                        <ul class="menu p-2 shadow bg-base-100 rounded-box w-52 z-[1]">
                            <li v-if="canUpdate">
                                <button @click="handleEdit" class="flex items-center gap-2">
                                    <Icon source="fa-solid fa-pen" size="sm" />
                                    <span>Modifier</span>
                                </button>
                            </li>
                            <li>
                                <button @click="handleCopyLink" class="flex items-center gap-2">
                                    <Icon source="fa-solid fa-link" size="sm" />
                                    <span>Copier le lien</span>
                                </button>
                            </li>
                            <li>
                                <button @click="handleDownloadPdf" :disabled="isDownloading" class="flex items-center gap-2" :class="{ 'opacity-50 cursor-not-allowed': isDownloading }">
                                    <Icon source="fa-solid fa-file-pdf" size="sm" :class="{ 'animate-spin': isDownloading }" />
                                    <span>{{ isDownloading ? 'Téléchargement...' : 'Télécharger PDF' }}</span>
                                </button>
                            </li>
                            <li v-if="isAdmin">
                                <button @click="handleRefresh" class="flex items-center gap-2">
                                    <Icon source="fa-solid fa-arrows-rotate" size="sm" />
                                    <span>Rafraîchir</span>
                                </button>
                            </li>
                        </ul>
                    </template>
                </Dropdown>
            </div>
        </div>

        <!-- Informations en liste compacte avec icônes -->
        <div class="space-y-2 text-sm">
            <template v-for="(value, key) in entity" :key="key">
                <div v-if="!['id', 'name', 'title', 'image', 'created_at', 'updated_at', 'deleted_at', 'can'].includes(key) && value !== null && value !== undefined"
                     class="flex items-start gap-2 p-2 rounded hover:bg-base-200 transition-colors">
                    <!-- Icône du champ -->
                    <Icon :source="getFieldIcon(key)" size="xs" class="text-primary-400 flex-shrink-0 mt-0.5" />
                    
                    <!-- Label et valeur -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-primary-400 text-xs font-semibold uppercase">{{ key }}</span>
                            <div class="flex-1 text-right min-w-0">
                                <!-- Texte tronqué avec dépliage -->
                                <template v-if="typeof value === 'string' && value.length > 30">
                                    <div class="flex items-center gap-1 justify-end">
                                        <span class="text-primary-200 break-words" :class="{ 'line-clamp-2': !isFieldExpanded(key) }">
                                            {{ isFieldExpanded(key) ? value : truncate(value, 30) }}
                                        </span>
                                        <button @click="toggleField(key)" class="text-primary-400 hover:text-primary-200 flex-shrink-0" type="button">
                                            <Icon :source="isFieldExpanded(key) ? 'fa-solid fa-chevron-up' : 'fa-solid fa-chevron-down'" size="xs" />
                                        </button>
                                    </div>
                                </template>
                                <!-- Badge pour booléen -->
                                <Badge v-else-if="typeof value === 'boolean'" 
                                       :color="value ? 'success' : 'error'" size="xs">
                                    {{ value ? 'Oui' : 'Non' }}
                                </Badge>
                                <!-- Tableau -->
                                <span v-else-if="Array.isArray(value)" class="text-primary-200">{{ value.length }} élément(s)</span>
                                <!-- Objet -->
                                <span v-else-if="typeof value === 'object'" class="text-primary-200 truncate block">{{ value.name || value.title || 'Objet' }}</span>
                                <!-- Autre -->
                                <span v-else class="text-primary-200 break-words">{{ value }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

