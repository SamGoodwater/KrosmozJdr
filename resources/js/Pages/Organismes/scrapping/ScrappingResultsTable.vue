<script setup>
/**
 * ScrappingResultsTable — Tableau résultats (lignes entité + relation + détail dépliable).
 * Reçoit rows et getters (statut, triple*, comparisonRows) ; émet toggle-expand, open-compare, open-entity, update:selectedIds.
 * Affiche le type d'entité via EntityLabel (icône + label).
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import EntityLabel from "@/Pages/Atoms/data-display/EntityLabel.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

/** Mapping type scrapping → clé EntityLabel (entities.js). */
const SCRAP_ENTITY_TO_LABEL = {
    monster: "mob",
    spell: "spell",
    item: "item",
    resource: "resource",
    consumable: "consumable",
    equipment: "item",
    class: "classe",
    panoply: "item",
};

function entityKeyForRow(row, entityTypeStr) {
    const raw = row?.isRelation ? (row.relation?.type ?? row.item?.type) : entityTypeStr;
    const key = String(raw ?? "").trim().toLowerCase();
    return SCRAP_ENTITY_TO_LABEL[key] ?? (key || "item");
}

const props = defineProps({
    rows: { type: Array, default: () => [] },
    selectedIds: { type: [Set, Array], default: () => new Set() },
    expandedRowKey: { type: [String, null], default: null },
    getExpandKey: { type: Function, default: (row) => String(row?.item?.id ?? "") },
    allSelected: { type: Boolean, default: false },
    getStatusEntry: { type: Function, default: () => null },
    getStatusLabel: { type: Function, default: () => null },
    getStatusColor: { type: Function, default: () => "neutral-300" },
    tripleName: { type: Function, default: () => ({ existant: null, converti: null, brut: null }) },
    tripleLevel: { type: Function, default: () => ({ existant: null, converti: null, brut: null }) },
    tripleType: { type: Function, default: () => ({ existant: null, converti: null, brut: null }) },
    comparisonRows: { type: Function, default: () => [] },
    formatCompareVal: { type: Function, default: (v) => (v != null ? String(v) : "—") },
    relationTypeLabel: { type: Function, default: (r) => r?.type ?? "—" },
    supports: { type: Function, default: () => false },
    formatName: { type: Function, default: (n) => (n?.fr ?? n?.en ?? (typeof n === "string" ? n : "—")) },
    existsLabel: { type: Function, default: (it) => (it?.exists ? "Existe" : "Nouveau") },
    existsTooltip: { type: Function, default: () => "" },
    existsEntityHref: { type: Function, default: () => "" },
    rowHasDiff: { type: Function, default: () => false },
    hasItemEffects: { type: Function, default: () => false },
    itemEffectsForRow: { type: Function, default: () => ({ rawEffects: [], convertedBonus: {} }) },
    getCharacteristicLabel: { type: Function, default: (id) => `#${id}` },
    entityTypeStr: { type: String, default: "" },
    entityModalLoading: { type: Boolean, default: false },
    entityModalLoadingId: { type: [Number, null], default: null },
});

const emit = defineEmits(["update:selectedIds", "toggle-expand", "open-compare", "open-entity"]);

function selectedHas(id) {
    const s = props.selectedIds;
    if (s instanceof Set) return s.has(Number(id));
    return Array.isArray(s) && s.includes(Number(id));
}
</script>

<template>
    <div class="overflow-x-auto rounded-lg border border-base-300">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" class="checkbox checkbox-sm" :checked="allSelected" @change="emit('update:selectedIds', 'toggle-all')" />
                    </th>
                    <th class="w-24">ID</th>
                    <th class="w-8" title="Détail des propriétés"></th>
                    <th class="w-36">État</th>
                    <th class="w-28">Entité</th>
                    <th>Nom</th>
                    <th class="w-28">Existe</th>
                    <th v-if="supports('typeId') || supports('typeIds') || supports('typeIdsNot')" class="w-48">Type</th>
                    <th v-if="supports('raceId')" class="w-56">Race</th>
                    <th v-if="supports('breedId')" class="w-24">breedId</th>
                    <th v-if="supports('levelMin') || supports('levelMax')" class="w-32">Niveau</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="row in rows" :key="row.isRelation ? `rel-${row.parent?.id}-${row.relation?.type}-${row.relation?.id}` : String(row.item?.id)">
                    <tr
                        v-if="row.item"
                        class="cursor-pointer hover:bg-base-200/50"
                        :class="[row.isRelation ? 'bg-base-200/30' : '', rowHasDiff(row) ? 'bg-warning/15' : '']"
                        @dblclick="emit('open-compare', row)"
                    >
                        <td>
                            <input
                                type="checkbox"
                                class="checkbox checkbox-sm"
                                :checked="!row.isRelation && selectedHas(row.item?.id)"
                                :disabled="row.isRelation"
                                @change="!row.isRelation && emit('update:selectedIds', { type: 'toggle-one', id: row.item?.id })"
                            />
                        </td>
                        <td class="font-mono" :class="row.isRelation ? 'pl-6 text-primary-300' : ''">{{ row.item?.id }}</td>
                        <td class="p-1">
                            <button
                                type="button"
                                class="btn btn-ghost btn-xs p-1"
                                :class="expandedRowKey === getExpandKey(row) ? 'text-primary' : 'text-primary-300'"
                                :title="expandedRowKey === getExpandKey(row) ? 'Replier' : 'Propriétés : Brut / Converti / Krosmoz'"
                                @click.stop="emit('toggle-expand', getExpandKey(row))"
                            >
                                <Icon :source="expandedRowKey === getExpandKey(row) ? 'fa-solid fa-chevron-down' : 'fa-solid fa-chevron-right'" alt="" pack="solid" />
                            </button>
                        </td>
                        <td class="align-middle">
                            <Tooltip v-if="getStatusEntry(row.item)?.error" :content="getStatusEntry(row.item).error">
                                <span class="inline-block">
                                    <Badge
                                        v-if="getStatusLabel(row.item)"
                                        :content="getStatusLabel(row.item)"
                                        :color="getStatusColor(row.item)"
                                        size="xs"
                                    />
                                    <span v-else class="text-primary-400 text-xs">—</span>
                                </span>
                            </Tooltip>
                            <template v-else>
                                <Badge
                                    v-if="getStatusLabel(row.item)"
                                    :content="getStatusLabel(row.item)"
                                    :color="getStatusColor(row.item)"
                                    size="xs"
                                />
                                <span v-else class="text-primary-400 text-xs">—</span>
                            </template>
                        </td>
                        <td class="align-middle">
                            <EntityLabel
                                :entity="entityKeyForRow(row, entityTypeStr)"
                                variant="icon-rect"
                                size="sm"
                            />
                        </td>
                        <td class="align-top text-sm" :class="row.isRelation ? 'text-primary-300 italic' : ''">
                            {{ formatName(tripleName(row).converti) ?? formatName(row.item?.name) ?? (row.isRelation ? relationTypeLabel(row.relation) + " (" + row.item?.id + ")" : "—") }}
                        </td>
                        <td>
                            <Tooltip :content="existsTooltip(row.item)">
                                <span class="inline-flex items-center gap-2">
                                    <button
                                        v-if="existsEntityHref(row.item)"
                                        type="button"
                                        class="text-xs px-2 py-1 rounded border border-success/30 bg-success/10 text-success hover:bg-success/20 hover:underline cursor-pointer"
                                        :disabled="entityModalLoading && entityModalLoadingId === row.item?.existing?.id"
                                        @click="emit('open-entity', row.item)"
                                    >
                                        <span v-if="entityModalLoading && entityModalLoadingId === row.item?.existing?.id">Chargement…</span>
                                        <span v-else>{{ existsLabel(row.item) }}</span>
                                    </button>
                                    <span
                                        v-else
                                        class="text-xs px-2 py-1 rounded border"
                                        :class="row.item?.exists ? 'border-success/30 bg-success/10 text-success' : 'border-base-300 bg-base-200/40 text-primary-300'"
                                    >
                                        {{ existsLabel(row.item) }}
                                    </span>
                                </span>
                            </Tooltip>
                        </td>
                        <td v-if="supports('typeId') || supports('typeIds') || supports('typeIdsNot')" class="align-top">
                            <div class="space-y-0.5 text-sm">
                                <div v-if="tripleType(row).existant != null" class="text-primary-100"><span class="text-xs text-primary-400">Krosmoz:</span> {{ tripleType(row).existant }}</div>
                                <div><span class="text-xs text-primary-400">Converti:</span> {{ tripleType(row).converti ?? "—" }}</div>
                                <div class="text-xs text-primary-300"><span class="font-medium">DofusDB:</span> {{ tripleType(row).brut ?? row.item?.typeName ?? (row.item?.typeId != null ? '#' + row.item.typeId : '—') }}</div>
                            </div>
                            <span v-if="row.item?.typeDecision === 'pending'" class="ml-2 badge badge-warning badge-xs" title="Ce type est en attente de validation.">
                                À valider
                            </span>
                        </td>
                        <td v-if="supports('raceId')" class="text-sm">
                            {{ row.item?.raceName ?? "—" }}<span v-if="(row.item?.raceId ?? row.item?.race) !== undefined" class="text-primary-300"> ({{ row.item?.raceId ?? row.item?.race }})</span>
                        </td>
                        <td v-if="supports('breedId')">{{ row.item?.breedId ?? "—" }}</td>
                        <td v-if="supports('levelMin') || supports('levelMax')" class="text-sm">
                            {{ tripleLevel(row).converti ?? "—" }}<span v-if="tripleLevel(row).brut != null" class="text-primary-300"> ({{ tripleLevel(row).brut }})</span>
                        </td>
                    </tr>
                    <tr v-if="row.item && expandedRowKey === getExpandKey(row)" :key="'exp-' + getExpandKey(row)" class="bg-base-200/60">
                        <td colspan="100" class="p-3 align-top">
                            <div class="text-xs font-semibold text-primary-200 mb-2">Propriétés : Brut / Converti / Krosmoz (existant)</div>
                            <div class="overflow-x-auto max-h-64 overflow-y-auto rounded border border-base-300">
                                <table class="table table-xs w-full">
                                    <thead>
                                        <tr class="bg-base-300/50">
                                            <th class="w-40 font-mono">Propriété</th>
                                            <th>Brut (DofusDB)</th>
                                            <th>Converti</th>
                                            <th>Krosmoz (existant)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="cmpRow in comparisonRows(row)"
                                            :key="cmpRow.key"
                                            class="border-b border-base-300/30"
                                            :class="cmpRow.differs ? 'bg-warning/15' : ''"
                                        >
                                            <td class="font-mono text-primary-200">{{ cmpRow.key }}</td>
                                            <td class="break-all text-sm text-primary-300">{{ formatCompareVal(cmpRow.brut) }}</td>
                                            <td class="break-all text-sm text-primary-100">{{ formatCompareVal(cmpRow.converti) }}</td>
                                            <td class="break-all text-sm" :class="cmpRow.differs ? 'text-warning font-medium' : 'text-primary-200'">
                                                {{ formatCompareVal(cmpRow.existant) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-if="hasItemEffects(entityTypeStr)" class="mt-4 space-y-3">
                                <div class="text-xs font-semibold text-primary-200">Effets (brut DofusDB) et bonus convertis (Krosmoz)</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="rounded border border-base-300 overflow-hidden">
                                        <div class="bg-base-300/50 px-2 py-1 text-xs font-medium text-primary-200">Effets DofusDB (brut)</div>
                                        <div class="overflow-x-auto max-h-48 overflow-y-auto">
                                            <table class="table table-xs w-full">
                                                <thead>
                                                    <tr class="bg-base-300/30">
                                                        <th class="w-20">characteristic</th>
                                                        <th class="w-16">from</th>
                                                        <th class="w-16">to</th>
                                                        <th>value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(eff, idx) in itemEffectsForRow(row.item).rawEffects" :key="idx" class="border-b border-base-300/30">
                                                        <td class="text-primary-300">{{ getCharacteristicLabel(eff.characteristic) }}</td>
                                                        <td class="font-mono text-primary-300">{{ eff.from ?? "—" }}</td>
                                                        <td class="font-mono text-primary-300">{{ eff.to ?? "—" }}</td>
                                                        <td class="font-mono text-primary-100">{{ eff.value ?? eff.min ?? eff.max ?? "—" }}</td>
                                                    </tr>
                                                    <tr v-if="!itemEffectsForRow(row.item).rawEffects.length">
                                                        <td colspan="4" class="text-xs text-primary-400 italic">Aucun effet brut</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="rounded border border-base-300 overflow-hidden">
                                        <div class="bg-base-300/50 px-2 py-1 text-xs font-medium text-primary-200">Bonus convertis (Krosmoz)</div>
                                        <div class="p-2 overflow-y-auto max-h-48">
                                            <ul class="space-y-1 text-sm">
                                                <li
                                                    v-for="(val, key) in itemEffectsForRow(row.item).convertedBonus"
                                                    :key="key"
                                                    class="flex justify-between gap-2 font-mono"
                                                >
                                                    <span class="text-primary-200">{{ key }}</span>
                                                    <span class="text-primary-100">{{ Number(val) >= 0 ? "+" : "" }}{{ val }}</span>
                                                </li>
                                                <li v-if="Object.keys(itemEffectsForRow(row.item).convertedBonus).length === 0" class="text-xs text-primary-400 italic">
                                                    Aucun bonus converti
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>
