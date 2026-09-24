<script setup>
/**
 * AccountMenuPanel — Contenu du menu « Mon compte » (GlassMenu).
 *
 * Partagé entre le header desktop et le dock mobile.
 *
 * @see LoggedHeaderContainer, Layouts/Footer.vue
 */
import GlassMenuPanel from '@/Pages/Atoms/navigation/GlassMenuPanel.vue';
import GlassMenuItem from '@/Pages/Atoms/navigation/GlassMenuItem.vue';
import GlassMenuSectionTitle from '@/Pages/Atoms/navigation/GlassMenuSectionTitle.vue';
import GlassMenuDivider from '@/Pages/Atoms/navigation/GlassMenuDivider.vue';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePermissions } from '@/Composables/permissions/usePermissions';

const { canAccess, isSuperAdmin } = usePermissions();
const canManagePages = computed(() => canAccess('pagesManager'));

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <GlassMenuPanel class="min-w-72">
        <div class="flex flex-col gap-0.5">
            <GlassMenuItem route="user.show" icon="fa-user" icon-alt="" hover3d>
                Mon compte
            </GlassMenuItem>
            <GlassMenuItem href="/feedback" icon="fa-comments" icon-alt="" hover3d>
                Mes retours
            </GlassMenuItem>
        </div>
        <GlassMenuDivider />
        <template v-if="canAccess('contentManagement')">
            <div class="flex flex-col gap-0.5">
                <GlassMenuSectionTitle>Gestion du contenu</GlassMenuSectionTitle>
                <GlassMenuItem
                    route="admin.content.dashboard.index"
                    icon="fa-book-open"
                    icon-alt="Gestion du contenu"
                    hover3d
                >
                    Gestion du contenu
                </GlassMenuItem>
            </div>
            <GlassMenuDivider />
        </template>
        <template v-if="canAccess('adminPanel') || isSuperAdmin">
            <div class="flex flex-col gap-0.5">
                <GlassMenuSectionTitle>Administration</GlassMenuSectionTitle>
                <GlassMenuItem
                    route="admin.recap.index"
                    icon="fa-screwdriver-wrench"
                    icon-alt="Espace administration"
                    hover3d
                >
                    Espace administration
                </GlassMenuItem>
            </div>
            <GlassMenuDivider />
        </template>
        <template v-if="canManagePages">
            <div class="flex flex-col gap-0.5">
                <GlassMenuSectionTitle v-if="!canAccess('adminPanel')">Gestion</GlassMenuSectionTitle>
                <GlassMenuItem route="pages.index" icon="fa-file-lines" icon-alt="Pages" hover3d>
                    Pages
                </GlassMenuItem>
            </div>
            <GlassMenuDivider v-if="!canAccess('adminPanel')" />
        </template>
        <div class="flex flex-col gap-0.5">
            <GlassMenuItem icon="fa-right-from-bracket" icon-alt="" danger hover3d @click="logout">
                Se déconnecter
            </GlassMenuItem>
        </div>
    </GlassMenuPanel>
</template>
