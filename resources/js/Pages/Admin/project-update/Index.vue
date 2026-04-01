<script setup>
/**
 * Lance `project:deps` via file d’attente — réservé aux environnements non production.
 */
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

defineOptions({ layout: AdminArea });

const { setPageTitle } = usePageTitle();
setPageTitle('Mise à jour stack');

const props = defineProps({
    isProduction: { type: Boolean, default: false },
});

const page = usePage();
const unlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);

function onPasswordConfirmed() {
    unlocked.value = true;
}

const form = useForm({
    all: true,
    apt: false,
    composer: false,
    pnpm: false,
    css: false,
    docs: false,
    dump: false,
    migrate: false,
    ide: false,
    laravel_clear: false,
});

const canSubmit = computed(
    () => unlocked.value && !form.processing && !props.isProduction
);

function submit() {
    form.post(route('admin.project-update.run'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Mise à jour stack" />

    <div class="space-y-6 pb-8 max-w-2xl">
        <div>
            <h1 class="text-2xl font-semibold text-base-content">Mise à jour de la stack</h1>
            <p class="mt-2 text-sm text-base-content/70">
                Enfile un job qui exécute <code class="rounded bg-base-300 px-1">project:deps</code> (apt, composer,
                pnpm, CSS, etc.). <strong>Interdit en production</strong> — réservé aux machines de développement.
            </p>
        </div>

        <div v-if="isProduction" class="alert alert-warning text-sm">
            Cette action n’est pas disponible lorsque <code class="px-1">APP_ENV=production</code>.
        </div>

        <div
            v-else-if="!unlocked"
            class="rounded-box border border-warning/40 bg-warning/10 p-6 text-center space-y-4"
        >
            <p class="text-warning-content text-sm">Confirmez votre mot de passe pour continuer.</p>
            <Btn color="primary" @click="showConfirmModal = true">Confirmer</Btn>
        </div>

        <form v-else class="space-y-4 rounded-box border border-base-content/10 bg-base-100/50 p-4" @submit.prevent="submit">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium">
                <input v-model="form.all" type="checkbox" class="checkbox checkbox-sm" />
                Tout (défaut commande : stack + migrate)
            </label>
            <p class="text-xs text-base-content/60">Décochez « Tout » pour ne sélectionner que des cibles précises :</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.apt" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> apt</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.composer" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" />
                    composer</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.pnpm" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> pnpm</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.css" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> css</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.docs" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> docs</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.dump" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> dump-autoload</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.migrate" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" />
                    migrate</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.ide" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" /> IDE helper</label
                >
                <label class="flex items-center gap-2 cursor-pointer"
                    ><input v-model="form.laravel_clear" type="checkbox" class="checkbox checkbox-sm" :disabled="form.all" />
                    optimize:clear Laravel</label
                >
            </div>

            <Btn type="submit" color="primary" :disabled="!canSubmit">
                {{ form.processing ? 'Envoi…' : 'Lancer la mise à jour' }}
            </Btn>
        </form>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Confirmer votre identité"
            message="Cette opération modifie les dépendances du projet. Entrez votre mot de passe."
            confirm-label="Continuer"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
