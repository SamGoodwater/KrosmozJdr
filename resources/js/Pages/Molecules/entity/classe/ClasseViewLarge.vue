<script setup>
/**
 * ClasseViewLarge — Vue Large pour Classe
 * 
 * @description
 * Vue complète d'une classe avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
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
            console.warn('[ClasseViewLarge] visibleIf failed for', fieldKey, e);
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
        'life',
        'life_dice',
        'specificity',
        'usable',
        'is_visible',
        'dofus_version',
        'auto_update',
        'dofusdb_id',
        'official_id',
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
    return props.classe.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
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
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la classe copié !");
            }
            emit('copy-link', props.classe);
            break;
        }
        case 'download-pdf':
            await downloadPdf(classeId);
            emit('download-pdf', props.classe);
            break;
        case 'refresh':
            router.reload({ only: ['classes'] });
            emit('refresh', props.classe);
            break;
        case 'delete':
            emit('delete', props.classe);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- En-tête avec image/icône, nom et actions -->
        <div class="flex flex-col md:flex-row gap-4 items-start">
            <!-- Image/icône -->
            <div v-if="classe.image || classe.icon" class="flex-shrink-0">
                <Image
                    :src="classe.image || classe.icon"
                    :alt="classe.name || 'Classe'"
                    size="lg"
                    class="rounded-lg"
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
                        <p v-if="classe.description" class="text-primary-300 mt-2 break-words">
                            {{ classe.description }}
                        </p>
                    </div>
                    
                    <!-- Actions en haut à droite -->
                    <div v-if="showActions" class="flex-shrink-0">
                        <EntityActions
                            entity-type="classe"
                            :entity="classe"
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
