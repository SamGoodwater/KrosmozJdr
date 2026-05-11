<script setup>
/**
 * ConsumableViewCompact — Vue Compact pour Consumable
 *
 * @description
 * Vue réduite d'un consommable avec informations essentielles.
 * Layout aligné sur ResourceViewCompact : EntityViewHeader mode compact,
 * badges niveau+type, rareté+prix, description, ingrédients.
 *
 * @props {Consumable} consumable - Instance du modèle Consumable
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 * @props {Object} [tableMeta] - Meta caractéristiques
 * @props {Object|null} [characteristicRuntime] - Payload runtime optionnel
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getConsumableFieldDescriptors } from '@/Entities/consumable/consumable-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import EntityPropertyDisplay from '@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue';
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';
import { PROPERTY_DISPLAY_MODES } from '@/Utils/Entity/Constants';

const props = defineProps({
    consumable: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    tableMeta: { type: Object, default: () => ({}) },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('consumables', 'viewAny'),
        createAny: permissions.can('consumables', 'createAny'),
        updateAny: permissions.can('consumables', 'updateAny'),
        deleteAny: permissions.can('consumables', 'deleteAny'),
        manageAny: permissions.can('consumables', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getConsumableFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'consumable' });

const getCell = (fieldKey) =>
    props.consumable.toCell(fieldKey, { size: 'md', context: 'compact' });

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        consumable_type: 'info',
        level: 'warning',
        rarity: 'auto',
        price: 'success',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const ingredients = computed(() => {
    const raw = props.consumable?.resources ?? props.consumable?._data?.resources ?? [];
    return Array.isArray(raw) ? raw : [];
});

const handleAction = async (actionKey) => {
    const consumableId = props.consumable.id;
    if (!consumableId) return;
    switch (actionKey) {
        case 'view':
            router.visit(route('entities.consumables.show', { consumable: consumableId }));
            emit('view', props.consumable);
            break;
        case 'edit':
            router.visit(route('entities.consumables.edit', { consumable: consumableId }));
            emit('edit', props.consumable);
            break;
        case 'quick-edit':
            emit('quick-edit', props.consumable);
            break;
        case 'copy-link': {
            const cfg = getEntityRouteConfig('consumable');
            const url = resolveEntityRouteUrl('consumable', 'show', consumableId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.consumable);
            break;
        }
        case 'delete':
            emit('delete', props.consumable);
            break;
    }
};
</script>

<template>
    <div class="space-y-3">
        <EntityViewHeader mode="compact">
            <template #media>
                <div class="group relative w-16 h-16">
                    <ImageViewer
                        v-if="consumable.image"
                        :src="consumable.image"
                        :alt="consumable.name || 'Consommable'"
                        :caption="consumable.name || ''"
                        preload="hover"
                        :image-props="{ size: 'sm', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-flask" :alt="consumable.name" size="md" />
                    </div>
                </div>
            </template>

            <template #title>
                <h3 class="text-lg font-semibold text-primary-100 truncate leading-tight">{{ consumable.name }}</h3>
            </template>

            <template #actions>
                <div class="flex items-center gap-2">
                    <EntityActions
                        v-if="showActions"
                        entity-type="consumable"
                        :entity="consumable"
                        format="dropdown"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </template>

            <template #mainInfos>
                <div class="space-y-1 mt-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <template v-if="canShowField('level')">
                            <Badge
                                :color="getBadgeColor('level')"
                                :auto-label="String(consumable.level ?? '')"
                                auto-scheme="level"
                                auto-tone="mid"
                                size="xs"
                            >
                                Nvx {{ consumable.level ?? '-' }}
                            </Badge>
                        </template>
                        <template v-if="canShowField('consumable_type')">
                            <Badge
                                color="auto"
                                :auto-label="consumable.consumableType?.name ?? consumable.consumableType?.label ?? '-'"
                                auto-scheme="labelHash"
                                auto-tone="light"
                                size="xs"
                            >
                                {{ consumable.consumableType?.name ?? consumable.consumableType?.label ?? '-' }}
                            </Badge>
                        </template>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                        <template v-if="canShowField('rarity')">
                            <Badge
                                :color="(getRarityConfig(consumable.rarity ?? 0))?.color ?? 'neutral'"
                                size="xs"
                            >
                                {{ (getRarityConfig(consumable.rarity ?? 0))?.label ?? '-' }}
                            </Badge>
                        </template>
                        <EntityPropertyDisplay
                            v-if="canShowField('price')"
                            field-key="price"
                            :entity="consumable"
                            entity-type="consumable"
                            :display-mode="PROPERTY_DISPLAY_MODES.compact"
                            :descriptors="descriptors"
                            :table-meta="tableMeta"
                            size="xs"
                            class="max-w-[18rem] min-w-0 text-primary-200"
                        />
                    </div>
                    <p v-if="consumable.description" class="text-primary-300 text-xs mt-1 line-clamp-2">{{ consumable.description }}</p>
                </div>
            </template>
        </EntityViewHeader>

        <ResourceIngredientsList
            v-if="ingredients.length > 0"
            :ingredients="ingredients"
            class="pt-3 border-t border-base-300"
        />
    </div>
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
