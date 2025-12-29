<script setup>
/**
 * TanStackTableFilters Molecule
 *
 * @description
 * UI générique des filtres côté client (select/boolean/text) à partir de la config.
 * Les options peuvent venir de `filterOptions` (serveur) ou de la colonne (fallback).
 */

import ToggleCore from "@/Pages/Atoms/data-input/ToggleCore.vue";
import InputCore from "@/Pages/Atoms/data-input/InputCore.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import RadioCore from "@/Pages/Atoms/data-input/RadioCore.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import { computed, unref, ref } from "vue";

const props = defineProps({
    columns: { type: Array, required: true },
    filterValues: { type: Object, default: () => ({}) }, // { [filterId]: any }
    filterOptions: { type: Object, default: () => ({}) }, // { [filterId]: [{value,label}] }
    /**
     * Couleur UI (Design System) appliquée aux contrôles de filtres.
     */
    uiColor: { type: String, default: "primary" },
    /**
     * Debug (logs console).
     * Activer via `config.ui.debug` dans TanStackTable.
     */
    debug: { type: Boolean, default: false },
});

const emit = defineEmits(["update:filters", "reset", "apply"]);

const filterableColumns = () => (props.columns || []).filter((c) => c?.filter?.id && c?.filter?.type);

// Support: le parent peut passer soit un objet, soit un ref({}) (compat).
const values = computed(() => unref(props.filterValues) || {});

const getOptions = (col) => {
    const id = col?.filter?.id;
    if (id && Array.isArray(props.filterOptions?.[id])) return props.filterOptions[id];
    if (Array.isArray(col?.filter?.options)) return col.filter.options;
    return [];
};

/**
 * UI options badges (opt-in via column config).
 *
 * @example
 * filter: {
 *   id: 'rarity',
 *   type: 'multi',
 *   ui: { optionBadge: { enabled: true, color: 'auto', autoLabelFrom: 'label', variant: 'soft' } }
 * }
 */
const getFilterUi = (col) => (col?.filter?.ui && typeof col.filter.ui === "object" ? col.filter.ui : {});
const getOptionBadgeCfg = (col) => {
    const ui = getFilterUi(col);
    const cfg = ui?.optionBadge;
    return cfg && typeof cfg === "object" ? cfg : null;
};
const isOptionBadgeEnabled = (col) => Boolean(getOptionBadgeCfg(col)?.enabled);
const optionBadgeProps = (col, opt) => {
    const cfg = getOptionBadgeCfg(col) || {};
    const autoLabelFrom = String(cfg.autoLabelFrom || "label"); // 'label' | 'value'
    const label = autoLabelFrom === "value"
        ? String(opt?.value ?? "")
        : String(opt?.label ?? opt?.value ?? "");

    return {
        color: cfg.color || props.uiColor,
        autoLabel: label,
        autoScheme: cfg.autoScheme,
        autoTone: cfg.autoTone,
        variant: cfg.variant || "soft",
        glassy: Boolean(cfg.glassy),
    };
};

/**
 * Détection: un select à 2 choix qui représente un booléen (0/1, true/false).
 * Dans ce cas, on préfère un ToggleCore plutôt qu'un select.
 */
const isBooleanSelect = (col) => {
    if (col?.filter?.type !== "select") return false;
    const opts = (getOptions(col) || []).map((o) => String(o?.value));
    if (opts.length !== 2) return false;
    const set = new Set(opts);
    const is01 = set.has("0") && set.has("1");
    const isTrueFalse = set.has("true") && set.has("false");
    return is01 || isTrueFalse;
};

const updateFilter = (filterId, value) => {
    const next = { ...(values.value || {}), [filterId]: value };
    if (props.debug) {
        console.log("[TanStackTableFilters] updateFilter", { filterId, value, next });
    }
    emit("update:filters", next);
};

const getRawFilterValue = (filterId) => String((values.value?.[filterId] ?? ""));
const getRawFilterArrayValue = (filterId) => {
    const raw = values.value?.[filterId];
    if (!Array.isArray(raw)) return [];
    return raw.map((v) => String(v));
};

// Recherche interne par filtre (dropdown multi)
const multiSearch = ref({});
const setMultiSearch = (filterId, value) => {
    multiSearch.value = { ...(multiSearch.value || {}), [filterId]: String(value ?? "") };
};
const getMultiSearch = (filterId) => String(multiSearch.value?.[filterId] ?? "");

const normalize = (s) => String(s ?? "").toLowerCase().normalize("NFD").replace(/\p{Diacritic}/gu, "");

const multiOptions = (col) => {
    const opts = getOptions(col) || [];
    const q = normalize(getMultiSearch(col?.filter?.id));
    if (!q) return opts;
    return opts.filter((o) => normalize(o?.label ?? o?.value).includes(q));
};

// Recherche interne par filtre (dropdown single)
const singleSearch = ref({});
const setSingleSearch = (filterId, value) => {
    singleSearch.value = { ...(singleSearch.value || {}), [filterId]: String(value ?? "") };
};
const getSingleSearch = (filterId) => String(singleSearch.value?.[filterId] ?? "");
const singleOptions = (col) => {
    const opts = getOptions(col) || [];
    const q = normalize(getSingleSearch(col?.filter?.id));
    if (!q) return opts;
    return opts.filter((o) => normalize(o?.label ?? o?.value).includes(q));
};

const getSelectedLabel = (col) => {
    const filterId = col?.filter?.id;
    const raw = getRawFilterValue(filterId);
    if (!raw) return "Tous";
    const opt = (getOptions(col) || []).find((o) => String(o.value) === String(raw));
    return opt?.label ? String(opt.label) : String(raw);
};

const multiSummary = (col) => {
    const filterId = col?.filter?.id;
    const selected = getRawFilterArrayValue(filterId);
    if (!selected.length) return "Tous";
    if (selected.length === 1) {
        const opt = (getOptions(col) || []).find((o) => String(o.value) === String(selected[0]));
        return opt?.label ? String(opt.label) : "1 sélection";
    }
    return `${selected.length} sélectionnés`;
};

const setMultiValues = (filterId, arr) => {
    const next = (Array.isArray(arr) ? arr : []).map((v) => String(v));
    updateFilter(filterId, next);
};

const toggleMultiValue = (filterId, value, checked) => {
    const v = String(value);
    const current = new Set(getRawFilterArrayValue(filterId));
    if (checked) current.add(v);
    else current.delete(v);
    setMultiValues(filterId, Array.from(current));
};

const clearMultiFilter = (filterId) => setMultiValues(filterId, []);
const selectAllMulti = (col) => {
    const filterId = col?.filter?.id;
    const all = (getOptions(col) || []).map((o) => String(o.value));
    setMultiValues(filterId, all);
};

/**
 * Adaptateur SelectCore <-> Table:
 * - Table: "" = Tous / pas de filtre
 * - SelectCore: "" => null (voir SelectCore: value === '' ? null : value)
 */
const handleSelectUpdate = (filterId, modelValue) => {
    // SelectCore émet null quand l'utilisateur choisit l'option value=""
    updateFilter(filterId, modelValue === null ? "" : String(modelValue));
};

const getTextModelValue = (filterId) => {
    const raw = values.value?.[filterId];
    if (raw === null || typeof raw === "undefined") return "";
    return String(raw);
};

const handleTextUpdate = (filterId, modelValue) => {
    updateFilter(filterId, modelValue === null || typeof modelValue === "undefined" ? "" : String(modelValue));
};

const booleanStateLabel = (raw) => {
    if (raw === "") return "Tous";
    return String(raw) === "1" ? "Oui" : "Non";
};

const isBooleanChecked = (raw) => String(raw) === "1";

const toggleBooleanFilter = (filterId, checked) => {
    const raw = getRawFilterValue(filterId);

    // Etat "Tous" (raw="") : un clic ON => Oui
    if (raw === "" && checked) {
        updateFilter(filterId, "1");
        return;
    }

    // Toggle classique Oui/Non
    updateFilter(filterId, checked ? "1" : "0");
};

const clearBooleanFilter = (filterId) => {
    updateFilter(filterId, "");
};

const hasBooleanFilter = (filterId) => getRawFilterValue(filterId) !== "";

const isBooleanIndeterminate = (raw) => raw === "";

// Badges "filtres actifs" (en bas)
const activeBadges = computed(() => {
    const cols = filterableColumns();
    const badges = [];

    for (const col of cols) {
        const f = col?.filter;
        if (!f?.id || !f?.type) continue;

        const raw = values.value?.[f.id];

        // multi => un badge par valeur sélectionnée
        if (f.type === "multi") {
            const arr = Array.isArray(raw) ? raw : [];
            for (const v of arr) {
                const vv = String(v);
                if (!vv) continue;
                const opt = (getOptions(col) || []).find((o) => String(o.value) === vv);
                const display = opt?.label ? String(opt.label) : vv;
                        const badgeCfg = isOptionBadgeEnabled(col) ? optionBadgeProps(col, opt || { value: vv, label: display }) : null;
                badges.push({
                    key: `${f.id}:${vv}`,
                    filterId: f.id,
                    type: "multi",
                    value: vv,
                    label: `${col.label}: ${display}`,
                            badge: badgeCfg ? { ...badgeCfg } : null,
                });
            }
            continue;
        }

        // text/select/boolean => un badge si valeur non vide
        if (raw === null || typeof raw === "undefined" || String(raw) === "") continue;

        if (f.type === "boolean") {
            badges.push({
                key: `${f.id}`,
                filterId: f.id,
                type: "boolean",
                value: String(raw),
                label: `${col.label}: ${booleanStateLabel(String(raw))}`,
            });
            continue;
        }

        if (f.type === "text") {
            badges.push({
                key: `${f.id}`,
                filterId: f.id,
                type: "text",
                value: String(raw),
                label: `${col.label}: ${String(raw)}`,
            });
            continue;
        }

        // select (ou autre) => afficher le label option
        const vv = String(raw);
        const opt = (getOptions(col) || []).find((o) => String(o.value) === vv);
        const display = opt?.label ? String(opt.label) : vv;
        const badgeCfg = isOptionBadgeEnabled(col) ? optionBadgeProps(col, opt || { value: vv, label: display }) : null;
        badges.push({
            key: `${f.id}`,
            filterId: f.id,
            type: "select",
            value: vv,
            label: `${col.label}: ${display}`,
            badge: badgeCfg ? { ...badgeCfg } : null,
        });
    }

    return badges;
});

const removeBadge = (badge) => {
    if (!badge?.filterId) return;
    if (badge.type === "multi") {
        const current = new Set(getRawFilterArrayValue(badge.filterId));
        current.delete(String(badge.value));
        setMultiValues(badge.filterId, Array.from(current));
        return;
    }
    // boolean/text/select => clear
    updateFilter(badge.filterId, "");
};
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold">Filtres</div>
            <div class="flex items-center gap-2">
                <Btn
                    size="xs"
                    variant="outline"
                    :color="uiColor"
                    opacity="lg"
                    type="button"
                    title="Appliquer les filtres"
                    @click="emit('apply')"
                >
                    <Icon source="fa-solid fa-check" alt="Appliquer" size="sm" />
                    <span class="hidden md:inline">Appliquer</span>
                </Btn>

                <Btn size="xs" variant="ghost" type="button" @click="emit('reset')">
                    Réinitialiser
                </Btn>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
            <div v-for="col in filterableColumns()" :key="col.id" class="space-y-1">
                <div class="text-xs opacity-70">{{ col.label }}</div>

                <!-- boolean (switch) -->
                <div
                    v-if="col.filter.type === 'boolean'"
                    class="flex items-center justify-between gap-3 w-full rounded-lg border border-base-300 px-2 py-1"
                >
                    <div class="flex items-center gap-3">
                        <!-- wrapper fixe pour éviter tout micro-shift de layout -->
                        <div class="w-12 flex items-center justify-center shrink-0">
                            <ToggleCore
                                :model-value="isBooleanChecked(getRawFilterValue(col.filter.id))"
                                :indeterminate="isBooleanIndeterminate(getRawFilterValue(col.filter.id))"
                                size="sm"
                                :color="uiColor"
                                @update:model-value="(v) => toggleBooleanFilter(col.filter.id, v)"
                            />
                        </div>

                        <span
                            class="text-sm min-w-10"
                            :class="{ 'opacity-70 italic': !hasBooleanFilter(col.filter.id) }"
                        >
                            {{ booleanStateLabel(getRawFilterValue(col.filter.id)) }}
                        </span>
                    </div>

                    <Btn
                        type="button"
                        size="xs"
                        variant="ghost"
                        square
                        class="w-7 h-7 min-h-0 px-0 flex items-center justify-center"
                        title="Retirer le filtre"
                        :disabled="!hasBooleanFilter(col.filter.id)"
                        :class="{ 'opacity-40 cursor-default': !hasBooleanFilter(col.filter.id) }"
                        @click="clearBooleanFilter(col.filter.id)"
                    >
                        ✕
                    </Btn>
                </div>

                <!-- select => dropdown single -->
                <div
                    v-else-if="col.filter.type === 'select' && !isBooleanSelect(col)"
                    class="flex items-center justify-between gap-2 w-full rounded-lg border border-base-300 px-2 py-1"
                >
                    <Dropdown placement="bottom-start" :close-on-content-click="false">
                        <template #trigger>
                            <Btn
                                size="sm"
                                variant="outline"
                                :color="uiColor"
                                opacity="lg"
                                class="gap-2"
                                title="Choisir une valeur"
                            >
                                <Icon source="fa-solid fa-filter" alt="Filtre" size="sm" />
                                <span class="truncate max-w-40">{{ getSelectedLabel(col) }}</span>
                            </Btn>
                        </template>
                        <template #content>
                            <div class="p-3 w-72 space-y-2">
                                <InputCore
                                    type="search"
                                    variant="glass"
                                    :color="uiColor"
                                    size="sm"
                                    class="w-full"
                                    placeholder="Rechercher…"
                                    :model-value="getSingleSearch(col.filter.id)"
                                    @update:model-value="(v) => setSingleSearch(col.filter.id, v)"
                                />

                                <label class="flex items-center gap-2 cursor-pointer">
                                    <RadioCore
                                        :name="`filter-${col.filter.id}`"
                                        value=""
                                        :model-value="getRawFilterValue(col.filter.id)"
                                        size="sm"
                                        :color="uiColor"
                                        @update:model-value="() => handleSelectUpdate(col.filter.id, null)"
                                    />
                                    <span class="text-sm">Tous</span>
                                </label>

                                <div class="max-h-64 overflow-y-auto pr-1 space-y-1">
                                    <label
                                        v-for="opt in singleOptions(col)"
                                        :key="String(opt.value)"
                                        class="flex items-center gap-2 cursor-pointer"
                                    >
                                        <RadioCore
                                            :name="`filter-${col.filter.id}`"
                                            :value="String(opt.value)"
                                            :model-value="getRawFilterValue(col.filter.id)"
                                            size="sm"
                                            :color="uiColor"
                                            @update:model-value="(v) => handleSelectUpdate(col.filter.id, v)"
                                        />
                                        <Badge
                                            v-if="isOptionBadgeEnabled(col)"
                                            :color="optionBadgeProps(col, opt).color"
                                            :auto-label="optionBadgeProps(col, opt).autoLabel"
                                            :auto-scheme="optionBadgeProps(col, opt).autoScheme || undefined"
                                            :auto-tone="optionBadgeProps(col, opt).autoTone || undefined"
                                            :variant="optionBadgeProps(col, opt).variant"
                                            :glassy="Boolean(optionBadgeProps(col, opt).glassy)"
                                            size="sm"
                                        >
                                            {{ opt.label }}
                                        </Badge>
                                        <span v-else class="text-sm">{{ opt.label }}</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </Dropdown>

                    <Btn
                        type="button"
                        size="xs"
                        variant="ghost"
                        square
                        class="w-7 h-7 min-h-0 px-0 flex items-center justify-center"
                        title="Retirer le filtre"
                        :disabled="getRawFilterValue(col.filter.id) === ''"
                        :class="{ 'opacity-40 cursor-default': getRawFilterValue(col.filter.id) === '' }"
                        @click="handleSelectUpdate(col.filter.id, null)"
                    >
                        ✕
                    </Btn>
                </div>

                <!-- select(2 choix booléen) => toggle -->
                <div
                    v-else-if="col.filter.type === 'select' && isBooleanSelect(col)"
                    class="flex items-center justify-between gap-3 w-full rounded-lg border border-base-300 px-2 py-1"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-12 flex items-center justify-center shrink-0">
                            <ToggleCore
                                :model-value="isBooleanChecked(getRawFilterValue(col.filter.id))"
                                :indeterminate="isBooleanIndeterminate(getRawFilterValue(col.filter.id))"
                                size="sm"
                                :color="uiColor"
                                @update:model-value="(v) => toggleBooleanFilter(col.filter.id, v)"
                            />
                        </div>

                        <span
                            class="text-sm min-w-10"
                            :class="{ 'opacity-70 italic': !hasBooleanFilter(col.filter.id) }"
                        >
                            {{ booleanStateLabel(getRawFilterValue(col.filter.id)) }}
                        </span>
                    </div>

                    <Btn
                        type="button"
                        size="xs"
                        variant="ghost"
                        square
                        class="w-7 h-7 min-h-0 px-0 flex items-center justify-center"
                        title="Retirer le filtre"
                        :disabled="!hasBooleanFilter(col.filter.id)"
                        :class="{ 'opacity-40 cursor-default': !hasBooleanFilter(col.filter.id) }"
                        @click="clearBooleanFilter(col.filter.id)"
                    >
                        ✕
                    </Btn>
                </div>

                <!-- multi (dropdown + checkboxes) -->
                <div
                    v-else-if="col.filter.type === 'multi'"
                    class="flex items-center justify-between gap-2 w-full rounded-lg border border-base-300 px-2 py-1"
                >
                    <Dropdown
                        placement="bottom-start"
                        :close-on-content-click="false"
                    >
                        <template #trigger>
                            <Btn
                                size="sm"
                                variant="outline"
                                :color="uiColor"
                                opacity="lg"
                                class="gap-2"
                                title="Choisir plusieurs valeurs"
                            >
                                <Icon source="fa-solid fa-filter" alt="Filtre" size="sm" />
                                <span class="truncate max-w-40">{{ multiSummary(col) }}</span>
                            </Btn>
                        </template>
                        <template #content>
                            <div class="p-3 w-72 space-y-2">
                                <InputCore
                                    type="search"
                                    variant="glass"
                                    :color="uiColor"
                                    size="sm"
                                    class="w-full"
                                    placeholder="Rechercher…"
                                    :model-value="getMultiSearch(col.filter.id)"
                                    @update:model-value="(v) => setMultiSearch(col.filter.id, v)"
                                />

                                <div class="flex items-center justify-between gap-2">
                                    <Btn size="xs" variant="ghost" type="button" @click="selectAllMulti(col)">
                                        Tout
                                    </Btn>
                                    <Btn size="xs" variant="ghost" type="button" @click="clearMultiFilter(col.filter.id)">
                                        Aucun
                                    </Btn>
                                </div>

                                <div class="max-h-64 overflow-y-auto pr-1 space-y-1">
                                    <label
                                        v-for="opt in multiOptions(col)"
                                        :key="String(opt.value)"
                                        class="flex items-center gap-2 cursor-pointer"
                                    >
                                        <CheckboxCore
                                            :model-value="getRawFilterArrayValue(col.filter.id).includes(String(opt.value))"
                                            size="sm"
                                            :color="uiColor"
                                            @update:model-value="(checked) => toggleMultiValue(col.filter.id, opt.value, Boolean(checked))"
                                        />
                                        <Badge
                                            v-if="isOptionBadgeEnabled(col)"
                                            :color="optionBadgeProps(col, opt).color"
                                            :auto-label="optionBadgeProps(col, opt).autoLabel"
                                            :auto-scheme="optionBadgeProps(col, opt).autoScheme || undefined"
                                            :auto-tone="optionBadgeProps(col, opt).autoTone || undefined"
                                            :variant="optionBadgeProps(col, opt).variant"
                                            :glassy="Boolean(optionBadgeProps(col, opt).glassy)"
                                            size="sm"
                                        >
                                            {{ opt.label }}
                                        </Badge>
                                        <span v-else class="text-sm">{{ opt.label }}</span>
                                    </label>
                                </div>
                            </div>
                        </template>
                    </Dropdown>

                    <Btn
                        type="button"
                        size="xs"
                        variant="ghost"
                        square
                        class="w-7 h-7 min-h-0 px-0 flex items-center justify-center"
                        title="Retirer le filtre"
                        :disabled="getRawFilterArrayValue(col.filter.id).length === 0"
                        :class="{ 'opacity-40 cursor-default': getRawFilterArrayValue(col.filter.id).length === 0 }"
                        @click="clearMultiFilter(col.filter.id)"
                    >
                        ✕
                    </Btn>
                </div>

                <!-- text -->
                <InputCore
                    v-else-if="col.filter.type === 'text'"
                    class="w-full"
                    type="text"
                    variant="glass"
                    :color="uiColor"
                    size="sm"
                    :model-value="getTextModelValue(col.filter.id)"
                    @update:model-value="(v) => handleTextUpdate(col.filter.id, v)"
                />

                <!-- unsupported (Phase 1) -->
                <div v-else class="text-xs opacity-50">
                    Filtre non supporté ({{ col.filter.type }})
                </div>
            </div>
        </div>

        <!-- Filtres actifs (chips / badges) -->
        <div v-if="activeBadges.length" class="flex flex-wrap items-center gap-2 pt-2">
            <div class="text-xs opacity-70 mr-1">Actifs :</div>
            <Badge
                v-for="b in activeBadges"
                :key="b.key"
                :color="b.badge?.color || uiColor"
                :auto-label="b.badge?.autoLabel || ''"
                :auto-scheme="b.badge?.autoScheme || undefined"
                :auto-tone="b.badge?.autoTone || undefined"
                :glassy="Boolean(b.badge?.glassy)"
                :variant="b.badge?.variant || 'soft'"
                size="sm"
                class="inline-flex items-center gap-1 pr-1"
            >
                <span class="max-w-64 truncate">{{ b.label }}</span>
                <Btn
                    type="button"
                    size="xs"
                    variant="ghost"
                    square
                    class="w-6 h-6 min-h-0 px-0 flex items-center justify-center"
                    title="Retirer"
                    @click="removeBadge(b)"
                >
                    ✕
                </Btn>
            </Badge>
        </div>
    </div>
</template>


