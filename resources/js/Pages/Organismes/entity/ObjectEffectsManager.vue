<script setup>
/**
 * Effets d’objet structurés (régénérer, ajouter, retirer, téléporter, invoquer) — hors système « effets de sort ».
 */
import { ref, watch } from 'vue';
import axios from 'axios';
import Container from '@/Pages/Atoms/data-display/Container.vue';

const ACTION_OPTIONS = [
    { value: 'regenerate', label: 'Régénérer' },
    { value: 'add', label: 'Ajouter' },
    { value: 'remove', label: 'Retirer' },
    { value: 'teleport', label: 'Téléporter' },
    { value: 'invoke', label: 'Invoquer' },
];

const props = defineProps({
    objectEffects: { type: Array, default: () => [] },
    objectEffectCharacteristics: { type: Array, default: () => [] },
    objectEffectMonsters: { type: Array, default: () => [] },
    entityType: { type: String, required: true },
    entityId: { type: Number, required: true },
});

const rows = ref([]);
const errorMessage = ref('');
const saveLoading = ref(false);

function mapFromProps(list) {
    return (list || []).map((r) => ({
        id: r.id,
        action: r.action || 'add',
        characteristic_id: r.characteristic_id ?? '',
        monster_id: r.monster_id ?? '',
        value: r.value === null || r.value === undefined ? '' : String(r.value),
    }));
}

watch(
    () => props.objectEffects,
    (list) => {
        rows.value = mapFromProps(list);
    },
    { immediate: true }
);

function emptyToNull(v) {
    if (v === '' || v === undefined || v === null) return null;
    const n = Number(v);
    return Number.isFinite(n) ? n : null;
}

function showCharacteristic(action) {
    return !['teleport', 'invoke'].includes(action);
}

function showMonster(action) {
    return action === 'invoke';
}

function showValue(action) {
    return action !== 'teleport' && action !== 'invoke';
}

async function fetchList() {
    if (!props.entityId) return;
    errorMessage.value = '';
    try {
        const { data } = await axios.get('/api/object-effects', {
            params: { entity_type: props.entityType, entity_id: props.entityId },
        });
        const list = data.data ?? data ?? [];
        rows.value = mapFromProps(Array.isArray(list) ? list : []);
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors du chargement des effets.';
    }
}

function addRow() {
    rows.value.push({
        id: null,
        action: 'add',
        characteristic_id: '',
        monster_id: '',
        value: '',
    });
}

function buildPayload(row) {
    return {
        action: row.action,
        characteristic_id: showCharacteristic(row.action) ? emptyToNull(row.characteristic_id) : null,
        monster_id: showMonster(row.action) ? emptyToNull(row.monster_id) : null,
        value: showValue(row.action) ? emptyToNull(row.value) : null,
    };
}

async function saveRow(index) {
    const row = rows.value[index];
    if (!props.entityId) return;
    saveLoading.value = true;
    errorMessage.value = '';
    try {
        const payload = buildPayload(row);
        if (row.id) {
            await axios.patch(`/api/object-effects/${row.id}`, payload);
        } else {
            await axios.post('/api/object-effects', {
                entity_type: props.entityType,
                entity_id: props.entityId,
                ...payload,
            });
        }
        await fetchList();
    } catch (err) {
        const msg = err.response?.data?.message;
        const errors = err.response?.data?.errors;
        errorMessage.value =
            msg ||
            (errors && Object.values(errors).flat().join(' ')) ||
            'Erreur lors de l’enregistrement.';
    } finally {
        saveLoading.value = false;
    }
}

async function deleteRow(id) {
    if (!id || !confirm('Supprimer cet effet ?')) return;
    saveLoading.value = true;
    errorMessage.value = '';
    try {
        await axios.delete(`/api/object-effects/${id}`);
        await fetchList();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors de la suppression.';
    } finally {
        saveLoading.value = false;
    }
}
</script>

<template>
    <Container>
        <h2 class="text-lg font-semibold mb-2">Effets d’objet (simple)</h2>
        <p class="text-sm text-base-content/70 mb-4">
            Actions sur caractéristique, invocation de monstre ou téléportation — distinct des effets de sort ci-dessus.
        </p>

        <p v-if="!entityId" class="text-sm text-warning">Enregistrez l’entité avant d’ajouter des effets.</p>
        <p v-if="errorMessage" class="text-error text-sm mb-3">{{ errorMessage }}</p>

        <div v-if="entityId" class="space-y-4">
            <div
                v-for="(row, index) in rows"
                :key="row.id || 'new-' + index"
                class="rounded-box border border-base-300 bg-base-200/30 p-4 flex flex-col gap-3"
            >
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="min-w-[160px]">
                        <label class="label text-xs">Action</label>
                        <select v-model="row.action" class="select select-bordered select-sm w-full">
                            <option v-for="opt in ACTION_OPTIONS" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </option>
                        </select>
                    </div>
                    <div v-if="showCharacteristic(row.action)" class="min-w-[220px] flex-1">
                        <label class="label text-xs">Caractéristique (objet)</label>
                        <select v-model="row.characteristic_id" class="select select-bordered select-sm w-full">
                            <option value="">—</option>
                            <option
                                v-for="c in objectEffectCharacteristics"
                                :key="c.id"
                                :value="c.id"
                            >
                                {{ c.name || c.key }}
                            </option>
                        </select>
                    </div>
                    <div v-if="showMonster(row.action)" class="min-w-[220px] flex-1">
                        <label class="label text-xs">Monstre</label>
                        <select v-model="row.monster_id" class="select select-bordered select-sm w-full">
                            <option value="">—</option>
                            <option v-for="m in objectEffectMonsters" :key="m.id" :value="m.id">
                                {{ m.name || 'Monstre #' + m.id }}
                            </option>
                        </select>
                    </div>
                    <div v-if="showValue(row.action)" class="w-28">
                        <label class="label text-xs">Valeur</label>
                        <input
                            v-model="row.value"
                            type="number"
                            class="input input-bordered input-sm w-full"
                            placeholder="—"
                        />
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        :disabled="saveLoading"
                        @click="saveRow(index)"
                    >
                        Enregistrer
                    </button>
                    <button
                        v-if="row.id"
                        type="button"
                        class="btn btn-ghost btn-sm text-error"
                        :disabled="saveLoading"
                        @click="deleteRow(row.id)"
                    >
                        Supprimer
                    </button>
                </div>
            </div>

            <button type="button" class="btn btn-outline btn-sm" :disabled="saveLoading" @click="addRow">
                + Ajouter un effet
            </button>
        </div>
    </Container>
</template>
