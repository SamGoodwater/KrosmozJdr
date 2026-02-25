<script setup>
/**
 * ScrappingFilters — Bloc filtres de recherche (IDs, nom, types, races, niveau, pagination).
 * Affiche les champs selon le type d'entité et la config ; émet @search au clic Rechercher.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */
import { computed } from "vue";
import Loading from "@/Pages/Atoms/feedback/Loading.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import InputField from "@/Pages/Molecules/data-input/InputField.vue";
import SelectField from "@/Pages/Molecules/data-input/SelectField.vue";
import SelectSearchField from "@/Pages/Molecules/data-input/SelectSearchField.vue";
import TanStackTablePagination from "@/Pages/Molecules/table/TanStackTablePagination.vue";
import Card from "@/Pages/Atoms/data-display/Card.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    config: { type: Object, default: () => ({}) },
    loadingMeta: { type: Boolean, default: false },
    loadingConfig: { type: Boolean, default: false },
    entityOptions: { type: Array, default: () => [] },
    selectedEntityType: { type: [String, Object], default: "" },
    labelForTypeId: { type: Function, default: (id) => `#${id}` },
    filterIds: { type: String, default: "" },
    filterName: { type: String, default: "" },
    typeMode: { type: String, default: "allowed" },
    typeModeOptions: { type: Array, default: () => [] },
    filterTypeIds: { type: Array, default: () => [] },
    filterTypeIdsNot: { type: Array, default: () => [] },
    selectedKnownTypeInclude: { type: [String, Number], default: "" },
    selectedKnownTypeExclude: { type: [String, Number], default: "" },
    knownTypeOptions: { type: Array, default: () => [] },
    knownTypesLoading: { type: Boolean, default: false },
    raceMode: { type: String, default: "allowed" },
    raceModeOptions: { type: Array, default: () => [] },
    filterRaceIds: { type: Array, default: () => [] },
    selectedKnownRace: { type: [String, Number], default: "" },
    knownRaceOptions: { type: Array, default: () => [] },
    knownRacesLoading: { type: Boolean, default: false },
    filterRaceId: { type: [String, Number], default: "" },
    filterBreedId: { type: [String, Number], default: "" },
    filterLevelMin: { type: [String, Number], default: "" },
    filterLevelMax: { type: [String, Number], default: "" },
    typeManagerConfig: { type: [Object, null], default: null },
    pageIndex: { type: Number, default: 0 },
    pageCount: { type: Number, default: 1 },
    perPage: { type: Number, default: 50 },
    totalRows: { type: Number, default: 0 },
    canPrev: { type: Boolean, default: false },
    canNext: { type: Boolean, default: false },
    searching: { type: Boolean, default: false },
    lastMeta: { type: [Object, null], default: null },
    rawItemsLength: { type: Number, default: 0 },
    /** Afficher le bouton Annuler (recherche ou conversion en cours). */
    cancelVisible: { type: Boolean, default: false },
});

const emit = defineEmits(["update:selectedEntityType", "update:filterIds", "update:filterName", "update:typeMode", "update:filterTypeIds", "update:filterTypeIdsNot", "update:selectedKnownTypeInclude", "update:selectedKnownTypeExclude", "update:raceMode", "update:filterRaceIds", "update:selectedKnownRace", "update:filterRaceId", "update:filterBreedId", "update:filterLevelMin", "update:filterLevelMax", "search", "open-type-manager", "add-known-type", "remove-known-type", "add-known-race", "remove-known-race", "prev", "next", "first", "last", "go", "set-page-size", "cancel"]);

function supports(key) {
    const supported = props.config?.[entityTypeStr.value]?.filters?.supported;
    return Array.isArray(supported) && supported.some((f) => String(f?.key || "") === key);
}

const entityTypeStr = computed(() => {
    const v = props.selectedEntityType;
    if (typeof v === "string") return v;
    if (v && typeof v === "object" && typeof v.value === "string") return v.value;
    return String(v ?? "");
});
</script>

<template>
    <Card class="p-6 space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex-1">
                <h2 class="text-xl font-bold text-primary-100">Scrapping</h2>
                <p class="text-sm text-primary-300 mt-1">
                    Choisis une entité, filtre, recherche, puis simule ou importe.
                </p>
            </div>
            <div class="min-w-[260px]">
                <SelectSearchField
                    label="Entité"
                    :model-value="selectedEntityType"
                    :options="entityOptions"
                    placeholder="Choisir…"
                    :disabled="loadingMeta || loadingConfig"
                    @update:model-value="emit('update:selectedEntityType', $event)"
                />
            </div>
        </div>

        <div v-if="loadingMeta || loadingConfig" class="py-4 flex items-center gap-3 text-primary-300">
            <Loading />
            <span>Chargement…</span>
        </div>

        <template v-else>
            <div class="grid gap-3 md:grid-cols-3">
                <InputField
                    :model-value="filterIds"
                    label="IDs"
                    placeholder="Ex: 12 | 12,13,14 | 12-50"
                    helper="IDs: un seul, une liste séparée par ',' ou une plage avec '-'"
                    @update:model-value="emit('update:filterIds', $event)"
                />
                <InputField
                    :model-value="filterName"
                    label="Nom"
                    placeholder="Ex: Bouftou"
                    @update:model-value="emit('update:filterName', $event)"
                />

                <div v-if="supports('typeIds') || supports('typeIdsNot')" class="md:col-span-3 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-sm text-primary-300">Filtre par types</div>
                        <div v-if="knownTypesLoading" class="text-xs text-primary-300 flex items-center gap-2">
                            <Loading />
                            <span>Chargement des types…</span>
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <SelectField
                            class="md:col-span-1"
                            label="Mode"
                            :model-value="typeMode"
                            :options="typeModeOptions"
                            :disabled="knownTypesLoading"
                            @update:model-value="emit('update:typeMode', $event)"
                        />
                        <div class="md:col-span-2 flex items-start justify-between gap-3">
                            <div class="text-xs text-primary-300 flex items-center gap-2">
                                <span v-if="String(typeMode) === 'all'">Tous les types DofusDB (utile pour détecter de nouveaux types → "À valider").</span>
                                <span v-else-if="String(typeMode) === 'allowed'">Uniquement les types validés (decision=allowed).</span>
                                <span v-else>Uniquement les types cochés ci-dessous (types connus).</span>
                            </div>
                            <Btn size="sm" variant="outline" type="button" :disabled="!typeManagerConfig" title="Ouvrir le gestionnaire de types/races" @click="emit('open-type-manager')">
                                Gérer les types
                            </Btn>
                        </div>
                    </div>
                    <div v-if="String(typeMode) === 'selected'" class="grid gap-3 md:grid-cols-2">
                        <div v-if="supports('typeIds')" class="space-y-2">
                            <div class="flex gap-2 items-end">
                                <SelectField
                                    class="flex-1"
                                    label="Types (inclure)"
                                    :model-value="selectedKnownTypeInclude"
                                    :options="knownTypeOptions"
                                    placeholder="Choisir un type…"
                                    :disabled="knownTypesLoading"
                                    @update:model-value="emit('update:selectedKnownTypeInclude', $event)"
                                />
                                <Btn size="sm" variant="outline" type="button" :disabled="!selectedKnownTypeInclude" @click="emit('add-known-type', 'include')">
                                    Ajouter
                                </Btn>
                            </div>
                            <div v-if="filterTypeIds.length" class="flex flex-wrap gap-2">
                                <span
                                    v-for="id in filterTypeIds"
                                    :key="`inc-${id}`"
                                    class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                >
                                    <span>{{ labelForTypeId(id) }}</span>
                                    <button type="button" class="btn btn-ghost btn-xs" @click="emit('remove-known-type', 'include', id)">
                                        ✕
                                    </button>
                                </span>
                            </div>
                            <div v-else class="text-xs text-primary-300 italic">Aucun type inclus.</div>
                        </div>
                        <div v-if="supports('typeIdsNot')" class="space-y-2">
                            <div class="flex gap-2 items-end">
                                <SelectField
                                    class="flex-1"
                                    label="Types (exclure)"
                                    :model-value="selectedKnownTypeExclude"
                                    :options="knownTypeOptions"
                                    placeholder="Choisir un type…"
                                    :disabled="knownTypesLoading"
                                    @update:model-value="emit('update:selectedKnownTypeExclude', $event)"
                                />
                                <Btn size="sm" variant="outline" type="button" :disabled="!selectedKnownTypeExclude" @click="emit('add-known-type', 'exclude')">
                                    Ajouter
                                </Btn>
                            </div>
                            <div v-if="filterTypeIdsNot.length" class="flex flex-wrap gap-2">
                                <span
                                    v-for="id in filterTypeIdsNot"
                                    :key="`exc-${id}`"
                                    class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                >
                                    <span>{{ labelForTypeId(id) }}</span>
                                    <button type="button" class="btn btn-ghost btn-xs" @click="emit('remove-known-type', 'exclude', id)">
                                        ✕
                                    </button>
                                </span>
                            </div>
                            <div v-else class="text-xs text-primary-300 italic">Aucun type exclu.</div>
                        </div>
                    </div>
                </div>
                <div v-if="supports('raceId')" class="md:col-span-3 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-sm text-primary-300">Filtre par races</div>
                        <div v-if="knownRacesLoading" class="text-xs text-primary-300 flex items-center gap-2">
                            <Loading />
                            <span>Chargement des races…</span>
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
                        <SelectField
                            class="md:col-span-1"
                            label="Mode"
                            :model-value="raceMode"
                            :options="raceModeOptions"
                            :disabled="knownRacesLoading"
                            @update:model-value="emit('update:raceMode', $event)"
                        />
                        <div class="md:col-span-2 flex items-start justify-between gap-3">
                            <div class="text-xs text-primary-300 flex items-center gap-2">
                                <span v-if="String(raceMode) === 'all'">Toutes les races DofusDB.</span>
                                <span v-else-if="String(raceMode) === 'allowed'">Uniquement les races validées (state=playable).</span>
                                <span v-else>Uniquement les races cochées ci-dessous (races validées).</span>
                            </div>
                            <Btn size="sm" variant="outline" type="button" :disabled="entityTypeStr !== 'monster'" title="Ouvrir le gestionnaire de races" @click="emit('open-type-manager')">
                                Gérer les races
                            </Btn>
                        </div>
                    </div>
                    <div v-if="String(raceMode) === 'selected'" class="grid gap-3 md:grid-cols-2">
                        <div class="space-y-2">
                            <div class="flex gap-2 items-end">
                                <SelectField
                                    class="flex-1"
                                    label="Races (inclure)"
                                    :model-value="selectedKnownRace"
                                    :options="knownRaceOptions"
                                    placeholder="Choisir une race…"
                                    :disabled="knownRacesLoading"
                                    @update:model-value="emit('update:selectedKnownRace', $event)"
                                />
                                <Btn size="sm" variant="outline" type="button" :disabled="!selectedKnownRace" @click="emit('add-known-race')">
                                    Ajouter
                                </Btn>
                            </div>
                            <div v-if="filterRaceIds.length" class="flex flex-wrap gap-2">
                                <span
                                    v-for="id in filterRaceIds"
                                    :key="`race-${id}`"
                                    class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded border border-base-300 bg-base-200/40"
                                >
                                    <span>{{ knownRaceOptions.find((o) => Number(o.value) === Number(id))?.label || `#${id}` }}</span>
                                    <button type="button" class="btn btn-ghost btn-xs" @click="emit('remove-known-race', id)">
                                        ✕
                                    </button>
                                </span>
                            </div>
                            <div v-else class="text-xs text-primary-300 italic">Aucune race incluse.</div>
                        </div>
                        <InputField
                            :model-value="filterRaceId"
                            label="raceId (manuel)"
                            type="number"
                            helper="Optionnel : utile pour debug (non recommandé)."
                            @update:model-value="emit('update:filterRaceId', $event)"
                        />
                    </div>
                </div>
                <InputField
                    v-if="supports('breedId')"
                    :model-value="filterBreedId"
                    label="breedId"
                    type="number"
                    @update:model-value="emit('update:filterBreedId', $event)"
                />
                <InputField
                    v-if="supports('levelMin')"
                    :model-value="filterLevelMin"
                    label="Niveau min"
                    type="number"
                    @update:model-value="emit('update:filterLevelMin', $event)"
                />
                <InputField
                    v-if="supports('levelMax')"
                    :model-value="filterLevelMax"
                    label="Niveau max"
                    type="number"
                    @update:model-value="emit('update:filterLevelMax', $event)"
                />
            </div>

            <div class="grid gap-3 md:grid-cols-4">
                <div class="md:col-span-4">
                    <TanStackTablePagination
                        :page-index="pageIndex"
                        :page-count="pageCount"
                        :page-size="Math.max(1, Math.min(200, perPage))"
                        :total-rows="totalRows"
                        :per-page-options="[50, 100, 200]"
                        :can-prev="canPrev"
                        :can-next="canNext"
                        ui-size="sm"
                        ui-color="primary"
                        @prev="emit('prev')"
                        @next="emit('next')"
                        @first="emit('first')"
                        @last="emit('last')"
                        @go="emit('go', $event)"
                        @set-page-size="emit('set-page-size', $event)"
                    />
                    <div class="mt-1 text-xs text-primary-300">
                        <span>Pagination serveur : bloc de {{ perPage }} résultat(s) par page.</span>
                        <span v-if="lastMeta && typeof lastMeta.total === 'number'">
                            · Total filtré : <span class="font-semibold">{{ lastMeta.total }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <Btn color="primary" :disabled="searching" @click="emit('search')">
                    <Loading v-if="searching" class="mr-2" />
                    <Icon v-else source="fa-solid fa-magnifying-glass" alt="Rechercher" pack="solid" class="mr-2" />
                    Rechercher
                </Btn>
                <Btn
                    v-if="cancelVisible"
                    color="error"
                    variant="outline"
                    size="sm"
                    title="Arrêter la recherche et la conversion en cours"
                    @click="emit('cancel')"
                >
                    <Icon source="fa-solid fa-stop" alt="" pack="solid" class="mr-2" />
                    Annuler
                </Btn>
                <div v-if="lastMeta" class="text-xs text-primary-300">
                    <Badge :content="String(lastMeta.returned ?? rawItemsLength)" color="primary" />
                    <span class="ml-2">retourné(s)</span>
                    <template v-if="typeof lastMeta.total === 'number'">
                        <span class="mx-2">/</span>
                        <Badge :content="String(lastMeta.total)" color="neutral" />
                        <span class="ml-2">total</span>
                    </template>
                </div>
            </div>
        </template>
    </Card>
</template>
