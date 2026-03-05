<script setup>
/**
 * DynamicMenu Organism
 * 
 * @description
 * Composant organisme pour afficher le menu dynamique des pages.
 * - Affiche les pages publiées et visibles dans le menu
 * - Gère l'arborescence (pages parentes/enfants)
 * - Supporte les menus déroulants
 * - Intègre avec le système de navigation existant
 * 
 * @props {String} currentRoute - Route actuelle (optionnel, pour déterminer l'item actif)
 * 
 * @example
 * <DynamicMenu :current-route="$page.url" />
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useDynamicMenu } from '@/Composables/layout/useDynamicMenu';
import GlassMenuItem from '@/Pages/Atoms/navigation/GlassMenuItem.vue';
import GlassMenuSectionTitle from '@/Pages/Atoms/navigation/GlassMenuSectionTitle.vue';
import GlassMenuGroup from '@/Pages/Atoms/navigation/GlassMenuGroup.vue';

const page = usePage();
const { menuItems, loading, error, isPageActive, shouldMenuBeOpen } = useDynamicMenu();

const props = defineProps({
    currentRoute: {
        type: String,
        default: null
    }
});

/**
 * Route actuelle
 */
const currentRouteValue = computed(() => {
    return props.currentRoute || page.url;
});

/**
 * Rend un item de menu récursivement
 */
const renderMenuItem = (item) => {
    const hasChildren = item.children && item.children.length > 0;
    const isActive = isPageActive(item, currentRouteValue.value);
    const isOpen = shouldMenuBeOpen(item, currentRouteValue.value);
    
    if (hasChildren) {
        // Menu avec sous-menu
        return {
            type: 'parent',
            item,
            isActive,
            isOpen,
            children: item.children.map(child => renderMenuItem(child))
        };
    } else {
        // Menu simple
        return {
            type: 'simple',
            item,
            isActive
        };
    }
};

/**
 * Items de menu formatés
 */
const formattedMenuItems = computed(() => {
    return menuItems.value.map(item => renderMenuItem(item));
});

const groupedMenuItems = computed(() => {
    const ungrouped = [];
    const groupedByTitle = new Map();

    for (const item of formattedMenuItems.value) {
        const rawGroup = typeof item.item?.menu_group === 'string' ? item.item.menu_group.trim() : '';
        if (rawGroup === '') {
            ungrouped.push(item);
            continue;
        }

        if (!groupedByTitle.has(rawGroup)) {
            groupedByTitle.set(rawGroup, []);
        }
        groupedByTitle.get(rawGroup).push(item);
    }

    const groups = Array.from(groupedByTitle.entries())
        .map(([title, items]) => ({
            title,
            items,
            minOrder: Math.min(...items.map(entry => Number(entry.item?.order ?? 0))),
        }))
        .sort((a, b) => a.minOrder - b.minOrder);

    return { ungrouped, groups };
});
</script>

<template>
    <div class="dynamic-menu">
        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center py-4">
            <div class="loading loading-spinner loading-sm"></div>
            <span class="ml-2 text-sm text-base-content/50">Chargement du menu...</span>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="alert alert-error alert-sm">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <p class="text-xs">Erreur lors du chargement du menu</p>
            </div>
        </div>

        <!-- Menu items -->
        <template v-else-if="formattedMenuItems.length > 0">
            <template v-for="menuItem in groupedMenuItems.ungrouped" :key="menuItem.item.id">
                <!-- Menu parent avec sous-menu -->
                <GlassMenuGroup
                    v-if="menuItem.type === 'parent'"
                    :open="menuItem.isOpen"
                    class="main-menu-group"
                    compact
                >
                    <template #title>{{ menuItem.item.title }}</template>
                    <template #default>
                            <GlassMenuItem
                                v-for="child in menuItem.children"
                                :key="child.item.id"
                                :href="child.item.url"
                                class="main-menu-item"
                                compact
                                :active="child.isActive"
                                hover3d
                            >
                                {{ child.item.title }}
                            </GlassMenuItem>
                    </template>
                </GlassMenuGroup>

                <!-- Menu simple -->
                <GlassMenuItem
                    v-else
                    :href="menuItem.item.url"
                    :active="menuItem.isActive"
                    class="main-menu-item"
                    compact
                    hover3d
                >
                    {{ menuItem.item.title }}
                </GlassMenuItem>
            </template>

            <template v-for="group in groupedMenuItems.groups" :key="group.title">
                <GlassMenuSectionTitle compact>{{ group.title }}</GlassMenuSectionTitle>
                <template v-for="menuItem in group.items" :key="menuItem.item.id">
                    <GlassMenuGroup
                        v-if="menuItem.type === 'parent'"
                        :open="menuItem.isOpen"
                        class="main-menu-group"
                        compact
                    >
                        <template #title>{{ menuItem.item.title }}</template>
                        <template #default>
                            <GlassMenuItem
                                v-for="child in menuItem.children"
                                :key="child.item.id"
                                :href="child.item.url"
                                class="main-menu-item"
                                compact
                                :active="child.isActive"
                                hover3d
                            >
                                {{ child.item.title }}
                            </GlassMenuItem>
                        </template>
                    </GlassMenuGroup>

                    <GlassMenuItem
                        v-else
                        :href="menuItem.item.url"
                        :active="menuItem.isActive"
                        class="main-menu-item"
                        compact
                        hover3d
                    >
                        {{ menuItem.item.title }}
                    </GlassMenuItem>
                </template>
            </template>
        </template>

        <!-- Empty state -->
        <div v-else class="text-center py-4 text-sm text-base-content/50">
            <p>Aucune page disponible dans le menu.</p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.dynamic-menu {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Rendre l'état actif du menu principal plus discret */
.dynamic-menu :deep(.main-menu-item.glass-menu-item-active) {
    color: color-mix(in srgb, var(--color-base-content) 88%, transparent);
    background: color-mix(in srgb, var(--color-base-100) 30%, transparent);
    border-color: color-mix(in srgb, var(--color-base-content) 16%, transparent);
    box-shadow:
        inset 0 0 0 1px color-mix(in srgb, var(--color-primary-400) 24%, transparent),
        0 0 0 2px color-mix(in srgb, var(--color-primary-400) 10%, transparent);
}

/* Même logique pour les groupes ouverts (parents actifs) */
.dynamic-menu :deep(.main-menu-group[open] > .glass-menu-group-summary) {
    color: color-mix(in srgb, var(--color-base-content) 88%, transparent);
    background: color-mix(in srgb, var(--color-base-100) 28%, transparent);
    border-color: color-mix(in srgb, var(--color-base-content) 16%, transparent);
}
</style>

