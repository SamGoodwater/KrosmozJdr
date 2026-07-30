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
    if (item.path === '/admin/recap') {
        return p === '/admin/recap' || p === '/admin/recap/';
    }
    return p === item.path || p.startsWith(`${item.path}/`);
}

const adminNavItems = computed(() => {
    const items = [];

    items.push({
        title: 'Récapitulatif',
        href: 'admin.recap.index',
        path: '/admin/recap',
        icon: 'fa-chart-pie',
        show: true,
    });
    items.push({
        title: 'Journal',
        href: 'admin.activity-log.index',
        url: '/admin/activity-log',
        path: '/admin/activity-log',
        icon: 'fa-clock-rotate-left',
        show: canAccess('adminPanel'),
    });
    items.push({
        title: 'Retours',
        href: 'admin.feedback.index',
        url: '/admin/feedback',
        path: '/admin/feedback',
        icon: 'fa-comments',
        show: canAccess('adminPanel'),
    });

    if (isSuperAdmin.value) {
        items.push(
            {
                title: 'Planning cron',
                href: 'admin.project-schedule.index',
                path: '/admin/project-schedule',
                icon: 'fa-clock',
                show: true,
            },
            {
                title: 'Reviews dev',
                href: 'admin.project-review.index',
                path: '/admin/project-review',
                icon: 'fa-file-lines',
                show: true,
            },
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
                title: 'Affichage entités',
                href: 'admin.entity-display-visibility.index',
                path: '/admin/entity-display-visibility',
                icon: 'fa-eye',
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
