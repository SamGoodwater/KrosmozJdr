<script setup>
/**
 * Admin Caractéristiques — Liste à gauche, panneau d'édition à droite.
 * Accès : admin et super_admin.
 * Pour chaque champ formule : graphique (variable entre min et max).
 */
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import Main from '@/Pages/Layouts/Main.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import ColorCore from '@/Pages/Atoms/data-input/ColorCore.vue';
import FormulaOrTableField from '@/Pages/Molecules/data-input/FormulaOrTableField.vue';
import FormulaOrTableFieldWithChart from '@/Pages/Organismes/data-input/FormulaOrTableFieldWithChart.vue';
import axios from 'axios';

const { setPageTitle } = usePageTitle();

/** Contenu d’aide pour les champs formule (affiché dans le popover « ? »). */
const formulaHelpContent = {
    title: 'Construire une formule',
    variables: 'Variables : [id] avec id = level, vitality, d, etc. Selon le contexte (calcul ou conversion Dofus).',
    operators: 'Opérateurs : + - * / et parenthèses ( ).',
    funcs1: 'Fonctions à 1 argument : floor, ceil, round, sqrt, abs, cos, sin, tan, asin, acos, atan.',
    funcs2: 'Fonctions à 2 arguments : pow(base, exp), min(a, b), max(a, b).',
    examples: 'Exemples : [level]*2, floor([d]/10), sqrt([vitality]), pow(2,[level]), min(10, [level]*2).',
};

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

/** Popover aide formules : rendu dans body pour passer au-dessus du menu (Teleport). null = fermé, sinon { left, top } en px. */
const formulaHelpAnchor = ref(null);
const formulaHelpPopoverRef = ref(null);

/** Proposition de formule de conversion (affichée jusqu'à validation ou annulation). */
const conversionSuggestionFormula = ref(null);
const conversionSuggestionR2 = ref(null);
const conversionSuggestionLoading = ref(false);
const conversionSuggestionError = ref(null);
/** Entité concernée par la suggestion en cours ('*' = groupe, sinon clé d'entité). */
const conversionSuggestionForEntity = ref(null);

/** Ligne entity pour le groupe (*) ou une entité spécifique. */
function getConversionRow(entityKey) {
    return entityKey === '*' ? generalEntityRow() : entityRow(entityKey);
}

/** Demande une formule suggérée (table, linéaire, carré, carré décalé). Utilise les lignes du tableau (paires d, k). */
function requestConversionSuggestion(curveType, entityKey = '*') {
    const row = getConversionRow(entityKey);
    if (!row) return;
    const sampleRows = row.conversion_sample_rows ?? [];
    const pairs = sampleRows
        .filter((r) => r.dofus_value !== '' && r.dofus_value != null && r.krosmoz_value !== '' && r.krosmoz_value != null)
        .filter((r) => !Number.isNaN(Number(r.dofus_value)) && !Number.isNaN(Number(r.krosmoz_value)))
        .map((r) => ({ d: Number(r.dofus_value), k: Number(r.krosmoz_value) }));
    if (pairs.length < 2) {
        conversionSuggestionError.value = 'Renseignez au moins 2 lignes avec des valeurs Dofus et Krosmoz pour proposer une formule.';
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionForEntity.value = null;
        return;
    }
    conversionSuggestionError.value = null;
    conversionSuggestionFormula.value = null;
    conversionSuggestionR2.value = null;
    conversionSuggestionForEntity.value = entityKey;
    conversionSuggestionLoading.value = true;
    const suggestUrl = typeof route === 'function' ? (() => { try { return route('admin.characteristics.suggest-conversion-formula'); } catch { return '/admin/characteristics/suggest-conversion-formula'; } })() : '/admin/characteristics/suggest-conversion-formula';
    axios
        .post(suggestUrl, {
            pairs,
            curve_type: curveType,
        })
        .then((res) => {
            conversionSuggestionFormula.value = res.data.formula ?? null;
            conversionSuggestionR2.value = res.data.r2 ?? null;
            conversionSuggestionLoading.value = false;
        })
        .catch((err) => {
            conversionSuggestionError.value = err.response?.data?.message ?? err.message ?? 'Erreur lors de la suggestion';
            conversionSuggestionFormula.value = null;
            conversionSuggestionR2.value = null;
            conversionSuggestionLoading.value = false;
        });
}

/** Ajoute une ligne au tableau d'échantillons (groupe ou entité spécifique). */
function addConversionSampleRow(entityKey = '*') {
    const row = getConversionRow(entityKey);
    if (!row) return;
    const rows = Array.isArray(row.conversion_sample_rows) ? [...row.conversion_sample_rows] : getDefaultConversionSampleRows();
    const last = rows[rows.length - 1];
    const nextDofus = last ? Number(last.dofus_level) + 40 : 200;
    const nextKrosmoz = last ? Number(last.krosmoz_level) + 4 : 20;
    rows.push({ dofus_level: nextDofus, dofus_value: '', krosmoz_level: nextKrosmoz, krosmoz_value: '' });
    row.conversion_sample_rows = rows;
}

/** Supprime une ligne du tableau d'échantillons. Minimum 1 ligne. */
function removeConversionSampleRow(entityKey, index) {
    if (entityKey === undefined) {
        entityKey = '*';
    }
    const row = getConversionRow(entityKey);
    if (!row || !Array.isArray(row.conversion_sample_rows) || row.conversion_sample_rows.length <= 1) return;
    row.conversion_sample_rows = row.conversion_sample_rows.filter((_, i) => i !== index);
}

/** Valide la formule suggérée : remplace la valeur du champ conversion pour l'entité concernée et efface la proposition. */
function applyConversionSuggestion() {
    const entityKey = conversionSuggestionForEntity.value ?? '*';
    const row = getConversionRow(entityKey);
    if (row && conversionSuggestionFormula.value) {
        row.conversion_formula = conversionSuggestionFormula.value;
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionForEntity.value = null;
    }
}

/** Annule la proposition (sans modifier le champ). */
function clearConversionSuggestion() {
    conversionSuggestionFormula.value = null;
    conversionSuggestionR2.value = null;
    conversionSuggestionError.value = null;
    conversionSuggestionForEntity.value = null;
}

function openFormulaHelp(e) {
    const rect = e.currentTarget.getBoundingClientRect();
    formulaHelpAnchor.value = { left: rect.left, top: rect.bottom + 4 };
    nextTick(() => {
        const close = (ev) => {
            if (formulaHelpPopoverRef.value?.contains(ev.target)) return;
            formulaHelpAnchor.value = null;
            document.removeEventListener('click', close);
        };
        document.addEventListener('click', close);
    });
}

defineOptions({ layout: Main });
setPageTitle('Administration des caractéristiques');

/** Lignes par défaut pour le tableau Dofus/Krosmoz (6 paires niveau/valeur). */
function getDefaultConversionSampleRows() {
    const dofusLevels = [1, 40, 80, 120, 160, 200];
    const krosmozLevels = [1, 4, 8, 12, 16, 20];
    return dofusLevels.map((dofusLevel, i) => ({
        dofus_level: dofusLevel,
        dofus_value: '',
        krosmoz_level: krosmozLevels[i] ?? i + 1,
        krosmoz_value: '',
    }));
}

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
            entities: [],
            conversion_formulas: defaultConversionFormulasForGroup(entitiesByGroup ?? {}, 'creature'),
        };
    }
    const group = selected.group ?? 'creature';
    const allowedEntityKeys = entitiesByGroup?.[group] ?? ['*', 'monster', 'class', 'npc'];
    const entitiesForGroup = (selected.entities ?? []).filter((e) => allowedEntityKeys.includes(e.entity));
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
        helper: selected.helper ?? '',
        icon: selected.icon ?? '',
        color: selected.color ?? '',
        type: selected.type ?? 'int',
        unit: selected.unit ?? '',
        sort_order: selected.sort_order ?? 0,
        entities: entitiesForGroup.map((e) => ({
            entity: e.entity,
            db_column: e.db_column ?? '',
            min: e.min ?? '',
            max: e.max ?? '',
            formula: e.formula ?? '',
            formula_display: e.formula_display ?? '',
            default_value: e.default_value ?? '',
            forgemagie_allowed: e.forgemagie_allowed ?? false,
            forgemagie_max: e.forgemagie_max ?? 0,
            base_price_per_unit: e.base_price_per_unit ?? '',
            rune_price_per_unit: e.rune_price_per_unit ?? '',
            conversion_formula: e.conversion_formula ?? '',
            conversion_dofus_sample: e.conversion_dofus_sample ?? null,
            conversion_krosmoz_sample: e.conversion_krosmoz_sample ?? null,
            conversion_sample_rows: (e.conversion_sample_rows && e.conversion_sample_rows.length) ? e.conversion_sample_rows : getDefaultConversionSampleRows(),
        })),
        conversion_formulas: conversionFormulas,
    };
}

const form = useForm(buildFormData(props.selected, props.entitiesByGroup));

watch(
    () => props.selected,
    (s) => {
        const data = buildFormData(s, props.entitiesByGroup);
        if (data.key !== undefined) form.key = data.key;
        if (data.group !== undefined) form.group = data.group;
        form.name = data.name;
        form.short_name = data.short_name;
        form.description = data.description;
        form.helper = data.helper;
        form.icon = data.icon;
        form.color = data.color;
        form.type = data.type;
        form.unit = data.unit;
        form.sort_order = data.sort_order;
        form.entities = data.entities;
        form.conversion_formulas = data.conversion_formulas;
        // Réhydrater les panneaux « spécifique entité » à partir des clés renvoyées par le serveur (ex. après enregistrement)
        selectedEntityOverrides.value = Array.isArray(s?.entity_override_keys) ? [...s.entity_override_keys] : [];
        // Réinitialiser la proposition de formule quand on change de caractéristique
        conversionSuggestionFormula.value = null;
        conversionSuggestionR2.value = null;
        conversionSuggestionError.value = null;
        conversionSuggestionForEntity.value = null;
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
            forgemagie_allowed: false,
            forgemagie_max: 0,
            base_price_per_unit: '',
            rune_price_per_unit: '',
            conversion_formula: '',
            conversion_dofus_sample: null,
            conversion_krosmoz_sample: null,
            conversion_sample_rows: getDefaultConversionSampleRows(),
        };
        form.entities = [...(form.entities ?? []), defaultRow];
    }
}
function removeEntityOverride(entityKey) {
    const label = entityLabels[entityKey] || entityKey;
    if (!confirm(`Supprimer la spécificité pour ${label} ? Les paramètres spécifiques à cette entité seront perdus.`)) {
        return;
    }
    selectedEntityOverrides.value = selectedEntityOverrides.value.filter((e) => e !== entityKey);
    form.entities = (form.entities ?? []).filter((e) => e.entity !== entityKey);
    form.patch(route('admin.characteristics.update', props.selected.id));
}
function confirmDelete() {
    if (props.selected?.id && confirm('Supprimer cette caractéristique ? Les données associées seront perdues.')) {
        router.delete(route('admin.characteristics.destroy', props.selected.id));
    }
}

// En mode création : n'initialiser qu'avec la ligne par défaut (*). Les spécificités (monster, class, etc.) s'ajoutent via « Spécifier pour une entité ».
watch(
    () => [props.createMode, props.entitiesTemplate, props.entitiesByGroup, form.group],
    () => {
        if (props.createMode && form.group) {
            if (props.entitiesTemplate) {
                const template = props.entitiesTemplate[form.group];
                if (Array.isArray(template) && template.length) {
                    const defaultRow = template.find((e) => (e.entity ?? '*') === '*') ?? template[0];
                    form.entities = [
                        {
                            entity: defaultRow.entity ?? '*',
                            db_column: defaultRow.db_column ?? '',
                            min: defaultRow.min ?? '',
                            max: defaultRow.max ?? '',
                            formula: defaultRow.formula ?? '',
                            formula_display: defaultRow.formula_display ?? '',
                            default_value: defaultRow.default_value ?? '',
                            forgemagie_allowed: defaultRow.forgemagie_allowed ?? false,
                            forgemagie_max: defaultRow.forgemagie_max ?? 0,
                            base_price_per_unit: defaultRow.base_price_per_unit ?? '',
                            rune_price_per_unit: defaultRow.rune_price_per_unit ?? '',
                            conversion_formula: defaultRow.conversion_formula ?? '',
                        },
                    ];
                }
            }
            // Conversion : uniquement les entités du groupe (hors '*')
            form.conversion_formulas = defaultConversionFormulasForGroup(props.entitiesByGroup ?? {}, form.group);
        }
    },
    { immediate: true }
);

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

/** Caractéristiques pour les tables min/max : en création utilise form.group, en édition selected.group. */
const limitCharacteristicOptions = computed(() => {
    const group = props.selected?.group ?? form.group;
    if (!group) return [];
    return props.characteristicsByGroup?.[group] ?? [];
});

/** Clé finale en création : ajoute le suffixe du groupe si absent (ex. life_dice → life_dice_creature). */
const normalizedCreateKey = computed(() => {
    const key = (form.key ?? '').trim();
    const group = form.group ?? 'creature';
    if (!key) return '';
    const suffix = `_${group}`;
    if (key.length >= suffix.length && key.endsWith(suffix)) return key;
    return key + suffix;
});

/** Options pour la table de conversion : d (valeur Dofus), level (niveau JDR). */
const conversionTableCharacteristicOptions = [
    { id: 'd', name: 'Valeur Dofus (d)' },
    { id: 'level', name: 'Niveau JDR (level)' },
];

/** Retourne la valeur CSS pour afficher la couleur (hex ou ancien nom Tailwind). */
function displayColor(val) {
    if (!val) return null;
    if (typeof val === 'string' && val.startsWith('#')) return val;
    return `var(--color-${val})`;
}

/** Valeur pour le color picker (doit être un hex valide). */
const defaultHexForPicker = '#808080';
function colorForPicker(hex) {
    if (!hex || typeof hex !== 'string') return defaultHexForPicker;
    if (/^#([0-9A-Fa-f]{3}){1,2}$/.test(hex)) return hex;
    return defaultHexForPicker;
}

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
                                :style="displayColor(c.color) ? { borderLeftColor: displayColor(c.color) } : {}"
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
                                <span v-if="!c.icon && displayColor(c.color)" class="h-2.5 w-2.5 shrink-0 rounded-full" :style="{ backgroundColor: displayColor(c.color) }" />
                                <span class="min-w-0 flex flex-col">
                                    <span class="truncate">{{ c.name || c.id }}</span>
                                    <span
                                        class="truncate text-xs italic opacity-70"
                                        :class="selected?.id === c.id ? 'text-primary-content/80' : 'text-base-content/60'"
                                        :title="`Dans les formules : [${c.id}]`"
                                    >[{{ c.id }}]</span>
                                </span>
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
                                    helper="Lettres minuscules, chiffres et underscores. Si vous omettez le suffixe (_creature, _object, _spell), il sera ajouté automatiquement selon le groupe choisi (ex. life_dice → life_dice_creature)."
                                    required
                                />
                                <p v-if="createMode && form.key && form.group" class="text-xs text-base-content/60 mt-1">
                                    Clé enregistrée : <span class="font-mono">{{ normalizedCreateKey }}</span>
                                </p>
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
                                    <label class="label"><span class="label-text">Couleur</span></label>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input
                                            v-model="form.color"
                                            type="text"
                                            name="color"
                                            class="input input-bordered w-28 font-mono"
                                            placeholder="#000000"
                                            maxlength="7"
                                        />
                                        <ColorCore
                                            :model-value="colorForPicker(form.color)"
                                            @update:model-value="form.color = $event"
                                            class="input-primary"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-base-content/70">Code hexadécimal (ex. #3b82f6).</p>
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
                                        <div class="sm:col-span-2">
                                            <FormulaOrTableField
                                                :model-value="ent.min"
                                                @update:model-value="(v) => (ent.min = v)"
                                                :characteristic-options="limitCharacteristicOptions"
                                                label="Min"
                                                placeholder="ex: 0, 1 ou [level]*2"
                                            />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <FormulaOrTableField
                                                :model-value="ent.max"
                                                @update:model-value="(v) => (ent.max = v)"
                                                :characteristic-options="limitCharacteristicOptions"
                                                label="Max"
                                                placeholder="ex: 100, 200 ou [level]*10"
                                            />
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="label">
                                                <span class="label-text flex items-center gap-1">
                                                    Formule
                                                    <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                                </span>
                                            </label>
                                            <FormulaOrTableField
                                                :model-value="ent.formula"
                                                @update:model-value="(v) => (ent.formula = v)"
                                                :characteristic-options="limitCharacteristicOptions"
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
                <h1 class="mb-1 text-2xl font-bold flex items-center gap-2" :style="displayColor(form.color) ? { borderLeftColor: displayColor(form.color) } : {}" :class="displayColor(form.color) ? 'pl-3 border-l-4' : ''">
                    {{ selected.name || selected.id }}
                    <span v-if="selected.group" class="badge badge-sm badge-ghost">{{ groupLabels[selected.group] || selected.group }}</span>
                </h1>
                <p class="mb-1 text-sm italic text-base-content/60" :title="`Clé utilisée dans les formules (non modifiable)`">
                    Clé formule : <code class="rounded bg-base-200 px-1 font-mono">[{{ selected.id }}]</code>
                </p>
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
                                    <label class="label"><span class="label-text">Couleur</span></label>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <input
                                            v-model="form.color"
                                            type="text"
                                            name="color"
                                            class="input input-bordered w-28 font-mono"
                                            placeholder="#000000"
                                            maxlength="7"
                                        />
                                        <ColorCore
                                            :model-value="colorForPicker(form.color)"
                                            @update:model-value="form.color = $event"
                                            class="input-primary"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-base-content/70">Code hexadécimal (ex. #3b82f6). Utilisée pour les badges, graphiques et indicateurs.</p>
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
                                <div class="sm:col-span-2">
                                    <FormulaOrTableField
                                        :model-value="ent.min"
                                        @update:model-value="(v) => (ent.min = v)"
                                        :characteristic-options="limitCharacteristicOptions"
                                        label="Min (valeur fixe, formule ou table)"
                                        placeholder="ex: 0, 1 ou [level]*2"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <FormulaOrTableField
                                        :model-value="ent.max"
                                        @update:model-value="(v) => (ent.max = v)"
                                        :characteristic-options="limitCharacteristicOptions"
                                        label="Max (valeur fixe, formule ou table)"
                                        placeholder="ex: 100 ou [level]*10"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par caractéristique
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="ent.formula"
                                        @update:model-value="(v) => (ent.formula = v)"
                                        :characteristic-options="characteristicsForFormulaOptions"
                                        placeholder="ex: [vitality]*10+[level]*2"
                                        :preview="{ characteristicKey: selected.id, entity: ent.entity, variable: 'level', mode: 'formula' }"
                                        chart-y-label="Résultat"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), round(), sqrt(), pow(), min(), max(), cos/sin/tan, etc. + - * /</p>
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
                                <!-- Forgemagie et prix : uniquement pour l’équipement (item) -->
                                <template v-if="selected?.group === 'object'">
                                    <div class="sm:col-span-2 border-t border-base-300 pt-4 mt-2">
                                        <h3 class="mb-3 text-sm font-semibold text-base-content/80">Prix / unité et forgemagie (groupe objet)</h3>
                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center gap-2">
                                                    <input v-model="ent.forgemagie_allowed" type="checkbox" class="checkbox" />
                                                    <span>Forgemagie possible</span>
                                                </div>
                                                <InputField v-model="ent.forgemagie_max" label="Max forgemagie" type="number" />
                                                <p class="text-xs text-base-content/70">Valeur maximale ajoutable par forgemagie pour cette caractéristique.</p>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <InputField
                                                    v-model="ent.base_price_per_unit"
                                                    label="Prix par unité (kamas)"
                                                    type="number"
                                                    step="0.01"
                                                    helper="Prix de base par point de bonus (création équipement)."
                                                />
                                                <InputField
                                                    v-model="ent.rune_price_per_unit"
                                                    label="Prix rune par unité (kamas)"
                                                    helper="Prix de la rune de forgemagie par unité."
                                                    type="number"
                                                    step="0.01"
                                                />
                                                <p class="text-xs text-base-content/70">Prix base et rune pour création équipement et forgemagie (optionnel).</p>
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
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par valeur (tout le groupe)
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="generalEntityRow().conversion_formula"
                                        @update:model-value="(v) => (generalEntityRow().conversion_formula = v)"
                                        :characteristic-options="conversionTableCharacteristicOptions"
                                        placeholder="ex: [d]/10 ou floor([d]/200)+[level]*5"
                                        :preview="{ characteristicKey: selected.id, entity: '*', mode: 'conversion' }"
                                        chart-x-label="d (Dofus)"
                                        chart-y-label="k (JDR)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                </div>

                                <!-- Échantillons et proposition de formule (automatisation) -->
                                <div class="rounded-lg border border-base-300 bg-base-200/50 p-4 space-y-4">
                                    <h4 class="text-sm font-semibold text-base-content/80">Échantillons (automatisation des formules)</h4>
                                    <p class="text-xs text-base-content/70">Tableau : 2 colonnes Dofus (niveau, valeur) et 2 colonnes Krosmoz (niveau, valeur). Au moins 2 lignes renseignées pour proposer une formule. 6 points donnent en général un bon ajustement.</p>
                                    <div class="overflow-x-auto">
                                        <table class="table table-sm table-zebra">
                                            <thead>
                                                <tr>
                                                    <th class="bg-base-300">Dofus (niv.)</th>
                                                    <th class="bg-base-300">Dofus (valeur)</th>
                                                    <th class="bg-base-300">Krosmoz (niv.)</th>
                                                    <th class="bg-base-300">Krosmoz (valeur)</th>
                                                    <th class="bg-base-300 w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(sampleRow, idx) in (generalEntityRow()?.conversion_sample_rows || getDefaultConversionSampleRows())" :key="idx">
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.dofus_level"
                                                            type="number"
                                                            min="1"
                                                            class="input input-bordered input-sm w-20"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.dofus_value"
                                                            type="number"
                                                            step="any"
                                                            class="input input-bordered input-sm w-24"
                                                            placeholder="—"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.krosmoz_level"
                                                            type="number"
                                                            min="1"
                                                            class="input input-bordered input-sm w-20"
                                                        />
                                                    </td>
                                                    <td>
                                                        <input
                                                            v-model.number="sampleRow.krosmoz_value"
                                                            type="number"
                                                            step="any"
                                                            class="input input-bordered input-sm w-24"
                                                            placeholder="—"
                                                        />
                                                    </td>
                                                    <td>
                                                        <button
                                                            type="button"
                                                            class="btn btn-ghost btn-xs btn-error"
                                                            :disabled="(generalEntityRow()?.conversion_sample_rows?.length ?? 0) <= 1"
                                                            :aria-label="'Supprimer la ligne ' + (idx + 1)"
                                                            @click="removeConversionSampleRow('*', idx)"
                                                        >
                                                            −
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-ghost mt-2" @click="addConversionSampleRow">+ Ajouter une ligne</button>
                                    </div>
                                    <div>
                                        <span class="label-text block mb-2">Proposer une formule (à valider ensuite)</span>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('table', '*')">Table</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('linear', '*')">Linéaire</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('power', '*')">Carré</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('shifted_power', '*')">Carré décalé</button>
                                        </div>
                                        <p v-if="conversionSuggestionLoading && conversionSuggestionForEntity === '*'" class="mt-2 text-sm text-info">Calcul en cours…</p>
                                        <p v-else-if="conversionSuggestionError && conversionSuggestionForEntity === '*'" class="mt-2 text-sm text-error">{{ conversionSuggestionError }}</p>
                                        <div v-else-if="conversionSuggestionFormula && conversionSuggestionForEntity === '*'" class="mt-3 p-3 rounded-lg bg-base-300 border border-base-content/10">
                                            <p class="text-sm font-medium text-base-content/80 mb-1">Proposition :</p>
                                            <code class="block text-xs break-all mb-2">{{ conversionSuggestionFormula }}</code>
                                            <p v-if="conversionSuggestionR2 != null" class="text-xs text-base-content/70 mb-2">R² = {{ conversionSuggestionR2 }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" @click="applyConversionSuggestion">Valider (remplacer le champ)</button>
                                                <button type="button" class="btn btn-sm btn-ghost" @click="clearConversionSuggestion">Annuler</button>
                                            </div>
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
                                <button type="button" class="btn btn-ghost btn-sm btn-error" @click="removeEntityOverride(entityKey)">
                                    Supprimer la spécificité pour {{ entityLabels[entityKey] || entityKey }}
                                </button>
                            </div>
                            <p class="text-sm text-base-content/70 mb-4">Surcharge des paramètres et de la conversion pour cette entité uniquement.</p>

                            <!-- Limite et défaut (même interface que Général) -->
                            <div class="space-y-4 mb-6">
                                <h3 class="text-sm font-semibold text-base-content/80">Limite et défaut</h3>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <FormulaOrTableField
                                            :model-value="entityRow(entityKey).min"
                                            @update:model-value="(v) => (entityRow(entityKey).min = v)"
                                            :characteristic-options="limitCharacteristicOptions"
                                            label="Min"
                                            placeholder="ex: 0 ou [level]*2"
                                        />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <FormulaOrTableField
                                            :model-value="entityRow(entityKey).max"
                                            @update:model-value="(v) => (entityRow(entityKey).max = v)"
                                            :characteristic-options="limitCharacteristicOptions"
                                            label="Max"
                                            placeholder="ex: 100 ou [level]*10"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par caractéristique
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="entityRow(entityKey).formula"
                                        @update:model-value="(v) => (entityRow(entityKey).formula = v)"
                                        :characteristic-options="characteristicsForFormulaOptions"
                                        placeholder="ex: [level]*2"
                                        :preview="{ characteristicKey: selected.id, entity: entityKey, variable: 'level', mode: 'formula' }"
                                        chart-y-label="Résultat"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [id], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                </div>
                                <InputField v-model="entityRow(entityKey).formula_display" label="Formule (affichage)" class="sm:col-span-2" />
                                <InputField v-model="entityRow(entityKey).default_value" label="Valeur par défaut" />
                            </div>

                            <!-- Conversion (même interface que Général) -->
                            <div class="pt-4 border-t border-base-300">
                                <h3 class="text-sm font-semibold text-base-content/80 mb-2">Conversion (surcharge pour cette entité)</h3>
                                <p class="text-xs text-base-content/70 mb-2">Si renseigné, remplace la conversion du groupe.</p>
                                <div>
                                    <label class="label">
                                        <span class="label-text flex items-center gap-1">
                                            Formule ou table par valeur (surcharge)
                                            <button type="button" class="btn btn-circle btn-ghost btn-xs cursor-pointer" aria-label="Aide formules" @click.stop="openFormulaHelp">?</button>
                                        </span>
                                    </label>
                                    <FormulaOrTableFieldWithChart
                                        :model-value="entityRow(entityKey).conversion_formula"
                                        @update:model-value="(v) => (entityRow(entityKey).conversion_formula = v)"
                                        :characteristic-options="conversionTableCharacteristicOptions"
                                        placeholder="ex: [d]/10 (vide = même que le groupe)"
                                        :preview="{ characteristicKey: selected.id, entity: entityKey, mode: 'conversion' }"
                                        chart-x-label="d (Dofus)"
                                        chart-y-label="k (JDR)"
                                    />
                                    <p class="mt-1 text-xs text-base-content/70">Syntaxe : [d], [level], floor(), ceil(), round(), sqrt(), pow(), min(), max(), etc. + - * /</p>
                                </div>

                                <!-- Échantillons et proposition de formule (surcharge entité) -->
                                <div class="rounded-lg border border-base-300 bg-base-200/50 p-4 space-y-4 mt-4">
                                    <h4 class="text-sm font-semibold text-base-content/80">Échantillons (automatisation pour cette entité)</h4>
                                    <p class="text-xs text-base-content/70">Même tableau Dofus/Krosmoz que pour le groupe : renseignez les valeurs puis proposez une formule pour cette surcharge.</p>
                                    <div class="overflow-x-auto">
                                        <table class="table table-sm table-zebra">
                                            <thead>
                                                <tr>
                                                    <th class="bg-base-300">Dofus (niv.)</th>
                                                    <th class="bg-base-300">Dofus (valeur)</th>
                                                    <th class="bg-base-300">Krosmoz (niv.)</th>
                                                    <th class="bg-base-300">Krosmoz (valeur)</th>
                                                    <th class="bg-base-300 w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(sampleRow, idx) in (entityRow(entityKey)?.conversion_sample_rows || getDefaultConversionSampleRows())" :key="idx">
                                                    <td><input v-model.number="sampleRow.dofus_level" type="number" min="1" class="input input-bordered input-sm w-20" /></td>
                                                    <td><input v-model.number="sampleRow.dofus_value" type="number" step="any" class="input input-bordered input-sm w-24" placeholder="—" /></td>
                                                    <td><input v-model.number="sampleRow.krosmoz_level" type="number" min="1" class="input input-bordered input-sm w-20" /></td>
                                                    <td><input v-model.number="sampleRow.krosmoz_value" type="number" step="any" class="input input-bordered input-sm w-24" placeholder="—" /></td>
                                                    <td>
                                                        <button type="button" class="btn btn-ghost btn-xs btn-error" :disabled="(entityRow(entityKey)?.conversion_sample_rows?.length ?? 0) <= 1" :aria-label="'Supprimer la ligne ' + (idx + 1)" @click="removeConversionSampleRow(entityKey, idx)">−</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-sm btn-ghost mt-2" @click="addConversionSampleRow(entityKey)">+ Ajouter une ligne</button>
                                    </div>
                                    <div>
                                        <span class="label-text block mb-2">Proposer une formule (à valider ensuite)</span>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('table', entityKey)">Table</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('linear', entityKey)">Linéaire</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('power', entityKey)">Carré</button>
                                            <button type="button" class="btn btn-sm btn-outline" :disabled="conversionSuggestionLoading" @click="requestConversionSuggestion('shifted_power', entityKey)">Carré décalé</button>
                                        </div>
                                        <p v-if="conversionSuggestionLoading && conversionSuggestionForEntity === entityKey" class="mt-2 text-sm text-info">Calcul en cours…</p>
                                        <p v-else-if="conversionSuggestionError && conversionSuggestionForEntity === entityKey" class="mt-2 text-sm text-error">{{ conversionSuggestionError }}</p>
                                        <div v-else-if="conversionSuggestionFormula && conversionSuggestionForEntity === entityKey" class="mt-3 p-3 rounded-lg bg-base-300 border border-base-content/10">
                                            <p class="text-sm font-medium text-base-content/80 mb-1">Proposition :</p>
                                            <code class="block text-xs break-all mb-2">{{ conversionSuggestionFormula }}</code>
                                            <p v-if="conversionSuggestionR2 != null" class="text-xs text-base-content/70 mb-2">R² = {{ conversionSuggestionR2 }}</p>
                                            <div class="flex gap-2">
                                                <button type="button" class="btn btn-sm btn-primary" @click="applyConversionSuggestion">Valider (remplacer le champ)</button>
                                                <button type="button" class="btn btn-sm btn-ghost" @click="clearConversionSuggestion">Annuler</button>
                                            </div>
                                        </div>
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

        <!-- Popover aide formules : Teleport vers body pour passer au-dessus du menu latéral -->
        <Teleport to="body">
            <div
                v-if="formulaHelpAnchor"
                ref="formulaHelpPopoverRef"
                class="fixed z-[1100] rounded-box bg-base-200 p-4 shadow-xl border border-base-300 w-[90vw] max-w-[90vw] md:w-[60vw] md:max-w-[60vw] lg:w-96 lg:max-w-md min-w-0 overflow-hidden overflow-y-auto max-h-[85vh] box-border"
                :style="{ left: formulaHelpAnchor.left + 'px', top: formulaHelpAnchor.top + 'px' }"
            >
                <div class="w-full min-w-0 max-w-full overflow-hidden box-border [overflow-wrap:anywhere]">
                    <h4 class="font-semibold mb-2 w-full max-w-full">{{ formulaHelpContent.title }}</h4>
                    <ul class="text-sm space-y-1 list-disc list-inside w-full max-w-full min-w-0 [overflow-wrap:anywhere]">
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.variables }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.operators }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.funcs1 }}</li>
                        <li class="[overflow-wrap:anywhere]">{{ formulaHelpContent.funcs2 }}</li>
                    </ul>
                    <p class="text-sm mt-2 text-base-content/80 w-full max-w-full min-w-0 [overflow-wrap:anywhere]">{{ formulaHelpContent.examples }}</p>
                </div>
            </div>
        </Teleport>
    </div>
</template>
