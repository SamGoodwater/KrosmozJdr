<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

defineProps({
    threads: { type: Object, required: true },
});

usePageTitle().setPageTitle("Mes retours");

const statusLabel = (status) => ({
    open: "Ouvert",
    awaiting_user: "En attente de ta réponse",
    closed: "Fermé",
}[status] || status);
</script>

<template>
    <Head title="Mes retours" />
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-4 p-4">
        <header>
            <h1 class="text-2xl font-bold">Mes retours</h1>
            <p class="text-sm text-base-content/70">
                Suivi des bugs, erreurs et suggestions envoyés depuis ton compte.
            </p>
        </header>

        <div v-if="threads.data.length === 0" class="rounded-box border border-base-300 bg-base-100/70 p-6 text-center text-base-content/70">
            Aucun retour pour le moment.
        </div>

        <div v-else class="flex flex-col gap-3">
            <Link
                v-for="thread in threads.data"
                :key="thread.id"
                :href="thread.url"
                class="rounded-box border border-base-300 bg-base-100/70 p-4 transition hover:border-primary/50 hover:bg-base-100"
            >
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <span class="badge badge-soft badge-primary mr-2">{{ thread.type }}</span>
                        <span class="font-semibold">{{ thread.subject_preview }}</span>
                    </div>
                    <span class="badge badge-outline">{{ statusLabel(thread.status) }}</span>
                </div>
                <p class="mt-2 text-xs text-base-content/60">
                    Dernière activité : {{ thread.last_message_at ? new Date(thread.last_message_at).toLocaleString("fr-FR") : "n/a" }}
                    <span v-if="thread.user_unread_count > 0" class="ml-2 text-primary">
                        {{ thread.user_unread_count }} nouveau(x)
                    </span>
                </p>
            </Link>
        </div>
    </section>
</template>
