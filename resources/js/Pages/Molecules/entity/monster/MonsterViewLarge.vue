<script setup>
/**
 * MonsterViewLarge — Vue Large pour Monster
 * 
 * @description
 * Vue complète d'un monstre avec toutes les informations affichées.
 * Utilisée dans les grandes modals ou directement dans le main.
 * 
 * @props {Monster} monster - Instance du modèle Monster
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { getEntityFieldTooltip, getEntityFieldShortLabel, shouldOmitLabelInMeta } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    monster: {
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
const { downloadPdf } = useDownloadPdf('monster');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('monsters', 'viewAny'),
        createAny: permissions.can('monsters', 'createAny'),
        updateAny: permissions.can('monsters', 'updateAny'),
        deleteAny: permissions.can('monsters', 'deleteAny'),
        manageAny: permissions.can('monsters', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getMonsterFieldDescriptors(ctx.value));

const autoUpdateValue = computed(() => {
    const v = props.monster?.auto_update ?? props.monster?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[MonsterViewLarge] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ([
    'monster_race',
    'size',
    'is_boss',
].filter(canShowField)));

const metaFields = computed(() => ([
    'boss_pa',
].filter(canShowField).filter((k) => !headlineFields.value.includes(k))));

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ([
    'dofus_version',
    'auto_update',
].filter(canShowField)));

const technicalFields = computed(() => ([
    'dofusdb_id',
    'official_id',
    'created_at',
    'updated_at',
].filter(canShowField)));

const bodyFields = computed(() => ([
    // creature_name est le titre
].filter(canShowField)));

const getFieldLabel = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.label || fieldKey;
};

const getFieldTooltip = (fieldKey) => getEntityFieldTooltip(descriptors.value?.[fieldKey]);

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.monster.toCell(fieldKey, {
        size: 'lg',
        context: 'extended',
    });
};

const getBadgeColor = (fieldKey) => {
    const cell = getCell(fieldKey);
    if (cell?.params?.color) return cell.params.color;
    const colorMap = {
        monster_race: 'info',
        size: 'secondary',
        is_boss: 'warning',
        boss_pa: 'warning',
        dofus_version: 'secondary',
        auto_update: 'warning',
        dofusdb_id: 'neutral',
        official_id: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
    };
    return colorMap[fieldKey] || 'neutral';
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return { type: 'text', value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v), params: cell?.params || {} };
};

const handleAction = async (actionKey) => {
    const monsterId = props.monster.id;
    if (!monsterId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.monsters.show', { monster: monsterId }));
            emit('view', props.monster);
            break;
        case 'edit':
            router.visit(route('entities.monsters.edit', { monster: monsterId }));
            emit('edit', props.monster);
            break;
        case 'quick-edit':
            emit('quick-edit', props.monster);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('monster');
            const url = resolveEntityRouteUrl('monster', 'show', monsterId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien du monstre copié !");
            }
            emit('copy-link', props.monster);
            break;
        }
        case 'download-pdf':
            await downloadPdf(monsterId);
            emit('download-pdf', props.monster);
            break;
        case 'refresh':
            router.reload({ only: ['monsters'] });
            emit('refresh', props.monster);
            break;
        case 'delete':
            emit('delete', props.monster);
            break;
    }
};
</script>

<template>
    <div class="space-y-6">
        <EntityViewHeader mode="large">
            <template #media>
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-lg bg-base-200 flex items-center justify-center border border-base-300">
                    <Icon source="fa-solid fa-dragon" :alt="monster.creature?.name || 'Monstre'" size="xl" class="text-primary-400" />
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">
                    <CellRenderer :cell="getCell('creature_name')" ui-color="primary" />
                </h2>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <template v-for="fieldKey in displayMetaFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="flex items-start justify-between gap-2 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                                    <span
                                        v-if="!shouldOmitLabelInMeta(fieldKey)"
                                        class="text-xs uppercase font-semibold text-primary-300 truncate"
                                    >
                                        {{ getEntityFieldShortLabel(fieldKey, getFieldLabel(fieldKey)) }}
                                    </span>
                                </div>
                                <Badge
                                    :color="getBadgeColor(fieldKey)"
                                    size="sm"
                                    :truncate="false"
                                    class="max-w-[18rem] whitespace-normal break-words"
                                >
                                    <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                </Badge>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </template>

            <template #subtitle>
                <p v-if="monster.creature?.description" class="text-primary-300 mt-2 break-words">
                    {{ monster.creature.description }}
                </p>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="monster"
                        :entity="monster"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false, inPage: true }"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <template v-for="fieldKey in technicalFields" :key="fieldKey">
                    <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                        <div class="inline-flex items-center gap-2 min-w-0">
                            <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                            <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                            <span class="min-w-0 break-words">
                                <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <template v-for="fieldKey in userCanEditFields" :key="fieldKey">
                        <Tooltip :content="getFieldTooltip(fieldKey)" placement="top">
                            <div class="inline-flex items-center gap-2 min-w-0">
                                <Icon :source="getFieldIcon(fieldKey)" size="xs" class="text-primary-300 flex-shrink-0" />
                                <span class="uppercase tracking-wide text-primary-300">{{ getFieldLabel(fieldKey) }}</span>
                                <span class="min-w-0 break-words">
                                    <template v-if="fieldKey === 'auto_update'">
                                        <Icon
                                            v-if="autoUpdateValue !== null"
                                            :source="autoUpdateValue ? 'fa-solid fa-check' : 'fa-solid fa-xmark'"
                                            :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                            size="sm"
                                            :class="autoUpdateValue ? 'text-success-800' : 'text-error-800'"
                                        />
                                        <span v-else>—</span>
                                    </template>
                                    <template v-else>
                                        <Badge :color="getBadgeColor(fieldKey)" size="sm">
                                            <CellRenderer :cell="asTextCell(getCell(fieldKey))" ui-color="primary" />
                                        </Badge>
                                    </template>
                                </span>
                            </div>
                        </Tooltip>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
