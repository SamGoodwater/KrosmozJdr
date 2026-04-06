<script setup>
/**
 * DynamicMenu Organism
 *
 * @description
 * Menu dynamique des pages. Utilise les classes glass et --color.
 * Style discret, dense, moderne.
 *
 * @example
 * <DynamicMenu :current-route="$page.url" />
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDynamicMenu } from '@/Composables/layout/useDynamicMenu';
import { getEntityIconPath } from '@/config/entities';
import GlassMenuItem from '@/Pages/Atoms/navigation/GlassMenuItem.vue';
import GlassMenuCollapsibleSection from '@/Pages/Atoms/navigation/GlassMenuCollapsibleSection.vue';

const page = usePage();
const { menuItems, loading, error, isPageActive, shouldMenuBeOpen } = useDynamicMenu();

const props = defineProps({
    currentRoute: { type: String, default: null }
});

const currentRouteValue = computed(() => props.currentRoute || page.url);

const renderMenuItem = (item) => {
    const hasChildren = item.children?.length > 0;
    const isActive = isPageActive(item, currentRouteValue.value);
    const isOpen = shouldMenuBeOpen(item, currentRouteValue.value);

    if (hasChildren) {
        return {
            type: 'parent',
            item,
            isActive,
            isOpen,
            children: item.children.map(child => renderMenuItem(child))
        };
    }
    return { type: 'simple', item, isActive };
};

const formattedMenuItems = computed(() =>
    menuItems.value.map(item => renderMenuItem(item))
);

const groupedMenuItems = computed(() => {
    const ungrouped = [];
    const groupedByTitle = new Map();

    for (const item of formattedMenuItems.value) {
        const rawGroup = String(item.item?.menu_group ?? '').trim();
        if (rawGroup === '') {
            ungrouped.push(item);
            continue;
        }
        if (!groupedByTitle.has(rawGroup)) {
            groupedByTitle.set(rawGroup, []);
        }
        groupedByTitle.get(rawGroup).push(item);
    }

    return {
        ungrouped,
        groups: Array.from(groupedByTitle.entries())
            .map(([title, items]) => {
                const flatChildren = items.flatMap(e =>
                    e.type === 'parent' ? e.children : [e]
                );
                const isOpen = items.some(e =>
                    (e.type === 'parent' && e.isOpen) || (e.type === 'simple' && e.isActive)
                );
                return {
                    title,
                    children: flatChildren,
                    isOpen,
                    minOrder: Math.min(...items.map(e => Number(e.item?.order ?? 0))),
                };
            })
            .filter((group) => group.children.length > 0)
            .sort((a, b) => a.minOrder - b.minOrder),
    };
});
</script>

<template>
    <div class="dynamic-menu">
        <div v-if="loading" class="dynamic-menu-loading">
            <div class="loading loading-spinner loading-sm"></div>
            <span class="text-sm text-base-content/50">Chargement…</span>
        </div>

        <div v-else-if="error" class="alert alert-error alert-sm">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <p class="text-xs">Erreur lors du chargement du menu</p>
        </div>

        <template v-else-if="formattedMenuItems.length > 0">
            <template v-for="menuItem in groupedMenuItems.ungrouped" :key="menuItem.item.id">
                <GlassMenuCollapsibleSection
                    v-if="menuItem.type === 'parent'"
                    :section-id="`parent-${menuItem.item.id}`"
                    :default-open="menuItem.isOpen"
                    variant="parent"
                    compact
                    class="main-menu-collapsible"
                >
                    <template #title>{{ menuItem.item.title }}</template>
                    <GlassMenuItem
                        v-for="child in menuItem.children"
                        :key="child.item.id"
                        :href="child.item.url"
                        :icon="getEntityIconPath(child.item.entity_key) || child.item.icon || ''"
                        :class="[
                            'main-menu-item',
                            'main-menu-item-child',
                            child.item.menu_item_css_classes
                        ]"
                        compact
                        :active="child.isActive"
                    >
                        {{ child.item.title }}
                    </GlassMenuItem>
                </GlassMenuCollapsibleSection>

                <GlassMenuItem
                    v-else
                    :href="menuItem.item.url"
                    :active="menuItem.isActive"
                    :class="['main-menu-item', 'main-menu-item-leaf', menuItem.item.menu_item_css_classes]"
                    compact
                >
                    {{ menuItem.item.title }}
                </GlassMenuItem>
            </template>

            <template v-for="group in groupedMenuItems.groups" :key="group.title">
                <div class="dynamic-menu-group">
                    <GlassMenuCollapsibleSection
                        :section-id="`group-${group.title}`"
                        :default-open="group.isOpen"
                        variant="group"
                        compact
                        class="main-menu-collapsible"
                    >
                        <template #title>{{ group.title }}</template>
                        <template v-for="child in group.children" :key="child.item.id">
                            <GlassMenuCollapsibleSection
                                v-if="child.type === 'parent'"
                                :section-id="`parent-${child.item.id}`"
                                :default-open="child.isOpen"
                                variant="parent"
                                compact
                                class="main-menu-collapsible"
                            >
                                <template #title>{{ child.item.title }}</template>
                                <GlassMenuItem
                                    v-for="grandchild in child.children"
                                    :key="grandchild.item.id"
                                    :href="grandchild.item.url"
                                    :icon="getEntityIconPath(grandchild.item.entity_key) || grandchild.item.icon || ''"
                                    :class="[
                                        'main-menu-item',
                                        'main-menu-item-child',
                                        grandchild.item.menu_item_css_classes
                                    ]"
                                    compact
                                    :active="grandchild.isActive"
                                >
                                    {{ grandchild.item.title }}
                                </GlassMenuItem>
                            </GlassMenuCollapsibleSection>
                            <GlassMenuItem
                                v-else
                                :href="child.item.url"
                                :icon="getEntityIconPath(child.item.entity_key) || child.item.icon || ''"
                                :class="[
                                    'main-menu-item',
                                    'main-menu-item-leaf',
                                    child.item.menu_item_css_classes
                                ]"
                                compact
                                :active="child.isActive"
                            >
                                {{ child.item.title }}
                            </GlassMenuItem>
                        </template>
                    </GlassMenuCollapsibleSection>
                </div>
            </template>
        </template>

        <div v-else class="text-center py-4 text-sm text-base-content/50">
            <p>Aucune page disponible.</p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.dynamic-menu {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.dynamic-menu-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem;
}

.dynamic-menu-group {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

/* Taille et graisse homogènes pour toutes les entrées « page » */
.dynamic-menu :deep(.main-menu-item) {
    font-size: 0.8125rem;
    font-weight: 500;
}

/* Feuille : proche du parent, légèrement plus marquée */
.dynamic-menu :deep(.main-menu-item-leaf) {
    font-weight: 600;
}

/* Sous-page : même corps, un peu plus doux */
.dynamic-menu :deep(.main-menu-item-child) {
    font-weight: 500;
    color: color-mix(in srgb, var(--color-base-content) 82%, transparent);
}

.dynamic-menu :deep(.main-menu-item-child.glass-menu-item-active) {
    color: var(--color-base-content);
}
</style>
