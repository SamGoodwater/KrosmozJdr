<script setup>
/**
 * EffectUsagesManager — Gestion des effect_usages (item, consommable, ressource) : lien vers un degré d’effet.
 * Le seuil « niveau créature min. » est porté par le degré ; le modifier dans l’éditeur d’effet / admin.
 */
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import {
    formatConditionDispellable,
    formatConditionDuration,
    formatConditionIdentity,
    formatConditionMask,
    formatConditionMode,
    getConditionDispellableIcon,
} from '@/Composables/condition/conditionDisplay';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';

const props = defineProps({
    effectUsages: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    entityType: { type: String, required: true },
    entityId: { type: Number, required: true },
});

const usages = ref(
    (props.effectUsages || []).map((u) => ({
        id: u.id,
        effect_degree_id: u.effect_degree_id,
        effect: u.effect,
    }))
);
const previewLevel = ref(1);
const previewData = ref(null);
const previewLoading = ref(false);
const saveLoading = ref(false);
const errorMessage = ref('');
/** Onglet actif (un usage = souvent un degré lié au sort). */
const activeUsageTab = ref(0);

const effectOptions = computed(() => [
    { value: '', label: '— Choisir un effet —' },
    ...props.availableEffects.map((e) => {
        const base = e.name || e.slug || 'Effet #' + e.id;
        const deg = e.degree != null && e.degree !== '' ? ` · D${e.degree}` : '';
        const suffix = showTargetTypeBadge(e.target_type) ? ` (${targetTypeLabel(e.target_type)})` : '';
        return { value: e.id, label: base + deg + suffix, target_type: e.target_type };
    }),
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

watch(
    () => props.effectUsages,
    (list) => {
        usages.value = (list || []).map((u) => ({
            id: u.id,
            effect_degree_id: u.effect_degree_id,
            effect: u.effect,
        }));
    },
    { deep: true }
);

watch(
    () => usages.value.length,
    (len) => {
        if (activeUsageTab.value >= len) {
            activeUsageTab.value = Math.max(0, len - 1);
        }
    }
);

/** Libellé d’onglet : degré de l’effet si connu, sinon identifiant. */
function usageTabLabel(u, i) {
    const d = u.effect?.degree;
    if (d !== undefined && d !== null && d !== '') {
        return `Degré ${d}`;
    }
    if (u.effect_degree_id) {
        return `Degré #${u.effect_degree_id}`;
    }
    return `Usage ${i + 1}`;
}

async function fetchUsages() {
    try {
        const { data } = await axios.get('/api/effects/usages', {
            params: { entity_type: props.entityType, entity_id: props.entityId },
        });
        const list = data.data || [];
        usages.value = list.map((u) => ({
            id: u.id,
            effect_degree_id: u.effect_degree_id,
            effect: u.effect_degree
                ? {
                      id: u.effect_degree.effect_degree_id,
                      degree: u.effect_degree.degree,
                      name: u.effect_degree.name,
                      target_type: u.effect_degree.target_type,
                      area: u.effect_degree.area,
                      required_creature_level: u.effect_degree.required_creature_level,
                  }
                : null,
        }));
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Erreur lors du chargement des usages.';
    }
}

function addUsage() {
    usages.value.push({
        id: null,
        effect_degree_id: props.availableEffects[0]?.id ?? '',
        effect: null,
    });
    activeUsageTab.value = usages.value.length - 1;
}

async function saveUsage(index) {
    const u = usages.value[index];
    if (!u.effect_degree_id) return;
    saveLoading.value = true;
    errorMessage.value = '';
    try {
        if (u.id) {
            await axios.patch(`/api/effects/usages/${u.id}`, {
                effect_degree_id: Number(u.effect_degree_id),
            });
        } else {
            await axios.post('/api/effects/usages', {
                entity_type: props.entityType,
                entity_id: props.entityId,
                effect_degree_id: Number(u.effect_degree_id),
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

function isStateSubEffect(sub) {
    const slug = String(sub?.action_slug || "");
    return slug === "appliquer-etat" || slug === "s-appliquer-etat";
}

function conditionModeLabel(sub) {
    return formatConditionMode(sub?.action_slug, { variant: "table" });
}

function conditionName(sub) {
    const ctx = sub?.context ?? {};
    return formatConditionIdentity(ctx?.condition_name, ctx?.condition_dofusdb_id);
}

function conditionMeta(sub) {
    const ctx = sub?.context ?? {};
    const bits = [
        formatConditionDuration(ctx?.duration),
        formatConditionMask(ctx?.target_mask),
    ].filter(Boolean);
    return bits.join(" · ");
}

function stateDispellableText(sub) {
    return formatConditionDispellable(sub?.context?.dispellable);
}

/** Label pour target_type (direct, trap, glyph). */
function targetTypeLabel(type) {
    const m = { direct: 'Direct', trap: 'Piège', glyph: 'Glyphe' };
    return m[String(type || 'direct')] || type;
}

/** Indique si l'effet est piège ou glyphe (à afficher). */
function showTargetTypeBadge(type) {
    return type === 'trap' || type === 'glyph';
}

/** Retourne le target_type de l'effet sélectionné (par id) depuis availableEffects ou usages. */
function selectedEffectTargetType(effectDegreeId) {
    const e = props.availableEffects.find((x) => x.id == effectDegreeId);
    if (e?.target_type) return e.target_type;
    const u = usages.value.find((x) => x.effect_degree_id == effectDegreeId);
    return u?.effect?.target_type ?? null;
}
</script>

<template>
    <Container>
        <h2 class="text-lg font-semibold mb-3">Effets (système unifié)</h2>
        <p class="text-sm text-base-content/70 mb-4">
            Chaque usage pointe vers un <strong>degré d’effet</strong> (ligne dans la liste). Le seuil de niveau créature est défini
            sur ce degré (admin / éditeur d’effet). L’aperçu simule un porteur de niveau donné.
        </p>

        <p v-if="errorMessage" class="text-error text-sm mb-3">{{ errorMessage }}</p>

        <div class="mb-6 space-y-4">
            <div v-if="usages.length" class="space-y-4">
                <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1">
                    <button
                        v-for="(u, ti) in usages"
                        :key="u.id || 'tab-new-' + ti"
                        type="button"
                        role="tab"
                        class="tab"
                        :class="{ 'tab-active': activeUsageTab === ti }"
                        @click="activeUsageTab = ti"
                    >
                        {{ usageTabLabel(u, ti) }}
                    </button>
                </div>

                <div
                    v-for="(u, index) in usages"
                    v-show="activeUsageTab === index"
                    :key="u.id || 'panel-new-' + index"
                    class="rounded-box border border-base-300 bg-base-200/30 p-4 space-y-4"
                >
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="min-w-[200px] flex-1">
                            <label class="label text-xs">Degré d’effet</label>
                            <div class="flex items-center gap-2">
                                <select
                                    v-model="u.effect_degree_id"
                                    class="select select-bordered select-sm flex-1"
                                >
                                    <option
                                        v-for="opt in effectOptions"
                                        :key="opt.value || 'empty'"
                                        :value="opt.value"
                                    >
                                        {{ opt.label }}
                                    </option>
                                </select>
                                <span
                                    v-if="u.effect_degree_id && showTargetTypeBadge(selectedEffectTargetType(u.effect_degree_id))"
                                    class="badge badge-sm badge-outline badge-primary shrink-0"
                                    :title="'Type : ' + targetTypeLabel(selectedEffectTargetType(u.effect_degree_id))"
                                >
                                    {{ targetTypeLabel(selectedEffectTargetType(u.effect_degree_id)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <p v-if="u.effect?.required_creature_level != null" class="text-xs text-base-content/70">
                        Seuil sur le degré : créature niveau ≥ {{ u.effect.required_creature_level }}
                    </p>
                    <p v-else class="text-xs text-base-content/70">Seuil sur le degré : aucun (toujours actif si l’usage est pris en compte).</p>
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            class="btn btn-primary btn-sm"
                            :disabled="saveLoading || !u.effect_degree_id"
                            @click="saveUsage(index)"
                        >
                            Enregistrer cet usage
                        </button>
                        <button
                            v-if="u.id"
                            type="button"
                            class="btn btn-ghost btn-sm text-error"
                            :disabled="saveLoading"
                            @click="deleteUsage(u.id)"
                        >
                            Supprimer cet usage
                        </button>
                    </div>
                </div>
            </div>
            <p v-else class="text-sm text-base-content/70">Aucun usage pour l’instant.</p>
            <button type="button" class="btn btn-outline btn-sm" @click="addUsage">
                + Ajouter un usage d’effet
            </button>
        </div>

        <div class="border-t border-base-300 pt-4">
            <h3 class="font-medium mb-2">Aperçu pour un niveau de créature</h3>
            <div class="flex flex-wrap items-center gap-3 mb-3">
                <InputField
                    v-model="previewLevel"
                    label="Niveau créature"
                    type="number"
                    class="w-28"
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
            <div v-else-if="previewData" class="space-y-3">
                <div
                    v-for="(item, i) in previewData"
                    :key="i"
                    class="rounded-box border border-base-300 bg-base-200/40 p-3"
                >
                    <div class="text-sm flex flex-wrap items-center gap-2">
                        <span class="font-medium">{{ item.effect?.name || item.effect?.slug || 'Effet' }}</span>
                        <span
                            v-if="showTargetTypeBadge(item.effect?.target_type)"
                            class="badge badge-sm badge-primary badge-outline"
                            :title="'Type de cible : ' + targetTypeLabel(item.effect?.target_type)"
                        >
                            {{ targetTypeLabel(item.effect?.target_type) }}
                        </span>
                        <AreaDisplay
                            v-if="item.effect?.area"
                            :area="item.effect.area"
                            class="text-base-content/80"
                        />
                        <span class="text-base-content/70"> — {{ item.resolved_text || item.description || '—' }}</span>
                    </div>

                    <div
                        v-if="Array.isArray(item.resolved?.sub_effects) && item.resolved.sub_effects.length"
                        class="mt-2 pl-3 border-l-2 border-base-300 space-y-1"
                    >
                        <p class="text-xs uppercase tracking-wide text-base-content/60 font-semibold">
                            Sous-effets (normal)
                        </p>
                        <ul class="text-sm space-y-1">
                            <li v-for="(sub, si) in item.resolved.sub_effects" :key="`n-${i}-${si}`">
                                <template v-if="isStateSubEffect(sub)">
                                    <span class="font-mono">{{ sub.action_slug }}</span>
                                    <span class="text-base-content/80"> → {{ conditionName(sub) }}</span>
                                    <span class="text-base-content/60"> ({{ conditionModeLabel(sub) }})</span>
                                    <span
                                        v-if="stateDispellableText(sub)"
                                        class="inline-flex items-center gap-1 text-base-content/60"
                                    >
                                        <Icon
                                            v-if="getConditionDispellableIcon(sub?.context?.dispellable)"
                                            :source="getConditionDispellableIcon(sub?.context?.dispellable)"
                                            :alt="stateDispellableText(sub) || ''"
                                            size="xs"
                                        />
                                        {{ stateDispellableText(sub) }}
                                    </span>
                                    <span v-if="conditionMeta(sub)" class="text-base-content/60"> · {{ conditionMeta(sub) }}</span>
                                </template>
                                <template v-else>
                                    <span class="font-mono">{{ sub.action_slug || "—" }}</span>
                                    <span class="text-base-content/70"> — {{ sub.text || "—" }}</span>
                                </template>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="Array.isArray(item.resolved_crit?.sub_effects) && item.resolved_crit.sub_effects.length"
                        class="mt-2 pl-3 border-l-2 border-warning/40 space-y-1"
                    >
                        <p class="text-xs uppercase tracking-wide text-warning/80 font-semibold">
                            Sous-effets (critique)
                        </p>
                        <ul class="text-sm space-y-1">
                            <li v-for="(sub, si) in item.resolved_crit.sub_effects" :key="`c-${i}-${si}`">
                                <template v-if="isStateSubEffect(sub)">
                                    <span class="font-mono">{{ sub.action_slug }}</span>
                                    <span class="text-base-content/80"> → {{ conditionName(sub) }}</span>
                                    <span class="text-base-content/60"> ({{ conditionModeLabel(sub) }})</span>
                                    <span
                                        v-if="stateDispellableText(sub)"
                                        class="inline-flex items-center gap-1 text-base-content/60"
                                    >
                                        <Icon
                                            v-if="getConditionDispellableIcon(sub?.context?.dispellable)"
                                            :source="getConditionDispellableIcon(sub?.context?.dispellable)"
                                            :alt="stateDispellableText(sub) || ''"
                                            size="xs"
                                        />
                                        {{ stateDispellableText(sub) }}
                                    </span>
                                    <span v-if="conditionMeta(sub)" class="text-base-content/60"> · {{ conditionMeta(sub) }}</span>
                                </template>
                                <template v-else>
                                    <span class="font-mono">{{ sub.action_slug || "—" }}</span>
                                    <span class="text-base-content/70"> — {{ sub.text || "—" }}</span>
                                </template>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </Container>
</template>
