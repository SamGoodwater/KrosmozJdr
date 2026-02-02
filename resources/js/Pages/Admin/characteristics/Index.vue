<script setup>
/**
 * Admin Caractéristiques — Liste à gauche, panneau d'édition à droite.
 * Accès : admin et super_admin.
 * Pour chaque champ formule : graphique (variable entre min et max).
 */
import { onMounted, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import SelectFieldNative from '@/Pages/Molecules/data-input/SelectFieldNative.vue';
import FormulaChart from '@/Pages/Admin/characteristics/FormulaChart.vue';
import { decodeFormulaConfig, encodeFormulaConfig, isFormulaTable } from '@/Utils/characteristic/formulaConfig';
import axios from 'axios';

const { setPageTitle } = usePageTitle();

const props = defineProps({
    characteristics: { type: Array, required: true },
    selected: { type: Object, default: null },
});

defineOptions({ layout: Main });
setPageTitle('Administration des caractéristiques');

const defaultConversionFormulas = () => [
    { entity: 'monster', conversion_formula: '', formula_display: '', handler_name: '' },
    { entity: 'class', conversion_formula: '', formula_display: '', handler_name: '' },
    { entity: 'item', conversion_formula: '', formula_display: '', handler_name: '' },
];

function buildFormData(selected) {
    if (!selected) {
        return {
            name: '',
            short_name: '',
            description: '',
            icon: '',
            color: '',
            type: 'int',
            unit: '',
            sort_order: 0,
            applies_to: '',
            value_available: '',
            entities: [],
            conversion_formulas: defaultConversionFormulas(),
        };
    }
    const arrToLines = (arr) => (Array.isArray(arr) && arr.length ? arr.join('\n') : '');
    const conv = selected.conversion_formulas ?? defaultConversionFormulas();
    const conversionFormulas = ['monster', 'class', 'item'].map((entity) => {
        const row = conv.find((c) => c.entity === entity) ?? { entity, conversion_formula: '', formula_display: '', handler_name: '' };
        return {
            entity: row.entity,
            conversion_formula: row.conversion_formula ?? '',
            formula_display: row.formula_display ?? '',
            handler_name: row.handler_name ?? '',
        };
    });
    return {
        name: selected.name ?? '',
        short_name: selected.short_name ?? '',
        description: selected.description ?? '',
        icon: selected.icon ?? '',
        color: selected.color ?? '',
        type: selected.type ?? 'int',
        unit: selected.unit ?? '',
        sort_order: selected.sort_order ?? 0,
        applies_to: arrToLines(selected.applies_to),
        value_available: arrToLines(selected.value_available),
        entities: (selected.entities ?? []).map((e) => ({
            entity: e.entity,
            min: e.min ?? '',
            max: e.max ?? '',
            formula: e.formula ?? '',
            formula_display: e.formula_display ?? '',
            default_value: e.default_value ?? '',
            required: e.required ?? false,
            validation_message: e.validation_message ?? '',
            forgemagie_allowed: e.forgemagie_allowed ?? false,
            forgemagie_max: e.forgemagie_max ?? 0,
            base_price_per_unit: e.base_price_per_unit ?? '',
            rune_price_per_unit: e.rune_price_per_unit ?? '',
        })),
        conversion_formulas: conversionFormulas,
    };
}

const form = useForm(buildFormData(props.selected));

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s);
        form.name = data.name;
        form.short_name = data.short_name;
        form.description = data.description;
        form.icon = data.icon;
        form.color = data.color;
        form.type = data.type;
        form.unit = data.unit;
        form.sort_order = data.sort_order;
        form.applies_to = data.applies_to;
        form.value_available = data.value_available;
        form.entities = data.entities;
        form.conversion_formulas = data.conversion_formulas;
    },
    { immediate: true }
);

const formulaPoints = ref({});
const formulaLoading = ref({});
const conversionFormulaPoints = ref({});
const conversionFormulaLoading = ref({});
const conversionHandlers = ref([]);

function loadConversionHandlers() {
    axios
        .get(route('admin.dofus-conversion-formulas.handlers'))
        .then((res) => {
            conversionHandlers.value = res.data.handlers ?? [];
        })
        .catch(() => {
            conversionHandlers.value = [];
        });
}

onMounted(loadConversionHandlers);

function loadFormulaPreview(charId, entity, variable = 'level', formulaOverride = null) {
    const key = `${charId}-${entity}`;
    formulaLoading.value[key] = true;
    const params = { characteristic_id: charId, entity, variable };
    if (formulaOverride != null && String(formulaOverride).trim() !== '') {
        params.formula = formulaOverride;
    }
    axios
        .get(route('admin.characteristics.formula-preview'), {
            params,
        })
        .then((res) => {
            formulaPoints.value[key] = res.data.points ?? [];
        })
        .catch(() => {
            formulaPoints.value[key] = [];
        })
        .finally(() => {
            formulaLoading.value[key] = false;
        });
}

function getFormulaPoints(charId, entity) {
    const key = `${charId}-${entity}`;
    return formulaPoints.value[key] ?? [];
}

function getFormulaChartKey(charId, entity) {
    return `${charId}-${entity}`;
}

function loadConversionFormulaPreview(charId, entity, conversionFormulaOverride = null) {
    const key = `conv-${charId}-${entity}`;
    conversionFormulaLoading.value[key] = true;
    const params = { characteristic_id: charId, entity };
    if (conversionFormulaOverride != null && String(conversionFormulaOverride).trim() !== '') {
        params.conversion_formula = conversionFormulaOverride;
    }
    axios
        .get(route('admin.dofus-conversion-formulas.formula-preview'), { params })
        .then((res) => {
            conversionFormulaPoints.value[key] = res.data.points ?? [];
        })
        .catch(() => {
            conversionFormulaPoints.value[key] = [];
        })
        .finally(() => {
            conversionFormulaLoading.value[key] = false;
        });
}

function getConversionFormulaPoints(charId, entity) {
    const key = `conv-${charId}-${entity}`;
    return conversionFormulaPoints.value[key] ?? [];
}

function getConversionFormulaChartKey(charId, entity) {
    return `conv-${charId}-${entity}`;
}

let conversionFormulaDebounceTimer = null;
function debouncedLoadConversionFormula(charId, entity, conversionFormula) {
    if (conversionFormulaDebounceTimer) clearTimeout(conversionFormulaDebounceTimer);
    conversionFormulaDebounceTimer = setTimeout(() => {
        loadConversionFormulaPreview(charId, entity, conversionFormula);
        conversionFormulaDebounceTimer = null;
    }, 400);
}

// Chargement automatique des graphiques à l’ouverture du panneau et au changement de formule (debounce)
let formulaDebounceTimer = null;
function debouncedLoadFormula(charId, entity, formula) {
    if (formulaDebounceTimer) clearTimeout(formulaDebounceTimer);
    formulaDebounceTimer = setTimeout(() => {
        loadFormulaPreview(charId, entity, 'level', formula);
        formulaDebounceTimer = null;
    }, 400);
}

watch(
    () => [props.selected?.id, form.entities],
    () => {
        const charId = props.selected?.id;
        if (!charId) return;
        form.entities.forEach((ent) => {
            if (ent.formula && String(ent.formula).trim()) {
                debouncedLoadFormula(charId, ent.entity, ent.formula);
            }
        });
    },
    { immediate: true, deep: true }
);

watch(
    () => [props.selected?.id, form.conversion_formulas],
    () => {
        const charId = props.selected?.id;
        if (!charId) return;
        (form.conversion_formulas ?? []).forEach((cf) => {
            if (cf.conversion_formula && String(cf.conversion_formula).trim()) {
                debouncedLoadConversionFormula(charId, cf.entity, cf.conversion_formula);
            }
        });
    },
    { immediate: true, deep: true }
);

const entityLabels = { monster: 'Monstre', class: 'Classe / PNJ', item: 'Équipement', spell: 'Sort' };

function getDecodedFormula(ent) {
    return decodeFormulaConfig(ent?.formula ?? '');
}

function setFormulaAsSimple(ent) {
    ent.formula = '';
}

function setFormulaAsTable(ent) {
    ent.formula = encodeFormulaConfig({
        type: 'table',
        characteristic: 'level',
        entries: [{ from: 1, value: 0 }, { from: 7, value: 2 }, { from: 14, value: 4 }],
    });
}

function setTableCharacteristic(ent, characteristic) {
    const dec = getDecodedFormula(ent);
    if (dec.type !== 'table') return;
    ent.formula = encodeFormulaConfig({ type: 'table', characteristic, entries: dec.entries });
}

function setTableEntries(ent, entries) {
    const dec = getDecodedFormula(ent);
    if (dec.type !== 'table') return;
    ent.formula = encodeFormulaConfig({ type: 'table', characteristic: dec.characteristic, entries });
}

function addTableRow(ent) {
    const dec = getDecodedFormula(ent);
    if (dec.type !== 'table') return;
    const lastFrom = dec.entries.length ? dec.entries[dec.entries.length - 1].from : 0;
    const newEntries = [...dec.entries, { from: lastFrom + 1, value: 0 }];
    setTableEntries(ent, newEntries);
}

function removeTableRow(ent, index) {
    const dec = getDecodedFormula(ent);
    if (dec.type !== 'table' || index < 0 || index >= dec.entries.length) return;
    const newEntries = dec.entries.filter((_, i) => i !== index);
    setTableEntries(ent, newEntries);
}

function updateTableEntry(ent, index, field, value) {
    const dec = getDecodedFormula(ent);
    if (dec.type !== 'table' || index < 0 || index >= dec.entries.length) return;
    const newEntries = dec.entries.map((e, i) => {
        if (i !== index) return e;
        if (field === 'from') return { ...e, from: Number(value) || 0 };
        if (field === 'value') return { ...e, value: typeof value === 'number' ? value : String(value ?? '') };
        return e;
    });
    setTableEntries(ent, newEntries);
}

// ——— Formules de conversion Dofus → JDR (même format formule/table, variable [d] et [level])
function getDecodedConversionFormula(cf) {
    return decodeFormulaConfig(cf?.conversion_formula ?? '');
}
function setConversionFormulaAsSimple(cf) {
    cf.conversion_formula = '';
}
function setConversionFormulaAsTable(cf) {
    cf.conversion_formula = encodeFormulaConfig({
        type: 'table',
        characteristic: 'd',
        entries: [{ from: 0, value: 0 }, { from: 100, value: 10 }, { from: 200, value: 20 }],
    });
}
function setConversionTableCharacteristic(cf, characteristic) {
    const dec = getDecodedConversionFormula(cf);
    if (dec.type !== 'table') return;
    cf.conversion_formula = encodeFormulaConfig({ type: 'table', characteristic, entries: dec.entries });
}
function setConversionTableEntries(cf, entries) {
    const dec = getDecodedConversionFormula(cf);
    if (dec.type !== 'table') return;
    cf.conversion_formula = encodeFormulaConfig({ type: 'table', characteristic: dec.characteristic, entries });
}
function addConversionTableRow(cf) {
    const dec = getDecodedConversionFormula(cf);
    if (dec.type !== 'table') return;
    const lastFrom = dec.entries.length ? dec.entries[dec.entries.length - 1].from : 0;
    setConversionTableEntries(cf, [...dec.entries, { from: lastFrom + 1, value: 0 }]);
}
function removeConversionTableRow(cf, index) {
    const dec = getDecodedConversionFormula(cf);
    if (dec.type !== 'table' || index < 0 || index >= dec.entries.length) return;
    setConversionTableEntries(cf, dec.entries.filter((_, i) => i !== index));
}
function updateConversionTableEntry(cf, index, field, value) {
    const dec = getDecodedConversionFormula(cf);
    if (dec.type !== 'table' || index < 0 || index >= dec.entries.length) return;
    const newEntries = dec.entries.map((e, i) => {
        if (i !== index) return e;
        if (field === 'from') return { ...e, from: Number(value) || 0 };
        if (field === 'value') return { ...e, value: typeof value === 'number' ? value : String(value ?? '') };
        return e;
    });
    setConversionTableEntries(cf, newEntries);
}

/** Options pour la table de conversion : d (valeur Dofus), level (niveau JDR). */
const conversionTableCharacteristicOptions = [
    { id: 'd', name: 'Valeur Dofus (d)' },
    { id: 'level', name: 'Niveau JDR (level)' },
];

/** Couleurs Tailwind / DaisyUI pour le select (sémantiques + palette teinte 500). */
const colorOptions = [
    { value: '', label: '— Aucune —' },
    { value: 'red-500', label: 'Rouge' },
    { value: 'orange-500', label: 'Orange' },
    { value: 'amber-500', label: 'Ambre' },
    { value: 'yellow-500', label: 'Jaune' },
    { value: 'lime-500', label: 'Lime' },
    { value: 'green-500', label: 'Vert' },
    { value: 'emerald-500', label: 'Émeraude' },
    { value: 'teal-500', label: 'Teal' },
    { value: 'cyan-500', label: 'Cyan' },
    { value: 'sky-500', label: 'Sky' },
    { value: 'blue-500', label: 'Bleu' },
    { value: 'indigo-500', label: 'Indigo' },
    { value: 'violet-500', label: 'Violet' },
    { value: 'purple-500', label: 'Pourpre' },
    { value: 'fuchsia-500', label: 'Fuchsia' },
    { value: 'pink-500', label: 'Pink' },
    { value: 'rose-500', label: 'Rose' },
    { value: 'slate-500', label: 'Ardoise' },
    { value: 'gray-500', label: 'Gris' },
    { value: 'zinc-500', label: 'Zinc' },
    { value: 'stone-500', label: 'Stone' },
];

/** URL des icônes : storage/app/public/images/icons/characteristics/ (servi via /storage/...) */
const iconBasePath = '/storage/images/icons/characteristics';
function iconUrl(icon) {
    if (!icon || typeof icon !== 'string') return null;
    if (icon.startsWith('fa-') || icon.startsWith('http')) return null;
    return `${iconBasePath}/${icon.includes('/') ? icon.split('/').pop() : icon}`;
}
function isImageIcon(icon) {
    return icon && typeof icon === 'string' && !icon.startsWith('fa-') && !icon.startsWith('http');
}

const iconUploading = ref(false);
function getXsrfToken() {
    const match = document.cookie.match(/\bXSRF-TOKEN=([^;]*)/);
    return match ? decodeURIComponent(match[1]) : null;
}
async function onIconFileChange(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    iconUploading.value = true;
    try {
        const formData = new FormData();
        formData.append('file', file);
        const headers = { Accept: 'application/json' };
        const token = getXsrfToken();
        if (token) headers['X-XSRF-TOKEN'] = token;
        const res = await axios.post(route('admin.characteristics.upload-icon'), formData, { headers });
        if (res.data?.success && res.data?.icon) {
            form.icon = res.data.icon;
        }
    } finally {
        iconUploading.value = false;
        event.target.value = '';
    }
}

/** Classes de fond par zone : thème entités (monster = creature, class/breed = classe, item = item). */
const entityBgClasses = {
    monster: 'bg-color-creature-100',
    class: 'bg-color-classe-100',
    breed: 'bg-color-classe-100',
    item: 'bg-color-item-100',
    spell: 'bg-color-spell-100',
};

function submit() {
    if (!props.selected?.id) return;
    // Envoi applies_to et value_available en chaîne (lignes) : le backend les normalise en tableau
    form.patch(route('admin.characteristics.update', props.selected.id));
}
</script>

<template>
    <Head title="Caractéristiques" />
    <div class="flex h-full min-h-0 w-full">
        <!-- Liste à gauche -->
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Caractéristiques</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Définitions, formules et bornes min/max par type d’entité (monstre, classe, objet). Sélectionnez une ligne pour éditer.
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <p v-if="characteristics.length === 0" class="px-3 py-4 text-sm text-base-content/70">
                    Aucune caractéristique en base. Exécutez le seeder pour importer les définitions depuis la config :
                    <code class="mt-2 block rounded bg-base-300 px-2 py-1 text-xs">php artisan db:seed --class=CharacteristicConfigSeeder</code>
                </p>
                <Link
                    v-for="c in characteristics"
                    :key="c.id"
                    :href="route('admin.characteristics.show', c.id)"
                    class="flex items-center gap-2 rounded-lg px-3 py-2 text-left transition-colors border-l-4 border-transparent"
                    :class="selected?.id === c.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                    :style="c.color && selected?.id !== c.id ? { borderLeftColor: `var(--color-${c.color})` } : {}"
                >
                    <span v-if="c.icon" class="flex h-8 w-8 shrink-0 items-center justify-center text-lg">
                        <i v-if="c.icon.startsWith('fa-')" :class="['fa', c.icon]" />
                        <img
                            v-else-if="isImageIcon(c.icon)"
                            :src="iconUrl(c.icon)"
                            :alt="c.name || c.id"
                            class="h-6 w-6 object-contain"
                            @error="($e) => ($e.target.style.display = 'none')"
                        />
                        <span v-else>{{ c.icon }}</span>
                    </span>
                    <span v-if="!c.icon && c.color" class="h-3 w-3 shrink-0 rounded-full" :style="{ backgroundColor: `var(--color-${c.color})` }" />
                    <span class="truncate">{{ c.name || c.id }}</span>
                </Link>
            </nav>
        </aside>

        <!-- Panneau à droite -->
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <template v-if="selected">
                <h1 class="mb-2 text-2xl font-bold flex items-center gap-2" :style="form.color ? { borderLeftColor: `var(--color-${form.color})` } : {}" :class="form.color ? 'pl-3 border-l-4' : ''">
                    {{ selected.name || selected.id }}
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Modifiez les champs puis cliquez sur « Enregistrer » pour appliquer les changements.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Champs globaux (zone générale = campaign/stone, gris neutre) -->
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <p class="text-sm text-base-content/70">
                                Nom, affichage (icône, couleur), type de donnée et règles (unité, entités concernées). Forgemagie et prix sont dans la section Équipement.
                            </p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" required />
                                <InputField
                                    v-model="form.short_name"
                                    label="Nom abrégé"
                                    name="short_name"
                                    helper="Utilisé dans les listes compactes."
                                />
                                <InputField v-model="form.description" label="Description" name="description" type="textarea" />
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Icône</span></label>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <div v-if="form.icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-base-200">
                                            <i v-if="form.icon.startsWith('fa-')" :class="['fa text-xl', form.icon]" />
                                            <img
                                                v-else-if="isImageIcon(form.icon)"
                                                :src="iconUrl(form.icon)"
                                                :alt="form.name"
                                                class="h-8 w-8 object-contain"
                                                @error="($e) => ($e.target.style.display = 'none')"
                                            />
                                            <span v-else class="text-sm">{{ form.icon }}</span>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <input
                                                type="file"
                                                accept="image/*"
                                                class="file-input file-input-bordered file-input-sm w-full max-w-xs"
                                                :disabled="iconUploading"
                                                @change="onIconFileChange"
                                            />
                                            <p class="text-xs text-base-content/70">
                                                Fichier dans <code class="rounded bg-base-300 px-1">storage/app/public/images/icons/characteristics/</code>. Ou saisir <code class="rounded bg-base-300 px-1">fa-heart</code> pour Font Awesome.
                                            </p>
                                        </div>
                                        <input
                                            v-model="form.icon"
                                            type="text"
                                            class="input input-bordered input-sm w-48 font-mono"
                                            placeholder="fa-heart ou nom fichier"
                                        />
                                        <span v-if="iconUploading" class="text-sm text-base-content/60">Envoi…</span>
                                    </div>
                                </div>
                                <div>
                                    <SelectFieldNative
                                        v-model="form.color"
                                        label="Couleur"
                                        name="color"
                                        :options="colorOptions"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Utilisée pour les badges, graphiques et indicateurs.</p>
                                </div>
                                <div>
                                    <InputField v-model="form.type" label="Type" name="type" :options="['int', 'string', 'array']" />
                                    <p class="mt-1 text-xs text-base-content/70">int = nombre, string = texte, array = liste de valeurs.</p>
                                </div>
                                <InputField
                                    v-model="form.unit"
                                    label="Unité"
                                    name="unit"
                                    helper="ex. %, points, dégâts (affichage uniquement)."
                                />
                                <InputField
                                    v-model="form.sort_order"
                                    label="Ordre"
                                    name="sort_order"
                                    type="number"
                                    helper="Ordre d’affichage dans les listes (plus petit = plus haut)."
                                />
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">S’applique à (un type par ligne)</span></label>
                                    <textarea
                                        v-model="form.applies_to"
                                        class="textarea textarea-bordered w-full font-mono text-sm"
                                        rows="2"
                                        placeholder="ex: monster&#10;class&#10;item"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Types d’entités : <code class="rounded bg-base-300 px-1">monster</code>, <code class="rounded bg-base-300 px-1">class</code>, <code class="rounded bg-base-300 px-1">item</code> (équipement).</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Valeurs autorisées (un par ligne)</span></label>
                                    <textarea
                                        v-model="form.value_available"
                                        class="textarea textarea-bordered w-full font-mono text-sm"
                                        rows="3"
                                        placeholder="ex: 0&#10;1&#10;2"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Laissez vide si aucune liste imposée.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Par entité (monster, class, item) : fond par zone thème -->
                    <div v-for="ent in form.entities" :key="ent.entity" class="card bg-base-100 shadow" :class="entityBgClasses[ent.entity]">
                        <div class="card-body">
                            <h2 class="card-title text-lg">{{ entityLabels[ent.entity] || ent.entity }}</h2>
                            <p class="text-sm text-base-content/70">
                                Bornes min/max et formule de calcul pour ce type d’entité. Le graphique montre l’évolution en fonction du niveau.
                            </p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="ent.min" :label="`Min (${ent.entity})`" type="number" />
                                <InputField v-model="ent.max" :label="`Max (${ent.entity})`" type="number" />
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Formule ou table par caractéristique</span></label>
                                    <div class="flex gap-2 mb-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="!isFormulaTable(ent.formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setFormulaAsSimple(ent)"
                                        >
                                            Formule simple
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="isFormulaTable(ent.formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setFormulaAsTable(ent)"
                                        >
                                            Table par caractéristique
                                        </button>
                                    </div>
                                    <!-- Formule simple -->
                                    <template v-if="!isFormulaTable(ent.formula)">
                                        <textarea
                                            v-model="ent.formula"
                                            class="textarea textarea-bordered w-full font-mono text-sm"
                                            rows="2"
                                            placeholder="ex: [vitality]*10+[level]*2"
                                            @focus="loadFormulaPreview(selected.id, ent.entity, 'level', ent.formula)"
                                        />
                                        <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), + - * /</p>
                                    </template>
                                    <!-- Table par caractéristique -->
                                    <template v-else>
                                        <div class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                            <div>
                                                <label class="label label-text text-xs">Dépend de</label>
                                                <select
                                                    :value="getDecodedFormula(ent).characteristic"
                                                    class="select select-bordered select-sm w-full max-w-xs"
                                                    @change="setTableCharacteristic(ent, $event.target.value)"
                                                >
                                                    <option
                                                        v-for="c in characteristics"
                                                        :key="c.id"
                                                        :value="c.id"
                                                    >
                                                        {{ c.name || c.id }}
                                                    </option>
                                                </select>
                                                <p class="mt-1 text-xs text-base-content/70">À partir de chaque valeur ci‑dessous, ce résultat s’applique jusqu’à la valeur suivante (non comprise). La dernière ligne s’applique à toutes les valeurs supérieures.</p>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="label-text text-xs">À partir de (valeur) → résultat</span>
                                                    <button type="button" class="btn btn-ghost btn-xs" @click="addTableRow(ent)">Ajouter</button>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="table table-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>À partir de</th>
                                                                <th>Valeur (fixe ou formule)</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(row, idx) in getDecodedFormula(ent).entries" :key="idx">
                                                                <td>
                                                                    <input
                                                                        type="number"
                                                                        class="input input-bordered input-xs w-20"
                                                                        :value="row.from"
                                                                        @input="updateTableEntry(ent, idx, 'from', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="text"
                                                                        class="input input-bordered input-xs w-full font-mono"
                                                                        :value="row.value"
                                                                        placeholder="0 ou [level]*2"
                                                                        @input="updateTableEntry(ent, idx, 'value', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-ghost btn-xs btn-square"
                                                                        aria-label="Supprimer"
                                                                        @click="removeTableRow(ent, idx)"
                                                                    >
                                                                        ×
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Graphique formule -->
                                    <div v-if="ent.formula" class="mt-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="text-sm font-medium">
                                                Aperçu (variable : {{ isFormulaTable(ent.formula) ? getDecodedFormula(ent).characteristic : 'level' }})
                                            </span>
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-xs"
                                                @click="loadFormulaPreview(selected.id, ent.entity, 'level', ent.formula)"
                                            >
                                                Actualiser
                                            </button>
                                        </div>
                                        <div v-if="formulaLoading[getFormulaChartKey(selected.id, ent.entity)]" class="flex h-32 items-center justify-center text-sm text-base-content/60">
                                            Chargement…
                                        </div>
                                        <div v-else class="h-32 w-full">
                                            <FormulaChart :points="getFormulaPoints(selected.id, ent.entity)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <input
                                        v-model="ent.formula_display"
                                        type="text"
                                        class="input input-bordered w-full"
                                        placeholder="ex: Vitalité × 10 + Niveau × 2"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Version lisible affichée à l’utilisateur (sans code).</p>
                                </div>
                                <InputField v-model="ent.default_value" label="Valeur par défaut" type="text" />
                                <div class="sm:col-span-2">
                                    <div class="flex items-center gap-2">
                                        <input v-model="ent.required" type="checkbox" class="checkbox" />
                                        <span>Requis</span>
                                    </div>
                                    <p class="mt-1 text-xs text-base-content/70">Cochez si cette caractéristique doit être renseignée pour ce type d’entité.</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <InputField
                                        v-model="ent.validation_message"
                                        label="Message de validation"
                                        type="text"
                                        helper="Affiché si la valeur saisie est invalide (optionnel)."
                                    />
                                </div>
                                <!-- Forgemagie et prix : uniquement pour l’équipement (item) -->
                                <template v-if="ent.entity === 'item'">
                                    <div class="sm:col-span-2 border-t border-base-300 pt-4 mt-2">
                                        <h3 class="mb-3 text-sm font-semibold text-base-content/80">Forgemagie et prix (équipement)</h3>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <input v-model="ent.forgemagie_allowed" type="checkbox" class="checkbox" />
                                                    <span>Forgemagie autorisée</span>
                                                </div>
                                                <InputField v-model="ent.forgemagie_max" label="Forgemagie max" type="number" />
                                                <p class="text-xs text-base-content/70">Valeur maximale autorisée en forgemagie pour cet équipement.</p>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <InputField
                                                    v-model="ent.base_price_per_unit"
                                                    label="Prix de base (par unité)"
                                                    type="number"
                                                    step="0.01"
                                                />
                                                <InputField
                                                    v-model="ent.rune_price_per_unit"
                                                    label="Prix rune (par unité)"
                                                    type="number"
                                                    step="0.01"
                                                />
                                                <p class="text-xs text-base-content/70">Prix utilisés pour les calculs d’équipement (optionnel).</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Formules de conversion Dofus → JDR (zone générale = neutral) -->
                    <div class="card bg-base-100 shadow bg-color-neutral-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Formules de conversion Dofus → JDR</h2>
                            <p class="text-sm text-base-content/70">
                                Formules utilisées lors du scrapping pour convertir les valeurs DofusDB en valeurs JDR. Variable <code class="rounded bg-base-300 px-1">[d]</code> = valeur Dofus, <code class="rounded bg-base-300 px-1">[level]</code> = niveau JDR (pour la vie). Une formule différente par type d’entité (monstre, classe, équipement).
                            </p>
                            <div v-for="cf in form.conversion_formulas" :key="cf.entity" class="space-y-4 rounded-lg border border-base-300 bg-base-200/30 p-4">
                                <h3 class="text-sm font-semibold text-base-content/80">{{ entityLabels[cf.entity] ?? cf.entity }}</h3>
                                <div>
                                    <label class="label"><span class="label-text">Handler de conversion (optionnel)</span></label>
                                    <select
                                        v-model="cf.handler_name"
                                        class="select select-bordered select-sm w-full max-w-md"
                                    >
                                        <option value="">Aucun (formule seule)</option>
                                        <option
                                            v-for="h in conversionHandlers"
                                            :key="h.name"
                                            :value="h.name"
                                        >
                                            {{ h.label }} ({{ h.type }})
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">Fonction PHP pour cas complexes (ex. résistances Dofus → tiers JDR).</p>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule ou table par valeur</span></label>
                                    <div class="mb-2 flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="!isFormulaTable(cf.conversion_formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setConversionFormulaAsSimple(cf)"
                                        >
                                            Formule simple
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="isFormulaTable(cf.conversion_formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setConversionFormulaAsTable(cf)"
                                        >
                                            Table par caractéristique
                                        </button>
                                    </div>
                                    <template v-if="!isFormulaTable(cf.conversion_formula)">
                                        <textarea
                                            v-model="cf.conversion_formula"
                                            class="textarea textarea-bordered w-full font-mono text-sm"
                                            rows="2"
                                            placeholder="ex: [d]/10 ou floor([d]/200)+[level]*5"
                                            @focus="loadConversionFormulaPreview(selected.id, cf.entity, cf.conversion_formula)"
                                        />
                                        <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), + - * /</p>
                                    </template>
                                    <template v-else>
                                        <div class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                            <div>
                                                <label class="label label-text text-xs">Dépend de</label>
                                                <select
                                                    :value="getDecodedConversionFormula(cf).characteristic"
                                                    class="select select-bordered select-sm w-full max-w-xs"
                                                    @change="setConversionTableCharacteristic(cf, $event.target.value)"
                                                >
                                                    <option
                                                        v-for="opt in conversionTableCharacteristicOptions"
                                                        :key="opt.id"
                                                        :value="opt.id"
                                                    >
                                                        {{ opt.name }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="mb-1 flex items-center justify-between">
                                                    <span class="label-text text-xs">À partir de (valeur) → résultat</span>
                                                    <button type="button" class="btn btn-ghost btn-xs" @click="addConversionTableRow(cf)">Ajouter</button>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="table table-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>À partir de</th>
                                                                <th>Valeur (fixe ou formule)</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(row, idx) in getDecodedConversionFormula(cf).entries" :key="idx">
                                                                <td>
                                                                    <input
                                                                        type="number"
                                                                        class="input input-bordered input-xs w-20"
                                                                        :value="row.from"
                                                                        @input="updateConversionTableEntry(cf, idx, 'from', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="text"
                                                                        class="input input-bordered input-xs w-full font-mono"
                                                                        :value="row.value"
                                                                        placeholder="0 ou [d]/10"
                                                                        @input="updateConversionTableEntry(cf, idx, 'value', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-ghost btn-xs btn-square"
                                                                        aria-label="Supprimer"
                                                                        @click="removeConversionTableRow(cf, idx)"
                                                                    >
                                                                        ×
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Aperçu graphique conversion (d Dofus → k JDR) -->
                                    <div v-if="cf.conversion_formula" class="mt-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="text-sm font-medium">Aperçu (d Dofus → k JDR)</span>
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-xs"
                                                @click="loadConversionFormulaPreview(selected.id, cf.entity, cf.conversion_formula)"
                                            >
                                                Actualiser
                                            </button>
                                        </div>
                                        <div v-if="conversionFormulaLoading[getConversionFormulaChartKey(selected.id, cf.entity)]" class="flex h-32 items-center justify-center text-sm text-base-content/60">
                                            Chargement…
                                        </div>
                                        <div v-else class="h-32 w-full">
                                            <FormulaChart :points="getConversionFormulaPoints(selected.id, cf.entity)" />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <input
                                        v-model="cf.formula_display"
                                        type="text"
                                        class="input input-bordered w-full"
                                        placeholder="ex: k = d / 10"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Version lisible affichée à l’utilisateur.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <Btn type="submit" color="primary" :disabled="form.processing">Enregistrer</Btn>
                    </div>
                </form>
            </template>
            <div v-else class="flex h-64 items-center justify-center text-base-content/60">
                Sélectionnez une caractéristique dans la liste.
            </div>
        </main>
    </div>
</template>
