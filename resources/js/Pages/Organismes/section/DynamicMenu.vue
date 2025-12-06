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
import MenuItem from '@/Pages/Atoms/navigation/MenuItem.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';

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
            <template v-for="menuItem in formattedMenuItems" :key="menuItem.item.id">
                <!-- Menu parent avec sous-menu -->
                <li v-if="menuItem.type === 'parent'">
                    <details :open="menuItem.isOpen">
                        <summary class="flex items-center">
                            <Icon 
                                source="fa-file" 
                                pack="solid"
                                alt="Page" 
                                size="md" 
                                class="mr-2" 
                            />
                            <span>{{ menuItem.item.title }}</span>
                        </summary>
                        <ul class="ml-4">
                            <MenuItem
                                v-for="child in menuItem.children"
                                :key="child.item.id"
                                :href="child.item.url"
                                :active="child.isActive"
                            >
                                {{ child.item.title }}
                            </MenuItem>
                        </ul>
                    </details>
                </li>

                <!-- Menu simple -->
                <MenuItem
                    v-else
                    :href="menuItem.item.url"
                    :active="menuItem.isActive"
                    icon="fa-file"
                    pack="solid"
                >
                    {{ menuItem.item.title }}
                </MenuItem>
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
    // Styles spécifiques au menu dynamique si nécessaire
}
</style>

