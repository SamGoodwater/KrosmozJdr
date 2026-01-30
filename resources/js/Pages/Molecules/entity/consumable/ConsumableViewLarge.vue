<script setup>
/**
 * ConsumableViewLarge — Vue Large pour Consumable
 * 
 * @description
 * Vue complète d'un consommable avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
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
            console.warn('[ConsumableViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'rarity',
        'consumable_type',
        'level',
        'state',
        'price',
        'dofus_version',
        'read_level',
        'write_level',
        'auto_update',
        'dofusdb_id',
        'official_id',
        'effect',
        'recipe',
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
    return props.consumable.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du consommable copié !");
            }
            emit('copy-link', props.consumable);
            break;
        }
        case 'download-pdf':
            await downloadPdf(consumableId);
            emit('download-pdf', props.consumable);
            break;
        case 'refresh':
            router.reload({ only: ['consumables'] });
            emit('refresh', props.consumable);
            break;
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image à gauche -->
            <div class="flex-shrink-0">
                <div v-if="consumable.image" class="w-32 h-32 md:w-40 md:h-40">
                    <Image :source="consumable.image" :alt="consumable.name || 'Image'" size="lg" rounded="lg" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-32 h-32 md:w-40 md:h-40 flex items-center justify-center bg-base-200 rounded-lg">
                    <Icon source="fa-solid fa-flask" :alt="consumable.name" size="xl" />
                </div>
            </div>
            
            <!-- Informations principales à droite -->
            <div class="flex-1 w-full">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ consumable.name }}</h2>
                        <p v-if="consumable.description" class="text-primary-300 mt-2 break-words">{{ consumable.description }}</p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="consumable"
                            :entity="consumable"
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
