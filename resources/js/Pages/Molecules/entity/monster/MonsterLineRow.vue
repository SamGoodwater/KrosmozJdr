<script setup>
/**
 * MonsterLineRow — Une ligne de la vue Line pour Monster
 *
 * @description
 * État • Image • Niveau • Nom • métas (race, taille, hostilité, boss) • grille de caractéristiques (résumés) • maîtrises par stat • description.
 * Les résumés utilisent les mêmes clés que le tableau (`creature_summary_*`) via `getCellFor` + colonne factice.
 * Menu d’actions / sélection en overlay (coin supérieur droit) pour ne pas reflow la grille des résumés.
 * Carte Compétences sous les résumés : dépliée au survol / focus (contenu scrollable).
 * Liste des sorts de créature en fin de ligne (lien texte + aperçu minimal au survol).
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CharacteristicsCard from "@/Pages/Organismes/data-display/CharacteristicsCard.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { buildCreatureCompetenceGroupsByPrimary } from "@/Utils/Entity/buildCreatureCompetenceGroups";
import { buildCreatureCharacteristicGroups } from "@/Utils/Entity/buildCreatureCharacteristicGroups";
import { CHARACTERISTIC_CARD_DENSITY } from "@/Utils/Entity/creatureCharacteristicGroups.manifest";
import { useCreatureResolvedStats } from "@/Composables/entity/useCreatureResolvedStats";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import MonsterCreatureSpellsList from "@/Pages/Molecules/entity/monster/MonsterCreatureSpellsList.vue";
import MonsterBossMark from "@/Pages/Molecules/entity/monster/MonsterBossMark.vue";
import LanguageViewMinimal from "@/Pages/Molecules/entity/language/LanguageViewMinimal.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getMonsterFieldDescriptors } from "@/Entities/monster/monster-descriptors";
import { cellHasRenderableContent, resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "monsters" },
});

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => getRowEntity(props.row));

const permissions = usePermissions();
const monsterDescriptorCtx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("monsters", "viewAny"),
        createAny: permissions.can("monsters", "createAny"),
        updateAny: permissions.can("monsters", "updateAny"),
        deleteAny: permissions.can("monsters", "deleteAny"),
        manageAny: permissions.can("monsters", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const monsterDescriptors = computed(() => getMonsterFieldDescriptors(monsterDescriptorCtx.value));

const isBossMonster = computed(() => {
    const e = entity.value;
    if (!e) return false;
    if (typeof e.isBoss === "boolean") return e.isBoss;
    return Boolean(e?._data?.is_boss);
});

const bossTooltip = computed(() =>
    resolveEntityFieldUi({
        fieldKey: "is_boss",
        descriptors: monsterDescriptors.value,
        tableMeta: props.tableMeta,
        entityType: "monster",
    }).tooltip,
);

/** Données créature (colonnes `*_mastery`, stats, etc.) */
const creature = computed(() => entity.value?.creature ?? entity.value?._data?.creature ?? null);

const creatureIdForStats = computed(() => creature.value?.id ?? null);
const { runtime: fetchedRuntime } = useCreatureResolvedStats(creatureIdForStats);

const characteristicRuntime = computed(
    () => props.tableMeta?.characteristicRuntime ?? fetchedRuntime.value ?? null,
);

/** Compétences : totaux calculés (mod + maîtrise×bonus + bonus BDD/objets), y compris palier 0. */
const competenceGroups = computed(() =>
    buildCreatureCompetenceGroupsByPrimary(creature.value, {
        includeZero: true,
        runtime: characteristicRuntime.value,
    }),
);

const summaryCharacteristicGroups = computed(() =>
    buildCreatureCharacteristicGroups(creature.value, {
        mode: "summary",
        runtime: characteristicRuntime.value,
    }),
);

const cardEntityForCompetences = computed(() =>
    creature.value ? { level: creature.value.level } : null,
);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

/** Colonne factice : les résumés ne sont pas toujours présents dans `columns` (vue ligne). */
const cellForKey = (fieldKey) => {
    if (!props.getCellFor) return { type: "text", value: "—", params: {} };
    return (
        props.getCellFor(props.row, { id: fieldKey, cellId: fieldKey }) || {
            type: "text",
            value: "—",
            params: {},
        }
    );
};
const levelValue = computed(() => {
    const c = entity.value?.creature ?? entity.value?._data?.creature;
    const lv = c?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const nameCell = computed(() => getCell("creature_name"));
const imageCell = computed(() => getCell("creature_image"));
const raceCell = computed(() => getCell("monster_race"));
/** Colonne `size` souvent absente en vue ligne : même chemin que l'hostilité (colonne factice). */
const sizeCell = computed(() => cellForKey("size"));
const showSizeCell = computed(() => cellHasRenderableContent(sizeCell.value));
const hostilityCell = computed(() => cellForKey("creature_hostility"));
const showHostilityCell = computed(() => cellHasRenderableContent(hostilityCell.value));
const bossPaCell = computed(() => cellForKey("boss_pa"));
const showBossPaCell = computed(
    () => isBossMonster.value && cellHasRenderableContent(bossPaCell.value),
);

const descriptionFull = computed(
    () =>
        entity.value?.creature?.description ??
        entity.value?._data?.creature?.description ??
        ""
);

const linkedLanguages = computed(() => {
    const raw = entity.value?._data?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw =
        entity.value?._data?.creature?.creatureTraits ??
        entity.value?.creature?.creatureTraits ??
        [];
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

</script>

<template>
    <div
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <div
            v-if="showActions || showSelection"
            class="monster-line-actions-host absolute top-2 right-2 z-20 flex items-center gap-2"
            @click.stop
        >
            <div v-if="showActions" class="monster-line-actions-reveal">
                <EntityLineRowActions
                    entity-type="monsters"
                    :entity="entity"
                    @action="(k, e) => emit('action', k, e, row)"
                />
            </div>
            <div
                v-if="showSelection"
                class="flex h-8 w-8 shrink-0 items-center justify-end"
            >
                <CheckboxCore
                    :model-value="isSelected"
                    size="xs"
                    :color="uiColor"
                    aria-label="Sélectionner"
                    class="shrink-0 transition-opacity duration-150 ease-out"
                    :class="
                        isSelected
                            ? 'opacity-100'
                            : 'pointer-events-none opacity-0 group-hover:pointer-events-auto group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:opacity-100'
                    "
                    @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                />
            </div>
        </div>
        <div class="monster-line-main flex w-full min-w-0 flex-col gap-3 pr-14 sm:pr-16 lg:flex-row lg:items-start">
            <!-- Bloc identité : largeur contenu / plafonnée pour laisser la place aux caractéristiques -->
            <div class="flex min-w-0 shrink-0 gap-3 lg:max-w-[min(100%,26rem)]">
                <EntityThumb
                    size="line"
                    :src="imageCell?.value || ''"
                    :label="entity?.creature?.name ?? row?.name ?? 'Monstre'"
                />
                <div class="flex min-w-0 flex-1 flex-col gap-1.5 pl-1">
                    <div class="flex min-w-0 items-center gap-2">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <MonsterBossMark
                            v-if="isBossMonster"
                            :tooltip="bossTooltip"
                            size-class="h-6 w-6"
                            class="shrink-0"
                        />
                        <div class="min-w-0 flex-1">
                            <span class="block truncate font-semibold">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <CellRenderer
                            v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                            :cell="raceCell"
                            class="inline-flex text-xs"
                        />
                        <CellRenderer
                            v-if="showSizeCell"
                            :cell="sizeCell"
                            class="inline-flex text-xs text-base-content/80"
                        />
                        <CellRenderer
                            v-if="showHostilityCell"
                            :cell="hostilityCell"
                            class="inline-flex text-xs font-medium text-base-content/85"
                        />
                        <CellRenderer
                            v-if="showBossPaCell"
                            :cell="bossPaCell"
                            class="inline-flex text-xs font-medium text-warning"
                        />
                    </div>
                    <div
                        v-if="hasLinkedCreatureTraits"
                        class="flex flex-wrap gap-1"
                        role="region"
                        aria-label="Traits"
                    >
                        <CreatureTraitBadges :traits="linkedCreatureTraits" size="xs" />
                    </div>
                    <p
                        v-if="descriptionFull"
                        class="wrap-break-word text-xs whitespace-normal text-base-content/80 italic line-clamp-3 transition-[line-clamp] duration-150 group-hover:line-clamp-none"
                        :title="descriptionFull"
                    >
                        {{ descriptionFull }}
                    </p>
                    <div
                        v-if="hasLinkedLanguages"
                        class="flex flex-wrap gap-1"
                        role="region"
                        aria-label="Langues"
                    >
                        <LanguageViewMinimal
                            v-for="lang in linkedLanguages"
                            :key="lang.id"
                            :language="lang"
                            class="min-w-0 max-w-44"
                        />
                    </div>
                </div>
            </div>

            <!-- Caractéristiques résumé : mods + PA/PM/vie/CA/PO/ini/invoc -->
            <div class="w-full min-h-0 min-w-0 flex-1">
                <CharacteristicsCard
                    v-if="summaryCharacteristicGroups.length"
                    :entity="cardEntityForCompetences"
                    :groups="summaryCharacteristicGroups"
                    :runtime="characteristicRuntime"
                    :density="CHARACTERISTIC_CARD_DENSITY.icon"
                    class="border-0 bg-transparent p-0 shadow-none ring-0"
                />
            </div>
        </div>

        <!-- Maîtrises (par stat) : visibles sous le résumé (scroll si long) -->
        <div
            v-if="competenceGroups.length"
            class="monster-line-competences max-h-[min(85vh,42rem)] overflow-y-auto overscroll-contain rounded-box border border-base-300/60 bg-base-200/25 px-2 py-1.5"
        >
            <p class="mb-1 text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60">
                Compétences
            </p>
            <CharacteristicsCard
                :entity="cardEntityForCompetences"
                :groups="competenceGroups"
                :runtime="characteristicRuntime"
                :density="CHARACTERISTIC_CARD_DENSITY.icon"
                class="border-0 bg-transparent p-0 shadow-none ring-0"
            />
        </div>

        <!-- Sorts de la créature (texte + aperçu minimal au survol) -->
        <MonsterCreatureSpellsList
            v-if="creature"
            :creature="creature"
            :table-meta="tableMeta"
            :characteristic-runtime="characteristicRuntime"
            section-class="mt-1.5 border-t border-base-300/50 pt-1.5"
        />

    </div>
</template>

<style scoped>
/**
 * Vue ligne monstre : ne pas animer la largeur du bloc actions (max-width global sur
 * `.entity-row-actions-hover-reveal`) — cela change la boîte absolue et peut faire « sauter »
 * le contenu (overflow, alignements). On révèle par opacité : largeur stable, overlay au-dessus.
 */
.monster-line-actions-reveal :deep(.entity-row-actions-hover-reveal) {
    max-width: none !important;
    overflow: visible !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}
.group:hover .monster-line-actions-reveal :deep(.entity-row-actions-hover-reveal),
.group:focus-within .monster-line-actions-reveal :deep(.entity-row-actions-hover-reveal),
.group:has(.monster-line-actions-host .entity-row-actions-hover-reveal [data-dropdown-open="true"])
    .monster-line-actions-reveal
    :deep(.entity-row-actions-hover-reveal),
.group:has(.monster-line-actions-host .entity-row-actions-hover-reveal [aria-expanded="true"])
    .monster-line-actions-reveal
    :deep(.entity-row-actions-hover-reveal) {
    opacity: 1;
    pointer-events: auto;
}

/* Une seule ligne de titre (CharacteristicGroup) ; chips plus denses que la vue tableau */
.monster-line-char-cell :deep(.characteristics-card) {
    padding: 0.25rem 0.35rem;
    border: none;
    background: transparent;
    box-shadow: none;
}
.monster-line-char-cell :deep(.characteristic-group) {
    row-gap: 0.2rem;
}
.monster-line-char-cell :deep(.characteristic-group h4) {
    margin-bottom: 0.15rem;
    font-size: 0.625rem;
    font-weight: 600;
    line-height: 1.2;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    opacity: 0.72;
}
.monster-line-char-cell :deep(.characteristic-group__items) {
    gap: 0.2rem 0.3rem;
}
.monster-line-char-cell :deep(.characteristic-formula) {
    padding: 0.1rem 0.2rem;
    font-size: 0.7rem;
}
.monster-line-char-cell :deep(.characteristic-formula .text-sm) {
    font-size: 0.7rem;
    line-height: 1.15;
}
.monster-line-char-cell :deep(.characteristic-formula .text-xs) {
    font-size: 0.625rem;
}

.monster-line-competences :deep(.characteristics-card) {
    padding: 0;
    border: none;
    background: transparent;
    box-shadow: none;
}
.monster-line-competences :deep(.characteristic-group) {
    margin-bottom: 0.15rem;
}
.monster-line-competences :deep(.characteristic-group:last-child) {
    margin-bottom: 0;
}
.monster-line-competences :deep(.characteristic-group h4) {
    margin-bottom: 0.1rem;
    font-size: 0.6rem;
    font-weight: 600;
    line-height: 1.1;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    opacity: 0.8;
}
.monster-line-competences :deep(.characteristic-group__items) {
    gap: 0.15rem 0.35rem;
}
.monster-line-competences :deep(.characteristic-property) {
    font-size: 0.7rem;
    line-height: 1.15;
    gap: 0.2rem;
}
</style>
