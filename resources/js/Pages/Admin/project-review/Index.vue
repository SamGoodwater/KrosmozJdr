<script setup>
/**
 * Rapports Markdown `project:review` — lancement manuel ou téléchargements.
 */
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const { setPageTitle } = usePageTitle();
setPageTitle('Reviews dev');

const props = defineProps({
    reports: { type: Array, required: true },
    reportsPathHint: { type: String, default: '' },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);

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

const canSubmit = computed(() => unlocked.value && !form.processing);

/** @param {string} basename */
function downloadUrl(basename) {
    return route('admin.project-review.download', basename);
}

function refreshReports() {
    router.reload({ only: ['reports'], preserveScroll: true });
}

function submit() {
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

    <div class="space-y-8 pb-8 max-w-3xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Rapports de review</h1>
            <p class="mt-2 text-sm text-base-content/70">
                Les fichiers se trouvent sous
                <code class="rounded bg-base-300 px-1">{{ props.reportsPathHint }}</code>. La génération est exécutée en file
                d’attente (worker requis ; durée très longue si périmètre large).
            </p>
        </div>

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
            <Btn color="neutral" variant="ghost" size="sm" type="button" @click.prevent="refreshReports">
                Rafraîchir la liste
            </Btn>
        </section>

        <p v-if="page.props.flash?.success" class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2">
            {{ page.props.flash.success }}
        </p>

        <section class="rounded-box border border-base-content/10 p-6 space-y-4">
            <h2 class="text-lg font-medium">Nouvelle review</h2>
            <p class="text-sm text-base-content/70">
                Confirmez votre mot de passe avant d’accéder au formulaire ; le serveur doit traiter une file
                d’attente.
            </p>
            <div
                v-if="!unlocked"
                class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
            >
                <Btn color="primary" type="button" @click.prevent="showConfirmModal = true">Confirmer le mot de passe</Btn>
                <ConfirmPasswordModal
                    v-model:open="showConfirmModal"
                    title="Confirmer votre identité"
                    message="Lancer une review peut être coûteux en ressources. Entrez votre mot de passe pour continuer."
                    confirm-label="Continuer"
                    @confirmed="onPasswordConfirmed"
                />
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
                <Btn type="submit" color="primary" :disabled="!canSubmit">
                    {{ form.processing ? 'Envoi…' : 'Planifier une review' }}
                </Btn>
            </form>
        </section>
    </div>
</template>
