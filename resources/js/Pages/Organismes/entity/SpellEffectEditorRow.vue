<script setup>
/**
 * Ligne d’effet d’un degré — action, contexte dynamique, valeur / valeur critique, durée.
 *
 * @example
 * <SpellEffectEditorRow :row="row" :index="0" :options="effectFormOptions" @dirty="markDirty" />
 */
/* eslint-disable vue/no-mutating-props -- brouillon mutable détenu par SpellDegreesEditor */
import { computed, watch } from 'vue';
import { formatSubEffectSelectLabel } from '@/Utils/Entity/subEffectLabels.js';
import EffectContextCharacteristic from '@/Pages/Molecules/entity/spell/EffectContextCharacteristic.vue';
import EffectContextCreature from '@/Pages/Molecules/entity/spell/EffectContextCreature.vue';
import EffectContextCondition from '@/Pages/Molecules/entity/spell/EffectContextCondition.vue';
import EffectContextMove from '@/Pages/Molecules/entity/spell/EffectContextMove.vue';
import EffectConditionList from '@/Pages/Molecules/entity/spell/EffectConditionList.vue';
import EffectValueRulesEditor from '@/Pages/Molecules/entity/spell/EffectValueRulesEditor.vue';
import FormulaExpressionInput from '@/Pages/Molecules/data-input/FormulaExpressionInput.vue';
import {
    APPLIES_TO_OPTIONS,
    buildActorPrefixedSuggestions,
    normalizeAppliesTo,
    normalizeConditions,
    normalizeValueRules,
} from '@/Utils/Entity/effectConditions.js';
import { SHORT_ABILITY_TO_MOD_KEY } from '@/Utils/Entity/spellFormulaDisplay.js';
import { resolveDef } from '@/Composables/entity/useCharacteristicDisplay';

const props = defineProps({
    row: { type: Object, required: true },
    index: { type: Number, required: true },
    options: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['dirty', 'duplicate', 'remove']);

const LEGACY_CHAR_TO_OBJECT_KEY = Object.freeze({
    action_points: 'action_points_object',
    movement_points: 'movement_points_object',
    range: 'range_object',
    agility: 'agility_object',
    strength: 'strength_object',
    intelligence: 'intelligence_object',
    chance: 'chance_object',
    wisdom: 'wisdom_object',
    vitality: 'vitality_object',
    life_points: 'life_points_max_object',
    shield: 'armor_class_object',
    earth: 'fixed_damage_earth_object',
    fire: 'fixed_damage_fire_object',
    water: 'fixed_damage_water_object',
    air: 'fixed_damage_air_object',
    neutral: 'fixed_damage_neutral_object',
});

function ensureParams() {
    if (!props.row.params || typeof props.row.params !== 'object') {
        props.row.params = {};
    }
    return props.row.params;
}

function markDirty() {
    emit('dirty');
}

function subEffectForRow() {
    const id = props.row?.sub_effect_id;
    if (!id) return null;
    return (props.options.sub_effects ?? []).find((s) => Number(s.id) === Number(id)) ?? null;
}

const slug = computed(() => {
    const sub = subEffectForRow();
    return sub?.slug ?? props.row?.sub_effect?.slug ?? '';
});

function getParamSchema() {
    return subEffectForRow()?.param_schema ?? null;
}

function schemaHas(key) {
    return getParamSchema()?.params?.some((p) => p.key === key) ?? false;
}

const isFrapper = computed(() => slug.value === 'frapper');
const isBuffChar = computed(() =>
    ['booster', 'retirer', 'voler-caracteristiques'].includes(slug.value),
);
const isSummon = computed(() => slug.value === 'invoquer' || schemaHas('monster'));
const isCondition = computed(
    () =>
        ['appliquer-etat', 's-appliquer-etat'].includes(slug.value) ||
        schemaHas('condition') ||
        schemaHas('condition_id'),
);
const isMove = computed(() => slug.value === 'déplacer' || schemaHas('cells_formula'));
const hasCharacteristic = computed(
    () => schemaHas('characteristic') || isFrapper.value || isBuffChar.value || slug.value === 'soigner',
);
const hasValue = computed(
    () =>
        schemaHas('value') ||
        isFrapper.value ||
        isBuffChar.value ||
        ['soigner', 'protéger', 'autre'].includes(slug.value),
);
const hasLifeSteal = computed(() => schemaHas('life_steal_formula') || isFrapper.value);

function characteristicsList() {
    if (isBuffChar.value) {
        return (
            props.options.characteristics_spell_buff ??
            props.options.characteristics_object ??
            []
        );
    }
    const schema = getParamSchema();
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    const categories = param?.categories;
    const actionList = props.options.characteristics_spell_action ?? [];
    const slugVal = slug.value;
    const actionFiltered = actionList.filter((c) =>
        (c.sub_effect_slugs ?? []).includes(slugVal),
    );
    if (actionFiltered.length) {
        if (categories?.length) {
            return actionFiltered.filter((c) => categories.includes(c.category));
        }
        return actionFiltered;
    }
    const all = props.options.characteristics ?? [];
    if (!categories?.length) {
        if (isFrapper.value || slug.value === 'soigner') {
            return all.filter((c) => c.category === 'element');
        }
        return all;
    }
    return all.filter((c) => categories.includes(c.category));
}

const characteristicLabel = computed(() => {
    if (isBuffChar.value) return 'Caractéristique';
    if (isFrapper.value || slug.value === 'soigner') return 'Élément';
    const param = getParamSchema()?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
});

const characteristicHelper = computed(() => {
    if (isFrapper.value || slug.value === 'soigner') {
        return 'Élément utilisé pour calculer dégâts ou soins.';
    }
    if (isBuffChar.value) {
        return 'Caractéristique ciblée par le buff, debuff ou vol.';
    }
    return 'Contexte dépendant de l’action choisie.';
});

const valueHelp = computed(() => {
    const s = slug.value;
    if (s === 'frapper') {
        return 'Dégâts primaires : ndX, [min-max], [level], caractéristiques entre crochets…';
    }
    if (s === 'soigner') return 'Montant de soin : formule ou dés (ndX).';
    if (s === 'protéger') return 'Montant de bouclier / protection absorbée.';
    if (isBuffChar.value) return 'Valeur appliquée à la caractéristique choisie.';
    if (s === 'autre') return 'Texte ou formule libre.';
    return 'Formule : ndX, [min-max], [level], [agi], floor()…';
});

const critHelp = computed(
    () =>
        'Valeur appliquée en cas de critique. Laisser vide pour réutiliser la valeur normale.',
);

const durationHelp = computed(() => {
    if (isCondition.value) {
        return 'Durée de l’état en tours (combat) ou secondes (hors combat).';
    }
    return 'Durée de l’effet : tours en combat, secondes hors combat.';
});

const scopes = computed(
    () =>
        props.options.scopes ?? [
            { value: 'general', label: 'Général' },
            { value: 'combat', label: 'Combat' },
            { value: 'out_of_combat', label: 'Hors combat' },
        ],
);

function defaultParams() {
    return {
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
        applies_to: 'targets',
        conditions: [],
        value_rules: [],
    };
}

/** Suggestions de formules (clés courtes + modificateurs complets). */
const formulaSuggestions = computed(() => {
    const seen = new Set();
    const out = [];
    const push = (id, name, short_name = null) => {
        const key = String(id ?? '').trim();
        if (!key || seen.has(key)) return;
        seen.add(key);
        out.push({ id: key, name: name || key, short_name });
    };

    for (const c of props.options.characteristics ?? []) {
        push(c.key, c.label, c.label);
        const full = SHORT_ABILITY_TO_MOD_KEY[c.key];
        if (full) {
            const def = resolveDef(full);
            push(full, def?.name || c.label, def?.short_name || c.label);
        }
    }
    for (const [short, full] of Object.entries(SHORT_ABILITY_TO_MOD_KEY)) {
        push(short, short);
        const def = resolveDef(full);
        push(full, def?.name || full, def?.short_name || null);
    }
    push('level', 'Niveau', 'Niv.');
    push('niveau', 'Niveau', 'Niv.');

    return out;
});

const actorConditionSuggestions = computed(() =>
    buildActorPrefixedSuggestions(formulaSuggestions.value),
);

const appliesToModel = computed({
    get() {
        return normalizeAppliesTo(props.row?.params?.applies_to);
    },
    set(v) {
        ensureParams().applies_to = normalizeAppliesTo(v);
        markDirty();
    },
});

function resetIncompatibleParams(previousSlug) {
    const p = ensureParams();
    const next = { ...defaultParams(), ...p };
    const prevBuff = ['booster', 'retirer', 'voler-caracteristiques'].includes(previousSlug);
    const nextBuff = isBuffChar.value;
    const prevElement = ['frapper', 'soigner'].includes(previousSlug);
    const nextElement = isFrapper.value || slug.value === 'soigner';

    if (prevBuff !== nextBuff || prevElement !== nextElement) {
        next.characteristic = '';
    }
    if (!isSummon.value) {
        next.creature_id = '';
        next.monster_id = '';
    }
    if (!isCondition.value) {
        next.condition_id = '';
        next.condition_dofusdb_id = '';
        next.condition_name = '';
        next.dispellable = false;
    }
    if (!isMove.value) {
        next.cells_formula = '';
        next.movement_kind = 'movement';
        next.teleport = false;
    }
    if (!hasLifeSteal.value) {
        next.life_steal_formula = '';
    }
    if (nextBuff && next.characteristic && !String(next.characteristic).endsWith('_spell')) {
        const mapped = LEGACY_CHAR_TO_OBJECT_KEY[next.characteristic];
        if (mapped) {
            next.characteristic = mapped.replace(/_object$/, '_spell');
        }
    }
    props.row.params = next;
    props.row.sub_effect = subEffectForRow();
}

function onActionChange() {
    const previousSlug = props.row.sub_effect?.slug ?? '';
    resetIncompatibleParams(previousSlug);
    markDirty();
}

watch(
    () => props.row.sub_effect_id,
    () => {
        if (!props.row.params) {
            props.row.params = defaultParams();
        }
        const sub = subEffectForRow();
        if (sub) props.row.sub_effect = sub;
    },
    { immediate: true },
);
</script>

<template>
    <div
        class="rounded-box border border-base-300 bg-base-100"
        data-cy="spell-degree-effect-row"
    >
        <div class="flex flex-wrap items-end gap-2 gap-y-2 px-3 py-2 border-b border-base-300 bg-base-200/30">
            <div class="min-w-40 flex-1">
                <label class="text-xs font-medium text-base-content/70">Action</label>
                <select
                    v-model="row.sub_effect_id"
                    class="select select-bordered select-sm w-full mt-0.5"
                    required
                    @change="onActionChange"
                >
                    <option value="">— Choisir —</option>
                    <option
                        v-for="s in options.sub_effects || []"
                        :key="s.id"
                        :value="s.id"
                    >
                        {{ formatSubEffectSelectLabel(s) }}
                    </option>
                </select>
            </div>
            <label class="flex items-center gap-2 cursor-pointer shrink-0 mb-0.5">
                <input
                    v-model="row.crit_only"
                    type="checkbox"
                    class="checkbox checkbox-sm"
                    @change="markDirty"
                />
                <span class="text-xs leading-snug">Critique seulement</span>
            </label>
            <div class="flex gap-0.5 ml-auto">
                <button
                    type="button"
                    class="btn btn-ghost btn-sm btn-square"
                    title="Dupliquer cet effet"
                    @click="emit('duplicate')"
                >
                    +
                </button>
                <button
                    type="button"
                    class="btn btn-ghost btn-sm btn-square text-error"
                    title="Supprimer cet effet"
                    @click="emit('remove')"
                >
                    ×
                </button>
            </div>
        </div>

        <div v-if="row.sub_effect_id" class="p-3 space-y-3">
            <div class="space-y-1.5 rounded-box border border-dashed border-primary/30 bg-primary/5 p-2">
                <label class="text-xs font-medium text-base-content/80">
                    Conditions pour appliquer cet effet
                </label>
                <EffectConditionList
                    :model-value="normalizeConditions(row.params?.conditions)"
                    :suggestions="actorConditionSuggestions"
                    @update:model-value="
                        ensureParams().conditions = $event;
                        markDirty();
                    "
                    @dirty="markDirty"
                />
            </div>

            <div class="space-y-1 max-w-xs">
                <label class="text-xs font-medium text-base-content/80">S’applique à</label>
                <select
                    v-model="appliesToModel"
                    class="select select-bordered select-sm w-full"
                >
                    <option
                        v-for="opt in APPLIES_TO_OPTIONS"
                        :key="opt.value"
                        :value="opt.value"
                    >
                        {{ opt.label }}
                    </option>
                </select>
                <p class="text-xs text-base-content/70">
                    Qui reçoit l’effet : toi, les cibles, ou les deux.
                </p>
            </div>

            <EffectContextCondition
                v-if="isCondition"
                :condition-id="row.params.condition_id || null"
                :condition-name="row.params.condition_name || ''"
                :dispellable="Boolean(row.params.dispellable)"
                @update:condition-id="
                    ensureParams().condition_id = $event == null || $event === '' ? '' : Number($event)
                "
                @update:condition-meta="Object.assign(ensureParams(), $event)"
                @update:dispellable="ensureParams().dispellable = $event"
                @dirty="markDirty"
            />

            <EffectContextCreature
                v-if="isSummon"
                :model-value="row.params.creature_id || row.summon_creature?.id || null"
                @update:model-value="
                    ensureParams().creature_id = $event == null || $event === '' ? '' : Number($event);
                    ensureParams().monster_id = '';
                    markDirty();
                "
            />

            <EffectContextMove
                v-if="isMove"
                :cells-formula="row.params.cells_formula || ''"
                :movement-kind="row.params.movement_kind || 'movement'"
                :teleport="Boolean(row.params.teleport)"
                @update:cells-formula="
                    ensureParams().cells_formula = $event;
                    ensureParams().value_formula = $event;
                "
                @update:movement-kind="ensureParams().movement_kind = $event"
                @update:teleport="ensureParams().teleport = $event"
                @dirty="markDirty"
            />

            <EffectContextCharacteristic
                v-if="hasCharacteristic"
                :label="characteristicLabel"
                :helper="characteristicHelper"
                :options="characteristicsList()"
                :model-value="row.params.characteristic"
                @update:model-value="
                    ensureParams().characteristic = $event;
                    markDirty();
                "
            />

            <div v-if="hasValue" class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Valeur (formule)</label>
                    <FormulaExpressionInput
                        :model-value="row.params.value_formula || ''"
                        :suggestions="formulaSuggestions"
                        :min-query-length="2"
                        placeholder="ex: 2d6, [strong], min(3+[agi], 6)"
                        @update:model-value="
                            ensureParams().value_formula = $event;
                            markDirty();
                        "
                    />
                    <p class="text-xs text-base-content/70">{{ valueHelp }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Valeur critique</label>
                    <FormulaExpressionInput
                        :model-value="row.params.value_formula_crit || ''"
                        :suggestions="formulaSuggestions"
                        :min-query-length="2"
                        placeholder="optionnel"
                        @update:model-value="
                            ensureParams().value_formula_crit = $event;
                            markDirty();
                        "
                    />
                    <p class="text-xs text-base-content/70">{{ critHelp }}</p>
                </div>
            </div>

            <EffectValueRulesEditor
                v-if="hasValue"
                :model-value="normalizeValueRules(row.params?.value_rules)"
                :condition-suggestions="actorConditionSuggestions"
                :value-suggestions="formulaSuggestions"
                @update:model-value="
                    ensureParams().value_rules = $event;
                    markDirty();
                "
                @dirty="markDirty"
            />

            <div v-if="hasLifeSteal" class="space-y-1 max-w-md">
                <label class="text-xs font-medium text-base-content/80">Vol de vie (formule)</label>
                <FormulaExpressionInput
                    :model-value="row.params.life_steal_formula || ''"
                    :suggestions="formulaSuggestions"
                    :min-query-length="2"
                    placeholder="ex: floor([value]/2)"
                    @update:model-value="
                        ensureParams().life_steal_formula = $event;
                        markDirty();
                    "
                />
                <p class="text-xs text-base-content/70">
                    Part des dégâts convertie en soins. Laisser vide si aucun vol de vie.
                </p>
            </div>

            <div class="space-y-1 max-w-md">
                <label class="text-xs font-medium text-base-content/80">Durée de l’effet</label>
                <input
                    v-model="row.duration_formula"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="ex: 2, [level]"
                    @input="markDirty"
                />
                <p class="text-xs text-base-content/70">{{ durationHelp }}</p>
            </div>

            <div class="space-y-1 max-w-xs">
                <label class="text-xs font-medium text-base-content/80">Portée de jeu</label>
                <select
                    v-model="row.scope"
                    class="select select-bordered select-sm w-full"
                    @change="markDirty"
                >
                    <option v-for="sc in scopes" :key="sc.value" :value="sc.value">
                        {{ sc.label }}
                    </option>
                </select>
                <p class="text-xs text-base-content/70">
                    Général, combat uniquement, ou hors combat.
                </p>
            </div>
        </div>
    </div>
</template>
