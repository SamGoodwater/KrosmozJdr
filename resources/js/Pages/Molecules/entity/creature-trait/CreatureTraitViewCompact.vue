<script setup>
/**
 * CreatureTraitViewCompact — Vue Compact pour CreatureTrait
 * 
 * @description
 * Vue réduite d'un attribut avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {CreatureTrait} creatureTrait - Instance du modèle CreatureTrait
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCreatureTraitFieldDescriptors } from "@/Entities/creature-trait/creature-trait-descriptors";

const props = defineProps({
    creatureTrait: {
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
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('creature-traits', 'viewAny'),
        createAny: permissions.can('creature-traits', 'createAny'),
        updateAny: permissions.can('creature-traits', 'updateAny'),
        deleteAny: permissions.can('creature-traits', 'deleteAny'),
        manageAny: permissions.can('creature-traits', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getCreatureTraitFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[CreatureTraitViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'name',
    'description',
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
    return props.creatureTrait.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const creatureTraitId = props.creatureTrait.id;
    if (!creatureTraitId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.creature-traits.show', { creatureTrait: creatureTraitId }));
            emit('view', props.creatureTrait);
            break;
        case 'edit':
            router.visit(route('entities.creature-traits.edit', { creatureTrait: creatureTraitId }));
            emit('edit', props.creatureTrait);
            break;
        case 'quick-edit':
            emit('quick-edit', props.creatureTrait);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('creature-traits');
            const url = resolveEntityRouteUrl('creature-traits', 'show', creatureTraitId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.creatureTrait);
            break;
        }
        case 'delete':
            emit('delete', props.creatureTrait);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <div v-if="creatureTrait.image" class="flex-shrink-0">
                    <Image
                        :src="creatureTrait.image"
                        :alt="creatureTrait.name || 'CreatureTrait'"
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
                    entity-type="creature-traits"
                    :entity="creatureTrait"
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
    </div>
</template>

<style scoped>
.entity-radius-field {
    border-radius: var(--radius-field, 0.1rem);
}
</style>
