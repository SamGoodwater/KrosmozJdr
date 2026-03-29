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
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getAreaIcon } from '@/Utils/Entity/Areas';

const props = defineProps({
    options: {
        type: Object,
        default: () => ({ effect_groups: [], sub_effects: [], scopes: [], characteristics: [], monsters: [] }),
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
        monster_id: '',
    };
}

function getParamSchemaForRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) return null;
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.param_schema ?? null;
}

function characteristicsForRow(row) {
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

function characteristicLabelForRow(row) {
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
}

const TARGET_TYPE_OPTIONS = [
    { value: 'direct', label: 'Direct' },
    { value: 'trap', label: 'Piège' },
    { value: 'glyph', label: 'Glyphe' },
];

function mapSubEffectsFromApi(subEffects) {
    return (subEffects || []).map((s) => ({
        sub_effect_id: s.id,
        order: s.order ?? 0,
        scope: s.scope ?? 'general',
        value_min: s.value_min ?? '',
        value_max: s.value_max ?? '',
        dice_num: s.dice_num ?? '',
        dice_side: s.dice_side ?? '',
        duration_formula: s.duration_formula ?? '',
        logic_group: s.logic_group ?? '',
        logic_operator: s.logic_operator ?? '',
        logic_condition: s.logic_condition ?? '',
        crit_only: s.crit_only ?? false,
        params: {
            ...defaultParamsForSubEffect(),
            ...(s.params && typeof s.params === 'object' ? s.params : {}),
        },
    }));
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
    rows.push({
        sub_effect_id: first.id,
        order: rows.length,
        scope: 'general',
        value_min: '',
        value_max: '',
        dice_num: '',
        dice_side: '',
        crit_only: false,
        params: defaultParamsForSubEffect(),
    });
}

function onSubEffectChange(row) {
    row.params = {
        ...defaultParamsForSubEffect(),
        ...(row.params || {}),
    };
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
        logic_operator: row.logic_operator || null,
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
                                />
                                <Icon
                                    v-if="degreeForms[activeTab].area?.trim()"
                                    :source="getAreaIcon(degreeForms[activeTab].area)"
                                    :alt="degreeForms[activeTab].area"
                                    size="sm"
                                    class="shrink-0 mb-1 opacity-70"
                                />
                            </div>
                            <p class="text-xs text-base-content/70 leading-relaxed">
                                <strong>Notation</strong> <code class="text-[0.7rem]">forme[-paramètres]</code> :
                                <code class="text-[0.7rem]">point</code> ;
                                <code class="text-[0.7rem]">line-1xL</code> (ligne, L = longueur) ;
                                <code class="text-[0.7rem]">cross-a-b</code> ou <code class="text-[0.7rem]">circle-a-b</code>
                                (deux tailles : min puis max ; ex. <code class="text-[0.7rem]">circle-0-2</code> plein,
                                <code class="text-[0.7rem]">circle-1-2</code> anneau) ;
                                <code class="text-[0.7rem]">rect-LxH</code>.
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
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">Sous-effets (degré {{ degreeForms[activeTab].degree ?? '?' }})</h3>
                            <button type="button" class="btn btn-sm btn-outline" @click="addSubEffect">
                                Ajouter un sous-effet
                            </button>
                        </div>
                        <div v-if="!degreeForms[activeTab].effect_sub_effects.length" class="text-sm text-base-content/70 py-4">
                            Aucun sous-effet. Cliquez sur « Ajouter un sous-effet ».
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="(row, index) in degreeForms[activeTab].effect_sub_effects"
                                :key="'d' + activeTab + '-r' + index"
                                class="rounded-box border border-base-300 bg-base-200/30 p-3 space-y-3"
                            >
                                <div class="flex flex-wrap items-end gap-3">
                                    <div class="min-w-[160px]">
                                        <label class="label text-xs">Action</label>
                                        <select
                                            v-model="row.sub_effect_id"
                                            class="select select-bordered select-sm w-full"
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
                                    <div class="w-28">
                                        <label class="label text-xs">Contexte</label>
                                        <select v-model="row.scope" class="select select-bordered select-sm w-full">
                                            <option
                                                v-for="sc in options.scopes"
                                                :key="sc.value"
                                                :value="sc.value"
                                            >
                                                {{ sc.label }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex gap-1 ml-auto">
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
                                            title="Retirer"
                                            @click="removeSubEffect(index)"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <template v-if="row.sub_effect_id">
                                    <div class="flex flex-wrap items-end gap-3 border-t border-base-300 pt-3">
                                        <template v-if="rowHasCharacteristicParam(row)">
                                            <div class="min-w-[160px]">
                                                <label class="label text-xs">{{ characteristicLabelForRow(row) }}</label>
                                                <select
                                                    v-model="row.params.characteristic"
                                                    class="select select-bordered select-sm w-full"
                                                >
                                                    <option value="">— Choisir —</option>
                                                    <option
                                                        v-for="c in characteristicsForRow(row)"
                                                        :key="c.key"
                                                        :value="c.key"
                                                    >
                                                        {{ c.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </template>
                                        <template v-if="rowHasMonsterParam(row)">
                                            <div class="min-w-[220px]">
                                                <label class="label text-xs">Monstre</label>
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
                                        <template v-if="rowHasValueParam(row)">
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="label text-xs">Valeur (formule)</label>
                                                <input
                                                    v-model="row.params.value_formula"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: 2d6, [1-4], [level]*2+[agi]"
                                                />
                                                <p class="text-xs text-base-content/60 mt-0.5">
                                                    Formule : ndX, [min-max], [level], [agi], floor(), etc.
                                                </p>
                                            </div>
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="label text-xs">Valeur critique (formule, optionnel)</label>
                                                <input
                                                    v-model="row.params.value_formula_crit"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: [value]*2, 3d6…"
                                                />
                                                <p class="text-xs text-base-content/60 mt-0.5">
                                                    Utilisée uniquement en cas de critique.
                                                </p>
                                            </div>
                                        </template>
                                        <div class="flex flex-col gap-1 min-w-[120px]">
                                            <label class="label text-xs">Uniquement en critique</label>
                                            <label class="label cursor-pointer justify-start gap-2">
                                                <input
                                                    v-model="row.crit_only"
                                                    type="checkbox"
                                                    class="checkbox checkbox-sm"
                                                />
                                                <span class="label-text">Ce sous-effet ne s’applique qu’en cas de critique</span>
                                            </label>
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-[220px]">
                                            <label class="label text-xs">Durée (formule, en tours ou secondes)</label>
                                            <input
                                                v-model="row.duration_formula"
                                                type="text"
                                                class="input input-bordered input-sm w-full"
                                                placeholder="ex: 2 (tours), [level]/2, 10 (secondes)…"
                                            />
                                            <p class="text-xs text-base-content/60 mt-0.5">
                                                Formule numérique, interprétée selon le contexte (tours en combat, secondes hors combat).
                                            </p>
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-[220px]">
                                            <label class="label text-xs">Opérateur avec le précédent</label>
                                            <select
                                                v-model="row.logic_operator"
                                                class="select select-bordered select-sm w-full"
                                            >
                                                <option value="">— Aucun (premier) —</option>
                                                <option value="AND">ET</option>
                                                <option value="OR">OU (si condition)</option>
                                            </select>
                                            <div v-if="row.logic_operator === 'OR'" class="mt-1">
                                                <label class="label text-xs">Condition pour le OU</label>
                                                <input
                                                    v-model="row.logic_condition"
                                                    type="text"
                                                    class="input input-bordered input-sm w-full"
                                                    placeholder="ex: [target_is_ally] == 1"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
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
