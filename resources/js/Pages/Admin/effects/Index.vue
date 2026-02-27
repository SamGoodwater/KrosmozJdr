<script setup>
/**
 * Admin Effects — Liste à gauche, panneau groupe + édition d'un degré à droite.
 * Gestion des sous-effets (ordre, scope, paramètres). Bouton « Ajouter un degré ».
 */
import { watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    effects: { type: Array, required: true },
    groups: { type: Array, default: () => [] },
    selected: { type: [Object, String], default: null },
    options: {
        type: Object,
        default: () => ({ effect_groups: [], sub_effects: [], scopes: [], characteristics: [], monsters: [] }),
    },
});

defineOptions({ layout: Main });
setPageTitle('Effets');

function defaultParamsForSubEffect() {
    return {
        characteristic: '',
        value_formula: '',
        value_formula_crit: '',
        monster_id: '',
    };
}

/** Schéma des paramètres du sous-effet sélectionné (pour afficher les bons champs). */
function getParamSchemaForRow(row) {
    if (!row?.sub_effect_id || !props.options?.sub_effects) return null;
    const sub = props.options.sub_effects.find((s) => s.id === row.sub_effect_id);
    return sub?.param_schema ?? null;
}

/** Caractéristiques filtrées selon le param_schema du sous-effet (categories). */
function characteristicsForRow(row) {
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    const categories = param?.categories;
    if (!categories?.length) return props.options.characteristics ?? [];
    return (props.options.characteristics ?? []).filter((c) => categories.includes(c.category));
}

/** Indique si le sous-effet a un paramètre "caractéristique" (élément ou toute caractéristique). */
function rowHasCharacteristicParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'characteristic') ?? false;
}

/** Indique si le sous-effet a un paramètre "valeur" (formule). */
function rowHasValueParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.key === 'value') ?? false;
}

/** Indique si le sous-effet a un paramètre monstre (invoquer). */
function rowHasMonsterParam(row) {
    const schema = getParamSchemaForRow(row);
    return schema?.params?.some((p) => p.type === 'monster') ?? false;
}

/** Label du champ caractéristique (Élément vs Caractéristique). */
function characteristicLabelForRow(row) {
    const schema = getParamSchemaForRow(row);
    const param = schema?.params?.find((p) => p.key === 'characteristic');
    return param?.label ?? 'Caractéristique';
}

function buildFormData(selected) {
    if (!selected || selected === 'new') {
        return {
            name: '',
            slug: '',
            description: '',
            effect_group_id: '',
            degree: '',
            effect_sub_effects: [],
        };
    }
    return {
        name: selected.name ?? '',
        slug: selected.slug ?? '',
        description: selected.description ?? '',
        effect_group_id: selected.effect_group_id ?? '',
        degree: selected.degree ?? '',
        effect_sub_effects: (selected.sub_effects || []).map((s) => ({
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
        })),
    };
}

const form = useForm(buildFormData(props.selected));
const duplicateForm = useForm({});

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s);
        form.name = data.name;
        form.slug = data.slug;
        form.description = data.description;
        form.effect_group_id = data.effect_group_id;
        form.degree = data.degree;
        form.effect_sub_effects = data.effect_sub_effects;
    },
    { immediate: true }
);

function addSubEffect() {
    const first = props.options.sub_effects?.[0];
    if (!first) return;
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
    // Changement d'action : on réinitialise les paramètres à la structure générique.
    row.params = {
        ...defaultParamsForSubEffect(),
        ...(row.params || {}),
    };
}

function removeSubEffect(index) {
    form.effect_sub_effects.splice(index, 1);
    form.effect_sub_effects.forEach((row, i) => {
        row.order = i;
    });
}

function duplicateSubEffect(index) {
    const original = form.effect_sub_effects[index];
    if (!original) return;
    const clone = JSON.parse(JSON.stringify(original));
    // On insère juste après la ligne originale
    form.effect_sub_effects.splice(index + 1, 0, clone);
    form.effect_sub_effects.forEach((row, i) => {
        row.order = i;
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
        };
        form.transform(() => payload).post(route('admin.effects.store'));
        return;
    }
    if (props.selected?.id) {
        const payload = {
            name: form.name || null,
            slug: form.slug || null,
            description: form.description || null,
            effect_group_id: form.effect_group_id ? Number(form.effect_group_id) : null,
            degree: form.degree !== '' && form.degree != null ? Number(form.degree) : null,
            effect_sub_effects: form.effect_sub_effects.map((row, i) => ({
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
                params: row.params && typeof row.params === 'object' ? row.params : null,
            })),
        };
        form.transform(() => payload).patch(route('admin.effects.update', props.selected.id));
    }
}

function duplicateDegree() {
    if (!props.selected?.id) return;
    duplicateForm.post(route('admin.effects.duplicate-degree', props.selected.id));
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
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Effets</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Conteneurs de sous-effets. Les entrées listent les groupes d'effets (degrés).
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <Link
                    :href="route('admin.effects.create')"
                    class="rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors hover:bg-base-300 border-l-4 border-transparent"
                    :class="selected === 'new' ? 'bg-primary text-primary-content' : ''"
                >
                    + Nouvel effet
                </Link>
                <Link
                    v-for="g in groups"
                    :key="g.id ? 'group-' + g.id : 'single-' + g.effects[0]?.id"
                    :href="route('admin.effects.show', g.effects[0].id)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-left transition-colors border-l-4 border-transparent"
                    :class="selected && g.effects.some((e) => e.id === selected.id) ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                >
                    <span class="truncate">{{ g.label }}</span>
                    <span v-if="g.effects.length > 1" class="text-xs opacity-70 shrink-0">
                        {{ g.effects.length }} degrés
                    </span>
                    <span v-else-if="g.effects[0]?.degree != null" class="text-xs opacity-70 shrink-0">
                        d{{ g.effects[0].degree }}
                    </span>
                </Link>
            </nav>
        </aside>

        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold">
                    {{ selected === 'new' ? 'Nouvel effet' : (selected.name || selected.slug || 'Effet') }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Nom, description optionnelle, groupe et degré. Liste des sous-effets avec ordre, contexte (Général / Combat / Hors combat) et paramètres.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
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
                                    class="rounded-lg border border-base-300 bg-base-200/30 p-3 space-y-3"
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
                            {{ form.processing ? 'Enregistrement…' : (selected === 'new' ? 'Créer' : 'Enregistrer') }}
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing"
                            @click="duplicateDegree"
                        >
                            Ajouter un degré
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-outline"
                            :disabled="duplicateForm.processing"
                            @click="duplicateEffect"
                        >
                            Dupliquer l'effet
                        </button>
                        <button
                            v-if="selected?.id"
                            type="button"
                            class="btn btn-ghost btn-error"
                            :disabled="form.processing"
                            @click="destroy"
                        >
                            Supprimer
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
