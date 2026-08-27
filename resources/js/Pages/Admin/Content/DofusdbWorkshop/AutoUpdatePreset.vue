<script setup>
/**
 * Preset atelier : lance `project:data sync` (fiches auto_update) via la file d’attente.
 *
 * @example
 * <DofusdbAutoUpdatePreset :entity-choices="entityChoices" :catalog-type-choices="catalogTypeChoices" :console-job="consoleJob" />
 */
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useProjectConsoleJob } from "@/Composables/admin/useProjectConsoleJob";
import AdminCommandMeta from "@/Pages/Admin/_components/AdminCommandMeta.vue";
import AdminConsoleJobPanel from "@/Pages/Admin/_components/AdminConsoleJobPanel.vue";
import Alert from "@/Pages/Atoms/feedback/Alert.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";

const props = defineProps({
    entityChoices: { type: Array, default: () => [] },
    catalogTypeChoices: { type: Array, default: () => [] },
    consoleJob: { type: Object, default: null },
});

const { liveJob, pollError, busy } = useProjectConsoleJob(props, { title: "Synchronisation auto_update" });

const ENTITY_LABELS = {
    class: "Classes",
    spell: "Sorts",
    monster: "Monstres",
    panoply: "Panoplies",
    resource: "Ressources",
    item: "Objets",
    consumable: "Consommables",
};

const CATALOG_LABELS = {
    all: "Tout le catalogue (types + races + sorts)",
    monster: "Races monstres (DofusDB)",
    spell: "Types de sorts (seeder)",
    resource: "Types ressources",
    consumable: "Types consommables",
    item: "Types objets",
    equipment: "Types équipement (= objets)",
};

const form = useForm({
    entities: [],
    catalog_types: [],
    races: false,
    lang: "fr",
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
        return "Aucun filtre : mise à jour complète des fiches auto_update, comme `php artisan project:data sync`.";
    }
    if (hasCatalogFilter.value && !hasEntityFilter.value) {
        return "Catalogue seul : types/races mis à jour ; le sync entités ne partira pas tant qu’une entité n’est pas cochée.";
    }
    return "Les options cochées seront passées à `project:data sync`.";
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
    form.post(route("admin.content.dofusdb.sync"), { preserveScroll: true });
}

const langOptions = [
    { value: "fr", label: "Français" },
    { value: "en", label: "English" },
    { value: "de", label: "Deutsch" },
    { value: "es", label: "Español" },
    { value: "pt", label: "Português" },
];
</script>

<template>
    <div class="space-y-4">
        <AdminCommandMeta
            signature="project:data sync"
            cron-key="project_data_sync"
            cron-command="project:data sync"
        />
        <AdminConsoleJobPanel :job="liveJob" :poll-error="pollError" />

        <Alert v-if="$page.props.flash?.success" color="success" class="text-sm" :show-icon="false" role="status">
            {{ $page.props.flash.success }}
        </Alert>
        <Alert v-if="$page.props.flash?.error" color="error" class="text-sm" :show-icon="false" role="alert">
            {{ $page.props.flash.error }}
        </Alert>

        <form class="space-y-6" @submit.prevent="submit">
            <section class="rounded-box border border-base-300 bg-base-200/40 p-4">
                <h3 class="font-medium">Catalogue (types / races)</h3>
                <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm">
                    <input v-model="form.races" type="checkbox" class="checkbox checkbox-sm" />
                    <span>Races monstres</span>
                </label>
                <div class="mt-3 flex flex-wrap gap-2">
                    <label
                        v-for="key in catalogTypeChoices"
                        :key="`cat-${key}`"
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
                <h3 class="font-medium">Entités (auto_update)</h3>
                <p class="mt-1 text-xs text-base-content/60">Tout décoché = toutes les entités supportées.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <label
                        v-for="key in entityChoices"
                        :key="`ent-${key}`"
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
                <h3 class="font-medium">Options</h3>
                <div class="mt-3 max-w-xs">
                    <SelectField v-model="form.lang" label="Langue DofusDB" :options="langOptions" :searchable="false" />
                </div>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-center gap-2 text-sm">
                        <input v-model="form.noimage" type="checkbox" class="checkbox checkbox-sm" />
                        <span>Pas d’images</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-sm">
                        <input v-model="form.skip_cache" type="checkbox" class="checkbox checkbox-sm" />
                        <span>Ignorer le cache HTTP</span>
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-sm">
                        <input v-model="form.dry_run" type="checkbox" class="checkbox checkbox-sm" />
                        <span>Dry-run</span>
                    </label>
                </div>
            </section>

            <p class="text-sm text-base-content/70">{{ summaryHint }}</p>
            <Btn type="submit" color="primary" :disabled="form.processing || busy">
                {{ busy ? "Job déjà en cours…" : form.processing ? "Envoi…" : "Lancer tout auto_update" }}
            </Btn>
        </form>
    </div>
</template>
