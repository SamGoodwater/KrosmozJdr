<script setup>
/**
 * Planification Laravel (source de vérité BDD `project_schedule_tasks`).
 */
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';

defineOptions({ layout: AdminArea });

const { setPageTitle } = usePageTitle();
setPageTitle('Planning (cron Laravel)');

const page = usePage();

const props = defineProps({
    tasks: { type: Array, required: true },
    schedulerHint: { type: String, default: '' },
});

/** @type {Record<number, { enabled: boolean, cron_expression: string, without_overlapping: boolean }>} */
const drafts = reactive({});
for (const t of props.tasks ?? []) {
    drafts[t.id] = {
        enabled: t.enabled,
        cron_expression: t.cron_expression,
        without_overlapping: t.without_overlapping,
    };
}

/** @param {number} taskId */
function saveTask(taskId) {
    router.patch(route('admin.project-schedule.tasks.update', taskId), drafts[taskId], {
        preserveScroll: true,
    });
}

function draft(taskId) {
    return drafts[taskId];
}

</script>

<template>
    <Head title="Planning (cron Laravel)" />

    <div class="space-y-6 pb-8 max-w-5xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Planning des tâches</h1>
            <p class="mt-2 text-sm text-base-content/70">
                Ces réglages alimentent
                <code class="rounded bg-base-300 px-1">schedule:run</code>
                (crontab serveur : une ligne par minute). Chaque ligne du catalogue correspond à une commande Artisan
                (ou un job) ; le lien « Page » ouvre l’écran thématique quand il existe. Les clés
                <code class="rounded bg-base-300 px-1">task_key</code>
                ne changent pas.
            </p>
            <p v-if="schedulerHint" class="mt-3 text-xs text-warning/90 border border-warning/30 rounded-box p-3">
                {{ schedulerHint }}
            </p>
            <p v-if="page.props.flash?.success" class="mt-3 text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2">
                {{ page.props.flash.success }}
            </p>
            <p v-if="page.props.flash?.error" class="mt-3 text-error text-sm">{{ page.props.flash.error }}</p>
            <ul
                v-if="page.props.errors && Object.keys(page.props.errors).length"
                class="mt-3 text-error text-sm list-disc list-inside space-y-0.5"
            >
                <li v-for="(msgs, field) in page.props.errors" :key="field">
                    {{ field }} :
                    {{ Array.isArray(msgs) ? msgs.join(', ') : msgs }}
                </li>
            </ul>
        </div>

        <div class="rounded-box border border-base-content/10 overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Tâche</th>
                        <th>Commande</th>
                        <th>Page</th>
                        <th>Activée</th>
                        <th>Cron</th>
                        <th>Sans ré-entrée</th>
                        <th aria-label="Actions" />
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
                            <input v-model="draft(t.id).enabled" type="checkbox" class="checkbox checkbox-sm" />
                        </td>
                        <td>
                            <Tooltip content="Minute heure jour_mois mois jour_semaine">
                                <input
                                    v-model="draft(t.id).cron_expression"
                                    type="text"
                                    maxlength="120"
                                    class="input input-bordered input-sm font-mono w-48 lg:w-64"
                                    spellcheck="false"
                                />
                            </Tooltip>
                        </td>
                        <td>
                            <input v-model="draft(t.id).without_overlapping" type="checkbox" class="checkbox checkbox-sm" />
                        </td>
                        <td class="text-right">
                            <Btn size="xs" color="primary" @click.prevent="saveTask(t.id)" type="button">
                                Enregistrer
                            </Btn>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
