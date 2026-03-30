<script setup>
/**
 * Panneau unifié « effets du sort » : liaison pivot effect_spell, édition par définition (degrés + seuils).
 */
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { Link, router } from '@inertiajs/vue3';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import EffectGroupEditorForm from '@/Pages/Organismes/entity/EffectGroupEditorForm.vue';
import {
    formatSpellStateDispellable,
    formatSpellStateDuration,
    formatSpellStateIdentity,
    formatSpellStateMask,
    formatSpellStateMode,
    getSpellStateDispellableIcon,
} from '@/Composables/spell/spellStateDisplay';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';

const props = defineProps({
    availableEffects: { type: Array, default: () => [] },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellEffectGroups: { type: Array, default: () => [] },
    entityId: { type: Number, required: true },
    entityType: { type: String, default: 'spell' },
});

const selectedAnchorId = ref(null);
const effectLinkSearch = ref('');
const effectToAttach = ref(0);
const attachLoading = ref(false);
const detachLoading = ref(false);
const errorMessage = ref('');
const lastAttachedDefinitionId = ref(null);

const previewLevel = ref(1);
const previewData = ref(null);
const previewLoading = ref(false);

const selectedGroup = computed(() => {
    if (!selectedAnchorId.value || !props.spellEffectGroups?.length) {
        return null;
    }
    return props.spellEffectGroups.find((g) => g.anchor_effect_id === selectedAnchorId.value) ?? null;
});

const selectedDegreeIdForEditor = computed(() => selectedGroup.value?.group_effects?.[0]?.id ?? 0);

watch(
    () => props.spellEffectGroups,
    (groups) => {
        if (!groups?.length) {
            selectedAnchorId.value = null;
            return;
        }
        const ids = groups.map((g) => g.anchor_effect_id);
        if (lastAttachedDefinitionId.value) {
            const hit = groups.find((gr) => gr.anchor_effect_id === lastAttachedDefinitionId.value);
            if (hit) {
                selectedAnchorId.value = hit.anchor_effect_id;
                lastAttachedDefinitionId.value = null;
                return;
            }
        }
        if (!selectedAnchorId.value || !ids.includes(selectedAnchorId.value)) {
            selectedAnchorId.value = ids[0];
        }
    },
    { immediate: true }
);

const linkedDefinitionIds = computed(
    () => new Set((props.spellEffectGroups || []).map((g) => g.anchor_effect_id))
);

const availableDefinitionsForAttach = computed(() => {
    const seen = new Set();
    const out = [];
    for (const e of props.availableEffects || []) {
        const defId = e.effect_definition_id;
        if (defId == null || seen.has(defId)) {
            continue;
        }
        seen.add(defId);
        out.push(e);
    }
    return out;
});

const filteredEffectsForAttach = computed(() => {
    const q = effectLinkSearch.value.trim().toLowerCase();
    return availableDefinitionsForAttach.value.filter((e) => {
        if (linkedDefinitionIds.value.has(e.effect_definition_id)) {
            return false;
        }
        if (!q) {
            return true;
        }
        const name = (e.name || '').toLowerCase();
        const slug = (e.slug || '').toLowerCase();
        const deg = String(e.degree ?? '');
        return name.includes(q) || slug.includes(q) || deg.includes(q);
    });
});

function effectOptionLabel(e) {
    const base = e.name || e.slug || `Effet #${e.effect_definition_id}`;
    const deg = e.degree != null && e.degree !== '' ? ` · D${e.degree}` : '';
    return base + deg;
}

async function reloadSpellEffectData() {
    await router.reload({ only: ['spellEffectGroups', 'spell', 'availableEffects'] });
}

async function attachEffect() {
    if (!effectToAttach.value) {
        return;
    }
    attachLoading.value = true;
    errorMessage.value = '';
    try {
        lastAttachedDefinitionId.value = effectToAttach.value;
        await axios.post('/api/effects/spell-attachments', {
            spell_id: props.entityId,
            effect_id: effectToAttach.value,
        });
        effectToAttach.value = 0;
        effectLinkSearch.value = '';
        await reloadSpellEffectData();
    } catch (err) {
        lastAttachedDefinitionId.value = null;
        errorMessage.value = err.response?.data?.message || "Impossible de lier cet effet au sort.";
    } finally {
        attachLoading.value = false;
    }
}

async function detachCurrentGroup() {
    const g = selectedGroup.value;
    if (!g?.anchor_effect_id) {
        return;
    }
    if (!confirm('Retirer cette définition d’effet du sort ?')) {
        return;
    }
    detachLoading.value = true;
    errorMessage.value = '';
    try {
        await axios.delete('/api/effects/spell-attachments', {
            data: {
                spell_id: props.entityId,
                effect_id: g.anchor_effect_id,
            },
        });
        await reloadSpellEffectData();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Impossible de détacher cet effet.';
    } finally {
        detachLoading.value = false;
    }
}

async function fetchPreview() {
    if (!props.entityId) {
        return;
    }
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

function isStateSubEffect(sub) {
    const slug = String(sub?.action_slug || '');
    return slug === 'appliquer-etat' || slug === "s-appliquer-etat";
}

function stateModeLabel(sub) {
    return formatSpellStateMode(sub?.action_slug, { variant: 'table' });
}

function stateName(sub) {
    const ctx = sub?.context ?? {};
    return formatSpellStateIdentity(ctx?.state_name, ctx?.state_dofusdb_id);
}

function stateMeta(sub) {
    const ctx = sub?.context ?? {};
    const bits = [formatSpellStateDuration(ctx?.duration), formatSpellStateMask(ctx?.target_mask)].filter(Boolean);
    return bits.join(' · ');
}

function stateDispellableText(sub) {
    return formatSpellStateDispellable(sub?.context?.dispellable);
}

function targetTypeLabel(type) {
    const m = { direct: 'Direct', trap: 'Piège', glyph: 'Glyphe' };
    return m[String(type || 'direct')] || type;
}

function showTargetTypeBadge(type) {
    return type === 'trap' || type === 'glyph';
}

const patchUrlForSelectedGroup = computed(() => {
    if (!selectedGroup.value || !props.entityId) {
        return '';
    }
    return route('entities.spells.updateEffectGroup', {
        spell: props.entityId,
        effect: selectedGroup.value.anchor_effect_id,
    });
});
</script>

<template>
    <Container>
        <div class="mb-4">
            <h2 class="text-xl font-semibold border-b border-base-300 pb-2">Effets du sort</h2>
            <p class="text-sm text-base-content/70 mt-2">
                Liez une ou plusieurs <strong>définitions</strong> d’effet (pivot <code class="text-xs">effect_spell</code>). Chaque
                définition porte ses degrés : zone, seuil de niveau créature et sous-effets s’éditent dans le bloc ci-dessous.
            </p>
        </div>

        <p v-if="errorMessage" class="text-error text-sm mb-3">{{ errorMessage }}</p>

        <div class="card bg-base-100 shadow border border-base-300 mb-6">
            <div class="card-body py-4 gap-3">
                <h3 class="card-title text-base">Rechercher et lier une définition d’effet</h3>
                <p class="text-sm text-base-content/70">
                    La liste est dédupliquée par définition ; les blocs déjà liés au sort sont masqués.
                </p>
                <div class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="label text-xs">Recherche</label>
                        <input
                            v-model="effectLinkSearch"
                            type="search"
                            class="input input-bordered input-sm w-full"
                            placeholder="Nom, slug, degré…"
                            autocomplete="off"
                        />
                    </div>
                    <div class="flex-1 min-w-[220px]">
                        <label class="label text-xs">Définition à lier</label>
                        <select v-model.number="effectToAttach" class="select select-bordered select-sm w-full">
                            <option :value="0">— Choisir —</option>
                            <option v-for="e in filteredEffectsForAttach" :key="e.effect_definition_id" :value="e.effect_definition_id">
                                {{ effectOptionLabel(e) }}
                            </option>
                        </select>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        :disabled="!effectToAttach || attachLoading"
                        @click="attachEffect"
                    >
                        {{ attachLoading ? 'Liaison…' : 'Lier au sort' }}
                    </button>
                    <Link :href="route('admin.effects.create')" class="btn btn-sm btn-outline">
                        Créer un effet (admin)
                    </Link>
                </div>
            </div>
        </div>

        <div v-if="spellEffectGroups.length > 1" class="mb-4">
            <label class="label text-xs">Bloc d’effets à éditer</label>
            <select v-model.number="selectedAnchorId" class="select select-bordered select-sm max-w-xl w-full">
                <option v-for="g in spellEffectGroups" :key="g.anchor_effect_id" :value="g.anchor_effect_id">
                    {{ g.label }} ({{ g.group_effects?.length ?? 0 }} degré(s))
                </option>
            </select>
        </div>

        <EffectGroupEditorForm
            v-if="selectedGroup && patchUrlForSelectedGroup && selectedDegreeIdForEditor > 0"
            :key="selectedGroup.anchor_effect_id"
            :options="effectFormOptions"
            :group-effects="selectedGroup.group_effects"
            :selected-effect-id="selectedDegreeIdForEditor"
            :patch-url="patchUrlForSelectedGroup"
            :heading="selectedGroup.label"
            submit-label="Enregistrer les effets"
        />

        <div v-if="selectedGroup && patchUrlForSelectedGroup" class="mt-3">
            <button
                type="button"
                class="btn btn-sm btn-ghost text-error"
                :disabled="detachLoading"
                @click="detachCurrentGroup"
            >
                {{ detachLoading ? '…' : 'Détacher ce bloc du sort' }}
            </button>
        </div>

        <div v-else-if="!spellEffectGroups.length" class="alert alert-info text-sm">
            <span>
                Aucun effet lié à ce sort pour l’instant. Utilisez la zone « Rechercher et lier » ci-dessus, ou créez une définition
                dans l’admin puis rattachez-la ici.
            </span>
        </div>

        <details class="collapse collapse-arrow bg-base-200/40 border border-base-300 rounded-box mt-8">
            <summary class="collapse-title font-medium min-h-0 py-3">
                Aperçu pour un niveau de créature
            </summary>
            <div class="collapse-content text-sm pt-0">
                <div class="flex flex-wrap items-center gap-3 mb-3">
                    <InputField v-model="previewLevel" label="Niveau créature" type="number" class="w-28" />
                    <button
                        type="button"
                        class="btn btn-sm btn-ghost"
                        :disabled="previewLoading"
                        @click="fetchPreview"
                    >
                        {{ previewLoading ? 'Chargement…' : 'Rafraîchir' }}
                    </button>
                </div>
                <div v-if="previewLoading" class="text-base-content/70">Chargement…</div>
                <div v-else-if="previewData && previewData.length === 0" class="text-base-content/70">
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
                            <ul class="space-y-1 text-xs">
                                <li v-for="(sub, si) in item.resolved.sub_effects" :key="si" class="flex flex-col gap-0.5">
                                    <template v-if="isStateSubEffect(sub)">
                                        <span class="flex flex-wrap items-center gap-1">
                                            <Icon
                                                v-if="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                                :source="getSpellStateDispellableIcon(sub?.context?.dispellable)"
                                                size="xs"
                                                class="opacity-80"
                                            />
                                            <span class="font-medium">{{ stateModeLabel(sub) }}</span>
                                            <span>{{ stateName(sub) }}</span>
                                            <span v-if="stateMeta(sub)" class="text-base-content/60">{{ stateMeta(sub) }}</span>
                                            <span v-if="stateDispellableText(sub)" class="text-base-content/50">{{
                                                stateDispellableText(sub)
                                            }}</span>
                                        </span>
                                    </template>
                                    <template v-else>
                                        <span>{{ sub.text || '—' }}</span>
                                    </template>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </details>
    </Container>
</template>
