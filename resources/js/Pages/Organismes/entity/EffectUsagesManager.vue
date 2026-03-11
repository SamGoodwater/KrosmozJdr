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
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import {
    formatSpellStateDispellable,
    formatSpellStateDuration,
    formatSpellStateIdentity,
    formatSpellStateMask,
    formatSpellStateMode,
    getSpellStateDispellableIcon,
} from '@/Composables/spell/spellStateDisplay';
import { getAreaIcon } from '@/Utils/Entity/Areas';

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
    ...props.availableEffects.map((e) => {
        const base = e.name || e.slug || 'Effet #' + e.id;
        const suffix = showTargetTypeBadge(e.target_type) ? ` (${targetTypeLabel(e.target_type)})` : '';
        return { value: e.id, label: base + suffix };
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

function isStateSubEffect(sub) {
    const slug = String(sub?.action_slug || "");
    return slug === "appliquer-etat" || slug === "s-appliquer-etat";
}

function stateModeLabel(sub) {
    return formatSpellStateMode(sub?.action_slug, { variant: "table" });
}

function stateName(sub) {
    const ctx = sub?.context ?? {};
    return formatSpellStateIdentity(ctx?.state_name, ctx?.state_dofusdb_id);
}

function stateMeta(sub) {
    const ctx = sub?.context ?? {};
    const bits = [
        formatSpellStateDuration(ctx?.duration),
        formatSpellStateMask(ctx?.target_mask),
    ].filter(Boolean);
    return bits.join(" · ");
}

function stateDispellableText(sub) {
    return formatSpellStateDispellable(sub?.context?.dispellable);
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
function selectedEffectTargetType(effectId) {
    const e = props.availableEffects.find((x) => x.id == effectId);
    if (e?.target_type) return e.target_type;
    const u = usages.value.find((x) => x.effect_id == effectId);
    return u?.effect?.target_type ?? null;
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
                    <div class="flex items-center gap-2">
                        <select
                            v-model="u.effect_id"
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
                            v-if="u.effect_id && showTargetTypeBadge(selectedEffectTargetType(u.effect_id))"
                            class="badge badge-sm badge-outline badge-primary shrink-0"
                            :title="'Type : ' + targetTypeLabel(selectedEffectTargetType(u.effect_id))"
                        >
                            {{ targetTypeLabel(selectedEffectTargetType(u.effect_id)) }}
                        </span>
                    </div>
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
            <div v-else-if="previewData" class="space-y-3">
                <div
                    v-for="(item, i) in previewData"
                    :key="i"
                    class="rounded border border-base-300 bg-base-200/40 p-3"
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
                        <span v-if="item.effect?.area" class="inline-flex items-center gap-1 text-base-content/50 text-xs font-mono" :title="'Zone : ' + item.effect.area">
                            <Icon
                                :source="getAreaIcon(item.effect.area)"
                                :alt="item.effect.area"
                                size="xs"
                                class="shrink-0 opacity-80"
                            />
                            {{ item.effect.area }}
                        </span>
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
                                    <span class="text-base-content/80"> → {{ stateName(sub) }}</span>
                                    <span class="text-base-content/60"> ({{ stateModeLabel(sub) }})</span>
                                    <span
                                        v-if="stateDispellableText(sub)"
                                        class="inline-flex items-center gap-1 text-base-content/60"
                                    >
                                        <Icon
                                            v-if="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                            :source="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                            :alt="stateDispellableText(sub) || ''"
                                            size="xs"
                                        />
                                        {{ stateDispellableText(sub) }}
                                    </span>
                                    <span v-if="stateMeta(sub)" class="text-base-content/60"> · {{ stateMeta(sub) }}</span>
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
                                    <span class="text-base-content/80"> → {{ stateName(sub) }}</span>
                                    <span class="text-base-content/60"> ({{ stateModeLabel(sub) }})</span>
                                    <span
                                        v-if="stateDispellableText(sub)"
                                        class="inline-flex items-center gap-1 text-base-content/60"
                                    >
                                        <Icon
                                            v-if="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                            :source="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                            :alt="stateDispellableText(sub) || ''"
                                            size="xs"
                                        />
                                        {{ stateDispellableText(sub) }}
                                    </span>
                                    <span v-if="stateMeta(sub)" class="text-base-content/60"> · {{ stateMeta(sub) }}</span>
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
