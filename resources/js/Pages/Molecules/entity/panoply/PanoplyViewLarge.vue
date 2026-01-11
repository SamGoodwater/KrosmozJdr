<script setup>
/**
 * PanoplyViewLarge — Vue Large pour Panoply
 * 
 * @description
 * Vue complète d'une panoplie avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
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

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'name',
        'description',
        'bonus',
        'items_count',
        'usable',
        'is_visible',
    ];
    
    if (props.panoply.canView) {
        fields.push('dofusdb_id', 'created_by', 'created_at', 'updated_at');
    }
    
    return fields;
});

const getFieldLabel = (fieldKey) => {
    const labels = {
        name: 'Nom',
        description: 'Description',
        bonus: 'Bonus',
        items_count: 'Nb objets',
        usable: 'Utilisable',
        is_visible: 'Visible',
        dofusdb_id: 'DofusDB ID',
        created_by: 'Créé par',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        description: 'fa-solid fa-align-left',
        bonus: 'fa-solid fa-star',
        items_count: 'fa-solid fa-boxes',
        usable: 'fa-solid fa-check-circle',
        is_visible: 'fa-solid fa-eye',
        dofusdb_id: 'fa-solid fa-link',
        created_by: 'fa-solid fa-user',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.panoply.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la panoplie copié !");
            }
            emit('copy-link', props.panoply);
            break;
        }
        case 'download-pdf':
            await downloadPdf(panoplyId);
            emit('download-pdf', props.panoply);
            break;
        case 'refresh':
            router.reload({ only: ['panoplies'] });
            emit('refresh', props.panoply);
            break;
        case 'delete':
            emit('delete', props.panoply);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec nom et actions -->
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold text-primary-100 break-words">
                    <CellRenderer
                        :cell="getCell('name')"
                        ui-color="primary"
                    />
                </h2>
                <p v-if="panoply.description" class="text-primary-300 mt-2 break-words">
                    {{ panoply.description }}
                </p>
            </div>
            
            <!-- Actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="panoply"
                    :entity="panoply"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false, inPage: true }"
                    @action="handleAction"
                />
            </div>
        </div>

        <!-- Informations principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="fieldKey in extendedFields"
                :key="fieldKey"
                class="p-3 bg-base-200 rounded-lg"
            >
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <Icon
                            :source="getFieldIcon(fieldKey)"
                            :alt="getFieldLabel(fieldKey)"
                            size="xs"
                            class="text-primary-400"
                        />
                        <span class="text-xs text-primary-400 uppercase font-semibold">
                            {{ getFieldLabel(fieldKey) }}
                        </span>
                    </div>
                    <div class="text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell(fieldKey)"
                            ui-color="primary"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
