<script setup>
/**
 * Admin Mapping effectId DofusDB → sous-effet Krosmoz (effets de sorts).
 * Liste des mappings ; création / édition / suppression.
 */
import { computed, onMounted, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import axios from 'axios';
import ConfirmPasswordModal from '@/Pages/Molecules/action/ConfirmPasswordModal.vue';
import {
    filterDofusdbEffectMappings,
    groupDofusdbEffectMappings,
} from '@/Utils/effects/groupDofusdbEffectMappings';

const { setPageTitle } = usePageTitle();

const page = usePage();
const adminUnlocked = ref(Boolean(page.props.auth?.password_recently_confirmed));
const showAdminConfirmModal = ref(false);
function onAdminPasswordConfirmed() {
    adminUnlocked.value = true;
}

const props = defineProps({
    effectIdFilter: { type: String, default: '' },
    mappings: { type: Array, default: () => [] },
    subEffectsForSelect: { type: Array, default: () => [] },
    characteristicSourceOptions: { type: Array, default: () => [] },
    characteristicsForSelect: { type: Array, default: () => [] },
});

defineOptions({ layout: AdminArea });
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
const showAutre = ref(false);

const autreCount = computed(() =>
    (Array.isArray(props.mappings) ? props.mappings : []).filter(
        (m) => String(m?.sub_effect_slug ?? '') === 'autre'
    ).length
);

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
const filteredMappings = computed(() =>
    filterDofusdbEffectMappings(props.mappings, {
        query: listFilter.value,
        showAutre: showAutre.value,
        prefillEffectId: prefillEffectId.value,
    })
);

const groupedMappings = computed(() => groupDofusdbEffectMappings(filteredMappings.value));

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

function goBackToEffects() {
    router.visit(route('admin.effects.index'));
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

    <div
        v-if="!adminUnlocked"
        class="rounded-box border border-warning/40 bg-warning/10 p-8 mx-auto max-w-lg my-8 text-center space-y-4"
    >
        <p class="text-warning-content">
            Les mappings modifient la base de données. Confirme ton mot de passe pour continuer.
        </p>
        <Btn color="primary" @click="showAdminConfirmModal = true">
            Accéder aux mappings effets DofusDB
        </Btn>
    </div>

    <div v-else class="flex h-full min-h-0 w-full flex-col lg:flex-row">
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <div class="mb-4">
                <Btn color="neutral" variant="ghost" size="sm" class="gap-2 mb-2" @click="goBackToEffects">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    Retour aux effets
                </Btn>
                <h1 class="text-2xl font-bold">Mapping effectId DofusDB → sous-effet Krosmoz</h1>
                <p class="mt-1 text-sm text-base-content/70">
                    Définit pour chaque effectId DofusDB l’action Krosmoz (sous-effet) et la source de caractéristique.
                    Le groupe <code class="text-xs">autre</code> est volontairement volumineux (hors périmètre :
                    glyphes, pièges, placeholders). Il est masqué par défaut.
                </p>
            </div>

            <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                <h2 class="text-lg font-semibold">Mappings en base</h2>
                <div class="flex flex-wrap items-end gap-3">
                    <InputField
                        v-model="listFilter"
                        label="Rechercher"
                        placeholder="effectId, sous-effet, libellé…"
                    />
                    <label class="label cursor-pointer gap-2 pb-2">
                        <input v-model="showAutre" type="checkbox" class="checkbox checkbox-sm" />
                        <span class="label-text">
                            Afficher « autre »
                            <span class="text-base-content/50">({{ autreCount }})</span>
                        </span>
                    </label>
                    <Btn color="primary" size="sm" @click="openCreate">Ajouter un mapping</Btn>
                </div>
            </div>

            <div
                v-if="filteredMappings.length === 0"
                class="rounded-box border border-base-300 bg-base-200/30 p-8 text-center text-base-content/70"
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

            <div v-else class="space-y-4">
                <section
                    v-for="group in groupedMappings"
                    :key="group.slug"
                    class="overflow-x-auto rounded-box border border-base-300"
                >
                    <header class="flex items-center justify-between gap-2 border-b border-base-300 bg-base-200/40 px-4 py-2">
                        <div>
                            <h3 class="font-semibold">
                                <span class="font-mono text-sm">{{ group.slug }}</span>
                                <span
                                    v-if="group.label && group.label !== group.slug"
                                    class="ml-2 font-normal text-base-content/70"
                                >
                                    {{ group.label }}
                                </span>
                            </h3>
                        </div>
                        <span class="badge badge-ghost">{{ group.rows.length }}</span>
                    </header>
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>effectId DofusDB</th>
                                <th>Source carac.</th>
                                <th>Clé carac.</th>
                                <th class="w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="m in group.rows" :key="m.id">
                                <td class="font-mono font-semibold">{{ m.dofusdb_effect_id }}</td>
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
                </section>
            </div>
        </main>
    </div>

    <!-- Modal création / édition -->
    <dialog v-if="adminUnlocked" class="modal" :class="{ 'modal-open': showModal }">
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
                <SelectSearchField
                    v-model="form.sub_effect_slug"
                    label="Sous-effet (action Krosmoz)"
                    name="sub_effect_slug"
                    :options="subEffectsForSelect"
                    required
                />
                <SelectSearchField
                    v-model="form.characteristic_source"
                    label="Source de caractéristique"
                    name="characteristic_source"
                    :options="characteristicSourceOptions"
                    :searchable="false"
                />
                <SelectSearchField
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

    <ConfirmPasswordModal
        v-model:open="showAdminConfirmModal"
        title="Mapping effets DofusDB"
        message="Cette section modifie les mappings effectId en base. Entre ton mot de passe pour confirmer ton identité."
        confirm-label="Accéder"
        @confirmed="onAdminPasswordConfirmed"
    />
</template>
