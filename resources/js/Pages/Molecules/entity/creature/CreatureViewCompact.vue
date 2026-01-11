<script setup>
/**
 * CreatureViewCompact — Vue Compact pour Creature
 * 
 * @description
 * Vue réduite d'une créature avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Creature} creature - Instance du modèle Creature
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
    creature: {
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
const { downloadPdf } = useDownloadPdf('creature');

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'name',
    'level',
    'hostility',
    'life',
    'pa',
    'pm',
    'po',
]);

const getFieldLabel = (fieldKey) => {
    const labels = {
        name: 'Nom',
        level: 'Niveau',
        hostility: 'Hostilité',
        life: 'Vie',
        pa: 'PA',
        pm: 'PM',
        po: 'PO',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        name: 'fa-solid fa-font',
        level: 'fa-solid fa-level-up-alt',
        hostility: 'fa-solid fa-exclamation-triangle',
        life: 'fa-solid fa-heart',
        pa: 'fa-solid fa-running',
        pm: 'fa-solid fa-walking',
        po: 'fa-solid fa-crosshairs',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.creature.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const creatureId = props.creature.id;
    if (!creatureId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.creatures.show', { creature: creatureId }));
            emit('view', props.creature);
            break;
        case 'edit':
            router.visit(route('entities.creatures.edit', { creature: creatureId }));
            emit('edit', props.creature);
            break;
        case 'quick-edit':
            emit('quick-edit', props.creature);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('creature');
            const url = resolveEntityRouteUrl('creature', 'show', creatureId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.creature);
            break;
        }
        case 'delete':
            emit('delete', props.creature);
            break;
    }
};
</script>

<template>
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Icon source="fa-solid fa-dragon" :alt="creature.name || 'Créature'" size="md" class="flex-shrink-0" />
                <h3 class="text-lg font-semibold text-primary-100 truncate">
                    <CellRenderer
                        :cell="getCell('name')"
                        ui-color="primary"
                    />
                </h3>
            </div>
            
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="creature"
                    :entity="creature"
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
