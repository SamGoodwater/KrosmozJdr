<script setup>
/**
 * SidebarNav Organism
 *
 * @description
 * Sous-menu liste + détail. Desktop (≥ lg) : colonne fixe. Mobile / tablette : même contenu
 * dans un menu déroulant en tête (pleine largeur en xs ; à partir de sm : largeur max + alignement à gauche).
 *
 * @see SidebarNavPanel.vue, SidebarNavItem.vue
 */
import { computed, ref } from 'vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import SidebarNavPanel from '@/Pages/Organismes/layout/SidebarNavPanel.vue';
import { useDevice } from '@/Composables/layout/useDevice';
import { LAYOUT_APP_SIDEBAR_WIDTH_CLASS } from '@/Composables/layout/viewport-breakpoints';

const props = defineProps({
    title: { type: String, default: '' },
    description: { type: String, default: '' },
    itemsByGroup: { type: Object, default: () => ({}) },
    groupLabels: { type: Object, default: () => ({}) },
    items: { type: Array, default: () => [] },
    groupsMode: { type: [String, Boolean], default: false },
    searchable: { type: Boolean, default: false },
    searchPlaceholder: { type: String, default: 'Rechercher…' },
    searchKeys: { type: Array, default: () => ['name', 'label', 'key', 'id'] },
    getItemHref: { type: [Function, String], default: null },
    isItemActive: { type: Function, default: () => false },
    getItemCssClasses: { type: Function, default: () => '' },
    getItemColor: { type: Function, default: () => null },
    getItemIcon: { type: Function, default: () => null },
    getItemIconUrl: { type: Function, default: () => null },
    getItemLabel: { type: Function, default: (item) => item.name || item.label || item.id || '' },
    getItemLabelSecondary: { type: Function, default: () => null },
    getItemKey: { type: Function, default: (item) => item.id ?? item.key ?? JSON.stringify(item) },
    getItemClick: { type: Function, default: null },
    iconBasePath: { type: String, default: '/storage/images/icons/caracteristics' },
    accentGlassBorder: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md'].includes(v),
    },
});

const { isDesktop } = useDevice();
const searchQuery = ref('');

const hasGroups = computed(() => {
    if (props.groupsMode === false && props.items?.length) return false;
    const keys = Object.keys(props.itemsByGroup || {});
    return keys.some((k) => (props.itemsByGroup[k] || []).length > 0);
});

const groups = computed(() => {
    if (!hasGroups.value) return [];
    return Object.keys(props.itemsByGroup || {});
});

function itemMatchesSearch(item, query) {
    if (!query || !query.trim()) return true;
    const q = query.trim().toLowerCase();
    const keys = props.searchKeys.length ? props.searchKeys : ['name', 'label', 'key', 'id'];
    const fromKeys = keys.some((k) => {
        const v = item?.[k];
        return v != null && String(v).toLowerCase().includes(q);
    });
    if (fromKeys) return true;
    const label = props.getItemLabel(item);
    return label != null && String(label).toLowerCase().includes(q);
}

const filteredItemsByGroup = computed(() => {
    const q = searchQuery.value?.trim() || '';
    const byGroup = props.itemsByGroup || {};
    if (!q) return byGroup;
    const result = {};
    for (const groupKey of Object.keys(byGroup)) {
        const items = (byGroup[groupKey] || []).filter((item) =>
            itemMatchesSearch(item, q)
        );
        if (items.length > 0) result[groupKey] = items;
    }
    return result;
});

const filteredFlatItems = computed(() => {
    const q = searchQuery.value?.trim() || '';
    const list = props.items || [];
    if (!q) return list;
    return list.filter((item) => itemMatchesSearch(item, q));
});

const panelBind = computed(() => ({
    title: props.title,
    description: props.description,
    searchable: props.searchable,
    searchPlaceholder: props.searchPlaceholder,
    hasGroups: hasGroups.value,
    groups: groups.value,
    groupLabels: props.groupLabels,
    groupsMode: props.groupsMode,
    filteredFlatItems: filteredFlatItems.value,
    filteredItemsByGroup: filteredItemsByGroup.value,
    getItemHref: props.getItemHref,
    getItemClick: props.getItemClick,
    isItemActive: props.isItemActive,
    getItemCssClasses: props.getItemCssClasses,
    getItemColor: props.getItemColor,
    getItemIcon: props.getItemIcon,
    getItemIconUrl: props.getItemIconUrl,
    getItemLabel: props.getItemLabel,
    getItemLabelSecondary: props.getItemLabelSecondary,
    getItemKey: props.getItemKey,
    iconBasePath: props.iconBasePath,
    accentGlassBorder: props.accentGlassBorder,
}));

const mobileTriggerLabel = computed(() => {
    if (!hasGroups.value) {
        const active = filteredFlatItems.value.find((i) => props.isItemActive(i));
        if (active) return props.getItemLabel(active);
    } else {
        for (const g of groups.value) {
            const arr = filteredItemsByGroup.value[g] || [];
            const active = arr.find((i) => props.isItemActive(i));
            if (active) return props.getItemLabel(active);
        }
    }
    return props.title || 'Liste';
});
</script>

<template>
    <div class="sidebar-nav-root w-full shrink-0 lg:w-64 lg:shrink-0">
        <aside
            v-if="isDesktop"
            :class="[
                'sidebar-nav hidden h-full min-h-0 shrink-0 flex-col overflow-hidden border-r border-base-300 bg-base-200/50 lg:flex',
                LAYOUT_APP_SIDEBAR_WIDTH_CLASS,
            ]"
        >
            <SidebarNavPanel v-bind="panelBind" v-model:search-query="searchQuery">
                <template #nav-before>
                    <slot name="nav-before" />
                </template>
                <template #empty>
                    <slot name="empty">Aucun élément.</slot>
                </template>
                <template #nav-after>
                    <slot name="nav-after" />
                </template>
                <template #item-suffix="slotProps">
                    <slot name="item-suffix" v-bind="slotProps" />
                </template>
                <template
                    v-for="g in groups"
                    :key="'d-' + g"
                    #[`group-${g}`]="slotProps"
                >
                    <slot :name="`group-${g}`" v-bind="slotProps" />
                </template>
            </SidebarNavPanel>
        </aside>

        <div
            v-else
            class="sidebar-nav-mobile mb-3 w-full max-w-full self-start sm:max-w-md lg:hidden"
        >
            <Dropdown
                class="w-full"
                placement="bottom-start"
                variant="glass"
                size="sm"
                :aria-label="title || 'Liste de navigation'"
            >
                <template #trigger>
                    <Btn
                        variant="glass"
                        color="neutral"
                        size="md"
                        class="w-full min-h-10 justify-between gap-2"
                    >
                        <span class="min-w-0 truncate">{{ mobileTriggerLabel }}</span>
                        <span
                            class="fa-solid fa-chevron-down shrink-0 opacity-70"
                            aria-hidden="true"
                        />
                    </Btn>
                </template>
                <template #content>
                    <div
                        class="max-h-[min(70vh,28rem)] w-[min(calc(100vw-2rem),24rem)] overflow-hidden rounded-box border border-base-300 bg-base-200/50"
                    >
                        <SidebarNavPanel v-bind="panelBind" v-model:search-query="searchQuery">
                            <template #nav-before>
                                <slot name="nav-before" />
                            </template>
                            <template #empty>
                                <slot name="empty">Aucun élément.</slot>
                            </template>
                            <template #nav-after>
                                <slot name="nav-after" />
                            </template>
                            <template #item-suffix="slotProps">
                                <slot name="item-suffix" v-bind="slotProps" />
                            </template>
                            <template
                                v-for="g in groups"
                                :key="'m-' + g"
                                #[`group-${g}`]="slotProps"
                            >
                                <slot :name="`group-${g}`" v-bind="slotProps" />
                            </template>
                        </SidebarNavPanel>
                    </div>
                </template>
            </Dropdown>
        </div>
    </div>
</template>

<style scoped lang="scss">
.sidebar-nav {
    display: flex;
    flex-direction: column;
}
</style>
