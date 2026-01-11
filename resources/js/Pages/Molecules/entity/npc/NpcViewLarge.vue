<script setup>
/**
 * NpcViewLarge — Vue Large pour NPC
 * 
 * @description
 * Vue complète d'un NPC avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
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

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'creature_name',
        'classe',
        'specialization',
        'story',
        'historical',
        'age',
        'size',
    ];
    
    if (props.npc.canView) {
        fields.push('created_at', 'updated_at');
    }
    
    return fields;
});

const getFieldLabel = (fieldKey) => {
    const labels = {
        creature_name: 'Créature',
        classe: 'Classe',
        specialization: 'Spécialisation',
        story: 'Histoire',
        historical: 'Historique',
        age: 'Âge',
        size: 'Taille',
        created_at: 'Créé le',
        updated_at: 'Modifié le',
    };
    return labels[fieldKey] || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    const icons = {
        creature_name: 'fa-solid fa-user',
        classe: 'fa-solid fa-user-tie',
        specialization: 'fa-solid fa-star',
        story: 'fa-solid fa-book',
        historical: 'fa-solid fa-scroll',
        age: 'fa-solid fa-birthday-cake',
        size: 'fa-solid fa-expand',
        created_at: 'fa-solid fa-calendar',
        updated_at: 'fa-solid fa-clock',
    };
    return icons[fieldKey] || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.npc.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du NPC copié !");
            }
            emit('copy-link', props.npc);
            break;
        }
        case 'download-pdf':
            await downloadPdf(npcId);
            emit('download-pdf', props.npc);
            break;
        case 'refresh':
            router.reload({ only: ['npcs'] });
            emit('refresh', props.npc);
            break;
        case 'delete':
            emit('delete', props.npc);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Informations principales -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">
                            <CellRenderer
                                :cell="getCell('creature_name')"
                                ui-color="primary"
                            />
                        </h2>
                        <p v-if="npc.creature?.description" class="text-primary-300 mt-2 break-words">
                            {{ npc.creature.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="npc"
                            :entity="npc"
                            format="buttons"
                            display="icon-only"
                            size="sm"
                            color="primary"
                            :context="{ inPanel: false, inPage: true }"
                            @action="handleAction"
                        />
                    </div>
                </div>
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
