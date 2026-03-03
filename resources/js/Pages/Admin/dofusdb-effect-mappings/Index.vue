<script setup>
/**
 * Admin Mapping effectId DofusDB → sous-effet Krosmoz (effets de sorts).
 * Liste des mappings ; création / édition / suppression.
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
    effectIdFilter: { type: String, default: '' },
    mappings: { type: Array, default: () => [] },
    subEffectsForSelect: { type: Array, default: () => [] },
    characteristicSourceOptions: { type: Array, default: () => [] },
    characteristicsForSelect: { type: Array, default: () => [] },
});

defineOptions({ layout: Main });
setPageTitle('Mapping effets DofusDB → Krosmoz');

const showModal = ref(false);
const modalMode = ref('create');
const editingId = ref(null);
const form = ref({
    dofusdb_effect_id: '',
    sub_effect_slug: '',
    characteristic_source: 'element',
    characteristic_key: '',
});
const formErrors = ref({});
const formSaving = ref(false);
const listFilter = ref(String(props.effectIdFilter || ''));

const characteristicKeyOptions = computed(() => [
    { value: '', label: '— Aucune —' },
    ...props.characteristicsForSelect,
]);

const showCharacteristicKey = computed(() => form.value.characteristic_source === 'characteristic');
const prefillEffectId = computed(() => {
    const raw = String(props.effectIdFilter || '').trim();
    const id = Number(raw);
    return Number.isFinite(id) && id > 0 ? String(Math.floor(id)) : '';
});
const hasExactPrefillMatch = computed(() => {
    if (!prefillEffectId.value) return false;
    return (Array.isArray(props.mappings) ? props.mappings : []).some(
        (m) => String(m?.dofusdb_effect_id ?? '') === prefillEffectId.value
    );
});
const isQuickCreateFromAnalysis = computed(() =>
    modalMode.value === 'create'
    && showModal.value
    && !!prefillEffectId.value
    && !hasExactPrefillMatch.value
);
const filteredMappings = computed(() => {
    const rows = Array.isArray(props.mappings) ? props.mappings : [];
    const q = String(listFilter.value || '').trim().toLowerCase();
    if (!q) {
        return rows;
    }

    return rows.filter((m) => {
        const effectId = String(m?.dofusdb_effect_id ?? '').toLowerCase();
        const subEffect = String(m?.sub_effect_slug ?? '').toLowerCase();
        const source = String(m?.characteristic_source ?? '').toLowerCase();
        const characteristic = String(m?.characteristic_key ?? '').toLowerCase();
        return effectId.includes(q) || subEffect.includes(q) || source.includes(q) || characteristic.includes(q);
    });
});

function openCreate() {
    modalMode.value = 'create';
    editingId.value = null;
    form.value = {
        dofusdb_effect_id: '',
        sub_effect_slug: props.subEffectsForSelect[0]?.value ?? '',
        characteristic_source: 'element',
        characteristic_key: '',
    };
    formErrors.value = {};
    showModal.value = true;
}

function openEdit(mapping) {
    modalMode.value = 'edit';
    editingId.value = mapping.id;
    form.value = {
        dofusdb_effect_id: String(mapping.dofusdb_effect_id),
        sub_effect_slug: mapping.sub_effect_slug ?? '',
        characteristic_source: mapping.characteristic_source ?? 'none',
        characteristic_key: mapping.characteristic_key ?? '',
    };
    formErrors.value = {};
    showModal.value = true;
}

function submitMapping() {
    formErrors.value = {};
    formSaving.value = true;
    const payload = {
        dofusdb_effect_id: form.value.dofusdb_effect_id ? parseInt(form.value.dofusdb_effect_id, 10) : null,
        sub_effect_slug: form.value.sub_effect_slug,
        characteristic_source: form.value.characteristic_source,
        characteristic_key: showCharacteristicKey.value ? (form.value.characteristic_key || null) : null,
    };
    const url =
        modalMode.value === 'create'
            ? route('admin.dofusdb-effect-mappings.store')
            : route('admin.dofusdb-effect-mappings.update', editingId.value);
    const method = modalMode.value === 'create' ? 'post' : 'patch';
    axios[method](url, payload)
        .then(() => {
            showModal.value = false;
            router.reload({ only: ['mappings'] });
        })
        .catch((err) => {
            formErrors.value = err.response?.data?.errors ?? { form: err.response?.data?.message ?? err.message };
            formSaving.value = false;
        });
}

function confirmDelete(mapping) {
    if (!confirm(`Supprimer le mapping effectId ${mapping.dofusdb_effect_id} → ${mapping.sub_effect_slug} ?`)) return;
    axios.delete(route('admin.dofusdb-effect-mappings.destroy', mapping.id)).then(() => {
        router.reload({ only: ['mappings'] });
    });
}

onMounted(() => {
    if (!prefillEffectId.value || hasExactPrefillMatch.value) {
        return;
    }
    openCreate();
    form.value.dofusdb_effect_id = prefillEffectId.value;
});
</script>

<template>
    <Head title="Mapping effets DofusDB" />
    <div class="flex h-full min-h-0 w-full">
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <div class="mb-4">
                <h1 class="text-2xl font-bold">Mapping effectId DofusDB → sous-effet Krosmoz</h1>
                <p class="mt-1 text-sm text-base-content/70">
                    Définit pour chaque effectId DofusDB l’action Krosmoz (sous-effet) et la source de caractéristique.
                    Les effectId non mappés tombent dans le sous-effet « autre ». Après modification, le cache de
                    résolution est invalidé automatiquement.
                </p>
            </div>

            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-semibold">Mappings en base</h2>
                <div class="flex items-center gap-2">
                    <InputField
                        v-model="listFilter"
                        label="Filtrer"
                        placeholder="effectId, slug, source…"
                    />
                    <Btn variant="primary" size="sm" @click="openCreate">Ajouter un mapping</Btn>
                </div>
            </div>

            <div
                v-if="filteredMappings.length === 0"
                class="rounded-lg border border-base-300 bg-base-200/30 p-8 text-center text-base-content/70"
            >
                <template v-if="mappings.length === 0">
                    Aucun mapping en base. Exécutez le seeder
                    <code class="rounded bg-base-300 px-1 text-xs">php artisan db:seed --class=DofusdbEffectMappingSeeder</code>
                    ou ajoutez des mappings manuellement.
                </template>
                <template v-else>
                    Aucun mapping ne correspond au filtre courant.
                </template>
                <br />
                <button type="button" class="btn btn-primary btn-sm mt-4" @click="openCreate">
                    Ajouter un mapping
                </button>
            </div>

            <div v-else class="overflow-x-auto rounded-lg border border-base-300">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>effectId DofusDB</th>
                            <th>Sous-effet</th>
                            <th>Source carac.</th>
                            <th>Clé carac.</th>
                            <th class="w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="m in filteredMappings" :key="m.id">
                            <td class="font-mono font-semibold">{{ m.dofusdb_effect_id }}</td>
                            <td class="font-mono text-sm">{{ m.sub_effect_slug }}</td>
                            <td>{{ m.characteristic_source }}</td>
                            <td class="font-mono text-sm">{{ m.characteristic_key ?? '—' }}</td>
                            <td>
                                <div class="flex gap-1">
                                    <button type="button" class="btn btn-ghost btn-xs" @click="openEdit(m)">
                                        Modifier
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-xs text-error" @click="confirmDelete(m)">
                                        Suppr.
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal création / édition -->
    <dialog class="modal" :class="{ 'modal-open': showModal }">
        <div class="modal-box max-w-lg">
            <h3 class="text-lg font-bold">
                {{ modalMode === 'create' ? 'Nouveau mapping effet DofusDB' : 'Modifier le mapping' }}
            </h3>
            <div
                v-if="isQuickCreateFromAnalysis"
                class="mt-3 rounded border border-info/40 bg-info/10 p-2 text-xs text-info-content"
            >
                Création rapide depuis analyse :
                <span class="font-mono">effectId {{ prefillEffectId }}</span>
            </div>
            <form @submit.prevent="submitMapping" class="space-y-4 pt-4">
                <InputField
                    v-model="form.dofusdb_effect_id"
                    label="effectId DofusDB"
                    name="dofusdb_effect_id"
                    type="number"
                    min="1"
                    required
                    placeholder="ex. 96"
                    :disabled="modalMode === 'edit'"
                />
                <p v-if="modalMode === 'edit'" class="label-text-alt text-base-content/60">
                    L’effectId ne peut pas être modifié en édition.
                </p>
                <SelectFieldNative
                    v-model="form.sub_effect_slug"
                    label="Sous-effet (action Krosmoz)"
                    name="sub_effect_slug"
                    :options="subEffectsForSelect"
                    required
                />
                <SelectFieldNative
                    v-model="form.characteristic_source"
                    label="Source de caractéristique"
                    name="characteristic_source"
                    :options="characteristicSourceOptions"
                />
                <SelectFieldNative
                    v-if="showCharacteristicKey"
                    v-model="form.characteristic_key"
                    label="Clé caractéristique"
                    name="characteristic_key"
                    :options="characteristicKeyOptions"
                />
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
