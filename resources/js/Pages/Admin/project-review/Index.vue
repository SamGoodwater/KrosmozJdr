<script setup>
/**
 * Rapports Markdown `project:review` — lancement manuel ou téléchargements.
 */
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useProjectConsoleJob } from '@/Composables/admin/useProjectConsoleJob';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import AdminCommandMeta from '@/Pages/Admin/_components/AdminCommandMeta.vue';
import AdminConsoleJobPanel from '@/Pages/Admin/_components/AdminConsoleJobPanel.vue';
import AdminRunAction from '@/Pages/Admin/_components/AdminRunAction.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import { ACTION } from '@/Utils/atomic-design/actionLabels';

defineOptions({ layout: AdminArea });

const props = defineProps({
    reports: { type: Array, required: true },
    reportsPathHint: { type: String, default: '' },
    consoleJob: { type: Object, default: null },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, { title: 'Review' });

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    run_all: false,
    pint: false,
    tests: false,
    test_back: true,
    test_front: false,
    phpstan: false,
    eslint: false,
    security: false,
    docs: false,
});

const canSubmit = computed(() => unlocked.value && !form.processing && !busy.value);

/** @param {string} basename */
function downloadUrl(basename) {
    return route('admin.project-review.download', basename);
}

function refreshReports() {
    router.reload({ only: ['reports'], preserveScroll: true });
}

function submit() {
    if (!canSubmit.value) return;
    form.post(route('admin.project-review.run'), { preserveScroll: true });
}

function setRunPartial() {
    form.run_all = false;
}

function setRunAll() {
    form.run_all = true;
}
</script>

<template>
    <Head title="Reviews dev" />

    <PageHeader title="Reviews dev">
        <template #subtitle>
            Les fichiers se trouvent sous
            <code class="rounded bg-base-300 px-1">{{ props.reportsPathHint }}</code>. La génération est exécutée en file
            d’attente (worker requis ; durée très longue si périmètre large).
        </template>
        <template #meta>
            <AdminCommandMeta signature="project:review" />
        </template>
        <template #actions>
            <Btn variant="ghost" size="sm" type="button" @click="refreshReports">
                <i :class="ACTION.refresh.icon" class="mr-1.5" aria-hidden="true"></i>
                {{ ACTION.refresh.label }}
            </Btn>
        </template>
        <template #primary>
            <AdminRunAction
                :unlocked="unlocked"
                :busy="busy"
                :processing="form.processing"
                label="Planifier"
                @confirm="showConfirmModal = true"
                @run="submit"
            />
        </template>
    </PageHeader>

    <div class="space-y-8 pb-8 max-w-3xl">
        <section class="space-y-4 rounded-box border border-base-content/10 bg-base-100/40 p-4">
            <h2 class="text-lg font-medium">Historique</h2>
            <div v-if="!props.reports?.length" class="text-sm text-base-content/60">Aucun rapport pour l’instant.</div>
            <ul v-else class="divide-y divide-base-content/10 text-sm">
                <li v-for="r in props.reports" :key="r.basename" class="py-2 flex flex-wrap items-center justify-between gap-2">
                    <span class="font-mono text-xs">{{ r.basename }}</span>
                    <span class="text-[11px] text-base-content/50">{{ Math.round(r.size / 1024) }} KB — {{ r.modified_at }}</span>
                    <a
                        class="btn btn-ghost btn-xs"
                        :href="downloadUrl(r.basename)"
                        :download="r.basename"
                    >
                        <i class="fa-solid fa-download mr-1" aria-hidden="true"></i>Télécharger
                    </a>
                </li>
            </ul>
        </section>

        <p v-if="page.props.flash?.success" class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2">
            {{ page.props.flash.success }}
        </p>
        <p v-if="page.props.flash?.error" class="text-error text-sm rounded-box border border-error/30 bg-error/10 px-3 py-2">
            {{ page.props.flash.error }}
        </p>

        <AdminConsoleJobPanel :job="liveJob" :poll-error="pollError" :cancelling="cancelling" @cancel="cancelJob" />

        <section class="rounded-box border border-base-content/10 p-6 space-y-4">
            <h2 class="text-lg font-medium">Nouvelle review</h2>
            <div
                v-if="!unlocked"
                class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm"
            >
                Confirmez votre mot de passe (bouton « Confirmer » en haut) pour choisir le périmètre et planifier une
                review ; le serveur doit traiter une file d’attente.
            </div>
            <form v-else class="space-y-4" @submit.prevent="submit">
                <div class="flex flex-wrap gap-2 items-center">
                    <Btn size="xs" variant="outline" color="neutral" type="button" @click.prevent="setRunAll">
                        Tout le périmètre (--all)
                    </Btn>
                    <Btn size="xs" variant="outline" color="neutral" type="button" @click.prevent="setRunPartial">
                        Périmètre partiel
                    </Btn>
                </div>
                <fieldset v-if="!form.run_all" class="space-y-2 text-sm grid sm:grid-cols-2 gap-x-6 gap-y-2">
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.pint" class="checkbox checkbox-sm" /> Pint (--test)</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.tests" class="checkbox checkbox-sm" /> Tests back + front</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.test_back" class="checkbox checkbox-sm" /> PHPUnit seul</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.test_front" class="checkbox checkbox-sm" /> Vitest</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.phpstan" class="checkbox checkbox-sm" /> PHPStan</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.eslint" class="checkbox checkbox-sm" /> ESLint</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.security" class="checkbox checkbox-sm" /> composer audit</label>
                    <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" v-model="form.docs" class="checkbox checkbox-sm" /> Documentation</label>
                </fieldset>
                <p v-if="form.errors.scope" class="text-error text-sm">{{ form.errors.scope }}</p>
            </form>
        </section>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="Lancer une review peut être coûteux en ressources. Entrez votre mot de passe pour continuer."
            confirm-label="Confirmer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
