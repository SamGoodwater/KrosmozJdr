<script setup>
/**
 * Admin Effects — Données communes (nom, groupe, cible, description) puis onglets par degré
 * (slug, zone, sous-effets). « Ajouter un degré » duplique l’effet courant côté serveur.
 */
import { computed, reactive, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import SidebarNav from '@/Pages/Organismes/layout/SidebarNav.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { getAreaIcon } from '@/Utils/Entity/Areas';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    effects: { type: Array, required: true },
    groups: { type: Array, default: () => [] },
    selected: { type: [Object, String], default: null },
    /** Effets du même groupe (même ordre que les degrés), pour l’édition groupée */
    groupEffects: { type: Array, default: null },
    options: {
        type: Object,
        default: () => ({ effect_groups: [], sub_effects: [], scopes: [], characteristics: [], monsters: [] }),
    },
});

defineOptions({ layout: Main });
setPageTitle('Effets');

const isGroupEdit = computed(
    () => Boolean(props.selected && typeof props.selected === 'object' && props.selected.id && props.groupEffects?.length)
);

const common = reactive({
    name: '',
    description: '',
    effect_group_id: '',
    target_type: 'direct',
});

const degreeForms = ref([]);
const activeTab = ref(0);

const groupSaveForm = useForm({
    common: {},
    degrees: [],
});

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
    common.effect_group_id = first.effect_group_id ?? '';
    common.target_type = first.target_type ?? 'direct';
    degreeForms.value = list.map((ge) => ({
        id: ge.id,
        degree: ge.degree,
        slug: ge.slug ?? '',
        area: ge.area ?? '',
        effect_sub_effects: mapSubEffectsFromApi(ge.sub_effects || []),
    }));
    const idx = list.findIndex((e) => e.id === props.selected?.id);
    activeTab.value = idx >= 0 ? idx : 0;
}

function buildFormData(selected) {
    if (!selected || selected === 'new') {
        return {
            name: '',
            slug: '',
            description: '',
            effect_group_id: '',
            degree: '',
            target_type: 'direct',
            area: '',
            effect_sub_effects: [],
        };
    }
    return {
        name: selected.name ?? '',
        slug: selected.slug ?? '',
        description: selected.description ?? '',
        effect_group_id: selected.effect_group_id ?? '',
        degree: selected.degree ?? '',
        target_type: selected.target_type ?? 'direct',
        area: selected.area ?? '',
        effect_sub_effects: mapSubEffectsFromApi(selected.sub_effects || []),
    };
}

const form = useForm(buildFormData(props.selected));
const duplicateForm = useForm({});

watch(
    () => [props.selected, props.groupEffects],
    () => {
        if (isGroupEdit.value) {
            initDegreeFormsFromProps();
            return;
        }
        const s = props.selected;
        const data = buildFormData(s);
        form.name = data.name;
        form.slug = data.slug;
        form.description = data.description;
        form.effect_group_id = data.effect_group_id;
        form.degree = data.degree;
        form.target_type = data.target_type;
        form.area = data.area;
        form.effect_sub_effects = data.effect_sub_effects;
    },
    { immediate: true }
);

function addSubEffect() {
    const first = props.options.sub_effects?.[0];
    if (!first) return;
    if (isGroupEdit.value) {
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
        return;
    }
    form.effect_sub_effects.push({
        sub_effect_id: first.id,
        order: form.effect_sub_effects.length,
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
    if (isGroupEdit.value) {
        const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
        if (!rows) return;
        rows.splice(index, 1);
        rows.forEach((row, i) => {
            row.order = i;
        });
        return;
    }
    form.effect_sub_effects.splice(index, 1);
    form.effect_sub_effects.forEach((row, i) => {
        row.order = i;
    });
}

function duplicateSubEffect(index) {
    if (isGroupEdit.value) {
        const rows = degreeForms.value[activeTab.value]?.effect_sub_effects;
        if (!rows) return;
        const original = rows[index];
        if (!original) return;
        const clone = JSON.parse(JSON.stringify(original));
        rows.splice(index + 1, 0, clone);
        rows.forEach((row, i) => {
            row.order = i;
        });
        return;
    }
    const original = form.effect_sub_effects[index];
    if (!original) return;
    const clone = JSON.parse(JSON.stringify(original));
    form.effect_sub_effects.splice(index + 1, 0, clone);
    form.effect_sub_effects.forEach((row, i) => {
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

function submitGroup() {
    if (!props.selected?.id) return;
    groupSaveForm.common = {
        name: common.name || null,
        description: common.description || null,
        effect_group_id: common.effect_group_id ? Number(common.effect_group_id) : null,
        target_type: common.target_type || 'direct',
    };
    groupSaveForm.degrees = degreeForms.value.map((d) => ({
        id: d.id,
        slug: d.slug || null,
        area: d.area || null,
        effect_sub_effects: payloadRows(d.effect_sub_effects),
    }));
    groupSaveForm.patch(route('admin.effects.group-update', props.selected.id), {
        preserveScroll: true,
    });
}

function submit() {
    if (props.selected === 'new') {
        const payload = {
            name: form.name || null,
            slug: form.slug || null,
            description: form.description || null,
            effect_group_id: form.effect_group_id ? Number(form.effect_group_id) : null,
            degree: form.degree !== '' && form.degree != null ? Number(form.degree) : null,
            target_type: form.target_type || 'direct',
            area: form.area || null,
        };
        form.transform(() => payload).post(route('admin.effects.store'));
    }
}

function duplicateDegree() {
    const effectId = isGroupEdit.value ? degreeForms.value[activeTab.value]?.id : props.selected?.id;
    if (!effectId) return;
    duplicateForm.post(route('admin.effects.duplicate-degree', effectId));
}

function destroy() {
    if (!props.selected?.id) return;
    if (confirm('Supprimer cet effet ?')) {
        form.delete(route('admin.effects.destroy', props.selected.id));
    }
}

function duplicateEffect() {
    if (!props.selected?.id) return;
    duplicateForm.post(route('admin.effects.duplicate', props.selected.id));
}
</script>

<template>
    <Head title="Effets" />
    <div class="flex h-full min-h-0 w-full">
        <SidebarNav
            title="Effets"
            description="Conteneurs de sous-effets. Les entrées listent les groupes d'effets (degrés)."
            :items="groups"
            :get-item-href="(g) => route('admin.effects.show', g.effects[0].id)"
            :is-item-active="(g) => selected && g.effects.some((e) => e.id === selected.id)"
            :get-item-label="(g) => g.label"
            :get-item-label-secondary="(g) => (g.effects.length > 1 ? `${g.effects.length} degrés` : (g.effects[0]?.degree != null ? `d${g.effects[0].degree}` : null))"
            :get-item-key="(g) => (g.id ? 'group-' + g.id : 'single-' + g.effects[0]?.id)"
            searchable
            search-placeholder="Filtrer par nom…"
            :search-keys="['label']"
        >
            <template #nav-before>
                <Link
                    :href="route('admin.effects.create')"
                    :class="[
                        'sidebar-nav-item flex items-center gap-2 rounded-box border-l-4 border-transparent px-3 py-2 text-left text-sm font-medium transition-colors',
                        selected === 'new' && 'sidebar-nav-item-active'
                    ]"
                >
                    + Nouvel effet
                </Link>
            </template>
        </SidebarNav>

        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected === 'new' ? 'Nouvel effet' : (selected.name || selected.slug || 'Effet') }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    <template v-if="isGroupEdit">
                        Champs communs à tous les degrés du groupe, puis un onglet par degré (slug, <strong>zone</strong> et sous-effets peuvent différer par degré).
                        Le <strong>niveau de créature requis</strong> pour lier un degré à un sort se règle dans la fiche entité (onglets « usages »), pas ici.
                    </template>
                    <template v-else>
                        Nom, description optionnelle, groupe et degré. Liste des sous-effets avec ordre, contexte (Général / Combat / Hors combat) et paramètres.
                    </template>
                </p>

                <!-- ——— Édition groupe (effet existant avec groupEffects) ——— -->
                <form v-if="isGroupEdit" class="space-y-6" @submit.prevent="submitGroup">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Données communes au groupe</h2>
                            <p class="text-sm text-base-content/70 mb-2">
                                Appliquées à chaque degré : nom, description, groupe, type de cible. La zone se règle dans chaque onglet de degré.
                            </p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="common.name" label="Nom" name="common_name" />
                                <div class="sm:col-span-2">
                                    <InputField v-model="common.description" label="Description (aperçu)" name="common_description" type="textarea" />
                                </div>
                                <SelectFieldNative
                                    v-model="common.effect_group_id"
                                    label="Groupe d'effets"
                                    name="common_effect_group_id"
                                    :options="[{ value: '', label: '— Aucun —' }, ...(options.effect_groups || [])]"
                                />
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

                    <div class="card bg-base-100 shadow">
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
                                <InputField
                                    v-model="degreeForms[activeTab].slug"
                                    label="Slug (ce degré)"
                                    :name="'slug_d' + degreeForms[activeTab].degree"
                                    helper="Unique dans la base. Souvent un suffixe -d1, -d2…"
                                />
                                <div class="flex items-end gap-2 max-w-xl">
                                    <InputField
                                        v-model="degreeForms[activeTab].area"
                                        label="Zone (ce degré)"
                                        :name="'area_d' + degreeForms[activeTab].degree"
                                        helper="ex: point, line-1x9, circle-0-2 — peut varier entre degrés."
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
                            {{ groupSaveForm.processing ? 'Enregistrement…' : 'Enregistrer le groupe' }}
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing"
                            @click="duplicateDegree"
                        >
                            Ajouter un degré
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing"
                            @click="duplicateEffect"
                        >
                            Dupliquer l'effet
                        </button>
                        <button
                            type="button"
                            class="btn btn-ghost btn-error"
                            :disabled="groupSaveForm.processing"
                            @click="destroy"
                        >
                            Supprimer
                        </button>
                        <p v-if="groupSaveForm.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
                    </div>
                </form>

                <!-- ——— Création (nouvel effet) ——— -->
                <form v-else-if="selected === 'new'" class="space-y-6" @submit.prevent="submit">
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" />
                                <InputField v-model="form.slug" label="Slug" name="slug" helper="Optionnel, unique." />
                                <div class="sm:col-span-2">
                                    <InputField v-model="form.description" label="Description (aperçu)" name="description" type="textarea" />
                                </div>
                                <SelectFieldNative
                                    v-model="form.effect_group_id"
                                    label="Groupe d'effets"
                                    name="effect_group_id"
                                    :options="[{ value: '', label: '— Aucun —' }, ...(options.effect_groups || [])]"
                                />
                                <InputField v-model="form.degree" label="Degré" name="degree" type="number" helper="1, 2, 3… pour sorts." />
                                <SelectFieldNative
                                    v-model="form.target_type"
                                    label="Type de cible"
                                    name="target_type"
                                    :options="TARGET_TYPE_OPTIONS"
                                    helper="Direct, piège ou glyphe."
                                />
                                <div class="flex items-end gap-2">
                                    <InputField v-model="form.area" label="Zone" name="area" helper="ex: point, line-1x9, circle-0-2." class="flex-1" />
                                    <Icon
                                        v-if="form.area?.trim()"
                                        :source="getAreaIcon(form.area)"
                                        :alt="form.area"
                                        size="sm"
                                        class="shrink-0 mb-1 opacity-70"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <div class="flex items-center justify-between">
                                <h2 class="card-title text-lg">Sous-effets</h2>
                                <button type="button" class="btn btn-sm btn-outline" @click="addSubEffect">
                                    Ajouter un sous-effet
                                </button>
                            </div>
                            <div v-if="form.effect_sub_effects.length === 0" class="text-sm text-base-content/70 py-4">
                                Aucun sous-effet. Cliquez sur « Ajouter un sous-effet ».
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="(row, index) in form.effect_sub_effects"
                                    :key="index"
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

                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Enregistrement…' : 'Créer' }}
                        </button>
                        <p v-if="form.recentlySuccessful" class="text-sm text-success">Enregistré.</p>
                    </div>
                </form>
            </template>
            <template v-else>
                <p class="text-base-content/70">
                    Sélectionnez un effet ou créez-en un nouveau.
                </p>
            </template>
        </main>
    </div>
</template>
