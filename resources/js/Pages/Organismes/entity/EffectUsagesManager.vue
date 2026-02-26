<script setup>
/**
 * EffectUsagesManager — Gestion des effect_usages sur une entité (sort, item, consumable).
 * Liste des usages (tranche level_min–level_max → effect), ajout/édition/suppression via API.
 * Prévisualisation « effet pour niveau X » via GET api/effects/for-entity.
 */
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';

const props = defineProps({
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    entityType: { type: String, required: true },
    entityId: { type: Number, required: true },
});

const usages = ref(
    (props.effectUsages || []).map((u) => ({
        id: u.id,
        effect_id: u.effect_id,
        effect: u.effect,
        level_min: u.level_min ?? null,
        level_max: u.level_max ?? null,
    }))
);
const previewLevel = ref(1);
const previewData = ref(null);
const previewLoading = ref(false);
const saveLoading = ref(false);
const errorMessage = ref('');

const effectOptions = computed(() => [
    { value: '', label: '— Choisir un effet —' },
    ...props.availableEffects.map((e) => ({ value: e.id, label: e.name || e.slug || 'Effet #' + e.id })),
]);

async function fetchPreview() {
    if (!props.entityId) return;
    previewLoading.value = true;
    previewData.value = null;
    errorMessage.value = '';
    try {
        const { data } = await axios.get('/api/effects/for-entity', {
            params: {
                entity_type: props.entityType,
                entity_id: props.entityId,
                level: previewLevel.value,
                format_dice_human: false,
            },
        });
        previewData.value = data.data || [];
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors du chargement de l’aperçu.';
    } finally {
        previewLoading.value = false;
    }
}

watch([previewLevel, () => props.entityId], () => fetchPreview(), { immediate: true });

async function fetchUsages() {
    try {
        const { data } = await axios.get('/api/effects/usages', {
            params: { entity_type: props.entityType, entity_id: props.entityId },
        });
        const list = data.data || [];
        usages.value = list.map((u) => ({
            id: u.id,
            effect_id: u.effect_id,
            effect: u.effect,
            level_min: u.level_min ?? null,
            level_max: u.level_max ?? null,
        }));
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors du chargement des usages.';
    }
}

function addUsage() {
    usages.value.push({
        id: null,
        effect_id: props.availableEffects[0]?.id ?? '',
        effect: null,
        level_min: null,
        level_max: null,
    });
}

async function saveUsage(index) {
    const u = usages.value[index];
    if (!u.effect_id) return;
    saveLoading.value = true;
    errorMessage.value = '';
    try {
        if (u.id) {
            await axios.patch(`/api/effects/usages/${u.id}`, {
                effect_id: u.effect_id,
                level_min: u.level_min || null,
                level_max: u.level_max || null,
            });
        } else {
            await axios.post('/api/effects/usages', {
                entity_type: props.entityType,
                entity_id: props.entityId,
                effect_id: u.effect_id,
                level_min: u.level_min || null,
                level_max: u.level_max || null,
            });
        }
        await fetchUsages();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors de l’enregistrement.';
    } finally {
        saveLoading.value = false;
    }
}

async function deleteUsage(id) {
    if (!id || !confirm('Supprimer cet usage ?')) return;
    saveLoading.value = true;
    errorMessage.value = '';
    try {
        await axios.delete(`/api/effects/usages/${id}`);
        await fetchUsages();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors de la suppression.';
    } finally {
        saveLoading.value = false;
    }
}

function effectName(effectId) {
    const e = props.availableEffects.find((x) => x.id === effectId);
    return e ? (e.name || e.slug || 'Effet #' + effectId) : '—';
}
</script>

<template>
    <Container>
        <h2 class="text-lg font-semibold mb-3">Effets (système unifié)</h2>
        <p class="text-sm text-base-content/70 mb-4">
            Tranches de niveau → effet. Les usages sont enregistrés via l’API. Aperçu pour un niveau donné ci-dessous.
        </p>

        <p v-if="errorMessage" class="text-error text-sm mb-3">{{ errorMessage }}</p>

        <div class="space-y-3 mb-6">
            <div
                v-for="(u, index) in usages"
                :key="u.id || 'new-' + index"
                class="flex flex-wrap items-end gap-3 rounded-lg border border-base-300 bg-base-200/30 p-3"
            >
                <div class="min-w-[200px] flex-1">
                    <label class="label text-xs">Effet</label>
                    <select
                        v-model="u.effect_id"
                        class="select select-bordered select-sm w-full"
                    >
                        <option
                            v-for="opt in effectOptions"
                            :key="opt.value || 'empty'"
                            :value="opt.value"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
                <InputField v-model="u.level_min" label="Niveau min" type="number" class="w-24" />
                <InputField v-model="u.level_max" label="Niveau max" type="number" class="w-24" />
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    :disabled="saveLoading || !u.effect_id"
                    @click="saveUsage(index)"
                >
                    Enregistrer
                </button>
                <button
                    v-if="u.id"
                    type="button"
                    class="btn btn-ghost btn-sm btn-square text-error"
                    title="Supprimer"
                    :disabled="saveLoading"
                    @click="deleteUsage(u.id)"
                >
                    ×
                </button>
            </div>
            <button type="button" class="btn btn-outline btn-sm" @click="addUsage">
                + Ajouter un usage d’effet
            </button>
        </div>

        <div class="border-t border-base-300 pt-4">
            <h3 class="font-medium mb-2">Aperçu pour un niveau</h3>
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <InputField
                    v-model="previewLevel"
                    label="Niveau"
                    type="number"
                    class="w-24"
                />
                <button
                    type="button"
                    class="btn btn-sm btn-ghost"
                    :disabled="previewLoading"
                    @click="fetchPreview"
                >
                    {{ previewLoading ? 'Chargement…' : 'Rafraîchir' }}
                </button>
            </div>
            <div v-if="previewLoading" class="text-sm text-base-content/70">Chargement…</div>
            <div v-else-if="previewData && previewData.length === 0" class="text-sm text-base-content/70">
                Aucun effet pour ce niveau.
            </div>
            <ul v-else-if="previewData" class="list-disc list-inside space-y-1 text-sm">
                <li v-for="(item, i) in previewData" :key="i">
                    <span class="font-medium">{{ item.effect?.name || item.effect?.slug || 'Effet' }}</span>
                    : {{ item.resolved_text || item.description || '—' }}
                </li>
            </ul>
        </div>
    </Container>
</template>
