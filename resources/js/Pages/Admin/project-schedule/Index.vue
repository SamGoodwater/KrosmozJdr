<script setup>
/**
 * Planification Laravel (source de vérité BDD `project_schedule_tasks`).
 * Un formulaire par tâche ; « Enregistrer » de l’en-tête n’envoie que les tâches modifiées.
 */
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { usePageForms } from '@/Composables/form/usePageForms';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';

defineOptions({ layout: AdminArea });

const page = usePage();

const props = defineProps({
    tasks: { type: Array, required: true },
    schedulerHint: { type: String, default: '' },
});

const pageForms = usePageForms();

/** @type {Record<number, import('@inertiajs/vue3').InertiaForm<{ enabled: boolean, cron_expression: string, without_overlapping: boolean }>>} */
const taskForms = {};
for (const t of props.tasks ?? []) {
    const form = useForm({
        enabled: t.enabled,
        cron_expression: t.cron_expression,
        without_overlapping: t.without_overlapping,
    });
    taskForms[t.id] = form;
    pageForms.register(t.task_key, form, (callbacks) =>
        form.patch(route('admin.project-schedule.tasks.update', t.id), {
            preserveScroll: true,
            preserveState: true,
            ...callbacks,
        }),
    );
}
</script>

<template>
    <Head title="Planning cron" />

    <PageHeader title="Planning cron" :forms="pageForms">
        <template #subtitle>
            Ces réglages alimentent
            <code class="rounded bg-base-300 px-1">schedule:run</code>
            (crontab serveur : une ligne par minute). Chaque ligne du catalogue correspond à une commande Artisan
            (ou un job) ; le lien « Page » ouvre l’écran thématique quand il existe. Les clés
            <code class="rounded bg-base-300 px-1">task_key</code>
            ne changent pas.
        </template>
    </PageHeader>

    <div class="space-y-6 pb-8 max-w-5xl">
        <p v-if="schedulerHint" class="text-xs text-warning/90 border border-warning/30 rounded-box p-3">
            {{ schedulerHint }}
        </p>
        <p v-if="page.props.flash?.success" class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2">
            {{ page.props.flash.success }}
        </p>
        <p v-if="page.props.flash?.error" class="text-error text-sm">{{ page.props.flash.error }}</p>
        <ul
            v-if="page.props.errors && Object.keys(page.props.errors).length"
            class="text-error text-sm list-disc list-inside space-y-0.5"
        >
            <li v-for="(msgs, field) in page.props.errors" :key="field">
                {{ field }} :
                {{ Array.isArray(msgs) ? msgs.join(', ') : msgs }}
            </li>
        </ul>

        <form class="rounded-box border border-base-content/10 overflow-x-auto" @submit.prevent="pageForms.saveAll()">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Tâche</th>
                        <th>Commande</th>
                        <th>Page</th>
                        <th>Activée</th>
                        <th>Cron</th>
                        <th>Sans ré-entrée</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in props.tasks" :key="t.id">
                        <td class="max-w-56">
                            <span class="font-medium">{{ t.label }}</span>
                            <div class="mt-1 text-[11px] text-base-content/50 font-mono">{{ t.task_key }}</div>
                        </td>
                        <td class="max-w-xs">
                            <code v-if="t.command" class="text-[11px] font-mono break-all text-base-content/80">{{
                                t.command
                            }}</code>
                            <span v-else class="text-base-content/40">—</span>
                        </td>
                        <td>
                            <Link
                                v-if="t.admin_href"
                                :href="t.admin_href"
                                class="link link-hover text-sm whitespace-nowrap"
                            >
                                {{ t.admin_label || 'Ouvrir' }}
                            </Link>
                            <span v-else class="text-base-content/40 text-sm">—</span>
                        </td>
                        <td>
                            <input v-model="taskForms[t.id].enabled" type="checkbox" class="checkbox checkbox-sm" />
                        </td>
                        <td>
                            <Tooltip content="Minute heure jour_mois mois jour_semaine">
                                <input
                                    v-model="taskForms[t.id].cron_expression"
                                    type="text"
                                    maxlength="120"
                                    class="input input-bordered input-sm font-mono w-48 lg:w-64"
                                    spellcheck="false"
                                />
                            </Tooltip>
                        </td>
                        <td>
                            <input v-model="taskForms[t.id].without_overlapping" type="checkbox" class="checkbox checkbox-sm" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</template>
