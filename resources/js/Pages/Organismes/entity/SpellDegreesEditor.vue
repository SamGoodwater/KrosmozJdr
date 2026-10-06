<script setup>
/**
 * Éditeur compact des degrés d’un sort (onglets niveau, propriétés, effets).
 */
import { computed, ref, toRaw, watch } from 'vue';
import axios from 'axios';
import { getAreaHumanReadable } from '@/Utils/Entity/Areas';
import { formatPoRange, parsePoRange } from '@/Utils/Entity/poRange.js';
import { SPELL_TARGET_TYPE_OPTIONS } from '@/Entities/spell/spell-descriptors';
import SpellDegreeEffectRow from '@/Pages/Organismes/entity/SpellDegreeEffectRow.vue';
import SpellElementPrimariesField from '@/Pages/Molecules/entity/spell/SpellElementPrimariesField.vue';
import SpellDegreePropertyField from '@/Pages/Molecules/entity/spell/SpellDegreePropertyField.vue';
import AreaNotationField from '@/Pages/Molecules/data-input/AreaNotationField.vue';

const props = defineProps({
    spellId: { type: Number, required: true },
    /** Payload `{ degrees, default_degree_id }` depuis l’API / Inertia. */
    spellDegrees: { type: Object, default: () => ({ degrees: [], default_degree_id: null }) },
    effectFormOptions: { type: Object, default: () => ({}) },
    embeddedInModal: { type: Boolean, default: false },
});

const emit = defineEmits(['changed', 'dirty-change']);

/**
 * Clone JSON sûr pour les proxies Vue / Inertia (évite DataCloneError de structuredClone).
 *
 * @param {unknown} value
 * @returns {any}
 */
function clonePlain(value) {
    return JSON.parse(JSON.stringify(toRaw(value)));
}

const degreesPayload = ref({ degrees: [], default_degree_id: null });
const activeIndex = ref(0);
const saving = ref(false);
const errorMessage = ref('');
const propsOpen = ref(false);
const dirty = ref(false);

watch(
    () => props.spellDegrees,
    (v) => {
        degreesPayload.value = {
            degrees: Array.isArray(v?.degrees) ? clonePlain(v.degrees) : [],
            default_degree_id: v?.default_degree_id ?? null,
        };
        if (activeIndex.value >= degreesPayload.value.degrees.length) {
            activeIndex.value = 0;
        }
        dirty.value = false;
        emit('dirty-change', false);
    },
    { immediate: true, deep: true },
);

const degrees = computed(() => degreesPayload.value.degrees || []);
const active = computed(() => degrees.value[activeIndex.value] || null);
const subEffectOptions = computed(() => props.effectFormOptions?.sub_effects ?? []);
const targetTypeOptions = SPELL_TARGET_TYPE_OPTIONS();

function markDirty() {
    dirty.value = true;
    emit('dirty-change', true);
}

function degreeTitle(deg) {
    const lvl = Number(deg?.required_level);
    if (!Number.isNaN(lvl) && lvl > 0) {
        return `Niveau ${lvl}`;
    }
    return `Degré ${deg?.position ?? '?'}`;
}

function propertySummary(deg) {
    if (!deg) return '';
    const p = deg.properties || {};
    const parts = [];
    if (p.pa != null && p.pa !== '') parts.push(`${p.pa} PA`);
    const poMin = p.po_min ?? '';
    const poMax = p.po_max ?? '';
    if (poMin !== '' || poMax !== '') {
        parts.push(`PO ${formatPoRange(poMin, poMax)}`);
    }
    if (p.area) parts.push(getAreaHumanReadable(p.area) || p.area);
    if (p.sight_line) parts.push('LdV');
    return parts.join(' · ') || 'Propriétés non renseignées';
}

function selectDegree(index) {
    if (index === activeIndex.value) return;
    if (dirty.value && !confirm('Les modifications de ce degré ne sont pas enregistrées. Changer de degré ?')) {
        return;
    }
    activeIndex.value = index;
    propsOpen.value = false;
}

function updatePoRange(value) {
    const deg = active.value;
    if (!deg) return;
    const parsed = parsePoRange(value);
    const properties = localPropsModel(deg);
    properties.po_min = parsed.po_min;
    properties.po_max = parsed.po_max;
    markDirty();
}

async function reloadDegrees() {
    const { data } = await axios.get(`/api/spells/${props.spellId}/degrees`);
    degreesPayload.value = data?.data || { degrees: [], default_degree_id: null };
    dirty.value = false;
    emit('dirty-change', false);
    emit('changed', degreesPayload.value);
}

async function addDegree() {
    saving.value = true;
    errorMessage.value = '';
    try {
        const prev = degrees.value[degrees.value.length - 1];
        const nextLevel = prev?.required_level != null ? Number(prev.required_level) + 1 : 1;
        const { data } = await axios.post(`/api/spells/${props.spellId}/degrees`, {
            required_level: nextLevel,
        });
        degreesPayload.value = data?.data || degreesPayload.value;
        activeIndex.value = Math.max(0, (degreesPayload.value.degrees?.length || 1) - 1);
        emit('changed', degreesPayload.value);
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Impossible d’ajouter le degré.';
    } finally {
        saving.value = false;
    }
}

async function deleteActiveDegree() {
    if (!active.value?.id) return;
    if (!confirm('Supprimer ce degré ?')) return;
    saving.value = true;
    errorMessage.value = '';
    try {
        const { data } = await axios.delete(`/api/spells/${props.spellId}/degrees/${active.value.id}`);
        degreesPayload.value = data?.data || { degrees: [] };
        activeIndex.value = 0;
        emit('changed', degreesPayload.value);
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Suppression impossible.';
    } finally {
        saving.value = false;
    }
}

function localPropsModel(deg) {
    if (!deg.properties) deg.properties = {};
    return deg.properties;
}

async function saveActiveDegree() {
    const deg = active.value;
    if (!deg?.id) return true;
    saving.value = true;
    errorMessage.value = '';
    try {
        const propsPayload = { ...(deg.properties || {}) };
        const body = {
            required_level: deg.required_level,
            inherits_effects: Boolean(deg.inherits_effects),
            ...propsPayload,
        };
        if (!deg.inherits_effects) {
            body.effects = (deg.rows || []).map((row, i) => ({
                sub_effect_id: Number(row.sub_effect_id),
                order: i,
                scope: row.scope || 'general',
                duration_formula: row.duration_formula || null,
                logic_operator: i > 0 ? row.logic_operator || 'AND' : null,
                logic_condition: row.logic_condition || null,
                crit_only: Boolean(row.crit_only),
                params: row.params || {},
            }));
        }
        const { data } = await axios.patch(`/api/spells/${props.spellId}/degrees/${deg.id}`, body);
        degreesPayload.value = data?.data || degreesPayload.value;
        dirty.value = false;
        emit('dirty-change', false);
        emit('changed', degreesPayload.value);
        return true;
    } catch (err) {
        errorMessage.value =
            Object.values(err.response?.data?.errors || {}).flat()[0] ||
            err.response?.data?.message ||
            'Enregistrement impossible.';
        return false;
    } finally {
        saving.value = false;
    }
}

async function materializeEffects() {
    const deg = active.value;
    if (!deg?.id) return;
    saving.value = true;
    errorMessage.value = '';
    try {
        const { data } = await axios.post(
            `/api/spells/${props.spellId}/degrees/${deg.id}/materialize-effects`,
        );
        degreesPayload.value = data?.data || degreesPayload.value;
        emit('changed', degreesPayload.value);
        markDirty();
    } catch (err) {
        errorMessage.value = err.response?.data?.message || 'Personnalisation impossible.';
    } finally {
        saving.value = false;
    }
}

function addEffectRow() {
    const deg = active.value;
    if (!deg || deg.inherits_effects) return;
    const first = subEffectOptions.value[0];
    if (!first) {
        errorMessage.value = 'Référentiel d’effets indisponible.';
        return;
    }
    if (!Array.isArray(deg.rows)) deg.rows = [];
    deg.rows.push({
        sub_effect_id: first.id,
        order: deg.rows.length,
        scope: 'general',
        crit_only: false,
        logic_operator: deg.rows.length ? 'AND' : '',
        duration_formula: '',
        logic_condition: '',
        params: {
            characteristic: '',
            value_formula: '',
            value_formula_crit: '',
            life_steal_formula: '',
            creature_id: '',
            monster_id: '',
            condition_id: '',
            condition_dofusdb_id: '',
            condition_name: '',
            dispellable: false,
            cells_formula: '',
            movement_kind: 'movement',
            teleport: false,
        },
        sub_effect: first,
    });
    markDirty();
}

function removeEffectRow(index) {
    const deg = active.value;
    if (!deg?.rows) return;
    deg.rows.splice(index, 1);
    markDirty();
}

function duplicateEffectRow(index) {
    const deg = active.value;
    if (!deg?.rows?.[index]) return;
    const clone = clonePlain(deg.rows[index]);
    deg.rows.splice(index + 1, 0, clone);
    markDirty();
}

/** Exposé au parent (sauvegarde sort). */
async function flushSave() {
    if (!dirty.value) return true;
    return saveActiveDegree();
}

defineExpose({ flushSave, isDirty: dirty, reloadDegrees });
</script>

<template>
    <div class="space-y-3" data-cy="spell-degrees-editor">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-base-content/70 max-w-xl">
                Un onglet = un niveau. Les propriétés (PA, PO, zone…) changent avec le degré ; les effets
                décrivent ce que fait le sort.
            </p>
            <button
                type="button"
                class="btn btn-sm btn-primary"
                :disabled="saving"
                data-cy="spell-degree-add"
                @click="addDegree"
            >
                + Degré
            </button>
        </div>

        <p v-if="errorMessage" class="text-sm text-error">{{ errorMessage }}</p>

        <div v-if="!degrees.length" class="rounded-box border border-dashed border-base-300 p-4 text-sm">
            Aucun degré. Les propriétés du sort s’appliquent telles quelles. Cliquez sur « + Degré » pour
            définir une progression.
        </div>

        <template v-else>
            <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1 bg-base-200/60 p-1">
                <button
                    v-for="(deg, idx) in degrees"
                    :key="deg.id"
                    type="button"
                    role="tab"
                    class="tab tab-sm"
                    :class="{ 'tab-active': activeIndex === idx }"
                    @click="selectDegree(idx)"
                >
                    {{ degreeTitle(deg) }}
                </button>
            </div>

            <div v-if="active" class="rounded-box border border-base-300 bg-base-100 p-3 space-y-3">
                <div class="flex flex-wrap items-end gap-3">
                    <div class="w-28">
                        <label class="label text-xs py-0">Niveau requis</label>
                        <input
                            v-model.number="active.required_level"
                            type="number"
                            min="0"
                            class="input input-bordered input-sm w-full"
                            @input="markDirty"
                        />
                    </div>
                    <div class="flex-1 min-w-[12rem] text-sm text-base-content/80">
                        {{ propertySummary(active) }}
                    </div>
                    <button type="button" class="btn btn-ghost btn-sm" @click="propsOpen = !propsOpen">
                        {{ propsOpen ? 'Masquer les propriétés' : 'Modifier les propriétés' }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-ghost btn-sm text-error"
                        :disabled="saving"
                        @click="deleteActiveDegree"
                    >
                        Supprimer
                    </button>
                </div>

                <div v-if="propsOpen" class="border-t border-base-300 pt-3 space-y-3">
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        <SpellDegreePropertyField characteristic-key="pa" label="PA" icon="fa-solid fa-bolt">
                            <input
                                v-model="localPropsModel(active).pa"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="po_min" label="Portée" icon="fa-solid fa-bullseye">
                            <input
                                :value="formatPoRange(localPropsModel(active).po_min, localPropsModel(active).po_max)"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                placeholder="4 ou 2-8"
                                data-cy="spell-degree-po-range"
                                @input="updatePoRange($event.target.value)"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField
                            class="sm:col-span-2 lg:col-span-3"
                            characteristic-key="area"
                            label="Zone"
                            icon="fa-solid fa-draw-polygon"
                        >
                            <AreaNotationField
                                v-model="localPropsModel(active).area"
                                label=""
                                :name="`degree-area-${active.id}`"
                                @update:model-value="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="element" label="Élément(s)" icon="fa-solid fa-fire">
                            <SpellElementPrimariesField
                                v-model="localPropsModel(active).element"
                                label=""
                                size="sm"
                                @update:model-value="markDirty"
                            />
                        </SpellDegreePropertyField>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        <SpellDegreePropertyField characteristic-key="global_cooldown" label="Temps de relance" icon="fa-solid fa-rotate">
                            <input
                                v-model.number="localPropsModel(active).global_cooldown"
                                type="number"
                                min="0"
                                max="255"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="cast_per_turn" label="Lancers / tour" icon="fa-solid fa-repeat">
                            <input
                                v-model="localPropsModel(active).cast_per_turn"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="cast_per_target" label="Lancers / cible" icon="fa-solid fa-crosshairs">
                            <input
                                v-model="localPropsModel(active).cast_per_target"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="number_between_two_cast" label="Délai entre deux lancers" icon="fa-solid fa-hourglass-half">
                            <input
                                v-model="localPropsModel(active).number_between_two_cast"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="casting_time" label="Temps d’incantation" icon="fa-solid fa-clock">
                            <input
                                v-model="localPropsModel(active).casting_time"
                                type="text"
                                class="input input-bordered input-sm w-full"
                                placeholder="Instantané, 1 action…"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="max_stack" label="Cumul maximal" icon="fa-solid fa-layer-group">
                            <input
                                v-model.number="localPropsModel(active).max_stack"
                                type="number"
                                min="0"
                                max="255"
                                class="input input-bordered input-sm w-full"
                                @input="markDirty"
                            />
                        </SpellDegreePropertyField>
                        <SpellDegreePropertyField characteristic-key="target_type" label="Type de ciblage" icon="fa-solid fa-location-crosshairs">
                            <select
                                v-model="localPropsModel(active).target_type"
                                class="select select-bordered select-sm w-full"
                                @change="markDirty"
                            >
                                <option v-for="option in targetTypeOptions" :key="option.value" :value="option.value || null">
                                    {{ option.label }}
                                </option>
                            </select>
                        </SpellDegreePropertyField>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <SpellDegreePropertyField
                            v-for="toggle in [
                                ['po_editable', 'Portée modifiable'],
                                ['sight_line', 'Ligne de vue'],
                                ['cast_in_line', 'Lancer en ligne'],
                                ['cast_in_diagonal', 'Lancer en diagonale'],
                                ['ritual_available', 'Rituel disponible'],
                            ]"
                            :key="toggle[0]"
                            :characteristic-key="toggle[0]"
                            :label="toggle[1]"
                            icon="fa-solid fa-toggle-on"
                        >
                            <label class="flex cursor-pointer items-center gap-2 text-xs">
                                <input
                                    v-model="localPropsModel(active)[toggle[0]]"
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                    @change="markDirty"
                                />
                                <span>Actif</span>
                            </label>
                        </SpellDegreePropertyField>
                    </div>
                </div>

                <div class="border-t border-base-300 pt-3 space-y-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h4 class="text-sm font-semibold">Effets</h4>
                        <div class="flex flex-wrap gap-2">
                            <label
                                v-if="activeIndex > 0"
                                class="flex items-center gap-2 text-xs cursor-pointer"
                            >
                                <input
                                    v-model="active.inherits_effects"
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                    @change="markDirty"
                                />
                                Reprendre les effets du degré précédent
                            </label>
                            <button
                                v-if="active.inherits_effects"
                                type="button"
                                class="btn btn-outline btn-xs"
                                :disabled="saving"
                                @click="materializeEffects"
                            >
                                Personnaliser les effets
                            </button>
                            <button
                                v-else
                                type="button"
                                class="btn btn-primary btn-xs"
                                @click="addEffectRow"
                            >
                                + Effet
                            </button>
                        </div>
                    </div>

                    <p v-if="active.inherits_effects" class="text-xs text-base-content/60">
                        Ce degré réutilise les effets du niveau précédent. Personnalisez pour les modifier.
                    </p>

                    <div v-else-if="!(active.rows || []).length" class="text-xs text-base-content/60 py-2">
                        Aucun effet. Ajoutez une action (frapper, soigner…).
                    </div>

                    <div v-else class="space-y-2">
                        <SpellDegreeEffectRow
                            v-for="(row, index) in active.rows"
                            :key="'fx-' + index"
                            :row="row"
                            :index="index"
                            :options="effectFormOptions"
                            @dirty="markDirty"
                            @duplicate="duplicateEffectRow(index)"
                            @remove="removeEffectRow(index)"
                        />
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-base-300 pt-2">
                    <button
                        type="button"
                        class="btn btn-sm btn-primary"
                        :disabled="saving || !dirty"
                        data-cy="spell-degree-save"
                        @click="saveActiveDegree"
                    >
                        {{ saving ? 'Enregistrement…' : 'Enregistrer le degré' }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
