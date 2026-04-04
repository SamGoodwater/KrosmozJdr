<script setup>
/**
 * Navigation horizontale de l’espace administration : construit la liste des entrées
 * selon les permissions, puis délègue l’UI à `HorizontalOverflowNav`.
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import HorizontalOverflowNav from '@/Pages/Organismes/layout/HorizontalOverflowNav.vue';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const page = usePage();
const { canAccess, isSuperAdmin } = usePermissions();

const path = computed(() => {
    const u = page.url.split('?')[0];
    return u.endsWith('/') && u.length > 1 ? u.slice(0, -1) : u;
});

/**
 * @param {{ path: string }} item
 * @returns {boolean}
 */
function isItemActive(item) {
    const p = path.value;
    if (item.path === '/admin') {
        return p === '/admin' || p === '/admin/';
    }
    return p === item.path || p.startsWith(`${item.path}/`);
}

const adminNavItems = computed(() => {
    const items = [];

    items.push({
        title: 'Vue d’ensemble',
        href: 'admin.dashboard.index',
        path: '/admin',
        icon: 'fa-gauge-high',
        show: true,
    });

    if (isSuperAdmin.value) {
        items.push(
            {
                title: 'Sync données',
                href: 'admin.project-maintenance.index',
                path: '/admin/project-maintenance',
                icon: 'fa-database',
                show: true,
            },
            {
                title: 'Sauvegarde',
                href: 'admin.backup.index',
                path: '/admin/backup',
                icon: 'fa-floppy-disk',
                show: true,
            },
            {
                title: 'Mise à jour stack',
                href: 'admin.project-update.index',
                path: '/admin/project-update',
                icon: 'fa-arrows-rotate',
                show: true,
            }
        );
    }

    if (canAccess('adminPanel')) {
        items.push(
            {
                title: 'Utilisateurs',
                href: 'user.index',
                path: '/user/list',
                icon: 'fa-users',
                show: true,
            },
            {
                title: 'Scrapping',
                href: 'scrapping.index',
                path: '/scrapping',
                icon: 'fa-magnifying-glass',
                show: canAccess('scrapping'),
            },
            {
                title: 'Caractéristiques',
                href: 'admin.characteristics.index',
                path: '/admin/characteristics',
                icon: 'fa-sliders',
                show: true,
            },
            {
                title: 'Mappings scrapping',
                href: 'admin.scrapping-mappings.index',
                path: '/admin/scrapping-mappings',
                icon: 'fa-diagram-project',
                show: true,
            },
            {
                title: 'Mappings effets DofusDB',
                href: 'admin.dofusdb-effect-mappings.index',
                path: '/admin/dofusdb-effect-mappings',
                icon: 'fa-link',
                show: true,
            }
        );
    }

    if (canAccess('effectsAdmin')) {
        items.push(
            {
                title: 'Effets',
                href: 'admin.effects.index',
                path: '/admin/effects',
                icon: 'fa-bolt',
                show: true,
            },
            {
                title: 'Sous-effets',
                href: 'admin.sub-effects.index',
                path: '/admin/sub-effects',
                icon: 'fa-wand-magic-sparkles',
                show: true,
            }
        );
    }

    return items.filter((i) => i.show);
});
</script>

<template>
    <HorizontalOverflowNav
        :items="adminNavItems"
        :is-item-active="isItemActive"
        aria-label="Navigation administration"
        mobile-menu-aria-label="Menu administration"
        mobile-trigger-label="Menu"
        overflow-menu-aria-label="Autres liens d’administration"
    />
</template>
