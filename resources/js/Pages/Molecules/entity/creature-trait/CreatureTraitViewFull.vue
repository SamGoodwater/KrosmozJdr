<script setup>
/**
 * CreatureTraitViewFull — Vue Full pour CreatureTrait
 * 
 * @description
 * Vue complète d'un attribut avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {CreatureTrait} creatureTrait - Instance du modèle CreatureTrait
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
import { getCreatureTraitFieldDescriptors } from "@/Entities/creature-trait/creature-trait-descriptors";
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';

const props = defineProps({
    creatureTrait: {
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

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('creature-traits');
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
            console.warn('[CreatureTraitViewFull] visibleIf failed for', fieldKey, e);
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
        'read_level',
        'write_level',
    ];
    ['created_by', 'created_at', 'updated_at'].forEach((k) => fields.push(k));
    return fields.filter(canShowField);
});

const getCell = (fieldKey) => {
    return props.creatureTrait.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de l'attribut copié !");
            }
            emit('copy-link', props.creatureTrait);
            break;
        }
        case 'download-pdf':
            await downloadPdf(creatureTraitId);
            emit('download-pdf', props.creatureTrait);
            break;
        case 'refresh':
            router.reload({ only: ['creatureTraits'] });
            emit('refresh', props.creatureTrait);
            break;
        case 'delete':
            emit('delete', props.creatureTrait);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image -->
            <div v-if="creatureTrait.image" class="flex-shrink-0">
                <Image
                    :src="creatureTrait.image"
                    :alt="creatureTrait.name || 'CreatureTrait'"
                    size="lg"
                    class="entity-radius-box"
                />
            </div>
            
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
                        <p v-if="creatureTrait.description" class="text-primary-300 mt-2 break-words">
                            {{ creatureTrait.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="creature-traits"
                            :entity="creatureTrait"
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
                class="p-3 bg-base-200 entity-radius-box"
            >
                <div class="flex flex-col gap-1">
                    <div class="text-primary-100 break-words">
                        <EntityPropertyDisplay
                            :field-key="fieldKey"
                            :entity="creatureTrait"
                            entity-type="creature-traits"
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
