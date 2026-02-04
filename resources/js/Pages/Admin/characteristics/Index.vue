<script setup>
/**
 * Admin Caractéristiques — Liste à gauche, panneau d'édition à droite.
 * Accès : admin et super_admin.
 * Pour chaque champ formule : graphique (variable entre min et max).
 */
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
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
    /** Caractéristiques regroupées par groupe : { creature: [{ id, name, ... }], object: [...], spell: [...] } */
    characteristicsByGroup: { type: Object, default: () => ({ creature: [], object: [], spell: [] }) },
    selected: { type: Object, default: null },
    createMode: { type: Boolean, default: false },
    groups: { type: Array, default: () => ['creature', 'object', 'spell'] },
    entitiesByGroup: { type: Object, default: () => ({}) },
    entitiesTemplate: { type: Object, default: () => ({}) },
});

/** Entités affichées en panneaux supplémentaires (hors Général). Clic sur "Spécifier pour une entité". */
const selectedEntityOverrides = ref([]);

defineOptions({ layout: Main });
setPageTitle('Administration des caractéristiques');

/** Conversion formulas par défaut pour un groupe (entités du groupe hors '*'). */
function defaultConversionFormulasForGroup(entitiesByGroup, group = 'creature') {
    const entities = (entitiesByGroup?.[group] ?? []).filter((e) => e !== '*');
    return entities.map((entity) => ({
        entity,
        conversion_formula: '',
        formula_display: '',
        handler_name: '',
    }));
}

function buildFormData(selected, entitiesByGroup = null) {
    if (!selected) {
        return {
            key: '',
            group: 'creature',
            name: '',
            short_name: '',
            description: '',
            helper: '',
            icon: '',
            color: '',
            type: 'int',
            unit: '',
            sort_order: 0,
            applies_to: '',
            value_available: '',
            entities: [],
            conversion_formulas: defaultConversionFormulasForGroup(entitiesByGroup ?? {}, 'creature'),
        };
    }
    const arrToLines = (arr) => (Array.isArray(arr) && arr.length ? arr.join('\n') : '');
    // Le backend envoie déjà conversion_formulas uniquement pour les entités du groupe de la caractéristique
    const conversionFormulas = (selected.conversion_formulas ?? []).map((row) => ({
        entity: row.entity,
        conversion_formula: row.conversion_formula ?? '',
        formula_display: row.formula_display ?? '',
        handler_name: row.handler_name ?? '',
    }));
    return {
        key: '',
        group: '',
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
            db_column: e.db_column ?? '',
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
            conversion_formula: e.conversion_formula ?? '',
        })),
        conversion_formulas: conversionFormulas,
    };
}

const form = useForm(buildFormData(props.selected, props.entitiesByGroup));

watch(
    () => props.selected,
    (s) => {
        selectedEntityOverrides.value = [];
        const data = buildFormData(s, props.entitiesByGroup);
        if (data.key !== undefined) form.key = data.key;
        if (data.group !== undefined) form.group = data.group;
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

function addEntityOverride(entityKey) {
    if (!selectedEntityOverrides.value.includes(entityKey)) {
        selectedEntityOverrides.value = [...selectedEntityOverrides.value, entityKey];
    }
    // S'assurer qu'une ligne existe dans form.entities pour cette entité
    const existing = (form.entities ?? []).find((e) => e.entity === entityKey);
    if (!existing) {
        const defaultRow = {
            entity: entityKey,
            db_column: '',
            min: '',
            max: '',
            formula: '',
            formula_display: '',
            default_value: '',
            required: false,
            validation_message: '',
            forgemagie_allowed: false,
            forgemagie_max: 0,
            base_price_per_unit: '',
            rune_price_per_unit: '',
            conversion_formula: '',
        };
        form.entities = [...(form.entities ?? []), defaultRow];
    }
}
function removeEntityOverride(entityKey) {
    selectedEntityOverrides.value = selectedEntityOverrides.value.filter((e) => e !== entityKey);
}
function confirmDelete() {
    if (props.selected?.id && confirm('Supprimer cette caractéristique ? Les données associées seront perdues.')) {
        router.delete(route('admin.characteristics.destroy', props.selected.id));
    }
}

// En mode création : initialiser entities et conversion_formulas selon le groupe
watch(
    () => [props.createMode, props.entitiesTemplate, props.entitiesByGroup, form.group],
    () => {
        if (props.createMode && form.group) {
            if (props.entitiesTemplate) {
                const template = props.entitiesTemplate[form.group];
                if (Array.isArray(template) && template.length) {
                    form.entities = template.map((e) => ({
                        entity: e.entity ?? '*',
                        db_column: e.db_column ?? '',
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
                    }));
                }
            }
            // Conversion : uniquement les entités du groupe (hors '*')
            form.conversion_formulas = defaultConversionFormulasForGroup(props.entitiesByGroup ?? {}, form.group);
        }
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

onMounted(() => {
    loadConversionHandlers();
    // En mode création, pré-sélection du groupe depuis l'URL (?group=creature)
    if (props.createMode) {
        const params = new URLSearchParams(window.location.search);
        const group = params.get('group');
        if (group && props.groups.includes(group)) {
            form.group = group;
        }
    }
});

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
    () => [props.selected?.id, form.entities],
    () => {
        const charId = props.selected?.id;
        if (!charId) return;
        (form.entities ?? []).forEach((ent) => {
            if (ent.conversion_formula != null && String(ent.conversion_formula).trim()) {
                debouncedLoadConversionFormula(charId, ent.entity, ent.conversion_formula);
            }
        });
    },
    { immediate: true, deep: true }
);

/** Libellés pour chaque entité (affichage dans les cartes). */
const entityLabels = {
    '*': 'Défaut (toutes les entités du groupe)',
    monster: 'Monstre',
    class: 'Classe',
    npc: 'PNJ',
    item: 'Équipement',
    consumable: 'Consommable',
    resource: 'Ressource',
    panoply: 'Panoplie',
    spell: 'Sort',
};

/** Groupes avec libellés. */
const groupLabels = {
    creature: 'Créature (monstre, classe, PNJ)',
    object: 'Objet (équipement, consommable, ressource, panoplie)',
    spell: 'Sort',
};

/** Ligne entity (défaut groupe) pour le panneau Général. */
function generalEntityRow() {
    return (form.entities ?? []).find((e) => e.entity === '*') ?? null;
}

/** Ligne entity pour une entité donnée. */
function entityRow(entityKey) {
    return (form.entities ?? []).find((e) => e.entity === entityKey) ?? null;
}

/** Entités du groupe (hors '*') pour le dropdown "Spécifier pour une entité". */
const entitiesForSpecifyDropdown = computed(() => {
    const group = props.selected?.group;
    if (!group) return [];
    const list = (props.selected?.entitiesByGroup ?? props.entitiesByGroup)?.[group] ?? [];
    return list.filter((e) => e !== '*');
});

/** Caractéristiques du groupe courant (pour dropdown "Dépend de" des formules table). */
const characteristicsForFormulaOptions = computed(() => {
    const group = props.selected?.group;
    if (!group) return [];
    return props.characteristicsByGroup[group] ?? [];
});


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

/** Classes de fond par zone : thème entités. */
const entityBgClasses = {
    '*': 'bg-base-200',
    monster: 'bg-color-creature-100',
    class: 'bg-color-classe-100',
    breed: 'bg-color-classe-100',
    npc: 'bg-color-classe-100',
    item: 'bg-color-item-100',
    consumable: 'bg-color-item-100',
    resource: 'bg-color-item-100',
    panoply: 'bg-color-item-100',
    spell: 'bg-color-spell-100',
};

function submit() {
    if (props.createMode) {
        form.post(route('admin.characteristics.store'));
        return;
    }
    if (!props.selected?.id) return;
    form.patch(route('admin.characteristics.update', props.selected.id));
}
</script>

<template>
    <Head title="Caractéristiques" />
    <div class="flex h-full min-h-0 w-full">
        <!-- Liste à gauche (vue par caractéristique) -->
        <aside class="flex w-64 shrink-0 flex-col border-r border-base-300 bg-base-200/50 overflow-y-auto">
            <div class="p-3">
                <div class="font-semibold text-base-content">Caractéristiques</div>
                <p class="mt-1 text-xs text-base-content/70">
                    Définitions, formules et bornes min/max par type d’entité (monstre, classe, objet). Cliquez pour éditer.
                </p>
            </div>
            <nav class="flex flex-col gap-0.5 p-2">
                <p v-if="Object.keys(characteristicsByGroup).every((g) => !(characteristicsByGroup[g] || []).length)" class="px-3 py-4 text-sm text-base-content/70">
                    Aucune caractéristique. Exécutez le seeder ou ajoutez-en via un groupe ci-dessous (ou exportez après modification via l’interface) :
                </p>
                <div
                    v-for="group in groups"
                    :key="group"
                    class="collapse collapse-arrow rounded-lg border border-base-300 bg-base-100"
                >
                    <input type="checkbox" :checked="selected && selected.group === group" />
                    <div class="collapse-title min-h-0 py-2 font-medium">
                        {{ groupLabels[group] || group }}
                    </div>
                    <div class="collapse-content">
                        <div class="flex flex-col gap-0.5 pb-2">
                            <Link
                                v-for="c in (characteristicsByGroup[group] || [])"
                                :key="c.id"
                                :href="route('admin.characteristics.show', c.id)"
                                class="flex items-center gap-2 rounded-lg px-3 py-2 text-left text-sm transition-colors border-l-4 border-transparent"
                                :class="selected?.id === c.id ? 'bg-primary text-primary-content' : 'hover:bg-base-300'"
                                :style="c.color && selected?.id !== c.id ? { borderLeftColor: `var(--color-${c.color})` } : {}"
                            >
                                <span v-if="c.icon" class="flex h-6 w-6 shrink-0 items-center justify-center text-sm">
                                    <i v-if="c.icon.startsWith('fa-')" :class="['fa', c.icon]" />
                                    <img
                                        v-else-if="isImageIcon(c.icon)"
                                        :src="iconUrl(c.icon)"
                                        :alt="c.name || c.id"
                                        class="h-5 w-5 object-contain"
                                        @error="($e) => ($e.target.style.display = 'none')"
                                    />
                                    <span v-else class="text-xs">{{ c.icon }}</span>
                                </span>
                                <span v-if="!c.icon && c.color" class="h-2.5 w-2.5 shrink-0 rounded-full" :style="{ backgroundColor: `var(--color-${c.color})` }" />
                                <span class="truncate">{{ c.name || c.id }}</span>
                            </Link>
                            <Link
                                :href="route('admin.characteristics.create') + '?group=' + group"
                                class="btn btn-ghost btn-sm mt-1 justify-start gap-2 text-primary"
                            >
                                <i class="fa fa-plus text-xs" />
                                Ajouter une caractéristique
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Panneau central -->
        <main class="min-w-0 flex-1 overflow-y-auto p-6">
            <!-- Mode création : nouvelle caractéristique -->
            <template v-if="createMode">
                <h1 class="mb-2 text-2xl font-bold">Nouvelle caractéristique</h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Choisissez le groupe d'entités, la clé et le nom, puis renseignez les paramètres par entité.
                </p>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Groupe et identifiant</h2>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="label"><span class="label-text">Groupe d'entités</span></label>
                                    <select
                                        v-model="form.group"
                                        class="select select-bordered w-full"
                                        required
                                    >
                                        <option
                                            v-for="g in groups"
                                            :key="g"
                                            :value="g"
                                        >
                                            {{ groupLabels[g] || g }}
                                        </option>
                                    </select>
                                    <p class="mt-1 text-xs text-base-content/70">Détermine quelles entités (monstre, objet, sort…) pourront utiliser cette caractéristique.</p>
                                </div>
                                <InputField
                                    v-model="form.key"
                                    label="Clé (identifiant technique)"
                                    name="key"
                                    helper="Lettres minuscules, chiffres et underscores uniquement (ex. life_creature, level_object)."
                                    required
                                />
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Définition</h2>
                            <p class="text-sm text-base-content/70">Nom, affichage et type de donnée.</p>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <InputField v-model="form.name" label="Nom" name="name" required />
                                <InputField v-model="form.short_name" label="Nom abrégé" name="short_name" helper="Listes compactes." />
                                <InputField v-model="form.description" label="Description" name="description" type="textarea" class="sm:col-span-2" />
                                <div>
                                    <SelectFieldNative v-model="form.color" label="Couleur" name="color" :options="colorOptions" />
                                </div>
                                <div>
                                    <InputField v-model="form.type" label="Type" name="type" :options="['int', 'string', 'array']" />
                                </div>
                                <InputField v-model="form.unit" label="Unité" name="unit" />
                                <InputField v-model="form.sort_order" label="Ordre" name="sort_order" type="number" />
                            </div>
                        </div>
                    </div>
                    <div class="card bg-base-100 shadow">
                        <div class="card-body">
                            <h2 class="card-title text-lg">Paramètres par entité du groupe</h2>
                            <p class="text-sm text-base-content/70">
                                Pour chaque entité (ou « Défaut » pour toutes), définissez les bornes min/max et éventuellement une formule. Un graphique s'affiche dès qu'une formule est renseignée.
                            </p>
                            <div
                                v-for="ent in form.entities"
                                :key="ent.entity"
                                class="collapse collapse-arrow border border-base-300 bg-base-200/30 rounded-lg mb-3"
                            >
                                <input type="checkbox" />
                                <div class="collapse-title font-medium">
                                    {{ entityLabels[ent.entity] || ent.entity }}
                                </div>
                                <div class="collapse-content">
                                    <div class="grid gap-4 sm:grid-cols-2 pt-2">
                                        <InputField v-model="ent.db_column" label="Colonne BDD" name="db_column" />
                                        <InputField v-model="ent.min" label="Min" type="number" />
                                        <InputField v-model="ent.max" label="Max" type="number" />
                                        <div class="sm:col-span-2">
                                            <label class="label"><span class="label-text">Formule</span></label>
                                            <textarea
                                                v-model="ent.formula"
                                                class="textarea textarea-bordered w-full font-mono text-sm"
                                                rows="2"
                                                placeholder="ex: [level]*2 ou table par niveau"
                                            />
                                            <p v-if="ent.formula" class="mt-2 text-xs text-base-content/60">Le graphique sera disponible après enregistrement.</p>
                                        </div>
                                        <InputField v-model="ent.formula_display" label="Formule (affichage)" class="sm:col-span-2" />
                                        <InputField v-model="ent.default_value" label="Valeur par défaut" />
                                    </div>
                                </div>
                            </div>
                    <div class="flex justify-end gap-2">
                        <Link :href="route('admin.characteristics.index')" class="btn btn-ghost">Annuler</Link>
                        <Btn type="submit" color="primary" :disabled="form.processing">Créer</Btn>
                    </div>
                        </div>
                    </div>
                </form>
            </template>

            <!-- Mode édition : caractéristique existante -->
            <template v-else-if="selected">
                <h1 class="mb-2 text-2xl font-bold flex items-center gap-2" :style="form.color ? { borderLeftColor: `var(--color-${form.color})` } : {}" :class="form.color ? 'pl-3 border-l-4' : ''">
                    {{ selected.name || selected.id }}
                    <span v-if="selected.group" class="badge badge-sm badge-ghost">{{ groupLabels[selected.group] || selected.group }}</span>
                </h1>
                <p class="mb-6 text-sm text-base-content/70">
                    Modifiez les champs puis cliquez sur « Enregistrer ». Les paramètres sont organisés par groupe d'entités ; un graphique apparaît pour chaque formule.
                </p>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Panel Général -->
                    <section class="space-y-4">
                        <h2 class="text-xl font-semibold text-base-content">Général</h2>
                    <!-- Définition -->
                    <div class="card bg-base-100 shadow bg-color-campaign-100">
                        <div class="card-body">
                            <h3 class="card-title text-lg">Définition</h3>
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

                    <!-- Général : Limite et défaut (entité * uniquement) -->
                    <div class="space-y-4" v-if="generalEntityRow()">
                                <div class="space-y-4 pt-2">
                    <div v-for="ent in (generalEntityRow() ? [generalEntityRow()] : [])" :key="ent.entity" class="card bg-base-100 shadow border border-base-200 bg-base-200">
                        <div class="card-body">
                            <h3 class="card-title text-lg">Limite et défaut</h3>
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
                                                        v-for="c in characteristicsForFormulaOptions"
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
                                            <FormulaChart
                                                :points="getFormulaPoints(selected.id, ent.entity)"
                                                :x-label="isFormulaTable(ent.formula) ? (getDecodedFormula(ent).characteristic || 'variable') : 'level'"
                                                y-label="Résultat"
                                            />
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
                                </div>
                    </div>

                    <!-- Conversion : une seule config pour tout le groupe -->
                    <div v-if="generalEntityRow()" class="card bg-base-100 shadow bg-color-neutral-100">
                        <div class="card-body">
                            <h3 class="card-title text-lg">Conversion</h3>
                            <p class="text-sm text-base-content/70">
                                Formules utilisées lors du scrapping pour convertir les valeurs DofusDB en valeurs JDR. Variable <code class="rounded bg-base-300 px-1">[d]</code> = valeur Dofus, <code class="rounded bg-base-300 px-1">[level]</code> = niveau JDR (pour la vie). Une formule différente par type d’entité (monstre, classe, équipement).
                            </p>
                            <div v-if="generalEntityRow()" class="space-y-4 rounded-lg border border-base-300 bg-base-200/30 p-4">
                                <div>
                                    <label class="label"><span class="label-text">Formule ou table par valeur (tout le groupe)</span></label>
                                    <div class="mb-2 flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="!isFormulaTable(generalEntityRow().conversion_formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setConversionFormulaAsSimple(generalEntityRow())"
                                        >
                                            Formule simple
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm"
                                            :class="isFormulaTable(generalEntityRow().conversion_formula) ? 'btn-primary' : 'btn-ghost'"
                                            @click="setConversionFormulaAsTable(generalEntityRow())"
                                        >
                                            Table par caractéristique
                                        </button>
                                    </div>
                                    <template v-if="!isFormulaTable(generalEntityRow().conversion_formula)">
                                        <textarea
                                            v-model="generalEntityRow().conversion_formula"
                                            class="textarea textarea-bordered w-full font-mono text-sm"
                                            rows="2"
                                            placeholder="ex: [d]/10 ou floor([d]/200)+[level]*5"
                                            @focus="loadConversionFormulaPreview(selected.id, '*', generalEntityRow().conversion_formula)"
                                        />
                                        <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), + - * /</p>
                                    </template>
                                    <template v-else>
                                        <div class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                            <div>
                                                <label class="label label-text text-xs">Dépend de</label>
                                                <select
                                                    :value="getDecodedConversionFormula(generalEntityRow()).characteristic"
                                                    class="select select-bordered select-sm w-full max-w-xs"
                                                    @change="setConversionTableCharacteristic(generalEntityRow(), $event.target.value)"
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
                                                    <button type="button" class="btn btn-ghost btn-xs" @click="addConversionTableRow(generalEntityRow())">Ajouter</button>
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
                                                            <tr v-for="(row, idx) in getDecodedConversionFormula(generalEntityRow()).entries" :key="idx">
                                                                <td>
                                                                    <input
                                                                        type="number"
                                                                        class="input input-bordered input-xs w-20"
                                                                        :value="row.from"
                                                                        @input="updateConversionTableEntry(generalEntityRow(), idx, 'from', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <input
                                                                        type="text"
                                                                        class="input input-bordered input-xs w-full font-mono"
                                                                        :value="row.value"
                                                                        placeholder="0 ou [d]/10"
                                                                        @input="updateConversionTableEntry(generalEntityRow(), idx, 'value', $event.target.value)"
                                                                    />
                                                                </td>
                                                                <td>
                                                                    <button
                                                                        type="button"
                                                                        class="btn btn-ghost btn-xs btn-square"
                                                                        aria-label="Supprimer"
                                                                        @click="removeConversionTableRow(generalEntityRow(), idx)"
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
                                    <div v-if="generalEntityRow().conversion_formula" class="mt-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="text-sm font-medium">Aperçu (d Dofus → k JDR)</span>
                                            <button
                                                type="button"
                                                class="btn btn-ghost btn-xs"
                                                @click="loadConversionFormulaPreview(selected.id, '*', generalEntityRow().conversion_formula)"
                                            >
                                                Actualiser
                                            </button>
                                        </div>
                                        <div v-if="conversionFormulaLoading[getConversionFormulaChartKey(selected.id, '*')]" class="flex h-32 items-center justify-center text-sm text-base-content/60">
                                            Chargement…
                                        </div>
                                        <div v-else class="h-32 w-full">
                                            <FormulaChart
                                                :points="getConversionFormulaPoints(selected.id, '*')"
                                                x-label="d (Dofus)"
                                                y-label="k (JDR)"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule (affichage)</span></label>
                                    <input
                                        v-model="generalEntityRow().formula_display"
                                        type="text"
                                        class="input input-bordered w-full"
                                        placeholder="ex: k = d / 10"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Version lisible affichée à l’utilisateur.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panneaux entité spécifique (sous Conversion) -->
                    <div v-for="entityKey in selectedEntityOverrides" :key="entityKey" class="card bg-base-100 shadow border border-base-200 mt-4" :class="entityBgClasses[entityKey]">
                        <div class="card-body" v-if="entityRow(entityKey)">
                            <div class="flex justify-between items-center flex-wrap gap-2">
                                <h2 class="card-title text-lg">{{ entityLabels[entityKey] || entityKey }}</h2>
                                <button type="button" class="btn btn-ghost btn-sm" @click="removeEntityOverride(entityKey)">Retirer ce panneau</button>
                            </div>
                            <p class="text-sm text-base-content/70 mb-4">Surcharge des paramètres et de la conversion pour cette entité uniquement.</p>

                            <!-- Limite et défaut (même interface que Général) -->
                            <div class="space-y-4 mb-6">
                                <h3 class="text-sm font-semibold text-base-content/80">Limite et défaut</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <InputField v-model="entityRow(entityKey).min" label="Min" type="number" />
                                    <InputField v-model="entityRow(entityKey).max" label="Max" type="number" />
                                </div>
                                <div>
                                    <label class="label"><span class="label-text">Formule ou table par caractéristique</span></label>
                                    <div class="flex gap-2 mb-2">
                                        <button type="button" class="btn btn-sm" :class="!isFormulaTable(entityRow(entityKey).formula) ? 'btn-primary' : 'btn-ghost'" @click="setFormulaAsSimple(entityRow(entityKey))">Formule simple</button>
                                        <button type="button" class="btn btn-sm" :class="isFormulaTable(entityRow(entityKey).formula) ? 'btn-primary' : 'btn-ghost'" @click="setFormulaAsTable(entityRow(entityKey))">Table par caractéristique</button>
                                    </div>
                                    <template v-if="!isFormulaTable(entityRow(entityKey).formula)">
                                        <textarea v-model="entityRow(entityKey).formula" class="textarea textarea-bordered w-full font-mono text-sm" rows="2" placeholder="ex: [level]*2" />
                                        <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), + - * /</p>
                                    </template>
                                    <template v-else>
                                        <div class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                            <div>
                                                <label class="label label-text text-xs">Dépend de</label>
                                                <select :value="getDecodedFormula(entityRow(entityKey)).characteristic" class="select select-bordered select-sm w-full max-w-xs" @change="setTableCharacteristic(entityRow(entityKey), $event.target.value)">
                                                    <option v-for="c in characteristicsForFormulaOptions" :key="c.id" :value="c.id">{{ c.name || c.id }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="label-text text-xs">À partir de (valeur) → résultat</span>
                                                    <button type="button" class="btn btn-ghost btn-xs" @click="addTableRow(entityRow(entityKey))">Ajouter</button>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="table table-xs">
                                                        <thead><tr><th>À partir de</th><th>Valeur (fixe ou formule)</th><th></th></tr></thead>
                                                        <tbody>
                                                            <tr v-for="(row, idx) in getDecodedFormula(entityRow(entityKey)).entries" :key="idx">
                                                                <td><input type="number" class="input input-bordered input-xs w-20" :value="row.from" @input="updateTableEntry(entityRow(entityKey), idx, 'from', $event.target.value)" /></td>
                                                                <td><input type="text" class="input input-bordered input-xs w-full font-mono" :value="row.value" placeholder="0 ou [level]*2" @input="updateTableEntry(entityRow(entityKey), idx, 'value', $event.target.value)" /></td>
                                                                <td><button type="button" class="btn btn-ghost btn-xs btn-square" aria-label="Supprimer" @click="removeTableRow(entityRow(entityKey), idx)">×</button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div v-if="entityRow(entityKey).formula" class="mt-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                        <FormulaChart :points="getFormulaPoints(selected.id, entityKey)" :x-label="isFormulaTable(entityRow(entityKey).formula) ? (getDecodedFormula(entityRow(entityKey)).characteristic || 'variable') : 'level'" y-label="Résultat" />
                                    </div>
                                </div>
                                <InputField v-model="entityRow(entityKey).formula_display" label="Formule (affichage)" class="sm:col-span-2" />
                                <InputField v-model="entityRow(entityKey).default_value" label="Valeur par défaut" />
                            </div>

                            <!-- Conversion (même interface que Général) -->
                            <div class="pt-4 border-t border-base-300">
                                <h3 class="text-sm font-semibold text-base-content/80 mb-2">Conversion (surcharge pour cette entité)</h3>
                                <p class="text-xs text-base-content/70 mb-2">Si renseigné, remplace la conversion du groupe.</p>
                                <div>
                                    <label class="label"><span class="label-text">Formule ou table par valeur</span></label>
                                    <div class="mb-2 flex gap-2">
                                        <button type="button" class="btn btn-sm" :class="!isFormulaTable(entityRow(entityKey).conversion_formula) ? 'btn-primary' : 'btn-ghost'" @click="setConversionFormulaAsSimple(entityRow(entityKey))">Formule simple</button>
                                        <button type="button" class="btn btn-sm" :class="isFormulaTable(entityRow(entityKey).conversion_formula) ? 'btn-primary' : 'btn-ghost'" @click="setConversionFormulaAsTable(entityRow(entityKey))">Table par caractéristique</button>
                                    </div>
                                    <template v-if="!isFormulaTable(entityRow(entityKey).conversion_formula)">
                                        <textarea v-model="entityRow(entityKey).conversion_formula" class="textarea textarea-bordered w-full font-mono text-sm" rows="2" placeholder="ex: [d]/10 (vide = même que le groupe)" />
                                        <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), + - * /</p>
                                    </template>
                                    <template v-else>
                                        <div class="space-y-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                            <div>
                                                <label class="label label-text text-xs">Dépend de</label>
                                                <select :value="getDecodedConversionFormula(entityRow(entityKey)).characteristic" class="select select-bordered select-sm w-full max-w-xs" @change="setConversionTableCharacteristic(entityRow(entityKey), $event.target.value)">
                                                    <option v-for="opt in conversionTableCharacteristicOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                                </select>
                                            </div>
                                            <div>
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="label-text text-xs">À partir de (valeur) → résultat</span>
                                                    <button type="button" class="btn btn-ghost btn-xs" @click="addConversionTableRow(entityRow(entityKey))">Ajouter</button>
                                                </div>
                                                <div class="overflow-x-auto">
                                                    <table class="table table-xs">
                                                        <thead><tr><th>À partir de</th><th>Valeur (fixe ou formule)</th><th></th></tr></thead>
                                                        <tbody>
                                                            <tr v-for="(row, idx) in getDecodedConversionFormula(entityRow(entityKey)).entries" :key="idx">
                                                                <td><input type="number" class="input input-bordered input-xs w-20" :value="row.from" @input="updateConversionTableEntry(entityRow(entityKey), idx, 'from', $event.target.value)" /></td>
                                                                <td><input type="text" class="input input-bordered input-xs w-full font-mono" :value="row.value" placeholder="0 ou [d]/10" @input="updateConversionTableEntry(entityRow(entityKey), idx, 'value', $event.target.value)" /></td>
                                                                <td><button type="button" class="btn btn-ghost btn-xs btn-square" aria-label="Supprimer" @click="removeConversionTableRow(entityRow(entityKey), idx)">×</button></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div v-if="entityRow(entityKey).conversion_formula" class="mt-3 rounded-lg border border-base-300 bg-base-200/30 p-3">
                                        <FormulaChart :points="getConversionFormulaPoints(selected.id, entityKey)" x-label="d (Dofus)" y-label="k (JDR)" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </section>

                    <div class="flex flex-wrap items-center justify-end gap-2 mt-6">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="label-text">Spécifier pour une entité :</span>
                            <select class="select select-bordered select-sm max-w-xs" @change="(e) => { const v = e.target.value; if (v) { addEntityOverride(v); e.target.value = ''; } }">
                                <option value="">— Choisir —</option>
                                <option v-for="ek in entitiesForSpecifyDropdown.filter(e => !selectedEntityOverrides.includes(e))" :key="ek" :value="ek">{{ entityLabels[ek] || ek }}</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-ghost btn-error" @click="confirmDelete">Supprimer</button>
                        <Btn type="submit" color="primary" :disabled="form.processing">Enregistrer</Btn>
                    </div>
                </form>
            </template>
            <div v-else class="flex h-64 flex-col items-center justify-center gap-2 text-base-content/60">
                <p>Sélectionnez une caractéristique dans la liste à gauche pour l'éditer.</p>
                <p class="text-sm">Ou cliquez sur « Créer une caractéristique » pour en ajouter une.</p>
            </div>
        </main>

    </div>
</template>
