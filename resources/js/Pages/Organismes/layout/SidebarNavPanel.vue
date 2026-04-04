<script setup>
/**
 * Corps du menu secondaire (en-tête, recherche, liste) — utilisé dans la colonne desktop
 * et dans le dropdown mobile / tablette.
 *
 * @see SidebarNav.vue
 */
import { computed } from 'vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SidebarNavItem from '@/Pages/Organismes/layout/SidebarNavItem.vue';

const searchQuery = defineModel('searchQuery', { type: String, default: '' });

const props = defineProps({
    title: { type: String, default: '' },
    description: { type: String, default: '' },
    searchable: { type: Boolean, default: false },
    searchPlaceholder: { type: String, default: 'Rechercher…' },
    hasGroups: { type: Boolean, required: true },
    groups: { type: Array, required: true },
    groupLabels: { type: Object, default: () => ({}) },
    groupsMode: { type: [String, Boolean], default: false },
    filteredFlatItems: { type: Array, required: true },
    filteredItemsByGroup: { type: Object, required: true },
    getItemHref: { type: [Function, String], default: null },
    getItemClick: { type: Function, default: null },
    isItemActive: { type: Function, required: true },
    getItemCssClasses: { type: Function, required: true },
    getItemColor: { type: Function, required: true },
    getItemIcon: { type: Function, required: true },
    getItemIconUrl: { type: Function, required: true },
    getItemLabel: { type: Function, required: true },
    getItemLabelSecondary: { type: Function, required: true },
    getItemKey: { type: Function, required: true },
    iconBasePath: { type: String, default: '/storage/images/icons/caracteristics' },
    accentGlassBorder: {
        type: String,
        default: 'md',
        validator: (v) => ['sm', 'md'].includes(v),
    },
});

const accentGlassClass = computed(() =>
    props.accentGlassBorder === 'sm' ? 'glass-border-l-sm' : 'glass-border-l-md'
);

function resolveHref(item) {
    if (typeof props.getItemHref === 'function') return props.getItemHref(item);
    if (typeof props.getItemHref === 'string') return item[props.getItemHref];
    return item.href ?? null;
}
</script>

<template>
    <div class="sidebar-nav-panel flex min-h-0 flex-1 flex-col">
        <div class="sidebar-nav-header shrink-0 p-3">
            <h2 v-if="title" class="font-semibold text-base-content">{{ title }}</h2>
            <p v-if="description" class="mt-1 text-xs text-base-content/70">{{ description }}</p>
            <div v-if="searchable" class="mt-2">
                <InputField
                    v-model="searchQuery"
                    type="search"
                    :placeholder="searchPlaceholder"
                    size="sm"
                    class="input-sm"
                />
            </div>
        </div>

        <nav class="sidebar-nav-list flex-1 min-h-0 overflow-y-auto p-2">
            <slot name="nav-before" />

            <template v-if="!hasGroups">
                <div
                    v-if="filteredFlatItems.length === 0"
                    class="px-3 py-4 text-sm text-base-content/70"
                >
                    <slot name="empty">Aucun élément.</slot>
                </div>
                <template v-else>
                    <SidebarNavItem
                        v-for="item in filteredFlatItems"
                        :key="getItemKey(item)"
                        :item="item"
                        :accent-glass-class="accentGlassClass"
                        :resolve-href="resolveHref"
                        :is-item-active="isItemActive"
                        :get-item-click="getItemClick"
                        :get-item-label="getItemLabel"
                        :get-item-label-secondary="getItemLabelSecondary"
                        :get-item-icon="getItemIcon"
                        :get-item-icon-url="getItemIconUrl"
                        :get-item-color="getItemColor"
                        :get-item-css-classes="getItemCssClasses"
                        :icon-base-path="iconBasePath"
                    >
                        <template #item-suffix="slotProps">
                            <slot name="item-suffix" v-bind="slotProps" />
                        </template>
                    </SidebarNavItem>
                </template>
            </template>

            <template v-else>
                <div
                    v-if="
                        Object.keys(filteredItemsByGroup).every(
                            (g) => !(filteredItemsByGroup[g] || []).length
                        )
                    "
                    class="px-3 py-4 text-sm text-base-content/70"
                >
                    <slot name="empty">Aucun résultat.</slot>
                </div>
                <template v-else>
                    <div
                        v-for="groupKey in groups"
                        :key="groupKey"
                        v-show="(filteredItemsByGroup[groupKey] || []).length > 0"
                        :class="[
                            'sidebar-nav-group',
                            groupsMode === 'collapse' &&
                                'collapse collapse-arrow rounded-box border border-base-300 bg-base-100',
                        ]"
                    >
                        <template v-if="groupsMode === 'collapse'">
                            <input
                                type="checkbox"
                                :checked="
                                    (filteredItemsByGroup[groupKey] || []).some((i) =>
                                        isItemActive(i)
                                    )
                                "
                                class="peer"
                            />
                            <div
                                class="collapse-title min-h-0 py-2 font-medium peer-checked:min-h-0"
                            >
                                {{ groupLabels[groupKey] || groupKey }}
                            </div>
                            <div class="collapse-content">
                                <div class="flex flex-col gap-0.5 pb-2">
                                    <SidebarNavItem
                                        v-for="item in filteredItemsByGroup[groupKey] || []"
                                        :key="getItemKey(item)"
                                        :item="item"
                                        :accent-glass-class="accentGlassClass"
                                        :resolve-href="resolveHref"
                                        :is-item-active="isItemActive"
                                        :get-item-click="getItemClick"
                                        :get-item-label="getItemLabel"
                                        :get-item-label-secondary="getItemLabelSecondary"
                                        :get-item-icon="getItemIcon"
                                        :get-item-icon-url="getItemIconUrl"
                                        :get-item-color="getItemColor"
                                        :get-item-css-classes="getItemCssClasses"
                                        :icon-base-path="iconBasePath"
                                        secondary-title
                                    >
                                        <template #item-suffix="slotProps">
                                            <slot name="item-suffix" v-bind="slotProps" />
                                        </template>
                                    </SidebarNavItem>
                                    <slot
                                        :name="`group-${groupKey}`"
                                        :group="groupKey"
                                        :items="filteredItemsByGroup[groupKey]"
                                    />
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div
                                class="sidebar-nav-group-title py-1.5 font-medium text-base-content/80"
                            >
                                {{ groupLabels[groupKey] || groupKey }}
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <SidebarNavItem
                                    v-for="item in filteredItemsByGroup[groupKey] || []"
                                    :key="getItemKey(item)"
                                    :item="item"
                                    :accent-glass-class="accentGlassClass"
                                    :resolve-href="resolveHref"
                                    :is-item-active="isItemActive"
                                    :get-item-click="getItemClick"
                                    :get-item-label="getItemLabel"
                                    :get-item-label-secondary="getItemLabelSecondary"
                                    :get-item-icon="getItemIcon"
                                    :get-item-icon-url="getItemIconUrl"
                                    :get-item-color="getItemColor"
                                    :get-item-css-classes="getItemCssClasses"
                                    :icon-base-path="iconBasePath"
                                >
                                    <template #item-suffix="slotProps">
                                        <slot name="item-suffix" v-bind="slotProps" />
                                    </template>
                                </SidebarNavItem>
                                <slot
                                    :name="`group-${groupKey}`"
                                    :group="groupKey"
                                    :items="filteredItemsByGroup[groupKey]"
                                />
                            </div>
                        </template>
                    </div>
                </template>
            </template>

            <slot name="nav-after" />
        </nav>
    </div>
</template>

<style scoped lang="scss">
.sidebar-nav-list {
    scrollbar-width: thin;
}

.sidebar-nav-group-title {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

:deep(.collapse-title) {
    min-height: 0;
}
</style>
