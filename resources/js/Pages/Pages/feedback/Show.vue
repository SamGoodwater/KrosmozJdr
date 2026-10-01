<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import PageHeader from "@/Pages/Molecules/layout/PageHeader.vue";

const props = defineProps({
    thread: { type: Object, required: true },
});

const form = useForm({
    message: "",
    attachment: null,
});

const submit = () => {
    form.post(`/feedback/${encodeURIComponent(props.thread.id)}/messages`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset("message", "attachment");
        },
    });
};
</script>

<template>
    <Head :title="thread.subject_preview || 'Retour utilisateur'" />
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-4 p-4">
        <PageHeader :title="thread.subject_preview || 'Retour utilisateur'" back-href="/feedback">
            <template v-if="thread.source_url" #subtitle>
                <a :href="thread.source_url" class="hover:underline">Page signalée : {{ thread.source_url }}</a>
            </template>
            <template #meta>
                <span class="badge badge-soft badge-primary">{{ thread.type }}</span>
                <span class="badge badge-outline">{{ thread.status }}</span>
            </template>
        </PageHeader>

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
                <span class="label-text mb-1">Répondre</span>
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
                    Envoyer
                </Btn>
            </div>
        </form>
    </section>
</template>
