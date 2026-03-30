<script setup>
/**
 * Super admin : lance `project:data sync` via file d’attente (job).
 */
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    entityChoices: { type: Array, default: () => [] },
    catalogTypeChoices: { type: Array, default: () => [] },
});

defineOptions({ layout: Main });
setPageTitle('Maintenance données');

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

        <div role="region" aria-label="Sécurité" class="alert alert-info mt-4 text-sm">
            <span>
                Zone réservée aux <strong>super administrateurs</strong> : accès soumis à
                <strong>confirmation du mot de passe</strong> (réutilisation du flux
                <code class="text-xs">password.confirm</code> / inactivité, comme le scrapping API). Les envois sont
                validés côté serveur, limités en débit, journalisés, et exécutés en file d’attente (pas de traitement
                long dans la requête HTTP).
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
    </div>
</template>
