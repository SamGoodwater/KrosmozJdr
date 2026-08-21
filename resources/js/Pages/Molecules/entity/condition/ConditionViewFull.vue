<script setup>
/**
 * ConditionViewFull — Vue Full pour Condition
 * 
 * @description
 * Vue complète d'un état avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Condition} condition - Instance du modèle Condition
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getConditionFieldDescriptors } from "@/Entities/condition/condition-descriptors";

const props = defineProps({
    condition: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },

    inModal: {
        type: Boolean,
        default: false,
    },
    titleTag: {
        type: String,
        default: 'h2',
        validator: (v) => ['h1', 'h2', 'h3'].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({})
    },
    characteristicRuntime: { type: Object, default: null },
});

const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);


const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('condition');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('condition', 'viewAny'),
        createAny: permissions.can('condition', 'createAny'),
        updateAny: permissions.can('condition', 'updateAny'),
        deleteAny: permissions.can('condition', 'deleteAny'),
        manageAny: permissions.can('condition', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getConditionFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ConditionViewFull] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue large
const extendedFields = computed(() => {
    const fields = [
        'name',
        'dissipable',
        'description',
        'read_level',
        'write_level',
    ];
    ['created_by', 'created_at', 'updated_at'].forEach((k) => fields.push(k));
    return fields.filter(canShowField);
});

const getCell = (fieldKey) => {
    return props.condition.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const handleAction = async (actionKey) => {
    const conditionId = props.condition.id;
    if (!conditionId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.conditions.show', { condition: conditionId }));
            emit('view', props.condition);
            break;
        case 'edit':
            router.visit(route('entities.conditions.edit', { condition: conditionId }));
            emit('edit', props.condition);
            break;
        case 'quick-edit':
            emit('quick-edit', props.condition);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('condition');
            const url = resolveEntityRouteUrl('condition', 'show', conditionId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'état copié !");
            }
            emit('copy-link', props.condition);
            break;
        }
        case 'download-pdf':
            await downloadPdf(conditionId);
            emit('download-pdf', props.condition);
            break;
        case 'refresh':
            router.reload({ only: ['conditions'] });
            emit('refresh', props.condition);
            break;
        case 'delete':
            emit('delete', props.condition);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image -->
            <div v-if="condition.image" class="shrink-0">
                <Image
                    :src="condition.image"
                    :alt="condition.name || 'État'"
                    size="lg"
                    class="entity-radius-box"
                />
            </div>
            
            <!-- Informations principales -->
            <div class="flex-1 w-full">
                <div class="flex w-full min-w-0 items-start gap-4">
                    <div class="min-w-0">
                        <h2 class="text-2xl font-bold text-primary-100 wrap-break-word">
                            <CellRenderer
                                :cell="getCell('name')"
                                ui-color="primary"
                            />
                        </h2>
                        <p v-if="condition.description" class="text-primary-300 mt-2 wrap-break-word">
                            {{ condition.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="ml-auto flex min-w-8 flex-1 justify-end">
                        <EntityActions
                            entity-type="conditions"
                            :entity="condition"
                            format="buttons"
                            display="icon-only"
                            size="sm"
                            color="primary"
                            :context="actionsContext"
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
                class="p-3 bg-base-200 entity-radius-box"
            >
                <div class="flex flex-col gap-1">
                    <div class="text-primary-100 wrap-break-word">
                        <EntityPropertyDisplay
                            :field-key="fieldKey"
                            :entity="condition"
                            entity-type="conditions"
                            display-mode="extended"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            size="sm"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
