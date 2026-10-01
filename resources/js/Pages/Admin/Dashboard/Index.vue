<script setup>
/**
 * Tableau de bord administration : liens rapides vers les outils (détail dans la barre latérale).
 */
import { Head } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';

defineOptions({ layout: AdminArea });

defineProps({
    appEnv: { type: String, default: '' },
});
</script>

<template>
    <Head title="Administration" />

    <PageHeader
        title="Administration"
        subtitle="Utilisez le menu à gauche pour accéder à la synchronisation DofusDB, aux sauvegardes, à la mise à jour de l’environnement de dev, aux utilisateurs, au scrapping, aux caractéristiques et aux effets."
    >
        <template v-if="appEnv" #meta>
            <span class="text-xs text-base-content/50">Environnement : {{ appEnv }}</span>
        </template>
    </PageHeader>

    <div class="space-y-6 pb-8">
        <div class="rounded-box border border-base-content/10 bg-base-100/50 p-4 text-sm text-base-content/80">
            <p class="font-medium text-base-content">Raccourcis</p>
            <ul class="mt-2 list-disc list-inside space-y-1">
                <li>Les actions lourdes (sync, sauvegarde, mise à jour stack) nécessitent le rôle super admin et un worker de file actif.</li>
                <li>Scrapping et certaines opérations demandent la confirmation du mot de passe (session récente).</li>
            </ul>
        </div>
    </div>
</template>
