<script setup>
/**
 * PanoplyViewCompact — Vue Compact pour Panoply
 * 
 * @description
 * Vue réduite d'une panoplie avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Panoply} panoply - Instance du modèle Panoply
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';

const props = defineProps({
    panoply: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('panoply');

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'name',
    'bonus',
    'items_count',
    'usable',
    'is_visible',
]);

const getFieldLabel = (fieldKey) => {
    const labels = {
        name: 'Nom',
        bonus: 'Bonus',
        items_count: 'Nb objets',
        usable: 'Utilisable',
        is_visible: 'Visible',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        bonus: 'fa-solid fa-star',
        items_count: 'fa-solid fa-boxes',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.panoply.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const panoplyId = props.panoply.id;
    if (!panoplyId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.panoplies.show', { panoply: panoplyId }));
            emit('view', props.panoply);
            break;
        case 'edit':
            router.visit(route('entities.panoplies.edit', { panoply: panoplyId }));
            emit('edit', props.panoply);
            break;
        case 'quick-edit':
            emit('quick-edit', props.panoply);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('panoply');
            const url = resolveEntityRouteUrl('panoply', 'show', panoplyId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.panoply);
            break;
        }
        case 'delete':
            emit('delete', props.panoply);
            break;
    }
};
</script>

<template>
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <h3 class="text-lg font-semibold text-primary-100 truncate">
                    <CellRenderer
                        :cell="getCell('name')"
                        ui-color="primary"
                    />
                </h3>
            </div>
            
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="panoply"
                    :entity="panoply"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false }"
                    @action="handleAction"
                />
            </div>
        </div>

        <!-- Informations en liste compacte -->
        <div class="space-y-2 text-sm">
            <div
                v-for="fieldKey in compactFields"
                :key="fieldKey"
                class="flex items-start gap-2 p-2 rounded hover:bg-base-200 transition-colors"
            >
                <Icon
                    :source="getFieldIcon(fieldKey)"
                    size="xs"
                    class="text-primary-400 flex-shrink-0 mt-0.5"
                />
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-primary-400 text-xs font-semibold uppercase">
                            {{ getFieldLabel(fieldKey) }}
                        </span>
                        <div class="flex-1 text-right min-w-0 text-primary-200">
                            <CellRenderer
                                :cell="getCell(fieldKey)"
                                ui-color="primary"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
