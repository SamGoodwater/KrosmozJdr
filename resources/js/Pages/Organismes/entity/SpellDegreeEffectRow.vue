<script setup>
/**
 * Ligne d’effet d’un degré de sort — champs adaptés à l’action (SubEffect).
 *
 * @example
 * <SpellDegreeEffectRow :row="row" :index="0" :options="effectFormOptions" @dirty="markDirty" />
 */
/* eslint-disable vue/no-mutating-props -- `row` est un brouillon mutable détenu par SpellDegreesEditor */
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import SelectSearchField from '@/Pages/Molecules/data-input/SelectSearchField.vue';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import { formatSubEffectSelectLabel } from '@/Utils/Entity/subEffectLabels.js';
import { METERS_PER_CASE, previewMetersFromCellsFormula } from '@/Utils/Entity/displacementFormat.js';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

const props = defineProps({
    row: { type: Object, required: true },
    index: { type: Number, required: true },
    options: { type: Object, default: () => ({}) },
});

const emit = defineEmits(['dirty', 'duplicate', 'remove']);

const notificationStore = useNotificationStore();
const creatingCondition = ref(false);

const CASE_SIZE_METERS = METERS_PER_CASE;

const MOVEMENT_KIND_OPTIONS = Object.freeze([
    { value: 'movement', label: 'Déplacement' },
    { value: 'teleport', label: 'Téléportation' },
    { value: 'jump', label: 'Saut' },
    { value: 'pull', label: 'Attirance' },
    { value: 'push', label: 'Repousse' },
]);

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
const hasDuration = computed(() => true);

function characteristicsList() {
    if (isBuffChar.value) {
        return props.options.characteristics_object ?? [];
    }
    const schema = getParamSchema();
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    const categories = param?.categories;
    const all = props.options.characteristics ?? [];
    if (!categories?.length) {
        if (isFrapper.value || slug.value === 'soigner') {
            return all.filter((c) => c.category === 'element');
        }
        return all;
    }
    return all.filter((c) => categories.includes(c.category));
}

const characteristicOptions = computed(() =>
    characteristicsList().map((c) => ({
        value: c.key,
        label: c.label ?? c.key,
    })),
);

const characteristicLabel = computed(() => {
    if (isBuffChar.value) return 'Caractéristique';
    if (isFrapper.value || slug.value === 'soigner') return 'Élément';
    const param = getParamSchema()?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
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

const durationHelp = computed(() => {
    if (isCondition.value) {
        return 'Durée de l’état en tours (combat) ou secondes (hors combat).';
    }
    return 'Durée de l’effet : tours en combat, secondes hors combat.';
});

const scopes = computed(() => props.options.scopes ?? [
    { value: 'general', label: 'Général' },
    { value: 'combat', label: 'Combat' },
    { value: 'out_of_combat', label: 'Hors combat' },
]);

const metersPreview = computed(() => {
    if (!isMove.value) return null;
    return previewMetersFromCellsFormula(props.row.params?.cells_formula);
});

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
    };
}

/**
 * Conserve les params encore valides ; nettoie le reste.
 *
 * @param {string} previousSlug
 */
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
    if (nextBuff && next.characteristic && !String(next.characteristic).endsWith('_object')) {
        const mapped = LEGACY_CHAR_TO_OBJECT_KEY[next.characteristic];
        if (mapped) next.characteristic = mapped;
    }
    props.row.params = next;
    props.row.sub_effect = subEffectForRow();
}

function onActionChange() {
    const previousSlug = props.row.sub_effect?.slug ?? '';
    resetIncompatibleParams(previousSlug);
    markDirty();
}

function syncMovementKind() {
    const p = ensureParams();
    p.teleport = p.movement_kind === 'teleport';
    markDirty();
}

function syncTeleport() {
    const p = ensureParams();
    p.movement_kind = p.teleport ? 'teleport' : 'movement';
    markDirty();
}

function syncCellsFormula() {
    const p = ensureParams();
    p.value_formula = typeof p.cells_formula === 'string' ? p.cells_formula : '';
    markDirty();
}

function onCreaturePicked(id) {
    const params = ensureParams();
    params.creature_id = id == null || id === '' ? '' : Number(id);
    // L’ancien canal monster_id reste uniquement pour les données déjà migrées.
    params.monster_id = '';
    markDirty();
}

function onConditionPicked(id) {
    ensureParams().condition_id = id == null || id === '' ? '' : Number(id);
    markDirty();
}

async function createConditionFromQuery() {
    const name = String(props.row._editor_condition_q ?? '').trim();
    if (!name || creatingCondition.value) return;
    creatingCondition.value = true;
    try {
        const { data } = await axios.post(route('entities.conditions.store'), {
            name,
            description: null,
            state: 'playable',
            read_level: 0,
            write_level: 4,
        });
        const p = ensureParams();
        p.condition_id = data.id;
        p.condition_dofusdb_id = data.dofusdb_id ?? '';
        p.condition_name = data.name ?? name;
        props.row._editor_condition_q = '';
        notificationStore.success('État créé et sélectionné.', {
            duration: 2500,
            placement: 'top-right',
        });
        markDirty();
    } catch (error) {
        notificationStore.error('Impossible de créer l’état.', {
            duration: 5000,
            placement: 'top-center',
        });
        console.warn('[SpellDegreeEffectRow] création état échouée', error);
    } finally {
        creatingCondition.value = false;
    }
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
    <div class="rounded-box border border-base-300 bg-base-100 overflow-hidden" data-cy="spell-degree-effect-row">
        <!-- Lien logique -->
        <div
            v-if="index > 0"
            class="border-b border-dashed border-base-300 bg-base-200/40 px-3 py-2"
        >
            <div class="text-xs font-medium text-base-content/70">Lien avec l’effet précédent</div>
            <div class="mt-1.5 flex flex-wrap items-end gap-2">
                <div class="min-w-40">
                    <label class="label text-xs py-0">Enchaînement</label>
                    <select
                        v-model="row.logic_operator"
                        class="select select-bordered select-sm w-full"
                        @change="markDirty"
                    >
                        <option value="AND">ET — le précédent doit s’appliquer</option>
                        <option value="OR">OU — si la condition &gt; 0</option>
                    </select>
                </div>
                <div v-if="row.logic_operator === 'OR'" class="flex-1 min-w-48 max-w-lg">
                    <label class="label text-xs py-0">Condition (formule &gt; 0)</label>
                    <input
                        v-model="row.logic_condition"
                        type="text"
                        class="input input-bordered input-sm w-full"
                        placeholder="ex: [target_is_ally]"
                        @input="markDirty"
                    />
                </div>
            </div>
        </div>

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
            <div class="w-32">
                <label class="text-xs font-medium text-base-content/70">Contexte</label>
                <select
                    v-model="row.scope"
                    class="select select-bordered select-sm w-full mt-0.5"
                    @change="markDirty"
                >
                    <option v-for="sc in scopes" :key="sc.value" :value="sc.value">
                        {{ sc.label }}
                    </option>
                </select>
            </div>
            <label class="flex items-center gap-2 cursor-pointer shrink-0 mb-0.5">
                <input v-model="row.crit_only" type="checkbox" class="checkbox checkbox-sm" @change="markDirty" />
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
            <!-- État -->
            <div v-if="isCondition" class="space-y-2 max-w-xl">
                <label class="text-xs font-medium text-base-content/80">État</label>
                <EntityPickerCore
                    entity-type="conditions"
                    :model-value="row.params.condition_id || null"
                    variant="compact"
                    placeholder="Rechercher un état…"
                    @update:model-value="onConditionPicked"
                />
                <div class="flex flex-wrap items-end gap-2">
                    <input
                        v-model="row._editor_condition_q"
                        type="search"
                        class="input input-bordered input-sm flex-1 min-w-40"
                        placeholder="Ou créer : nom de l’état…"
                        autocomplete="off"
                    />
                    <button
                        type="button"
                        class="btn btn-outline btn-sm"
                        :disabled="creatingCondition || !(row._editor_condition_q || '').trim()"
                        @click="createConditionFromQuery"
                    >
                        Créer
                    </button>
                </div>
                <p v-if="row.params.condition_name" class="text-xs text-base-content/60">
                    Sélection : {{ row.params.condition_name }}
                </p>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        v-model="row.params.dispellable"
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        @change="markDirty"
                    />
                    <span class="text-xs">Dissipable</span>
                </label>
            </div>

            <!-- Invocation -->
            <div v-if="isSummon" class="space-y-1 max-w-xl">
                <label class="text-xs font-medium text-base-content/80">Créature invoquée</label>
                <EntityPickerCore
                    entity-type="creatures"
                    :model-value="row.params.creature_id || row.summon_creature?.id || null"
                    variant="compact"
                    placeholder="Rechercher une créature…"
                    @update:model-value="onCreaturePicked"
                />
            </div>

            <!-- Déplacement -->
            <div v-if="isMove" class="space-y-2 max-w-xl">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Nombre de cases (formule)</label>
                    <input
                        v-model="row.params.cells_formula"
                        type="text"
                        inputmode="decimal"
                        class="input input-bordered input-sm w-full"
                        placeholder="ex: 3, 0,33, [level]…"
                        @input="syncCellsFormula"
                    />
                    <p class="text-[0.7rem] text-base-content/55">
                        Distance en cases (1 case = {{ CASE_SIZE_METERS }} m).
                    </p>
                    <p v-if="metersPreview" class="text-[0.7rem] font-medium tabular-nums text-base-content/80">
                        {{ metersPreview }}
                    </p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Type de mouvement</label>
                    <select
                        v-model="row.params.movement_kind"
                        class="select select-bordered select-sm w-full"
                        @change="syncMovementKind"
                    >
                        <option v-for="opt in MOVEMENT_KIND_OPTIONS" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </option>
                    </select>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        v-model="row.params.teleport"
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        @change="syncTeleport"
                    />
                    <span class="text-xs">Téléportation</span>
                </label>
            </div>

            <!-- Caractéristique / élément -->
            <SelectSearchField
                v-if="hasCharacteristic"
                size="xs"
                :label="characteristicLabel"
                :placeholder="characteristicLabel + '…'"
                :options="characteristicOptions"
                :model-value="row.params.characteristic"
                :searchable="characteristicOptions.length > 8"
                @update:model-value="
                    (v) => {
                        row.params.characteristic = v;
                        markDirty();
                    }
                "
            />

            <!-- Valeurs -->
            <div v-if="hasValue" class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Valeur (formule)</label>
                    <input
                        v-model="row.params.value_formula"
                        type="text"
                        class="input input-bordered input-sm w-full"
                        placeholder="ex: 2d6, [1-4], [level]*2"
                        @input="markDirty"
                    />
                    <p class="text-[0.7rem] text-base-content/55">{{ valueHelp }}</p>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-medium text-base-content/80">Valeur critique</label>
                    <input
                        v-model="row.params.value_formula_crit"
                        type="text"
                        class="input input-bordered input-sm w-full"
                        placeholder="optionnel"
                        @input="markDirty"
                    />
                </div>
            </div>

            <div v-if="hasLifeSteal" class="space-y-1 max-w-md">
                <label class="text-xs font-medium text-base-content/80">Vol de vie (formule)</label>
                <input
                    v-model="row.params.life_steal_formula"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="ex: floor([value]/2)"
                    @input="markDirty"
                />
            </div>

            <div v-if="hasDuration" class="space-y-1 max-w-md">
                <label class="text-xs font-medium text-base-content/80">Durée de l’effet</label>
                <input
                    v-model="row.duration_formula"
                    type="text"
                    class="input input-bordered input-sm w-full"
                    placeholder="ex: 2, [level]"
                    @input="markDirty"
                />
                <p class="text-[0.7rem] text-base-content/55">{{ durationHelp }}</p>
            </div>
        </div>
    </div>
</template>
