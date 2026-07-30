<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

defineProps({
    threads: { type: Object, required: true },
});

usePageTitle().setPageTitle("Retours utilisateurs");
</script>

<template>
    <Head title="Retours utilisateurs" />
    <section class="mx-auto flex w-full max-w-6xl flex-col gap-4 p-4">
        <header>
            <h1 class="text-2xl font-bold">Retours utilisateurs</h1>
            <p class="text-sm text-base-content/70">Inbox admin des bugs, erreurs et suggestions.</p>
        </header>

        <div v-if="threads.data.length === 0" class="rounded-box border border-base-300 bg-base-100/70 p-6 text-center text-base-content/70">
            Aucun retour ouvert.
        </div>

        <div v-else class="overflow-hidden rounded-box border border-base-300 bg-base-100/70">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Utilisateur</th>
                        <th>Sujet</th>
                        <th>Statut</th>
                        <th>Activité</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="thread in threads.data" :key="thread.id" class="hover">
                        <td><span class="badge badge-soft badge-primary">{{ thread.type }}</span></td>
                        <td>
                            <div class="font-medium">{{ thread.user?.name || 'Utilisateur supprimé' }}</div>
                            <div class="text-xs text-base-content/60">{{ thread.user?.email }}</div>
                        </td>
                        <td>
                            <Link :href="thread.url" class="font-medium text-primary hover:underline">
                                {{ thread.subject_preview }}
                            </Link>
                            <span v-if="thread.staff_unread_count > 0" class="badge badge-sm badge-warning ml-2">
                                {{ thread.staff_unread_count }}
                            </span>
                        </td>
                        <td>{{ thread.status }}</td>
                        <td class="text-xs">
                            {{ thread.last_message_at ? new Date(thread.last_message_at).toLocaleString("fr-FR") : "n/a" }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
