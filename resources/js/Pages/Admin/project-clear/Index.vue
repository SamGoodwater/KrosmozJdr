<script setup>
/**
 * Lance `project:clear` via file d’attente (super admin).
 */
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useProjectConsoleJob } from '@/Composables/admin/useProjectConsoleJob';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import AdminCommandMeta from '@/Pages/Admin/_components/AdminCommandMeta.vue';
import AdminConsoleJobPanel from '@/Pages/Admin/_components/AdminConsoleJobPanel.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const { setPageTitle } = usePageTitle();
setPageTitle('Nettoyage caches');

const props = defineProps({
    isProduction: { type: Boolean, default: false },
    consoleJob: { type: Object, default: null },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);
const { liveJob, pollError, busy, cancelJob, cancelling } = useProjectConsoleJob(props, { title: 'Nettoyage caches' });

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    mode: 'safe',
});

const canSubmit = computed(() => unlocked.value && !form.processing && !busy.value);

function submit() {
    form.post(route('admin.project-clear.run'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Nettoyage caches" />

    <div class="space-y-6 pb-8 max-w-2xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Nettoyage caches</h1>
            <p class="mt-2 text-sm text-base-content/70">
                Enfile un job qui exécute
                <code class="rounded bg-base-300 px-1">project:clear</code>
                (preset sûr pour le cron / la prod, ou nettoyage local large). Un worker doit traiter la file.
            </p>
            <AdminCommandMeta
                signature="project:clear"
                cron-key="project_clear_safe"
                cron-command="project:clear --safe"
            />
        </div>

        <p
            v-if="page.props.flash?.success"
            class="text-success text-sm rounded-box border border-success/30 bg-success/10 px-3 py-2"
        >
            {{ page.props.flash.success }}
        </p>
        <p
            v-if="page.props.flash?.error"
            class="text-error text-sm rounded-box border border-error/30 bg-error/10 px-3 py-2"
        >
            {{ page.props.flash.error }}
        </p>

        <AdminConsoleJobPanel :job="liveJob" :poll-error="pollError" :cancelling="cancelling" @cancel="cancelJob" />

        <div
            v-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
        >
            <p class="text-warning-content text-sm">Confirmez votre mot de passe pour lancer un nettoyage.</p>
            <Btn color="primary" @click="showConfirmModal = true">Confirmer</Btn>
        </div>

        <form v-else class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4" @submit.prevent="submit">
            <label class="flex items-start gap-2 cursor-pointer text-sm">
                <input v-model="form.mode" type="radio" value="safe" class="radio radio-sm mt-0.5" />
                <span>
                    <span class="font-medium">Sûr</span>
                    (<code class="text-xs">--safe</code>) : caches Laravel, rapports review, cache PHPStan. Identique au
                    cron.
                </span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer text-sm" :class="{ 'opacity-60': props.isProduction }">
                <input
                    v-model="form.mode"
                    type="radio"
                    value="all"
                    class="radio radio-sm mt-0.5"
                />
                <span>
                    <span class="font-medium">Large</span>
                    (<code class="text-xs">--all</code>) : en local, CSS généré, queue, debugbar, etc. En production, la
                    commande se comporte comme
                    <code class="text-xs">--safe</code>.
                </span>
            </label>

            <Btn type="submit" color="primary" :disabled="!canSubmit">
                {{ busy ? 'Job déjà en cours…' : form.processing ? 'Envoi…' : 'Lancer le nettoyage' }}
            </Btn>
        </form>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="Le nettoyage vide des caches applicatifs. Entrez votre mot de passe."
            confirm-label="Continuer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
