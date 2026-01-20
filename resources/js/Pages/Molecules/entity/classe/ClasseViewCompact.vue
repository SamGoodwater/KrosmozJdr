<script setup>
/**
 * ClasseViewCompact — Vue Compact pour Classe
 * 
 * @description
 * Vue réduite d'une classe avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Classe} classe - Instance du modèle Classe
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
import { getClasseFieldDescriptors } from "@/Entities/classe/classe-descriptors";

const props = defineProps({
    classe: {
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
const { downloadPdf } = useDownloadPdf('classe');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('classe', 'viewAny'),
        createAny: permissions.can('classe', 'createAny'),
        updateAny: permissions.can('classe', 'updateAny'),
        deleteAny: permissions.can('classe', 'deleteAny'),
        manageAny: permissions.can('classe', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getClasseFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ClasseViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs à afficher dans la vue compacte
const compactFields = computed(() => [
    'name',
    'life',
    'life_dice',
    'specificity',
    'usable',
    'is_visible',
].filter(canShowField));

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.classe.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const handleAction = async (actionKey) => {
    const classeId = props.classe.id;
    if (!classeId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.classes.show', { classe: classeId }));
            emit('view', props.classe);
            break;
        case 'edit':
            router.visit(route('entities.classes.edit', { classe: classeId }));
            emit('edit', props.classe);
            break;
        case 'quick-edit':
            emit('quick-edit', props.classe);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('classe');
            const url = resolveEntityRouteUrl('classe', 'show', classeId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.classe);
            break;
        }
        case 'delete':
            emit('delete', props.classe);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <!-- En-tête compact -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex gap-2 items-center flex-1 min-w-0">
                <div v-if="classe.image || classe.icon" class="flex-shrink-0">
                    <Image
                        :src="classe.image || classe.icon"
                        :alt="classe.name || 'Classe'"
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
                    entity-type="classe"
                    :entity="classe"
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
