<script setup>
/**
 * Navigation latérale de l’espace administration (filtrée par rôles / permissions).
 */
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const page = usePage();
const { canAccess, isSuperAdmin } = usePermissions();

const path = computed(() => {
    const u = page.url.split('?')[0];
    return u.endsWith('/') && u.length > 1 ? u.slice(0, -1) : u;
});

/** @param {{ path: string }} item */
function isNavActive(item) {
    const p = path.value;
    if (item.path === '/admin') {
        return p === '/admin' || p === '/admin/';
    }
    return p === item.path || p.startsWith(`${item.path}/`);
}

const sections = computed(() => {
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
    <nav
        class="rounded-box border border-base-content/10 bg-base-100/80 backdrop-blur-sm p-3 lg:sticky lg:top-24 self-start space-y-1"
        aria-label="Administration"
    >
        <p class="text-xs font-semibold uppercase tracking-wide text-base-content/50 px-2 pb-2">Administration</p>
        <ul class="space-y-0.5">
            <li v-for="item in sections" :key="item.href">
                <Link
                    :href="route(item.href)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm transition-colors"
                    :class="
                        isNavActive(item)
                            ? 'bg-primary/15 text-primary font-medium'
                            : 'text-base-content/80 hover:bg-base-200/80'
                    "
                >
                    <span class="fa-solid w-5 text-center opacity-90" :class="item.icon" aria-hidden="true" />
                    {{ item.title }}
                </Link>
            </li>
        </ul>
    </nav>
</template>
