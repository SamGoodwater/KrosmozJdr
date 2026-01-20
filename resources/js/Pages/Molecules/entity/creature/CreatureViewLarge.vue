<script setup>
/**
 * CreatureViewLarge — Vue Large pour Creature
 * 
 * @description
 * Vue complète d'une créature avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
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
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCreatureFieldDescriptors } from "@/Entities/creature/creature-descriptors";

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
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('creature', 'viewAny'),
        createAny: permissions.can('creature', 'createAny'),
        updateAny: permissions.can('creature', 'updateAny'),
        deleteAny: permissions.can('creature', 'deleteAny'),
        manageAny: permissions.can('creature', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCreatureFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[CreatureViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'name',
        'description',
        'level',
        'hostility',
        'location',
        'life',
        'pa',
        'pm',
        'po',
        'ini',
        'invocation',
        'touch',
        'ca',
        'dodge_pa',
        'dodge_pm',
        'fuite',
        'tacle',
        'vitality',
        'sagesse',
        'strong',
        'intel',
        'agi',
        'chance',
    ];
    ['created_by', 'created_at', 'updated_at'].forEach((k) => fields.push(k));
    return fields.filter(canShowField);
});

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.creature.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la créature copié !");
            }
            emit('copy-link', props.creature);
            break;
        }
        case 'download-pdf':
            await downloadPdf(creatureId);
            emit('download-pdf', props.creature);
            break;
        case 'refresh':
            router.reload({ only: ['creatures'] });
            emit('refresh', props.creature);
            break;
        case 'delete':
            emit('delete', props.creature);
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
                                :cell="getCell('name')"
                                ui-color="primary"
                            />
                        </h2>
                        <p v-if="creature.description" class="text-primary-300 mt-2 break-words">
                            {{ creature.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="creature"
                            :entity="creature"
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
