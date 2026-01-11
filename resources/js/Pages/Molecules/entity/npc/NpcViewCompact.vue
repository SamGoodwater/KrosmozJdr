<script setup>
/**
 * NpcViewCompact — Vue Compact pour NPC
 * 
 * @description
 * Vue réduite d'un NPC avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Npc} npc - Instance du modèle NPC
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
    npc: {
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
const { downloadPdf } = useDownloadPdf('npc');

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'creature_name',
    'classe',
    'specialization',
    'age',
    'size',
]);

const getFieldLabel = (fieldKey) => {
    const labels = {
        creature_name: 'Créature',
        classe: 'Classe',
        specialization: 'Spécialisation',
        age: 'Âge',
        size: 'Taille',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        creature_name: 'fa-solid fa-user',
        classe: 'fa-solid fa-user-tie',
        specialization: 'fa-solid fa-star',
        age: 'fa-solid fa-birthday-cake',
        size: 'fa-solid fa-expand',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.npc.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const npcId = props.npc.id;
    if (!npcId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.npcs.show', { npc: npcId }));
            emit('view', props.npc);
            break;
        case 'edit':
            router.visit(route('entities.npcs.edit', { npc: npcId }));
            emit('edit', props.npc);
            break;
        case 'quick-edit':
            emit('quick-edit', props.npc);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('npc');
            const url = resolveEntityRouteUrl('npc', 'show', npcId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.npc);
            break;
        }
        case 'delete':
            emit('delete', props.npc);
            break;
    }
};
</script>

<template>
    <div class="space-y-3 max-h-96 overflow-y-auto">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Icon source="fa-solid fa-user" :alt="npc.creature?.name || 'NPC'" size="md" class="flex-shrink-0" />
                <h3 class="text-lg font-semibold text-primary-100 truncate">
                    <CellRenderer
                        :cell="getCell('creature_name')"
                        ui-color="primary"
                    />
                </h3>
            </div>
            
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="npc"
                    :entity="npc"
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
