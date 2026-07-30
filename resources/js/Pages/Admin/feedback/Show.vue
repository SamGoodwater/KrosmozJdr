<script setup>
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
    thread: { type: Object, required: true },
});

usePageTitle().setPageTitle("Retour utilisateur");

const form = useForm({
    message: "",
    attachment: null,
});

const submit = () => {
    form.post(`/admin/feedback/${encodeURIComponent(props.thread.id)}/reply`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => form.reset("message", "attachment"),
    });
};

const updateStatus = (status) => {
    router.patch(`/admin/feedback/${encodeURIComponent(props.thread.id)}/status`, { status }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Retour utilisateur" />
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-4 p-4">
        <Link href="/admin/feedback" class="text-sm text-primary hover:underline">
            Retour aux retours
        </Link>

        <header class="rounded-box border border-base-300 bg-base-100/70 p-4">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <div>
                    <span class="badge badge-soft badge-primary mr-2">{{ thread.type }}</span>
                    <h1 class="inline text-xl font-bold">{{ thread.subject_preview }}</h1>
                    <p class="mt-1 text-sm text-base-content/70">
                        {{ thread.user?.name || 'Utilisateur supprimé' }} · {{ thread.user?.email }}
                    </p>
                </div>
                <select class="select select-bordered select-sm" :value="thread.status" @change="updateStatus($event.target.value)">
                    <option value="open">Ouvert</option>
                    <option value="awaiting_user">En attente utilisateur</option>
                    <option value="closed">Fermé</option>
                </select>
            </div>
            <a v-if="thread.source_url" :href="thread.source_url" class="mt-2 block text-xs text-base-content/60 hover:underline">
                Page signalée : {{ thread.source_url }}
            </a>
        </header>

        <div class="flex flex-col gap-3">
            <article
                v-for="message in thread.messages"
                :key="message.id"
                class="rounded-box border p-4"
                :class="message.author_role === 'staff' ? 'border-primary/30 bg-primary/5' : 'border-base-300 bg-base-100/70'"
            >
                <div class="mb-2 flex items-center justify-between gap-2 text-xs text-base-content/60">
                    <span>{{ message.author_name }} · {{ message.author_role === 'staff' ? 'Staff' : 'Utilisateur' }}</span>
                    <span>{{ new Date(message.created_at).toLocaleString("fr-FR") }}</span>
                </div>
                <p class="whitespace-pre-wrap text-sm">{{ message.body }}</p>
                <a v-if="message.attachment_url" :href="message.attachment_url" class="mt-3 inline-block text-xs text-primary hover:underline" target="_blank">
                    Pièce jointe : {{ message.attachment_name || 'ouvrir' }}
                </a>
            </article>
        </div>

        <form v-if="thread.status !== 'closed'" class="rounded-box border border-base-300 bg-base-100/70 p-4" @submit.prevent="submit">
            <label class="form-control">
                <span class="label-text mb-1">Réponse staff</span>
                <textarea v-model="form.message" class="textarea textarea-bordered min-h-28" maxlength="2000" required />
            </label>
            <input
                type="file"
                class="file-input file-input-bordered file-input-sm mt-3 w-full"
                accept=".jpg,.jpeg,.png,.gif,.pdf,.txt"
                @change="form.attachment = $event.target.files?.[0] || null"
            >
            <div class="mt-3 flex justify-end">
                <Btn type="submit" color="primary" :disabled="form.processing">
                    Répondre
                </Btn>
            </div>
        </form>
    </section>
</template>
