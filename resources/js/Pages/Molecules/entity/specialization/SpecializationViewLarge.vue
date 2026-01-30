<script setup>
/**
 * SpecializationViewLarge — Vue Large pour Specialization
 * 
 * @description
 * Vue complète d'une spécialisation avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Specialization} specialization - Instance du modèle Specialization
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
import { getSpecializationFieldDescriptors } from "@/Entities/specialization/specialization-descriptors";

const props = defineProps({
    specialization: {
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
const { downloadPdf } = useDownloadPdf('specialization');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('specialization', 'viewAny'),
        createAny: permissions.can('specialization', 'createAny'),
        updateAny: permissions.can('specialization', 'updateAny'),
        deleteAny: permissions.can('specialization', 'deleteAny'),
        manageAny: permissions.can('specialization', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpecializationFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[SpecializationViewLarge] visibleIf failed for', fieldKey, e);
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
        'capabilities_count',
        'state',
        'read_level',
        'write_level',
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
    return props.specialization.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const handleAction = async (actionKey) => {
    const specializationId = props.specialization.id;
    if (!specializationId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.specializations.show', { specialization: specializationId }));
            emit('view', props.specialization);
            break;
        case 'edit':
            router.visit(route('entities.specializations.edit', { specialization: specializationId }));
            emit('edit', props.specialization);
            break;
        case 'quick-edit':
            emit('quick-edit', props.specialization);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('specialization');
            const url = resolveEntityRouteUrl('specialization', 'show', specializationId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la spécialisation copié !");
            }
            emit('copy-link', props.specialization);
            break;
        }
        case 'download-pdf':
            await downloadPdf(specializationId);
            emit('download-pdf', props.specialization);
            break;
        case 'refresh':
            router.reload({ only: ['specializations'] });
            emit('refresh', props.specialization);
            break;
        case 'delete':
            emit('delete', props.specialization);
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
                        v-if="specialization.image"
                        :src="specialization.image"
                        :alt="specialization.name || 'Specialization'"
                        class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                    />
                    <h2 class="text-2xl font-bold text-primary-100 break-words">
                        <CellRenderer
                            :cell="getCell('name')"
                            ui-color="primary"
                        />
                    </h2>
                </div>
                <p v-if="specialization.description" class="text-primary-300 mt-2 break-words">
                    {{ specialization.description }}
                </p>
            </div>
            
            <!-- Actions en haut à droite -->
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="specialization"
                    :entity="specialization"
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
