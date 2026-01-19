<script setup>
/**
 * CapabilityViewLarge — Vue Large pour Capability
 * 
 * @description
 * Vue complète d'une capacité avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Capability} capability - Instance du modèle Capability
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
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";

const props = defineProps({
    capability: {
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
const { downloadPdf } = useDownloadPdf('capability');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('capabilities', 'viewAny'),
        createAny: permissions.can('capabilities', 'createAny'),
        updateAny: permissions.can('capabilities', 'updateAny'),
        deleteAny: permissions.can('capabilities', 'deleteAny'),
        manageAny: permissions.can('capabilities', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCapabilityFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[CapabilityViewLarge] visibleIf failed for', fieldKey, e);
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
        'effect',
        'level',
        'pa',
        'po',
        'element',
        'time_before_use_again',
        'casting_time',
        'duration',
        'is_magic',
        'ritual_available',
        'powerful',
        'usable',
        'is_visible',
    ];
    ['image', 'created_by', 'created_at', 'updated_at'].forEach((k) => fields.push(k));
    return fields.filter(canShowField);
});

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.capability.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const handleAction = async (actionKey) => {
    const capabilityId = props.capability.id;
    if (!capabilityId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.capabilities.show', { capability: capabilityId }));
            emit('view', props.capability);
            break;
        case 'edit':
            router.visit(route('entities.capabilities.edit', { capability: capabilityId }));
            emit('edit', props.capability);
            break;
        case 'quick-edit':
            emit('quick-edit', props.capability);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('capability');
            const url = resolveEntityRouteUrl('capability', 'show', capabilityId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la capacité copié !");
            }
            emit('copy-link', props.capability);
            break;
        }
        case 'download-pdf':
            await downloadPdf(capabilityId);
            emit('download-pdf', props.capability);
            break;
        case 'refresh':
            router.reload({ only: ['capabilities'] });
            emit('refresh', props.capability);
            break;
        case 'delete':
            emit('delete', props.capability);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec nom et actions -->
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3">
                    <Image
                        v-if="capability.image"
                        :src="capability.image"
                        :alt="capability.name || 'Capability'"
                        class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                    />
                    <h2 class="text-2xl font-bold text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell('name')"
                            ui-color="primary"
                        />
                    </h2>
                </div>
                <p v-if="capability.description" class="text-primary-300 mt-2 break-words">
                    {{ capability.description }}
                </p>
            </div>
            
            <!-- Actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="capability"
                    :entity="capability"
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
