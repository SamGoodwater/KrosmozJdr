<script setup>
/**
 * Éditeur compact des degrés d’un sort (onglets, propriétés, effets).
 * Enregistrement via flushAll (bulk) — pas de confirm au changement d’onglet.
 */
import { computed, watch } from 'vue';
import { getAreaHumanReadable } from '@/Utils/Entity/Areas';
import { formatPoRange } from '@/Utils/Entity/poRange.js';
import { useSpellDegreesDraft } from '@/Composables/entity/useSpellDegreesDraft.js';
import SpellDegreeTabs from '@/Pages/Molecules/entity/spell/SpellDegreeTabs.vue';
import SpellCastPropertiesGrid from '@/Pages/Molecules/entity/spell/SpellCastPropertiesGrid.vue';
import SpellEffectEditorRow from '@/Pages/Organismes/entity/SpellEffectEditorRow.vue';
import SpellDegreePropertyField from '@/Pages/Molecules/entity/spell/SpellDegreePropertyField.vue';

const props = defineProps({
    spellId: { type: Number, required: true },
    /** Payload `{ degrees, default_degree_id }` depuis l’API / Inertia. */
    spellDegrees: { type: Object, default: () => ({ degrees: [], default_degree_id: null }) },
    /** Sort de base (repli propriétés). */
    spell: { type: Object, default: null },
    effectFormOptions: { type: Object, default: () => ({}) },
    embeddedInModal: { type: Boolean, default: false },
});

const emit = defineEmits(['changed', 'dirty-change']);

const draft = useSpellDegreesDraft({
    spellId: () => props.spellId,
    spellDegrees: () => props.spellDegrees,
    spell: () => props.spell,
});

const {
    degrees,
    active,
    activeIndex,
    saving,
    errorMessage,
    dirty,
    dirtyDegreeIds,
    markDirty,
    selectDegree,
    ensureProperties,
    resolvedActive,
    addDegree,
    deleteActiveDegree,
    materializeEffects,
    flushAll,
    reloadDegrees,
} = draft;

watch(dirty, (v) => emit('dirty-change', v));
watch(
    () => draft.degreesPayload.value,
    (v) => emit('changed', v),
    { deep: true },
);

const subEffectOptions = computed(() => props.effectFormOptions?.sub_effects ?? []);

const propertiesReadonly = computed(() => {
    const src = active.value?.properties_source || 'own';
    return src === 'previous' || src === 'spell';
});

const sourceLabel = computed(() => {
    const src = active.value?.properties_source || 'own';
    if (src === 'previous') return 'Hérité du degré précédent';
    if (src === 'spell') return 'Hérité du sort de base';
    return '';
});

const displayProperties = computed(() => {
    if (propertiesReadonly.value) {
        return resolvedActive();
    }
    return ensureProperties(active.value);
});

const propertySources = computed(() => {
    const resolved = resolvedActive();
    const out = {};
    for (const key of Object.keys(resolved)) {
        if (key.endsWith('_source')) {
            out[key.replace(/_source$/, '')] = resolved[key];
        }
    }
    return out;
});

function propertySummary(deg) {
    if (!deg) return '';
    const p = draft.resolvedFor(deg);
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

function onPropertiesSource(value) {
    if (!active.value) return;
    active.value.properties_source = value;
    markDirty();
}

function onPropertiesUpdate(next) {
    if (!active.value || propertiesReadonly.value) return;
    active.value.properties = { ...next };
    markDirty();
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
    const clone = JSON.parse(JSON.stringify(deg.rows[index]));
    deg.rows.splice(index + 1, 0, clone);
    markDirty();
}

async function handleDelete() {
    if (!active.value?.id) return;
    if (!confirm('Supprimer ce degré ?')) return;
    await deleteActiveDegree();
}

async function handleAdd() {
    await addDegree();
}

defineExpose({
    flushSave: () => flushAll(),
    flushAll,
    isDirty: dirty,
    reloadDegrees,
    hasDegrees: computed(() => degrees.value.length > 0),
});
</script>

<template>
    <div class="space-y-3" data-cy="spell-degrees-editor">
        <p class="text-sm text-base-content/70 max-w-2xl">
            Un onglet = un niveau. Les propriétés (PA, PO, zone…) changent avec le degré ; les effets
            décrivent ce que fait le sort. Un nouveau degré copie le précédent.
        </p>

        <p v-if="errorMessage" class="text-sm text-error">{{ errorMessage }}</p>

        <div v-if="!degrees.length" class="rounded-box border border-dashed border-base-300 p-4 text-sm">
            Aucun degré. Les propriétés du sort s’appliquent telles quelles. Cliquez sur « + Degré »
            pour définir une progression.
            <div class="mt-2">
                <button
                    type="button"
                    class="btn btn-sm btn-primary"
                    :disabled="saving"
                    data-cy="spell-degree-add"
                    @click="handleAdd"
                >
                    + Degré
                </button>
            </div>
        </div>

        <template v-else>
            <SpellDegreeTabs
                :degrees="degrees"
                :active-index="activeIndex"
                :dirty-ids="dirtyDegreeIds"
                :properties-source="active?.properties_source || 'own'"
                :saving="saving"
                :show-previous-source="activeIndex > 0"
                @select="selectDegree"
                @add="handleAdd"
                @update:properties-source="onPropertiesSource"
            />

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
                    <button
                        type="button"
                        class="btn btn-ghost btn-sm text-error"
                        :disabled="saving"
                        @click="handleDelete"
                    >
                        Supprimer
                    </button>
                </div>

                <SpellCastPropertiesGrid
                    :model-value="displayProperties"
                    :readonly="propertiesReadonly"
                    :source-label="sourceLabel"
                    :property-sources="propertySources"
                    @update:model-value="onPropertiesUpdate"
                    @dirty="markDirty"
                />

                <div class="grid gap-2 sm:grid-cols-2 border-t border-base-300 pt-3">
                    <SpellDegreePropertyField
                        v-for="toggle in [
                            ['cast_in_line', 'Lancer en ligne'],
                            ['cast_in_diagonal', 'Lancer en diagonale'],
                        ]"
                        :key="toggle[0]"
                        :characteristic-key="toggle[0]"
                        :label="toggle[1]"
                        icon="fa-solid fa-toggle-on"
                    >
                        <label class="flex cursor-pointer items-center gap-2 text-xs">
                            <input
                                v-model="ensureProperties(active)[toggle[0]]"
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                :disabled="propertiesReadonly"
                                @change="markDirty"
                            />
                            <span>Actif</span>
                        </label>
                    </SpellDegreePropertyField>
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

                    <p v-if="active.inherits_effects" class="text-xs text-base-content/70">
                        Ce degré réutilise les effets du niveau précédent. Personnalisez pour les
                        modifier.
                    </p>

                    <div
                        v-else-if="!(active.rows || []).length"
                        class="text-xs text-base-content/70 py-2"
                    >
                        Aucun effet. Ajoutez une action (frapper, soigner…).
                    </div>

                    <div v-else class="space-y-2">
                        <SpellEffectEditorRow
                            v-for="(row, index) in active.rows"
                            :key="'fx-' + active.id + '-' + index"
                            :row="row"
                            :index="index"
                            :options="effectFormOptions"
                            @dirty="markDirty"
                            @duplicate="duplicateEffectRow(index)"
                            @remove="removeEffectRow(index)"
                        />
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
