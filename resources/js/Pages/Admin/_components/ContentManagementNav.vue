<script setup>
/**
 * Navigation gestion du contenu (game_master+).
 */
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import HorizontalOverflowNav from "@/Pages/Organismes/layout/HorizontalOverflowNav.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";

const page = usePage();
const { canAccess, isSuperAdmin } = usePermissions();

const path = computed(() => {
    const u = page.url.split("?")[0];
    return u.endsWith("/") && u.length > 1 ? u.slice(0, -1) : u;
});

/**
 * @param {{ path: string }} item
 */
function isItemActive(item) {
    const p = path.value;
    if (item.path === "/admin/content") {
        return p === "/admin/content" || p === "/admin/content/";
    }
    return p === item.path || p.startsWith(`${item.path}/`);
}

const navItems = computed(() => {
    const items = [
        {
            title: "Vue d’ensemble",
            href: "admin.content.dashboard.index",
            path: "/admin/content",
            icon: "fa-gauge-high",
            show: true,
        },
    ];

    if (canAccess("contentManagement") || canAccess("adminPanel")) {
        items.push({
            title: "Caractéristiques",
            href: "admin.characteristics.index",
            path: "/admin/characteristics",
            icon: "fa-sliders",
            show: true,
        });
    }

    items.push(
        {
            title: "Langues",
            href: "admin.languages.index",
            path: "/admin/languages",
            icon: "fa-language",
            show: canAccess("contentManagement") || canAccess("adminPanel"),
        },
        {
            title: "Effets",
            href: "admin.effects.index",
            path: "/admin/effects",
            icon: "fa-bolt",
            show: canAccess("effectsAdmin"),
        },
        {
            title: "Sous-effets",
            href: "admin.sub-effects.index",
            path: "/admin/sub-effects",
            icon: "fa-wand-magic-sparkles",
            show: canAccess("effectsAdmin"),
        }
    );

    return items.filter((i) => i.show);
});
</script>

<template>
    <HorizontalOverflowNav
        :items="navItems"
        :is-item-active="isItemActive"
        aria-label="Navigation gestion du contenu"
        mobile-menu-aria-label="Menu gestion du contenu"
        mobile-trigger-label="Menu contenu"
        overflow-menu-aria-label="Autres liens gestion du contenu"
    />
</template>
