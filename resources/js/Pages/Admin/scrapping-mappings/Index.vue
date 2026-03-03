<script setup>
/**
 * Admin Mapping scrapping (DofusDB → Krosmoz) par entité.
 * Liste des règles de mapping pour la source/entité choisie ; ajout / édition / suppression.
 */
import { computed, onMounted, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import axios from 'axios';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    source: { type: String, default: 'dofusdb' },
    sources: { type: Array, default: () => [] },
    entity: { type: String, default: '' },
    mappingKey: { type: String, default: '' },
    entities: { type: Array, default: () => [] },
    entitiesWithMapping: { type: Array, default: () => [] },
    mappings: { type: Array, default: () => [] },
    characteristicsForSelect: { type: Array, default: () => [] },
});

defineOptions({ layout: Main });
setPageTitle('Mapping scrapping DofusDB → Krosmoz');

function selectEntity(val) {
    router.get(route('admin.scrapping-mappings.index'), { source: props.source, entity: val || '' }, { preserveState: true });
}

const mappingFilter = ref(String(props.mappingKey || ''));
const filteredMappings = computed(() => {
    const rows = Array.isArray(props.mappings) ? props.mappings : [];
    const q = String(mappingFilter.value || '').trim().toLowerCase();
    if (!q) {
        return rows;
    }
    return rows.filter((m) => {
        const key = String(m?.mapping_key || '').toLowerCase();
        const fromPath = String(m?.from_path || '').toLowerCase();
        const targets = Array.isArray(m?.targets)
            ? m.targets.map((t) => `${t?.target_model || ''}.${t?.target_field || ''}`).join(' ').toLowerCase()
            : '';
        return key.includes(q) || fromPath.includes(q) || targets.includes(q);
    });
});
const prefillMappingKey = computed(() => String(props.mappingKey || '').trim());
const hasExactPrefillMatch = computed(() => {
    if (!prefillMappingKey.value) return false;
    return (Array.isArray(props.mappings) ? props.mappings : []).some(
        (m) => String(m?.mapping_key || '').trim() === prefillMappingKey.value
    );
});
const isQuickCreateFromDiagnostic = computed(() =>
    modalMode.value === 'create'
    && showModal.value
    && !!prefillMappingKey.value
    && !hasExactPrefillMatch.value
);

const showModal = ref(false);
const modalMode = ref('create');
const editingId = ref(null);
const form = ref({
    mapping_key: '',
    from_path: '',
    from_lang_aware: false,
    characteristic_id: '',
    formatters: [],
    sort_order: 0,
    targets: [{ target_model: '', target_field: '', sort_order: 0 }],
});
const formErrors = ref({});
const formSaving = ref(false);
/** Formatters édités en JSON (texte) pour le modal. */
const formattersJson = ref('[]');

function openCreate() {
    modalMode.value = 'create';
    editingId.value = null;
    form.value = {
        mapping_key: '',
        from_path: '',
        from_lang_aware: false,
        characteristic_id: '',
        formatters: [],
        sort_order: props.mappings.length,
        targets: [{ target_model: '', target_field: '', sort_order: 0 }],
    };
    formattersJson.value = '[]';
    formErrors.value = {};
    showModal.value = true;
}

function openEdit(mapping) {
    modalMode.value = 'edit';
    editingId.value = mapping.id;
    const formatters = Array.isArray(mapping.formatters) ? mapping.formatters : [];
    form.value = {
        mapping_key: mapping.mapping_key,
        from_path: mapping.from_path,
        from_lang_aware: mapping.from_lang_aware ?? false,
        characteristic_id: mapping.characteristic_id ?? '',
        formatters,
        sort_order: mapping.sort_order ?? 0,
        targets: (mapping.targets && mapping.targets.length)
            ? mapping.targets.map((t) => ({
                target_model: t.target_model,
                target_field: t.target_field,
                sort_order: t.sort_order ?? 0,
            }))
            : [{ target_model: '', target_field: '', sort_order: 0 }],
    };
    formattersJson.value = formatters.length ? JSON.stringify(formatters, null, 2) : '[]';
    formErrors.value = {};
    showModal.value = true;
}

function addTarget() {
    form.value.targets.push({ target_model: '', target_field: '', sort_order: form.value.targets.length });
}

function removeTarget(i) {
    form.value.targets.splice(i, 1);
    if (form.value.targets.length === 0) {
        form.value.targets.push({ target_model: '', target_field: '', sort_order: 0 });
    }
}

const characteristicOptions = computed(() => [
    { value: '', label: '— Aucune —' },
    ...props.characteristicsForSelect.map((c) => ({ value: String(c.id), label: `${c.name} (${c.key})` })),
]);

function submitMapping() {
    formErrors.value = {};
    let formatters = [];
    const raw = formattersJson.value.trim();
    if (raw) {
        try {
            formatters = JSON.parse(raw);
            if (!Array.isArray(formatters)) {
                formatters = [];
            }
        } catch {
            formErrors.value.formatters = 'JSON des formatters invalide.';
            return;
        }
    }
    formSaving.value = true;
    const payload = {
        source: props.source,
        entity: props.entity,
        mapping_key: form.value.mapping_key,
        from_path: form.value.from_path,
        from_lang_aware: form.value.from_lang_aware,
        characteristic_id: form.value.characteristic_id || null,
        formatters,
        sort_order: form.value.sort_order,
        targets: form.value.targets.filter((t) => t.target_model && t.target_field),
    };
    if (payload.targets.length === 0) {
        formErrors.value.targets = 'Au moins une cible (model, field) est requise.';
        formSaving.value = false;
        return;
    }
    const url =
        modalMode.value === 'create'
            ? route('admin.scrapping-mappings.store')
            : route('admin.scrapping-mappings.update', editingId.value);
    const method = modalMode.value === 'create' ? 'post' : 'patch';
    axios[method](url, payload)
        .then(() => {
            showModal.value = false;
            router.reload({ only: ['mappings', 'entitiesWithMapping'] });
        })
        .catch((err) => {
            formErrors.value = err.response?.data?.errors ?? { form: err.response?.data?.message ?? err.message };
            formSaving.value = false;
        });
}

function confirmDelete(mapping) {
    if (!confirm(`Supprimer la règle « ${mapping.mapping_key} » ?`)) return;
    axios.delete(route('admin.scrapping-mappings.destroy', mapping.id)).then(() => {
        router.reload({ only: ['mappings', 'entitiesWithMapping'] });
    });
}

function targetsSummary(mapping) {
    if (!mapping.targets?.length) return '—';
    return mapping.targets.map((t) => `${t.target_model}.${t.target_field}`).join(', ');
}

onMounted(() => {
    if (!props.entity || !prefillMappingKey.value || hasExactPrefillMatch.value) {
        return;
    }
    openCreate();
    form.value.mapping_key = prefillMappingKey.value;
    form.value.from_path = prefillMappingKey.value;
});
</script>

<template>
    <Head title="Mapping scrapping" />
    <div class="flex h-full min-h-0 w-full">
        <!-- Panneau gauche : liste des entités -->
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Mapping DofusDB → Krosmoz</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Règles par entité. Sélectionnez une entité pour afficher ou modifier ses règles.
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <button
                    v-for="e in entities"
                    :key="e"
                    type="button"
                    class="flex w-full items-center justify-between rounded-lg border-l-4 border-transparent px-3 py-2 text-left text-sm transition-colors"
                    :class="entity === e ? 'border-primary bg-primary text-primary-content' : 'hover:bg-base-300'"
                    @click="selectEntity(e)"
                >
                    <span class="truncate">{{ e }}</span>
                    <span v-if="entitiesWithMapping.includes(e)" class="badge badge-ghost badge-sm shrink-0">BDD</span>
                </button>
            </nav>
        </aside>

        <!-- Panneau droit : règles de l'entité sélectionnée -->
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <div class="mb-4">
                <h1 class="text-2xl font-bold">Règles de mapping</h1>
                <p class="mt-1 text-sm text-base-content/70">
                    Source de vérité en BDD. Après modification : <code class="rounded bg-base-300 px-1 text-xs">php artisan scrapping:seeders:export</code>
                    puis <code class="rounded bg-base-300 px-1 text-xs">db:seed --class=ScrappingEntityMappingSeeder</code> pour recréer le paramétrage.
                </p>
            </div>

            <template v-if="entity">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-lg font-semibold">Entité : {{ entity }}</h2>
                    <div class="flex items-center gap-2">
                        <InputField
                            v-model="mappingFilter"
                            label="Filtrer les règles"
                            placeholder="mapping_key, from_path, cible…"
                        />
                        <Btn variant="primary" size="sm" @click="openCreate">Ajouter une règle</Btn>
                    </div>
                </div>

                <div v-if="filteredMappings.length === 0" class="rounded-lg border border-base-300 bg-base-200/30 p-8 text-center text-base-content/70">
                    <template v-if="mappings.length === 0">
                        Aucune règle en base pour cette entité. Ajoutez des règles pour que le pipeline puisse convertir les données.
                    </template>
                    <template v-else>
                        Aucun résultat pour ce filtre. Ajustez la recherche ou videz le champ.
                    </template>
                    <br />
                    <button type="button" class="btn btn-primary btn-sm mt-4" @click="openCreate">
                        Ajouter une règle
                    </button>
                </div>

                <div v-else class="overflow-x-auto rounded-lg border border-base-300">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Clé</th>
                                <th>Chemin DofusDB</th>
                                <th>Lang. aware</th>
                                <th>Caractéristique</th>
                                <th>Cibles</th>
                                <th>Formatters</th>
                                <th class="w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in filteredMappings" :key="m.id">
                                <td class="font-mono text-sm">{{ m.mapping_key }}</td>
                                <td class="font-mono text-sm">{{ m.from_path }}</td>
                                <td>{{ m.from_lang_aware ? 'Oui' : 'Non' }}</td>
                                <td>{{ m.characteristic?.name ?? '—' }}</td>
                                <td class="text-sm">{{ targetsSummary(m) }}</td>
                                <td class="text-xs">
                                    <template v-if="m.formatters?.length">
                                        {{ m.formatters.map((f) => f.name).join(', ') }}
                                    </template>
                                    <span v-else>—</span>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <button type="button" class="btn btn-ghost btn-xs" @click="openEdit(m)">Modifier</button>
                                        <button type="button" class="btn btn-ghost btn-xs text-error" @click="confirmDelete(m)">Suppr.</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <p v-else class="text-base-content/70">
                Sélectionnez une entité dans la liste à gauche pour afficher ou gérer ses règles de mapping.
            </p>
        </main>
    </div>

    <!-- Modal création / édition -->
    <dialog class="modal" :class="{ 'modal-open': showModal }">
        <div class="modal-box max-w-2xl">
            <h3 class="text-lg font-bold">{{ modalMode === 'create' ? 'Nouvelle règle de mapping' : 'Modifier la règle' }}</h3>
            <div
                v-if="isQuickCreateFromDiagnostic"
                class="mt-3 rounded border border-info/40 bg-info/10 p-2 text-xs text-info-content"
            >
                Création rapide depuis diagnostic :
                <span class="font-mono">{{ prefillMappingKey }}</span>
            </div>
            <form @submit.prevent="submitMapping" class="space-y-4 pt-4">
                <div class="grid gap-4 sm:grid-cols-2">
                    <InputField v-model="form.mapping_key" label="Clé logique" name="mapping_key" required placeholder="ex. level, name" />
                    <InputField v-model="form.from_path" label="Chemin API (from_path)" name="from_path" required placeholder="ex. grades.0.level" />
                </div>
                <div class="flex items-center gap-2">
                    <input v-model="form.from_lang_aware" type="checkbox" class="checkbox checkbox-sm" name="from_lang_aware" />
                    <label for="from_lang_aware" class="label cursor-pointer">
                        <span class="label-text">Valeur multilingue (langAware)</span>
                    </label>
                </div>
                <div>
                    <SelectFieldNative
                        v-model="form.characteristic_id"
                        label="Caractéristique (conversion/limites)"
                        name="characteristic_id"
                        :options="characteristicOptions"
                    />
                </div>
                <div>
                    <label class="label">
                        <span class="label-text">Formatters (JSON)</span>
                    </label>
                    <textarea
                        v-model="formattersJson"
                        class="textarea textarea-bordered w-full font-mono text-sm min-h-[80px]"
                        name="formatters"
                        placeholder='[{"name":"toString","args":{}},{"name":"pickLang","args":{"lang":"fr"}}]'
                        spellcheck="false"
                    />
                    <p class="label-text-alt mt-1 text-base-content/60">
                        Tableau JSON : name + args par formatter. Vide = pas de formatter.
                    </p>
                    <p v-if="formErrors.formatters" class="text-error text-sm mt-1">{{ formErrors.formatters }}</p>
                </div>
                <div>
                    <label class="label"><span class="label-text">Cibles (model.field)</span></label>
                    <p v-if="formErrors.targets" class="text-error text-sm">{{ formErrors.targets }}</p>
                    <div v-for="(t, i) in form.targets" :key="i" class="mb-2 flex gap-2">
                        <input
                            v-model="t.target_model"
                            type="text"
                            class="input input-bordered input-sm flex-1"
                            placeholder="model (ex. creatures)"
                        />
                        <input
                            v-model="t.target_field"
                            type="text"
                            class="input input-bordered input-sm flex-1"
                            placeholder="field (ex. level)"
                        />
                        <button type="button" class="btn btn-ghost btn-sm" :disabled="form.targets.length <= 1" @click="removeTarget(i)">−</button>
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" @click="addTarget">+ Ajouter une cible</button>
                </div>
                <InputField v-model.number="form.sort_order" label="Ordre" name="sort_order" type="number" />
                <div class="modal-action">
                    <button type="button" class="btn" @click="showModal = false">Annuler</button>
                    <button type="submit" class="btn btn-primary" :disabled="formSaving">
                        {{ formSaving ? 'Enregistrement…' : (modalMode === 'create' ? 'Créer' : 'Enregistrer') }}
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button type="button" @click="showModal = false">fermer</button>
        </form>
    </dialog>
</template>
