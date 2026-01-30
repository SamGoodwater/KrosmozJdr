<script setup>
/**
 * ShopViewMinimal — Vue Minimal pour Shop
 * 
 * @description
 * Petite carte qui s'étend au survol.
 * Utilisée dans des grilles, petites modals ou hovers.
 * 
 * @props {Shop} shop - Instance du modèle Shop
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Image from '@/Pages/Atoms/data-display/Image.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getShopFieldDescriptors } from "@/Entities/shop/shop-descriptors";

const props = defineProps({
    shop: {
        type: Object,
        required: true
    },
    showActions: {
        type: Boolean,
        default: true
    },
    displayMode: {
        type: String,
        default: 'hover',
        validator: (v) => ['compact', 'hover', 'extended'].includes(v),
    }
});

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'quick-edit', 'delete', 'action']);

const isHovered = ref(props.displayMode === 'extended');
const canHoverExpand = computed(() => props.displayMode === 'hover');
const { copyToClipboard } = useCopyToClipboard();
const { downloadPdf } = useDownloadPdf('shop');
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can('shop', 'viewAny'),
        createAny: permissions.can('shop', 'createAny'),
        updateAny: permissions.can('shop', 'updateAny'),
        deleteAny: permissions.can('shop', 'deleteAny'),
        manageAny: permissions.can('shop', 'manageAny'),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getShopFieldDescriptors(ctx.value));

const stateValue = computed(() => props.shop?.state ?? props.shop?._data?.state ?? null);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn('[ShopViewMinimal] visibleIf failed for', fieldKey, e);
            return false;
        }
    }
    return true;
};

// Champs importants à afficher
const importantFields = computed(() => ['name', 'location', 'npc_name', 'items_count', 'price'].filter(canShowField));

// Champs supplémentaires à afficher au hover
const expandedFields = computed(() => [
    'read_level',
    'write_level',
].filter(canShowField));

const getFieldIcon = (fieldKey) => {
    return descriptors.value?.[fieldKey]?.general?.icon || 'fa-solid fa-info-circle';
};

const getCell = (fieldKey) => {
    return props.shop.toCell(fieldKey, {
        size: 'sm',
        context: 'minimal',
    });
};

const tooltipForField = (fieldKey, cell) => {
    const label = descriptors.value?.[fieldKey]?.general?.label || fieldKey;
    const value = (cell?.value === null || typeof cell?.value === 'undefined' || String(cell?.value) === '') ? '-' : cell.value;
    return `${label} : ${value}`;
};

const handleAction = async (actionKey) => {
    const shopId = props.shop.id;
    if (!shopId) return;

    switch (actionKey) {
        case 'view':
            router.visit(route('entities.shops.show', { shop: shopId }));
            emit('view', props.shop);
            break;
        case 'edit':
            router.visit(route('entities.shops.edit', { shop: shopId }));
            emit('edit', props.shop);
            break;
        case 'delete':
            emit('delete', props.shop);
            break;
    }
};
</script>

<template>
    <div 
        class="relative rounded-lg border border-base-300 transition-all duration-300 overflow-hidden"
        :class="{ 
            'bg-base-200 shadow-lg': isHovered,
            'bg-base-100': !isHovered
        }"
        :style="{ 
            width: isHovered ? 'auto' : '150px',
            minWidth: '150px',
            maxWidth: isHovered ? '300px' : '200px',
            height: isHovered ? 'auto' : '100px',
            minHeight: '80px'
        }"
        @mouseenter="canHoverExpand && (isHovered = true)"
        @mouseleave="canHoverExpand && (isHovered = false)">
        <div class="absolute top-1 left-1 z-20">
            <EntityUsableDot :state="stateValue" />
        </div>
        
        <div class="p-3">
            <!-- En-tête avec image, nom et actions -->
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <Image
                        v-if="shop.image"
                        :src="shop.image"
                        :alt="shop.name || 'Shop'"
                        class="w-8 h-8 rounded object-cover flex-shrink-0"
                    />
                    <Tooltip :content="shop.name || 'Shop'" placement="top">
                        <span class="font-semibold text-primary-100 text-sm truncate block">
                            <CellRenderer
                                :cell="getCell('name')"
                                ui-color="primary"
                            />
                        </span>
                    </Tooltip>
                </div>
                
                <div v-if="showActions && isHovered" class="flex-shrink-0">
                    <EntityActions
                        entity-type="shop"
                        :entity="shop"
                        format="buttons"
                        display="icon-only"
                        size="xs"
                        color="primary"
                        :context="{ inPanel: false }"
                        @action="handleAction"
                    />
                </div>
            </div>

            <!-- Infos importantes en icônes avec tooltips -->
            <div class="flex gap-2 flex-wrap">
                <template v-for="field in importantFields" :key="field">
                    <Tooltip
                        :content="tooltipForField(field, getCell(field))"
                        placement="top"
                    >
                        <div class="flex items-center gap-1 px-2 py-1 bg-base-200 rounded">
                            <Icon
                                :source="getFieldIcon(field)"
                                size="xs"
                                class="text-primary-400"
                            />
                            <span class="text-xs text-primary-300 font-medium">
                                <CellRenderer
                                    :cell="getCell(field)"
                                    ui-color="primary"
                                />
                            </span>
                        </div>
                    </Tooltip>
                </template>
            </div>

            <!-- Contenu supplémentaire au hover -->
            <div 
                v-if="isHovered" 
                class="mt-2 pt-2 border-t border-base-300 space-y-1 text-xs text-primary-300 animate-fade-in">
                <div
                    v-for="key in expandedFields"
                    :key="key"
                    class="flex items-start gap-2"
                >
                    <Tooltip
                        :content="tooltipForField(key, getCell(key))"
                        placement="left"
                    >
                        <div class="flex items-start gap-2 w-full">
                            <Icon
                                :source="getFieldIcon(key)"
                                size="xs"
                                class="text-primary-400 flex-shrink-0 mt-0.5"
                            />
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-primary-400">
                                    {{ descriptors?.[key]?.general?.label || key }}:
                                </div>
                                <div class="text-primary-200 truncate">
                                    <CellRenderer
                                        :cell="getCell(key)"
                                        ui-color="primary"
                                    />
                                </div>
                            </div>
                        </div>
                    </Tooltip>
                </div>
            </div>
        </div>
    </div>
</template>
