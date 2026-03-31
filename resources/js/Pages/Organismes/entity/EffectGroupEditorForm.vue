<script setup>
/**
 * Édition d’un groupe d’effets (champs communs + onglets par degré + sous-effets).
 * Utilisable depuis l’admin effets ou la fiche sort.
 */
import { computed, reactive, ref, useSlots, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import AreaDisplay from '@/Pages/Molecules/entity/spell/AreaDisplay.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { AREA_NOTATION_HELP, isValidAreaNotation } from '@/Utils/Entity/areaNotation.js';
import { getElementIcon } from '@/Utils/Entity/Elements.js';
import { getByCharacteristicKey } from '@/Composables/store/useCharacteristicsStore';
import {
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
} from '@/Composables/entity/useCharacteristicDisplay';

/** Clés config effet → characteristic_key du groupe spell (store Inertia). */
const EFFECT_CHAR_TO_SPELL_KEY = Object.freeze({
    action_points: 'action_points_spell',
    movement_points: 'movement_points_spell',
    range: 'range_spell',
    agility: 'agi_spell',
    strength: 'strong_spell',
    intelligence: 'intel_spell',
    chance: 'chance_spell',
    wisdom: 'sagesse_spell',
    vitality: 'vitality_spell',
    life_points: 'vitality_spell',
    shield: 'bouclier_spell',
    earth: 'res_terre_spell',
    fire: 'res_feu_spell',
    water: 'res_eau_spell',
    air: 'res_air_spell',
    neutral: 'res_neutre_spell',
});

const props = defineProps({
    options: {
        type: Object,
        default: () => ({
            effect_groups: [],
            sub_effects: [],
            scopes: [],
            characteristics: [],
            monsters: [],
            spell_states: [],
            characteristics_object: [],
        }),
    },
    groupEffects: { type: Array, required: true },
    /** Effet courant (onglet initial). */
    selectedEffectId: { type: Number, required: true },
    /** URL de PATCH (admin ou fiche sort). */
    patchUrl: { type: String, required: true },
    /** Titre optionnel au-dessus du formulaire. */
    heading: { type: String, default: '' },
    /** Libellé du bouton de soumission. */
    submitLabel: { type: String, default: 'Enregistrer le groupe' },
});

const common = reactive({
    name: '',
    description: '',
    target_type: 'direct',
});

const degreeForms = ref([]);
const activeTab = ref(0);

const groupSaveForm = useForm({
    common: {},
    degrees: [],
});

const slots = useSlots();
const hasDegreeExtraSlot = computed(() => Boolean(slots['degree-extra']));

function defaultParamsForSubEffect() {
    return {
        characteristic: '',
        value_formula: '',
        value_formula_crit: '',
        life_steal_formula: '',
        monster_id: '',
        spell_state_id: '',
        state_dofusdb_id: '',
        state_name: '',
        dispellable: false,
        cells_formula: '',
        teleport: false,
    };
}

function getParamSchemaForRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) return null;
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.param_schema ?? null;
}

/** Anciennes clés config (effect_sub_effects) → clés BDD groupe object. */
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

/**
 * @param {number|string} subEffectId
 * @param {object} params
 */
function normalizeLegacyCharacteristicKeyForSubEffect(subEffectId, params) {
    const sub = props.options.sub_effects?.find((s) => s.id === subEffectId);
    const slug = sub?.slug ?? '';
    if (!['booster', 'retirer', 'voler-caracteristiques'].includes(slug)) {
        return;
    }
    const ch = params?.characteristic;
    if (typeof ch !== 'string' || ch === '' || ch.endsWith('_object')) {
        return;
    }
    const mapped = LEGACY_CHAR_TO_OBJECT_KEY[ch];
    if (mapped) {
        params.characteristic = mapped;
    }
}

function characteristicsForRow(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'booster' || slug === 'retirer' || slug === 'voler-caracteristiques') {
        return props.options.characteristics_object ?? [];
    }
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    const categories = param?.categories;
    if (!categories?.length) return props.options.characteristics ?? [];
    return (props.options.characteristics ?? []).filter((c) => categories.includes(c.category));
}

function rowHasCharacteristicParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'characteristic') ?? false;
}

function rowHasValueParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'value') ?? false;
}

function rowHasMonsterParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.type === 'monster') ?? false;
}

/** @param {object} row */
function subEffectSlugForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    return sub?.slug ?? '';
}

/** @param {object} row */
function rowHasSpellStateParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'appliquer-etat' || slug === 's-appliquer-etat') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'spell_state_id' || p.type === 'spell_state') ?? false;
}

/** @param {object} row */
function rowHasCellsFormulaParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'déplacer') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'cells_formula') ?? false;
}

/** @param {object} row */
function rowHasTeleportParam(row) {
    const slug = subEffectSlugForRow(row);
    if (slug === 'déplacer') {
        return true;
    }
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'teleport') ?? false;
}

/**
 * Métadonnées affichage badge caractéristique (élément, groupe object BDD, ou fallback spell).
 *
 * @param {object} c - entrée characteristics (config ou characteristics_object)
 * @returns {{ kind: string, label: string, primaryId?: number, icon?: string|null, containerStyle?: object, colorStyle?: object }}
 */
function characteristicBadgeMeta(c) {
    const label = c.label ?? c.key;
    if (c.category === 'element') {
        return {
            kind: 'element',
            label,
            primaryId: elementKeyToPrimaryId(c.key),
        };
    }
    if (c.category === 'object' || (typeof c.key === 'string' && c.key.endsWith('_object'))) {
        const def = getByCharacteristicKey('item', c.key);
        return {
            kind: 'object',
            label,
            icon: def?.icon ?? null,
            containerStyle: def?.color ? getCharacteristicContainerStyle(def.color) : {},
            colorStyle: def?.color ? getCharacteristicColorStyle(def.color) : {},
        };
    }
    const sk = EFFECT_CHAR_TO_SPELL_KEY[c.key];
    const def = sk ? getByCharacteristicKey('spell', sk) : null;
    return {
        kind: 'spell',
        label,
        icon: def?.icon ?? null,
        containerStyle: def?.color ? getCharacteristicContainerStyle(def.color) : {},
        colorStyle: def?.color ? getCharacteristicColorStyle(def.color) : {},
    };
}

/**
 * @param {object} row
 * @param {object} c
 */
function characteristicBadgeButtonClass(row, c) {
    const meta = characteristicBadgeMeta(c);
    const sel = row.params.characteristic === c.key;
    const base =
        'btn btn-xs h-8 min-h-8 gap-1 font-normal border border-base-300';
    if (!sel) {
        return `${base} btn-ghost`;
    }
    if (meta.kind === 'element') {
        return `${base} btn-primary border-primary`;
    }
    return `${base} border-primary/40`;
}

/**
 * @param {object} row
 * @param {object} c
 */
function characteristicBadgeButtonStyle(row, c) {
    const meta = characteristicBadgeMeta(c);
    if (row.params.characteristic !== c.key || (meta.kind !== 'spell' && meta.kind !== 'object')) {
        return {};
    }
    return meta.containerStyle && Object.keys(meta.containerStyle).length ? meta.containerStyle : {};
}

/**
 * @param {object} st - entrée spell_states
 * @returns {string|null}
 */
function spellStateIconSource(st) {
    if (!st?.icon || typeof st.icon !== 'string' || st.icon.trim() === '') {
        return null;
    }
    const t = st.icon.trim();
    if (t.startsWith('http://') || t.startsWith('https://') || t.startsWith('/') || t.includes('icons/')) {
        return t;
    }
    return `icons/caracteristics/${t}`;
}

/**
 * @param {object} row
 * @returns {Array<object>}
 */
function filteredSpellStatesForRow(row) {
    const list = props.options.spell_states ?? [];
    const q = String(row._editor_spell_state_q ?? '')
        .trim()
        .toLowerCase();
    let out = list;
    if (q !== '') {
        out = list.filter((st) => {
            const name = String(st.name ?? '').toLowerCase();
            const idStr = String(st.id ?? '');
            const dof = String(st.dofusdb_id ?? '');
            return name.includes(q) || idStr === q || dof === q;
        });
    }
    return out.slice(0, 100);
}

/**
 * @param {object} row
 * @param {object} st
 */
function selectSpellState(row, st) {
    row.params.spell_state_id = st.id;
    row.params.state_dofusdb_id = st.dofusdb_id;
    row.params.state_name = st.name ?? '';
}

/**
 * @param {object} params
 */
function resolveSpellStateIdFromLegacy(params) {
    if (params == null || typeof params !== 'object') {
        return;
    }
    if (params.spell_state_id != null && params.spell_state_id !== '') {
        return;
    }
    const dof = params.state_dofusdb_id;
    if (dof == null || dof === '') {
        return;
    }
    const match = (props.options.spell_states ?? []).find((st) => Number(st.dofusdb_id) === Number(dof));
    if (match) {
        params.spell_state_id = match.id;
    }
}

/** Sous-effet « frapper » (id ou slug), même si param_schema en base n’est pas encore à jour. */
function isFrapperSubEffectRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) {
        return false;
    }
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.slug === 'frapper' || sub?.type_slug === 'frapper';
}

function rowHasLifeStealFormulaParam(row) {
    const schema = getParamSchemaForRow(row);
    if (schema?.params?.some((p) => p.key === 'life_steal_formula')) {
        return true;
    }
    return isFrapperSubEffectRow(row);
}

function characteristicLabelForRow(row) {
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
}

/** Clés primaires Dofus (config) → id 0–4 pour icônes Éléments. */
function elementKeyToPrimaryId(key) {
    const m = { neutral: 0, earth: 1, fire: 2, air: 3, water: 4 };
    return m[key] ?? 0;
}

/**
 * Texte d’aide sous le champ valeur selon l’action.
 *
 * @param {object} row
 * @returns {string}
 */
function valueHelpTextForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    const slug = sub?.slug ?? '';
    if (slug === 'frapper') {
        return 'Dégâts primaires (hors résistances) : ndX, plages [min-max], [level], caractéristiques entre crochets, floor(), etc.';
    }
    if (slug === 'soigner') {
        return 'Montant de soin : formule numérique ou dés (ndX).';
    }
    if (slug === 'protéger') {
        return 'Montant de bouclier / protection absorbée (sans élément), comme un soin.';
    }
    if (slug === 'booster' || slug === 'retirer' || slug === 'voler-caracteristiques') {
        return 'Valeur appliquée à la caractéristique choisie (bonus ou malus selon l’action).';
    }
    if (slug === 'autre') {
        return 'Texte ou formule libre (aperçu / import DofusDB).';
    }
    return 'Formule : ndX, [min-max], [level], [agi], floor(), etc.';
}

/** @param {object} row */
function durationHelpTextForRow(row) {
    const sub = props.options.sub_effects?.find((s) => s.id === row.sub_effect_id);
    const slug = sub?.slug ?? '';
    if (slug === 'appliquer-etat' || slug === 's-appliquer-etat') {
        return 'Durée de l’état en tours (combat) ou secondes (hors combat), selon le contexte.';
    }
    return 'Durée d’effet : nombre de tours en combat, ou secondes hors combat — selon le contexte de résolution.';
}

const TARGET_TYPE_OPTIONS = [
    { value: 'direct', label: 'Direct' },
    { value: 'trap', label: 'Piège' },
    { value: 'glyph', label: 'Glyphe' },
];

function mapSubEffectsFromApi(subEffects) {
    return (subEffects || []).map((s, idx) => {
        const rawOp = s.logic_operator ?? '';
        const logic_operator =
            idx > 0 && (rawOp === '' || rawOp == null) ? 'AND' : rawOp;

        return {
            sub_effect_id: s.id,
            order: s.order ?? 0,
            scope: s.scope ?? 'general',
            value_min: s.value_min ?? '',
            value_max: s.value_max ?? '',
            dice_num: s.dice_num ?? '',
            dice_side: s.dice_side ?? '',
            duration_formula: s.duration_formula ?? '',
            logic_group: s.logic_group ?? '',
            logic_operator,
            logic_condition: s.logic_condition ?? '',
            crit_only: s.crit_only ?? false,
            _editor_spell_state_q: '',
            params: (() => {
                const merged = {
                    ...defaultParamsForSubEffect(),
                    ...(s.params && typeof s.params === 'object' ? s.params : {}),
                };
                resolveSpellStateIdFromLegacy(merged);
                if (merged.dispellable == null) {
                    merged.dispellable = false;
                }
                if (merged.teleport == null) {
                    merged.teleport = false;
                }
                normalizeLegacyCharacteristicKeyForSubEffect(s.id, merged);
                return merged;
            })(),
        };
    });
}

function initDegreeFormsFromProps() {
    const list = props.groupEffects;
    if (!list?.length) return;
    const first = list[0];
    common.name = first.name ?? '';
    common.description = first.description ?? '';
    common.target_type = first.target_type ?? 'direct';
    degreeForms.value = list.map((ge) => ({
        id: ge.id,
        degree: ge.degree,
        slug: ge.slug ?? '',
        area: ge.area ?? '',
        required_creature_level:
            ge.required_creature_level === null || ge.required_creature_level === undefined
                ? ''
                : String(ge.required_creature_level),
        effect_sub_effects: mapSubEffectsFromApi(ge.sub_effects || []),
    }));
    const idx = list.findIndex((e) => e.id === props.selectedEffectId);
    activeTab.value = idx >= 0 ? idx : 0;
}

watch(
    () => [props.groupEffects, props.selectedEffectId],
    () => initDegreeFormsFromProps(),
    { immediate: true, deep: true }
);

function addSubEffect() {
    const first = props.options.sub_effects?.[0];
    if (!first) return;
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    const isLinked = rows.length > 0;
    rows.push({
        sub_effect_id: first.id,
        order: rows.length,
        scope: 'general',
        value_min: '',
        value_max: '',
        dice_num: '',
        dice_side: '',
        duration_formula: '',
        logic_group: '',
        logic_operator: isLinked ? 'AND' : '',
        logic_condition: '',
        crit_only: false,
        _editor_spell_state_q: '',
        params: defaultParamsForSubEffect(),
    });
}

function onSubEffectChange(row) {
    row.params = { ...defaultParamsForSubEffect() };
    row._editor_spell_state_q = '';
}

function removeSubEffect(index) {
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    rows.splice(index, 1);
    rows.forEach((row, i) => {
        row.order = i;
    });
}

function duplicateSubEffect(index) {
    const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
    if (!rows) return;
    const original = rows[index];
    if (!original) return;
    const clone = JSON.parse(JSON.stringify(original));
    rows.splice(index + 1, 0, clone);
    rows.forEach((row, i) => {
        row.order = i;
    });
}

function payloadRows(rows) {
    return rows.map((row, i) => ({
        sub_effect_id: Number(row.sub_effect_id),
        order: i,
        scope: row.scope || 'general',
        value_min: row.value_min !== '' && row.value_min != null ? Number(row.value_min) : null,
        value_max: row.value_max !== '' && row.value_max != null ? Number(row.value_max) : null,
        dice_num: row.dice_num !== '' && row.dice_num != null ? Number(row.dice_num) : null,
        dice_side: row.dice_side !== '' && row.dice_side != null ? Number(row.dice_side) : null,
        duration_formula: row.duration_formula || null,
        logic_group: row.logic_group || null,
        logic_operator:
            i > 0 ? row.logic_operator || 'AND' : row.logic_operator || null,
        logic_condition: row.logic_condition || null,
        crit_only: Boolean(row.crit_only),
        params: row.params && typeof row.params === 'object' ? row.params : null,
    }));
}

function normalizeCreatureLevel(raw) {
    if (raw === '' || raw == null) {
        return null;
    }
    const n = Number(raw);
    return Number.isFinite(n) ? n : null;
}

function submitGroup() {
    for (let i = 0; i < degreeForms.value.length; i += 1) {
        const raw = degreeForms.value[i]?.area;
        if (raw != null && String(raw).trim() !== '' && !isValidAreaNotation(raw)) {
            activeTab.value = i;
            return;
        }
    }

    groupSaveForm.common = {
        name: common.name || null,
        description: common.description || null,
        target_type: common.target_type || 'direct',
    };
    groupSaveForm.degrees = degreeForms.value.map((d) => ({
        id: d.id,
        slug: d.slug || null,
        area: d.area || null,
        required_creature_level: normalizeCreatureLevel(d.required_creature_level),
        effect_sub_effects: payloadRows(d.effect_sub_effects),
    }));

    groupSaveForm.patch(props.patchUrl, {
        preserveScroll: true,
    });
}

function getActiveEffectId() {
    return degreeForms.value[activeTab.value]?.id ?? null;
}

const saving = computed(() => groupSaveForm.processing);

/** Validation du champ zone pour l’onglet degré actif (affiche une erreur si rempli mais invalide). */
const activeAreaValidation = computed(() => {
    const raw = degreeForms.value[activeTab.value]?.area;
    if (raw == null || String(raw).trim() === '') return undefined;
    return isValidAreaNotation(raw)
        ? undefined
        : { state: 'error', message: `Notation invalide. ${AREA_NOTATION_HELP}` };
});

defineExpose({ getActiveEffectId, degreeForms, activeTab, saving });
</script>

<template>
    <div class="space-y-6">
        <h3 v-if="heading" class="text-lg font-semibold">{{ heading }}</h3>
        <form class="space-y-6" @submit.prevent="submitGroup">
            <div class="card bg-base-100 shadow border border-base-300">
                <div class="card-body">
                    <h2 class="card-title text-lg">Données communes au groupe</h2>
                    <p class="text-sm text-base-content/70 mb-2">
                        Appliquées à chaque degré : nom, description, type de cible. La zone et le seuil de niveau créature se règlent dans chaque onglet de degré.
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <InputField v-model="common.name" label="Nom" name="common_name" />
                        <div class="sm:col-span-2">
                            <InputField v-model="common.description" label="Description (aperçu)" name="common_description" type="textarea" />
                        </div>
                        <SelectFieldNative
                            v-model="common.target_type"
                            label="Type de cible"
                            name="common_target_type"
                            :options="TARGET_TYPE_OPTIONS"
                            helper="Direct, piège ou glyphe."
                        />
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow border border-base-300">
                <div class="card-body space-y-4">
                    <h2 class="card-title text-lg">Degrés</h2>
                    <div role="tablist" class="tabs tabs-boxed flex-wrap gap-1">
                        <button
                            v-for="(tab, ti) in degreeForms"
                            :key="tab.id"
                            type="button"
                            role="tab"
                            class="tab"
                            :class="{ 'tab-active': activeTab === ti }"
                            @click="activeTab = ti"
                        >
                            Degré {{ tab.degree ?? '?' }}
                        </button>
                    </div>

                    <div v-if="degreeForms[activeTab]" class="space-y-4 border-t border-base-300 pt-4">
                        <div class="grid gap-4 sm:grid-cols-2 max-w-3xl">
                            <InputField
                                v-model="degreeForms[activeTab].slug"
                                label="Slug (degré)"
                                :name="'slug_d' + degreeForms[activeTab].degree"
                                helper="Optionnel, unique."
                            />
                            <InputField
                                v-model="degreeForms[activeTab].required_creature_level"
                                label="Niveau créature min."
                                :name="'lvl_d' + degreeForms[activeTab].degree"
                                type="number"
                                helper="Vide = toujours actif pour le porteur."
                            />
                        </div>
                        <div class="space-y-2 max-w-3xl">
                            <div class="flex items-end gap-2 max-w-xl">
                                <InputField
                                    v-model="degreeForms[activeTab].area"
                                    label="Zone (ce degré)"
                                    :name="'area_d' + degreeForms[activeTab].degree"
                                    class="flex-1"
                                    :validation="activeAreaValidation"
                                />
                                <AreaDisplay
                                    v-if="degreeForms[activeTab].area?.trim()"
                                    :area="degreeForms[activeTab].area"
                                    icon-size="sm"
                                    class="shrink-0 mb-1"
                                />
                            </div>
                            <p class="text-xs text-base-content/70 leading-relaxed">
                                <strong>Notation</strong> <code class="text-[0.7rem]">forme[-paramètres]</code> :
                                <code class="text-[0.7rem]">point</code> ;
                                <code class="text-[0.7rem]">line-1xL</code> ;
                                <code class="text-[0.7rem]">cross-a-b</code> / <code class="text-[0.7rem]">circle-a-b</code> (a≤b) ;
                                <code class="text-[0.7rem]">rect-WxH</code> ;
                                forme DofusDB non mappée : <code class="text-[0.7rem]">shape-ID</code> ou
                                <code class="text-[0.7rem]">shape-ID-p1-p2</code>.
                            </p>
                        </div>
                        <div
                            v-if="hasDegreeExtraSlot && degreeForms[activeTab]?.id"
                            class="rounded-box border border-primary/20 bg-primary/5 p-4"
                        >
                            <slot
                                name="degree-extra"
                                :effect-id="degreeForms[activeTab].id"
                                :degree="degreeForms[activeTab].degree"
                                :tab-index="activeTab"
                            />
                        </div>
                        <div
                            class="flex flex-wrap items-start justify-between gap-3 border-t border-base-300/80 pt-4"
                        >
                            <div>
                                <h3 class="font-semibold text-base">
                                    Sous-effets (degré {{ degreeForms[activeTab].degree ?? '?' }})
                                </h3>
                                <p class="text-xs text-base-content/60 mt-0.5 max-w-xl">
                                    Enchaînement : entre deux blocs, définissez le lien avec le précédent (ET = le précédent
                                    doit s’appliquer ; OU = ce bloc s’applique si la condition &gt; 0).
                                </p>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary shrink-0" @click="addSubEffect">
                                + Ajouter un sous-effet
                            </button>
                        </div>
                        <div v-if="!degreeForms[activeTab].effect_sub_effects.length" class="text-sm text-base-content/70 py-4">
                            Aucun sous-effet. Utilisez « Ajouter un sous-effet ».
                        </div>
                        <div v-else class="space-y-3">
                            <template
                                v-for="(row, index) in degreeForms[activeTab].effect_sub_effects"
                                :key="'d' + activeTab + '-r' + index"
                            >
                                <!-- Lien logique avec le bloc précédent (hors carte) -->
                                <div
                                    v-if="index > 0"
                                    class="rounded-lg border border-dashed border-primary/25 bg-base-200/50 px-3 py-2"
                                >
                                    <div class="text-xs font-semibold uppercase tracking-wide text-primary/80">
                                        Lien avec le sous-effet précédent
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-end gap-2">
                                        <div class="min-w-[11rem]">
                                            <label class="label text-xs py-0">Enchaînement</label>
                                            <select
                                                v-model="row.logic_operator"
                                                class="select select-bordered select-sm w-full"
                                            >
                                                <option value="AND">ET — le précédent doit s’appliquer</option>
                                                <option value="OR">OU — si la condition &gt; 0</option>
                                            </select>
                                        </div>
                                        <div v-if="row.logic_operator === 'OR'" class="flex-1 min-w-[12rem] max-w-lg">
                                            <label class="label text-xs py-0">Condition (formule &gt; 0)</label>
                                            <input
                                                v-model="row.logic_condition"
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="ex: [target_is_ally]"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="rounded-box border border-base-300 bg-base-100 overflow-hidden shadow-sm"
                                >
                                    <!-- Barre : action, contexte, critique seulement, actions -->
                                    <div
                                        class="flex flex-wrap items-end gap-2 gap-y-2 px-3 py-2 border-b border-base-300 bg-base-200/40"
                                    >
                                        <div class="min-w-[10rem] flex-1">
                                            <label class="text-xs font-medium text-base-content/70">Action</label>
                                            <select
                                                v-model="row.sub_effect_id"
                                                class="select select-bordered select-sm w-full mt-0.5"
                                                required
                                                @change="onSubEffectChange(row)"
                                            >
                                                <option value="">— Choisir —</option>
                                                <option
                                                    v-for="s in options.sub_effects"
                                                    :key="s.id"
                                                    :value="s.id"
                                                >
                                                    {{ s.slug }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="w-[7.25rem]">
                                            <label class="text-xs font-medium text-base-content/70">Contexte</label>
                                            <select
                                                v-model="row.scope"
                                                class="select select-bordered select-sm w-full mt-0.5"
                                            >
                                                <option
                                                    v-for="sc in options.scopes"
                                                    :key="sc.value"
                                                    :value="sc.value"
                                                >
                                                    {{ sc.label }}
                                                </option>
                                            </select>
                                        </div>
                                        <label
                                            class="flex items-center gap-2 cursor-pointer shrink-0 max-w-[14rem] mb-0.5"
                                        >
                                            <input v-model="row.crit_only" type="checkbox" class="checkbox checkbox-sm" />
                                            <span class="text-xs leading-snug">Uniquement si critique</span>
                                        </label>
                                        <div class="flex gap-0.5 ml-auto">
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-sm btn-square"
                                                title="Dupliquer ce sous-effet"
                                                @click="duplicateSubEffect(index)"
                                            >
                                                +
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-sm btn-square text-error"
                                                title="Supprimer ce sous-effet"
                                                @click="removeSubEffect(index)"
                                            >
                                                ×
                                            </button>
                                        </div>
                                    </div>

                                    <div v-if="row.sub_effect_id" class="p-3 space-y-4">
                                        <!-- État (référentiel local) -->
                                        <div v-if="rowHasSpellStateParam(row)" class="space-y-2 max-w-xl">
                                            <label class="text-xs font-medium text-base-content/80">État</label>
                                            <input
                                                v-model="row._editor_spell_state_q"
                                                type="search"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="Rechercher par nom ou n°…"
                                                autocomplete="off"
                                            />
                                            <ul
                                                class="menu menu-xs bg-base-200 rounded-box max-h-48 overflow-y-auto border border-base-300 p-1"
                                                role="listbox"
                                            >
                                                <li v-for="st in filteredSpellStatesForRow(row)" :key="st.id">
                                                    <button
                                                        type="button"
                                                        class="flex items-center gap-2 text-left rounded-btn"
                                                        :class="
                                                            Number(row.params.spell_state_id) === Number(st.id)
                                                                ? 'bg-primary text-primary-content'
                                                                : ''
                                                        "
                                                        @click="selectSpellState(row, st)"
                                                    >
                                                        <Icon
                                                            v-if="spellStateIconSource(st)"
                                                            :source="spellStateIconSource(st)"
                                                            :alt="st.name || ''"
                                                            size="xs"
                                                        />
                                                        <span class="truncate">{{ st.name }} ({{ st.dofusdb_id }})</span>
                                                    </button>
                                                </li>
                                            </ul>
                                            <label
                                                v-if="rowHasSpellStateParam(row)"
                                                class="flex items-center gap-2 cursor-pointer"
                                            >
                                                <input
                                                    v-model="row.params.dispellable"
                                                    type="checkbox"
                                                    class="checkbox checkbox-sm"
                                                />
                                                <span class="text-xs">Dissipable</span>
                                            </label>
                                        </div>

                                        <!-- Déplacement : cases + téléportation -->
                                        <div v-if="rowHasCellsFormulaParam(row)" class="space-y-2 max-w-xl">
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80"
                                                    >Nombre de cases (formule)</label
                                                >
                                                <input
                                                    v-model="row.params.cells_formula"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: 3, [level], 1d3+1…"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    1 case = 1,5 m. La formule est évaluée en nombre de cases (entier attendu
                                                    selon le contexte).
                                                </p>
                                            </div>
                                            <label v-if="rowHasTeleportParam(row)" class="flex items-center gap-2 cursor-pointer">
                                                <input
                                                    v-model="row.params.teleport"
                                                    type="checkbox"
                                                    class="checkbox checkbox-sm"
                                                />
                                                <span class="text-xs">Téléportation (sinon déplacement simple)</span>
                                            </label>
                                        </div>

                                        <!-- Caractéristique / élément (au-dessus de la valeur) -->
                                        <template v-if="rowHasCharacteristicParam(row)">
                                            <div class="space-y-1.5">
                                                <label class="text-xs font-medium text-base-content/80">{{
                                                    characteristicLabelForRow(row)
                                                }}</label>
                                                <div
                                                    class="flex flex-wrap gap-1.5"
                                                    role="group"
                                                    :aria-label="characteristicLabelForRow(row)"
                                                >
                                                    <button
                                                        v-for="c in characteristicsForRow(row)"
                                                        :key="c.key"
                                                        type="button"
                                                        :class="characteristicBadgeButtonClass(row, c)"
                                                        :style="characteristicBadgeButtonStyle(row, c)"
                                                        :title="c.helper || ''"
                                                        @click="row.params.characteristic = c.key"
                                                    >
                                                        <Icon
                                                            v-if="characteristicBadgeMeta(c).kind === 'element'"
                                                            :source="getElementIcon(characteristicBadgeMeta(c).primaryId)"
                                                            :alt="c.label"
                                                            size="xs"
                                                        />
                                                        <Icon
                                                            v-else-if="characteristicBadgeMeta(c).icon"
                                                            :source="characteristicBadgeMeta(c).icon"
                                                            :alt="c.label"
                                                            size="xs"
                                                            :style="characteristicBadgeMeta(c).colorStyle"
                                                        />
                                                        <span :style="characteristicBadgeMeta(c).colorStyle">{{
                                                            c.label
                                                        }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Valeur + critique -->
                                        <div
                                            v-if="rowHasValueParam(row)"
                                            class="grid gap-3 sm:grid-cols-2"
                                        >
                                            <div class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80">Valeur (formule)</label>
                                                <input
                                                    v-model="row.params.value_formula"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: 2d6, [1-4], [level]*2+[agi]"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    {{ valueHelpTextForRow(row) }}
                                                </p>
                                            </div>
                                            <div v-if="!row.crit_only" class="space-y-1">
                                                <label class="text-xs font-medium text-base-content/80"
                                                    >Valeur critique (optionnel)</label
                                                >
                                                <input
                                                    v-model="row.params.value_formula_crit"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: [value]*2, 3d6…"
                                                />
                                                <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                    Remplace la valeur ci-contre lors d’un coup critique (si vide : même
                                                    valeur que la colonne de gauche).
                                                </p>
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <label class="text-xs font-medium text-base-content/80"
                                                >Durée (formule, tours ou secondes)</label
                                            >
                                            <input
                                                v-model="row.duration_formula"
                                                type="text"
                                                class="input input-bordered input-sm w-full max-w-xl"
                                                placeholder="ex: 2 (tours), [level]/2, 10 (secondes)…"
                                            />
                                            <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                {{ durationHelpTextForRow(row) }}
                                            </p>
                                        </div>

                                        <div v-if="rowHasLifeStealFormulaParam(row)" class="space-y-1 max-w-xl">
                                            <label class="text-xs font-medium text-base-content/80"
                                                >PV volés (formule, optionnel)</label
                                            >
                                            <input
                                                v-model="row.params.life_steal_formula"
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="ex: [dgt]/2, 50%, [dgt]+2d4"
                                            />
                                            <p class="text-[0.7rem] leading-snug text-base-content/55">
                                                Vide = dégâts seuls. Avec formule : vol de vie — [dgt] = dégâts primaires ;
                                                « 50% » = moitié des dégâts rendus en PV au lanceur.
                                            </p>
                                        </div>

                                        <template v-if="rowHasMonsterParam(row)">
                                            <div class="space-y-1 max-w-md">
                                                <label class="text-xs font-medium text-base-content/80">Monstre</label>
                                                <EntityPickerCore
                                                    v-model="row.params.monster_id"
                                                    entity-type="monsters"
                                                    :multiple="false"
                                                    variant="compact"
                                                    placeholder="Choisir un monstre…"
                                                    size="sm"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 items-center">
                <button type="submit" class="btn btn-primary" :disabled="groupSaveForm.processing">
                    {{ groupSaveForm.processing ? 'Enregistrement…' : submitLabel }}
                </button>
                <p v-if="groupSaveForm.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
            </div>
        </form>
    </div>
</template>
