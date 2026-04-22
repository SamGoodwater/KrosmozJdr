<script setup>
import { computed, onMounted, ref, watch } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import { getCharacteristicColorStyle, resolveDef } from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    section: { type: Object, required: true },
    data: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
});

const loading = ref(false);
const error = ref(null);
const rows = ref([]);
const meta = ref({});
const page = usePage();

const group = computed(() => props.settings?.group || "all");
const entity = computed(() => props.settings?.entity || "*");
const search = computed(() => props.settings?.search || "");
const sortBy = computed(() => props.settings?.sort_by || "group");
const sortDir = computed(() => props.settings?.sort_dir || "asc");
const statusFilter = computed(() => props.settings?.status_filter || "all");
const showPrices = computed(() => Boolean(props.settings?.show_prices ?? true));
const onlyWithEquipment = computed(() => Boolean(props.settings?.show_only_with_equipment ?? false));
const canSeeStatus = computed(() => {
    const role = Number(page.props?.auth?.user?.role ?? 0);
    return Number.isFinite(role) && role >= 3;
});

async function fetchTable() {
    loading.value = true;
    error.value = null;
    try {
        const { data } = await axios.get("/api/characteristics/reference-table", {
            params: {
                group: group.value,
                entity: entity.value,
                search: search.value,
                sort_by: sortBy.value,
                sort_dir: sortDir.value,
                status_filter: canSeeStatus.value ? statusFilter.value : "all",
                show_only_with_equipment: onlyWithEquipment.value,
            },
        });
        rows.value = Array.isArray(data?.rows) ? data.rows : [];
        meta.value = data?.meta || {};
    } catch (e) {
        error.value = resolveLoadError(e);
        rows.value = [];
        meta.value = {};
    } finally {
        loading.value = false;
    }
}

function fmtNumber(value) {
    if (value === null || value === undefined || value === "") return "—";
    const n = Number(value);
    if (!Number.isFinite(n)) return String(value);
    return new Intl.NumberFormat("fr-FR").format(n);
}

function statusLabel(status) {
    switch (status) {
        case "validee":
            return "Validée";
        case "en_cours_de_validation":
            return "En cours";
        default:
            return "À valider";
    }
}

function statusClass(status) {
    switch (status) {
        case "validee":
            return "badge-success";
        case "en_cours_de_validation":
            return "badge-warning";
        default:
            return "badge-neutral";
    }
}

function pickPreferredGroupRow(groupRows = []) {
    if (!Array.isArray(groupRows) || groupRows.length === 0) return null;
    const entityFilter = String(entity.value || "*");
    if (entityFilter !== "*") {
        const exact = groupRows.find((r) => String(r?.entity || "") === entityFilter);
        if (exact) return exact;
    }
    return groupRows.find((r) => String(r?.entity || "") === "*") || groupRows[0] || null;
}

function canonicalKey(rawKey) {
    return String(rawKey || "").replace(/_(creature|object|spell)$/i, "");
}

const conceptRows = computed(() => {
    const grouped = new Map();
    for (const row of rows.value) {
        const key = canonicalKey(row?.key);
        if (!key) continue;
        if (!grouped.has(key)) {
            grouped.set(key, { key, creatureRows: [], objectRows: [] });
        }
        const bucket = grouped.get(key);
        if (row.group === "creature") bucket.creatureRows.push(row);
        else if (row.group === "object") bucket.objectRows.push(row);
    }

    return Array.from(grouped.values()).map((entry) => ({
        key: entry.key,
        creature: pickPreferredGroupRow(entry.creatureRows),
        object: pickPreferredGroupRow(entry.objectRows),
    }));
});

const conceptRowsByKey = computed(() => {
    const map = new Map();
    for (const entry of conceptRows.value) {
        map.set(entry.key, entry);
    }
    return map;
});

const DISPLAY_SPEC = [
    // Général
    { category: "Général", label: "Niveau", creatureKey: "level", objectKey: "level" },
    { category: "Général", label: "Recharge de Wakfu", creatureKey: "wakfu_reserve", objectKey: "wakfu_recharge" },
    { category: "Général", label: "Bonus de maîtrise", creatureKey: "mastery_bonus", objectKey: "mastery_bonus" },

    // Principal — essentielles
    { category: "Principal", label: "Points de vie", creatureKey: "life_points", objectKey: "life_points_max" },
    { category: "Principal", label: "Dés de vie", creatureKey: "hit_dice", objectKey: "hit_dice" },
    { category: "Principal", label: "PA", creatureKey: "action_points", objectKey: "action_points" },
    { category: "Principal", label: "PM", creatureKey: "movement_points", objectKey: "movement_points" },
    { category: "Principal", label: "Initiative", creatureKey: "initiative", objectKey: "initiative" },
    { category: "Principal", label: "PO", creatureKey: "range", objectKey: "range" },
    { category: "Principal", label: "Nombre d'invocation", creatureKey: "summoning", objectKey: "summoning" },

    // Principal — primaires
    { category: "Principal", label: "Vitalité", creatureKey: "vitality", objectKey: "vitality" },
    { category: "Principal", label: "Modificateur de Vitalité", creatureKey: "modifier_vitality", objectKey: "modifier_vitality" },
    { category: "Principal", label: "Sauvegarde de Vitalité", creatureKey: "save_vitality", objectKey: "save_vitality" },
    { category: "Principal", label: "Sagesse", creatureKey: "wisdom", objectKey: "wisdom" },
    { category: "Principal", label: "Modificateur de Sagesse", creatureKey: "modifier_wisdom", objectKey: "modifier_wisdom" },
    { category: "Principal", label: "Sauvegarde de Sagesse", creatureKey: "save_wisdom", objectKey: "save_wisdom" },
    { category: "Principal", label: "Force", creatureKey: "strength", objectKey: "strength" },
    { category: "Principal", label: "Modificateur de Force", creatureKey: "modifier_strength", objectKey: "modifier_strength" },
    { category: "Principal", label: "Sauvegarde de Force", creatureKey: "save_strength", objectKey: "save_strength" },
    { category: "Principal", label: "Intelligence", creatureKey: "intelligence", objectKey: "intelligence" },
    { category: "Principal", label: "Modificateur d'Intelligence", creatureKey: "modifier_intelligence", objectKey: "modifier_intelligence" },
    { category: "Principal", label: "Sauvegarde d'Intelligence", creatureKey: "save_intelligence", objectKey: "save_intelligence" },
    { category: "Principal", label: "Agilité", creatureKey: "agility", objectKey: "agility" },
    { category: "Principal", label: "Modificateur d'Agilité", creatureKey: "modifier_agility", objectKey: "modifier_agility" },
    { category: "Principal", label: "Sauvegarde d'Agilité", creatureKey: "save_agility", objectKey: "save_agility" },
    { category: "Principal", label: "Chance", creatureKey: "chance", objectKey: "chance" },
    { category: "Principal", label: "Modificateur de Chance", creatureKey: "modifier_chance", objectKey: "modifier_chance" },
    { category: "Principal", label: "Sauvegarde de Chance", creatureKey: "save_chance", objectKey: "save_chance" },

    // Offensif
    { category: "Offensif", label: "Bonus de touche", creatureKey: "hit_bonus", objectKey: "hit_bonus" },
    { category: "Offensif", label: "Dommage fixe Neutre", creatureKey: "fixed_damage_neutral", objectKey: "fixed_damage_neutral" },
    { category: "Offensif", label: "Dommage fixe Terre", creatureKey: "fixed_damage_earth", objectKey: "fixed_damage_earth" },
    { category: "Offensif", label: "Dommage fixe Feu", creatureKey: "fixed_damage_fire", objectKey: "fixed_damage_fire" },
    { category: "Offensif", label: "Dommage fixe Air", creatureKey: "fixed_damage_air", objectKey: "fixed_damage_air" },
    { category: "Offensif", label: "Dommage fixe Eau", creatureKey: "fixed_damage_water", objectKey: "fixed_damage_water" },
    { category: "Offensif", label: "Dommage fixe Multiple", creatureKey: "fixed_damage_multiple", objectKey: "fixed_damage_multiple" },
    { category: "Offensif", label: "Bonus critique", creatureKey: "critical_hit", objectKey: "critical_hit" },
    { category: "Offensif", label: "Bonus de soin", creatureKey: "heal_bonus", objectKey: "heal_bonus" },

    // Défensif
    { category: "Défensif", label: "Classe d'armure", creatureKey: "armor_class", objectKey: "armor_class" },
    { category: "Défensif", label: "Esquive PA", creatureKey: "dodge_action_points", objectKey: "dodge_action_points" },
    { category: "Défensif", label: "Esquive PM", creatureKey: "dodge_movement_points", objectKey: "dodge_movement_points" },
    { category: "Défensif", label: "Fuite", creatureKey: "dodge", objectKey: "dodge" },
    { category: "Défensif", label: "Tacle", creatureKey: "tackle", objectKey: "tackle" },
    { category: "Défensif", label: "Résistance fixe Neutre", creatureKey: "fixed_resistance_neutral", objectKey: "fixed_resistance_neutral" },
    { category: "Défensif", label: "Résistance fixe Terre", creatureKey: "fixed_resistance_earth", objectKey: "fixed_resistance_earth" },
    { category: "Défensif", label: "Résistance fixe Feu", creatureKey: "fixed_resistance_fire", objectKey: "fixed_resistance_fire" },
    { category: "Défensif", label: "Résistance fixe Air", creatureKey: "fixed_resistance_air", objectKey: "fixed_resistance_air" },
    { category: "Défensif", label: "Résistance fixe Eau", creatureKey: "fixed_resistance_water", objectKey: "fixed_resistance_water" },
    { category: "Défensif", label: "Résistance % Neutre", creatureKey: "resistance_neutral", objectKey: "resistance_percent_tier_neutral" },
    { category: "Défensif", label: "Résistance % Terre", creatureKey: "resistance_earth", objectKey: "resistance_percent_tier_earth" },
    { category: "Défensif", label: "Résistance % Feu", creatureKey: "resistance_fire", objectKey: "resistance_percent_tier_fire" },
    { category: "Défensif", label: "Résistance % Air", creatureKey: "resistance_air", objectKey: "resistance_percent_tier_air" },
    { category: "Défensif", label: "Résistance % Eau", creatureKey: "resistance_water", objectKey: "resistance_percent_tier_water" },

    // Compétences
    { category: "Compétences", label: "Agilité — Acrobaties", creatureKey: "acrobatics", objectKey: "acrobatics" },
    { category: "Compétences", label: "Agilité — Discrétion", creatureKey: "stealth", objectKey: "stealth" },
    { category: "Compétences", label: "Agilité — Escamotage", creatureKey: "sleight_of_hand", objectKey: "sleight_of_hand" },
    { category: "Compétences", label: "Force — Athlétisme", creatureKey: "athletics", objectKey: "athletics" },
    { category: "Compétences", label: "Force — Intimidation", creatureKey: "intimidation", objectKey: "intimidation" },
    { category: "Compétences", label: "Intelligence — Arcanes", creatureKey: "arcana", objectKey: "arcana" },
    { category: "Compétences", label: "Intelligence — Histoire", creatureKey: "history", objectKey: "history" },
    { category: "Compétences", label: "Intelligence — Investigation", creatureKey: "investigation", objectKey: "investigation" },
    { category: "Compétences", label: "Intelligence — Nature", creatureKey: "nature", objectKey: "nature" },
    { category: "Compétences", label: "Intelligence — Religion", creatureKey: "religion", objectKey: "religion" },
    { category: "Compétences", label: "Sagesse — Dressage", creatureKey: "animal_handling", objectKey: "animal_handling" },
    { category: "Compétences", label: "Sagesse — Médecine", creatureKey: "medicine", objectKey: "medicine" },
    { category: "Compétences", label: "Sagesse — Perception", creatureKey: "perception", objectKey: "perception" },
    { category: "Compétences", label: "Sagesse — Intuition", creatureKey: "insight", objectKey: "insight" },
    { category: "Compétences", label: "Sagesse — Survie", creatureKey: "survival", objectKey: "survival" },
    { category: "Compétences", label: "Chance — Persuasion", creatureKey: "persuasion", objectKey: "persuasion" },
    { category: "Compétences", label: "Chance — Représentation", creatureKey: "performance", objectKey: "performance" },
    { category: "Compétences", label: "Chance — Supercherie", creatureKey: "deception", objectKey: "deception" },
];

function resolveConceptRow(creatureKey, objectKey) {
    const byKey = conceptRowsByKey.value;
    const creature = byKey.get(creatureKey)?.creature ?? null;
    const object = byKey.get(objectKey)?.object ?? byKey.get(creatureKey)?.object ?? null;
    return { creature, object };
}

const curatedRows = computed(() => {
    return DISPLAY_SPEC
        .map((item) => {
            const concept = resolveConceptRow(item.creatureKey, item.objectKey);
            const entry = {
                key: item.creatureKey,
                category: item.category,
                label: item.label,
                creature: concept.creature,
                object: concept.object,
            };
            return entry;
        })
        .filter((entry) => entry.creature || entry.object);
});

const pivotRows = computed(() => {
    return curatedRows.value.map((entry) => ({
        key: entry.key,
        creature: entry.creature,
        object: entry.object,
        category: entry.category,
        label: entry.label,
    }));
});

function fallbackCharacteristicIcon(group) {
    switch (group) {
        case "creature":
            return "fa-solid fa-heart-pulse";
        case "object":
            return "fa-solid fa-gem";
        case "spell":
            return "fa-solid fa-bolt";
        default:
            return "fa-solid fa-star";
    }
}

function sourceGroupsForRow(row) {
    switch (row?.group) {
        case "creature":
            return ["creature"];
        case "object":
            return ["item", "resource", "consumable", "panoply"];
        case "spell":
            return ["spell", "capability"];
        default:
            return ["creature", "item", "resource", "consumable", "panoply", "spell", "capability"];
    }
}

function resolvedDefForRow(row) {
    const lookupKey = row?.db_column || row?.key;
    if (!lookupKey) return null;
    return resolveDef(lookupKey, undefined, { sourceGroups: sourceGroupsForRow(row) });
}

function resolvedColorForRow(row) {
    const def = resolvedDefForRow(row);
    return def?._resolvedColor ?? def?.color ?? row?.color ?? null;
}

function resolvedIconForRow(row) {
    const def = resolvedDefForRow(row);
    return def?._resolvedIcon ?? def?.icon ?? row?.icon ?? null;
}

function resolvedNameForRow(row) {
    const def = resolvedDefForRow(row);
    return def?.name ?? row?.name ?? row?.key ?? "—";
}

function primaryRowForEntry(entry) {
    return entry?.creature || entry?.object || null;
}

function resolvedColorForEntry(entry) {
    return resolvedColorForRow(primaryRowForEntry(entry));
}

function resolvedIconForEntry(entry) {
    return resolvedIconForRow(primaryRowForEntry(entry));
}

function resolvedNameForEntry(entry) {
    if (entry?.label) return String(entry.label);
    return resolvedNameForRow(primaryRowForEntry(entry));
}

function resolvedStatusForEntry(entry) {
    const base = primaryRowForEntry(entry);
    return base?.status ?? null;
}

const categorizedRows = computed(() => {
    const categoryOrder = ["Général", "Principal", "Offensif", "Défensif", "Compétences"];
    const buckets = new Map(categoryOrder.map((c) => [c, []]));
    for (const entry of pivotRows.value) {
        const cat = String(entry.category || "Général");
        if (!buckets.has(cat)) buckets.set(cat, []);
        buckets.get(cat)?.push(entry);
    }

    const flattened = [];
    for (const category of categoryOrder) {
        const entries = buckets.get(category) || [];
        if (entries.length === 0) continue;
        entries.forEach((entry, idx) => {
            flattened.push({
                entry,
                categoryLabel: category,
                showCategoryCell: idx === 0,
                categoryRowSpan: idx === 0 ? entries.length : 0,
            });
        });
    }

    return flattened;
});

function rowAccentStyle(entry) {
    const style = getCharacteristicColorStyle(resolvedColorForEntry(entry));
    if (!style?.color) return {};

    return {
        boxShadow: `inset 2px 0 0 ${style.color}`,
    };
}

function cellValue(row, field) {
    if (!row) return "—";
    const value = row?.[field];
    return value ?? "—";
}

function resolveLoadError(e) {
    const apiError = e?.response?.data?.error;
    if (typeof apiError === "string" && apiError.trim() !== "") {
        return apiError;
    }

    const status = e?.response?.status;
    if (Number.isFinite(status)) {
        return `Impossible de charger le référentiel des caractéristiques (HTTP ${status}).`;
    }

    return "Impossible de charger le référentiel des caractéristiques.";
}

onMounted(fetchTable);
watch([group, entity, search, sortBy, sortDir, statusFilter, onlyWithEquipment, canSeeStatus], fetchTable);
</script>

<template>
    <div class="space-y-3">
        <div class="rounded-box border border-base-content/15 bg-base-200/40 px-3 py-2 text-xs text-base-content/75">
            {{ meta.price_notice || "Prix indicatifs: valeurs de référence, non contractuelles." }}
        </div>

        <div v-if="loading" class="flex items-center justify-center py-8">
            <span class="loading loading-spinner loading-md" />
        </div>

        <div v-else-if="error" class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation" />
            <span>{{ error }}</span>
        </div>

        <div v-else-if="rows.length === 0" class="alert alert-info">
            <i class="fa-solid fa-circle-info" />
            <span>Aucune caractéristique trouvée pour ces filtres.</span>
        </div>

        <div v-else class="overflow-x-auto rounded-box border border-base-content/20 bg-base-100/40">
            <table class="table w-full">
                <thead>
                    <tr class="bg-base-200/70 text-sm">
                        <th rowspan="2" class="text-base font-extrabold">Groupe</th>
                        <th rowspan="2" class="text-base font-extrabold">Caractéristique</th>
                        <th v-if="canSeeStatus" rowspan="2" class="font-bold">Statut</th>
                        <th colspan="4" class="text-center text-base font-extrabold tracking-wide">Valeurs de référence (Créature)</th>
                        <th colspan="2" class="text-center text-base font-extrabold tracking-wide">Équipements (Objet)</th>
                    </tr>
                    <tr class="bg-base-200/45 text-xs uppercase tracking-wide text-base-content/85">
                        <th class="text-sm font-extrabold">Défaut</th>
                        <th class="font-semibold">Formule</th>
                        <th class="text-sm font-extrabold">Min</th>
                        <th class="text-sm font-extrabold">Max</th>
                        <th class="text-sm font-extrabold">Équipement</th>
                        <th class="text-sm font-extrabold">Forgemagie</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in categorizedRows"
                        :key="row.entry.key"
                        :style="rowAccentStyle(row.entry)"
                        class="hover:bg-base-200/30"
                    >
                        <td
                            v-if="row.showCategoryCell"
                            :rowspan="row.categoryRowSpan"
                            class="w-10 min-w-10 border-r border-base-content/10 bg-base-200/35 px-1 text-center align-middle"
                        >
                            <span class="inline-block text-[11px] font-extrabold uppercase tracking-wider text-base-content/80 [writing-mode:vertical-rl] rotate-180">
                                {{ row.categoryLabel }}
                            </span>
                        </td>
                        <td class="py-3">
                            <span class="inline-flex items-center gap-2">
                                <Icon
                                    v-if="resolvedIconForEntry(row.entry)"
                                    :source="resolvedIconForEntry(row.entry)"
                                    :alt="resolvedNameForEntry(row.entry)"
                                    size="xs"
                                    :style="getCharacteristicColorStyle(resolvedColorForEntry(row.entry)) || {}"
                                />
                                <span
                                    v-else
                                    class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-base-200 text-[10px]"
                                    :style="getCharacteristicColorStyle(resolvedColorForEntry(row.entry)) || {}"
                                >
                                    <i :class="fallbackCharacteristicIcon(primaryRowForEntry(row.entry)?.group)" />
                                </span>
                                <span class="text-base font-extrabold leading-tight" :style="getCharacteristicColorStyle(resolvedColorForEntry(row.entry)) || {}">
                                    {{ resolvedNameForEntry(row.entry) }}
                                </span>
                            </span>
                        </td>
                        <td v-if="canSeeStatus" class="py-3">
                            <span class="badge badge-xs" :class="statusClass(resolvedStatusForEntry(row.entry))">
                                {{ statusLabel(resolvedStatusForEntry(row.entry)) }}
                            </span>
                        </td>

                        <td class="text-base font-extrabold text-primary">{{ cellValue(row.entry.creature, "default_value") }}</td>
                        <td class="max-w-56 whitespace-normal text-sm text-base-content/85">{{ cellValue(row.entry.creature, "formula_display") !== "—" ? row.entry.creature?.formula_display : cellValue(row.entry.creature, "formula") }}</td>
                        <td class="text-base font-bold">{{ cellValue(row.entry.creature, "min") }}</td>
                        <td class="text-base font-bold">{{ cellValue(row.entry.creature, "max") }}</td>

                        <td class="text-sm">
                            <div class="font-extrabold text-secondary">{{ cellValue(row.entry.object, "equipment_max_bonus") }}</div>
                            <div v-if="showPrices" class="text-xs font-semibold text-base-content/75">
                                Prix : {{ fmtNumber(row.entry.object?.equipment_price_per_unit) }}
                            </div>
                        </td>
                        <td class="text-sm">
                            <div class="font-extrabold text-accent">{{ cellValue(row.entry.object, "forgemagie_max_bonus") }}</div>
                            <div v-if="showPrices" class="text-xs font-semibold text-base-content/75">
                                Prix rune : {{ fmtNumber(row.entry.object?.forgemagie_price_per_unit) }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

