<script setup>
/**
 * ConsumableViewCompact — Vue Compact pour Consumable
 * 
 * @description
 * Vue réduite d'un consommable avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Consumable} consumable - Instance du modèle Consumable
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
import { getConsumableFieldDescriptors } from "@/Entities/consumable/consumable-descriptors";

const props = defineProps({
    consumable: {
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
const { downloadPdf } = useDownloadPdf('consumable');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('consumable', 'viewAny'),
        createAny: permissions.can('consumable', 'createAny'),
        updateAny: permissions.can('consumable', 'updateAny'),
        deleteAny: permissions.can('consumable', 'deleteAny'),
        manageAny: permissions.can('consumable', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ConsumableViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'rarity',
    'level',
    'usable',
    'price',
    'dofus_version',
    'is_visible',
].filter(canShowField));

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.consumable.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const consumableId = props.consumable.id;
    if (!consumableId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.consumables.show', { consumable: consumableId }));
            emit('view', props.consumable);
            break;
        case 'edit':
            router.visit(route('entities.consumables.edit', { consumable: consumableId }));
            emit('edit', props.consumable);
            break;
        case 'quick-edit':
            emit('quick-edit', props.consumable);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', consumableId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.consumable);
            break;
        }
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <Icon source="fa-solid fa-flask" :alt="consumable.name" size="md" class="flex-shrink-0" />
                <h3 class="text-lg font-semibold text-primary-100 truncate">{{ consumable.name }}</h3>
            </div>
            
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="consumable"
                    :entity="consumable"
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
