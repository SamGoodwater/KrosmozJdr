<script setup>
/**
 * SpellViewCompact — Vue Compact pour Spell
 * 
 * @description
 * Vue réduite d'un sort avec informations essentielles.
 * Utilisée dans les modals compacts.
 * 
 * @props {Spell} spell - Instance du modèle Spell
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @props {Object} [tableMeta] - Meta caractéristiques
 * @props {Object|null} [characteristicRuntime] - Payload runtime (tooltips formules)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import { resolveEntityFieldUi, resolveEntityBadgeUi } from "@/Utils/Entity/entity-view-ui";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

const props = defineProps({
    spell: {
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
    },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('spells', 'viewAny'),
        createAny: permissions.can('spells', 'createAny'),
        updateAny: permissions.can('spells', 'updateAny'),
        deleteAny: permissions.can('spells', 'deleteAny'),
        manageAny: permissions.can('spells', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const stateValue = computed(() => props.spell?.state ?? props.spell?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[SpellViewCompact] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

const headlineFields = computed(() => ([
    'spell_types',
    'level',
].filter(canShowField)));

const metaFields = computed(() => ([
    'pa',
    'po',
    'area',
    'element',
    'category',
].filter(canShowField).filter((k) => !headlineFields.value.includes(k))));

const displayMetaFields = computed(() => [...headlineFields.value, ...metaFields.value]);

const userCanEditFields = computed(() => ([
    'auto_update',
    'read_level',
    'write_level',
].filter(canShowField)));

const technicalFields = computed(() => ([
    'dofusdb_id',
    'official_id',
    'created_by',
    'created_at',
    'updated_at',
].filter(canShowField)));

const getCell = (fieldKey) => {
    return props.spell.toCell(fieldKey, {
        size: 'md',
        context: 'compact',
    });
};

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return { type: 'text', value: (v === null || typeof v === 'undefined' || String(v) === '') ? '-' : String(v), params: cell?.params || {} };
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        level: 'warning',
        auto_update: 'warning',
        read_level: 'primary',
        write_level: 'secondary',
        dofusdb_id: 'neutral',
        official_id: 'neutral',
        created_by: 'neutral',
        created_at: 'neutral',
        updated_at: 'neutral',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: resolveEntityFieldUi({
            fieldKey,
            descriptors: descriptors.value,
            tableMeta: props.tableMeta,
            entityType: 'spell',
        }),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: resolveEntityFieldUi({
            fieldKey,
            descriptors: descriptors.value,
            tableMeta: props.tableMeta,
            entityType: 'spell',
        }),
    });
    return { autoLabel, autoScheme, autoTone };
};

const handleAction = async (actionKey) => {
    const spellId = props.spell.id;
    if (!spellId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.spells.show', { spell: spellId }));
            emit('view', props.spell);
            break;
        case 'edit':
            router.visit(route('entities.spells.edit', { spell: spellId }));
            emit('edit', props.spell);
            break;
        case 'quick-edit':
            emit('quick-edit', props.spell);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('spell');
            const url = resolveEntityRouteUrl('spell', 'show', spellId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien copié !");
            }
            emit('copy-link', props.spell);
            break;
        }
        case 'delete':
            emit('delete', props.spell);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader mode="compact">
            <template #media>
                <div class="group relative w-16 h-16">
                    <div class="absolute top-1 left-1 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <EntityUsableDot :state="stateValue" />
                    </div>
                    <div class="absolute top-1 right-1 z-20 transition-opacity duration-150 group-hover:opacity-0">
                        <Badge
                            :color="getBadgeColor('level')"
                            :auto-label="getBadgeAutoParams('level').autoLabel"
                            :auto-scheme="getBadgeAutoParams('level').autoScheme"
                            :auto-tone="getBadgeAutoParams('level').autoTone"
                            size="xs"
                        >
                            <CellRenderer :cell="asTextCell(getCell('level'))" ui-color="primary" />
                        </Badge>
                    </div>

                    <ImageViewer
                        v-if="spell.image"
                        :source="spell.image"
                        :alt="spell.name || 'Sort'"
                        :caption="spell.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'sm',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-wand-magic-sparkles" :alt="spell.name" size="md" />
                    </div>
                </div>
            </template>

            <template #title>
                <h3 class="text-lg font-semibold text-primary-100 truncate">{{ spell.name }}</h3>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="spells"
                        :entity="spell"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </template>

            <template #mainInfos>
                <div v-if="displayMetaFields.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <EntityPropertyDisplay
                        v-for="fieldKey in displayMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        :display-mode="PROPERTY_DISPLAY_MODES.extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="xs"
                        class="min-w-0 max-w-full text-primary-200"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <div v-if="technicalFields.length > 0 || userCanEditFields.length > 0" class="pt-3 border-t border-base-300">
            <div v-if="technicalFields.length > 0" class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                <EntityPropertyDisplay
                    v-for="fieldKey in technicalFields"
                    :key="fieldKey"
                    :field-key="fieldKey"
                    :entity="spell"
                    entity-type="spell"
                    :display-mode="PROPERTY_DISPLAY_MODES.compact"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    size="xs"
                    class="min-w-0 max-w-[20rem]"
                />
            </div>

            <div v-if="userCanEditFields.length > 0" class="mt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-primary-300 mb-2">Paramètres</div>
                <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-primary-200/80">
                    <EntityPropertyDisplay
                        v-for="fieldKey in userCanEditFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="spell"
                        entity-type="spell"
                        :display-mode="PROPERTY_DISPLAY_MODES.compact"
                        variant="badge"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="min-w-0 max-w-[20rem]"
                    />
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
