<script setup>
/**
 * Super admin : lance `project:data sync` via file d’attente (job).
 * Porte d’accès identique au scrapping : ConfirmPasswordModal + session (user.password.confirm).
 */
import { computed, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';

const { setPageTitle } = usePageTitle();

const page = usePage();
const maintenanceUnlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showConfirmModal = ref(false);

const props = defineProps({
    entityChoices: { type: Array, default: () => [] },
    catalogTypeChoices: { type: Array, default: () => [] },
});

defineOptions({ layout: AdminArea });
setPageTitle('Maintenance données');

function onPasswordConfirmed() {
    maintenanceUnlocked.value = true;
}

const ENTITY_LABELS = {
    class: 'Classes',
    spell: 'Sorts',
    monster: 'Monstres',
    panoply: 'Panoplies',
    resource: 'Ressources',
    item: 'Objets',
    consumable: 'Consommables',
};

const CATALOG_LABELS = {
    all: 'Tout le catalogue (types + races + sorts)',
    monster: 'Races monstres (DofusDB)',
    spell: 'Types de sorts (seeder)',
    resource: 'Types ressources',
    consumable: 'Types consommables',
    item: 'Types objets',
    equipment: 'Types équipement (= objets)',
};

const form = useForm({
    entities: [],
    catalog_types: [],
    races: false,
    lang: 'fr',
    noimage: false,
    skip_cache: false,
    dry_run: false,
    skip_clear_queue: false,
    skip_notify: false,
});

const hasEntityFilter = computed(() => Array.isArray(form.entities) && form.entities.length > 0);
const hasCatalogFilter = computed(
    () => (Array.isArray(form.catalog_types) && form.catalog_types.length > 0) || form.races
);

const summaryHint = computed(() => {
    if (!hasCatalogFilter.value && !hasEntityFilter.value) {
        return 'Aucun filtre catalogue ni entité : mise à jour complète (toutes les entités avec auto_update), comme `php artisan project:data sync`.';
    }
    if (hasCatalogFilter.value && !hasEntityFilter.value) {
        return 'Catalogue seul : types/races mis à jour ; le sync entités (scrapping auto_update) ne sera pas lancé tant que vous ne cochez pas au moins une entité.';
    }
    return 'Les options cochées seront passées à la commande `project:data sync`.';
});

function toggleInList(listRef, value) {
    const list = Array.isArray(form[listRef]) ? [...form[listRef]] : [];
    const i = list.indexOf(value);
    if (i === -1) {
        list.push(value);
    } else {
        list.splice(i, 1);
    }
    form[listRef] = list;
}

function isChecked(listRef, value) {
    return Array.isArray(form[listRef]) && form[listRef].includes(value);
}

function submit() {
    form.post(route('admin.project-maintenance.sync'), { preserveScroll: true });
}

const langOptions = [
    { value: 'fr', label: 'Français' },
    { value: 'en', label: 'English' },
    { value: 'de', label: 'Deutsch' },
    { value: 'es', label: 'Español' },
    { value: 'pt', label: 'Português' },
];
</script>

<template>
    <Head title="Maintenance données" />

    <div class="mx-auto max-w-3xl px-4 py-8">
        <h1 class="text-2xl font-semibold text-base-content">Synchronisation données DofusDB</h1>
        <p class="mt-2 text-sm text-base-content/70">
            Enfile un job qui exécute <code class="rounded bg-base-300 px-1">project:data sync</code> côté serveur. Un
            worker doit traiter la file (<code class="rounded bg-base-300 px-1">queue:work</code>).
        </p>

        <!-- Porte d’accès : même principe que la page Scrapping (ConfirmPasswordModal + session) -->
        <div
            v-if="!maintenanceUnlocked"
            class="rounded-box border border-warning/40 bg-warning/10 mt-6 p-6 text-center space-y-4"
        >
            <p class="text-warning-content text-sm">
                Cette section est réservée aux super administrateurs et protégée. Confirme ton mot de passe pour accéder
                aux actions de maintenance (sync données).
            </p>
            <Btn color="primary" @click="showConfirmModal = true">
                Accéder à la maintenance
            </Btn>
        </div>

        <template v-else>
            <div role="region" aria-label="Sécurité" class="alert alert-info mt-4 text-sm">
                <span>
                    Zone sensible : l’envoi du formulaire exige une session récente de confirmation mot de passe (comme le
                    scrapping). Les requêtes sont validées côté serveur, limitées en débit et journalisées ; le traitement
                    est asynchrone (file d’attente).
                </span>
            </div>

            <div v-if="$page.props.flash?.success" role="status" class="alert alert-success mt-6 text-sm">
                {{ $page.props.flash.success }}
            </div>

            <form class="mt-8 space-y-8" @submit.prevent="submit">
                <section class="rounded-box border border-base-300 bg-base-200/40 p-4">
                    <h2 class="font-medium text-base-content">Catalogue (types / races)</h2>
                    <p class="mt-1 text-xs text-base-content/60">
                        Laissez vide pour ne pas rafraîchir les référentiels catalogue avant le sync entités.
                    </p>
                    <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm">
                        <input v-model="form.races" type="checkbox" class="checkbox checkbox-sm" />
                        <span>Raccourci races monstres (<code class="text-xs">--races</code>)</span>
                    </label>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <label
                            v-for="key in catalogTypeChoices"
                            :key="key"
                            class="flex cursor-pointer items-center gap-2 rounded-lg border border-base-300 bg-base-100 px-3 py-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                :checked="isChecked('catalog_types', key)"
                                @change="toggleInList('catalog_types', key)"
                            />
                            <span>{{ CATALOG_LABELS[key] ?? key }}</span>
                        </label>
                    </div>
                </section>

                <section class="rounded-box border border-base-300 bg-base-200/40 p-4">
                    <h2 class="font-medium text-base-content">Entités (mise à jour auto_update)</h2>
                    <p class="mt-1 text-xs text-base-content/60">
                        Laissez tout décoché pour traiter toutes les entités supportées (équivalent sans
                        <code class="text-xs">--entity</code>).
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <label
                            v-for="key in entityChoices"
                            :key="key"
                            class="flex cursor-pointer items-center gap-2 rounded-lg border border-base-300 bg-base-100 px-3 py-2 text-sm"
                        >
                            <input
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                :checked="isChecked('entities', key)"
                                @change="toggleInList('entities', key)"
                            />
                            <span>{{ ENTITY_LABELS[key] ?? key }}</span>
                        </label>
                    </div>
                </section>

                <section class="rounded-box border border-base-300 bg-base-200/40 p-4">
                    <h2 class="font-medium text-base-content">Options</h2>
                    <div class="mt-3 max-w-xs">
                        <SelectFieldNative v-model="form.lang" label="Langue DofusDB (catalogue)" :options="langOptions" />
                    </div>
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.noimage" type="checkbox" class="checkbox checkbox-sm" />
                            <span>Pas de téléchargement d’images</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.skip_cache" type="checkbox" class="checkbox checkbox-sm" />
                            <span>Ignorer le cache HTTP</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.dry_run" type="checkbox" class="checkbox checkbox-sm" />
                            <span>Dry-run (simulation)</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.skip_clear_queue" type="checkbox" class="checkbox checkbox-sm" />
                            <span>Ne pas vider la queue avant sync</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input v-model="form.skip_notify" type="checkbox" class="checkbox checkbox-sm" />
                            <span>Pas de notification admin en fin</span>
                        </label>
                    </div>
                </section>

                <div class="rounded-box border border-warning/40 bg-warning/10 p-4 text-sm text-base-content">
                    <strong class="font-medium">Résumé</strong>
                    <p class="mt-1">{{ summaryHint }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <Btn type="submit" color="primary" :disabled="form.processing">
                        {{ form.processing ? 'Envoi…' : 'Lancer la synchronisation (file d’attente)' }}
                    </Btn>
                    <span v-if="form.hasErrors" class="text-sm text-error">
                        {{ form.errors?.entities?.[0] || form.errors?.catalog_types?.[0] || 'Erreur de validation.' }}
                    </span>
                </div>
            </form>
        </template>

        <ConfirmPasswordModal
            v-model:open="showConfirmModal"
            title="Accéder à la maintenance données"
            message="Cette section permet de lancer des synchronisations DofusDB côté serveur. Entre ton mot de passe pour confirmer ton identité."
            confirm-label="Accéder"
            @confirmed="onPasswordConfirmed"
        />
    </div>
</template>
