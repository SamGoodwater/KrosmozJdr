<script setup>
/**
 * BreedViewCompact — Vue Compact pour Breed
 * 
 * @description
 * Vue réduite d'une classe avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Breed} breed - Instance du modèle Breed
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
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import BreedSpellSlotsDisplay from "@/Pages/Molecules/entity/breed/BreedSpellSlotsDisplay.vue";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";

const props = defineProps({
    breed: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    tableMeta: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('breed');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('breeds', 'viewAny'),
        createAny: permissions.can('breeds', 'createAny'),
        updateAny: permissions.can('breeds', 'updateAny'),
        deleteAny: permissions.can('breeds', 'deleteAny'),
        manageAny: permissions.can('breeds', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getBreedFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[BreedViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'name',
    'life_dice',
    'specificity',
    'state',
    'read_level',
    'write_level',
].filter(canShowField));

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.breed.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const hasSpellSlotGroups = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    return buildSpellSlotGroups(raw).length > 0;
});

const handleAction = async (actionKey) => {
    const breedId = props.breed.id;
    if (!breedId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.breeds.show', { breed: breedId }));
            emit('view', props.breed);
            break;
        case 'edit':
            router.visit(route('entities.breeds.edit', { breed: breedId }));
            emit('edit', props.breed);
            break;
        case 'quick-edit':
            emit('quick-edit', props.breed);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('breed');
            const url = resolveEntityRouteUrl('breed', 'show', breedId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la classe copié !");
            }
            emit('copy-link', props.breed);
            break;
        }
        case 'download-pdf':
            await downloadPdf(breedId);
            emit('download-pdf', props.breed);
            break;
        case 'refresh':
            router.reload({ only: ['breeds'] });
            emit('refresh', props.breed);
            break;
        case 'delete':
            emit('delete', props.breed);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <div v-if="breed.image || breed.icon" class="flex-shrink-0">
                    <Image
                        :src="breed.image || breed.icon"
                        :alt="breed.name || 'Breed'"
                        size="sm"
                        class="rounded"
                    />
                </div>
                <h3 class="text-lg font-semibold text-primary-100 truncate">
                    <CellRenderer
                        :cell="getCell('name')"
                        ui-color="primary"
                    />
                </h3>
            </div>
            
            <div v-if="showActions" class="flex-shrink-0">
                <EntityActions
                    entity-type="breeds"
                    :entity="breed"
                    format="buttons"
                    display="icon-only"
                    size="sm"
                    color="primary"
                    :context="{ inPanel: false, inPage: true }"
                    @action="handleAction"
                />
            </div>
        </div>

        <!-- Informations en liste compacte -->
        <div class="space-y-2 text-sm">
            <div
                v-for="fieldKey in compactFields"
                :key="fieldKey"
                class="flex items-start gap-2 p-2 entity-radius-field hover:bg-base-200 transition-colors"
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

        <BreedSpellSlotsDisplay
            v-if="hasSpellSlotGroups"
            class="mt-2"
            :breed="breed?._data ?? breed"
            density="minimal"
        />
    </div>
</template>

<style scoped>
.entity-radius-field {
    border-radius: var(--radius-field, 0.1rem);
}
</style>
