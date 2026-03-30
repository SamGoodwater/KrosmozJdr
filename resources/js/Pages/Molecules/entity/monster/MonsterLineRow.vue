<script setup>
/**
 * MonsterLineRow — Une ligne de la vue Line pour Monster
 *
 * @description
 * État • Image • Niveau • Nom • métas (race, taille, hostilité, boss) • grille de caractéristiques (résumés) • description.
 * Les résumés utilisent les mêmes clés que le tableau (`creature_summary_*`) via `getCellFor` + colonne factice.
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

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

const emit = defineEmits(["row-click", "toggle-select", "action"]);

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

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

/** Clés résumé affichées à droite (titres : `CharacteristicGroup` dans `CharacteristicsCard` uniquement). */
const SUMMARY_CHARACTERISTIC_KEYS = [
    "creature_summary_combat",
    "creature_summary_stats",
    "creature_summary_control",
    "creature_summary_resistance",
    "creature_summary_damage",
];

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

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
const sizeCell = computed(() => getCell("size"));
const isBossCell = computed(() => getCell("is_boss"));
const hostilityCell = computed(() => cellForKey("creature_hostility"));

const descriptionFull = computed(
    () =>
        entity.value?.creature?.description ??
        entity.value?._data?.creature?.description ??
        ""
);

const handleRowClick = (e) => emit("row-click", props.row, e);

const contextMenuVisible = ref(false);
const contextMenuPosition = ref({ x: 0, y: 0 });
const handleContextMenu = (e) => {
    if (!props.entityType) return;
    e.preventDefault();
    e.stopPropagation();
    contextMenuPosition.value = { x: e.clientX, y: e.clientY };
    contextMenuVisible.value = true;
};
const closeContextMenu = () => {
    contextMenuVisible.value = false;
    nextTick(() => focusTableRowById(props.row?.id));
};
const handleContextAction = (actionKey) => {
    closeContextMenu();
    emit("action", actionKey, entity.value ?? props.row, props.row);
};
onUnmounted(() => {
    if (typeof window !== "undefined") document.removeEventListener("click", closeContextMenu);
});
if (typeof window !== "undefined") document.addEventListener("click", closeContextMenu);
</script>

<template>
    <div
        class="relative rounded-box border border-base-300 bg-base-100/50 p-3 flex flex-col gap-2 transition-colors hover:bg-glass-sm"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        data-row-contextmenu-target
        @click="handleRowClick"
        @contextmenu="handleContextMenu"
    >
        <div class="absolute top-2 left-2 z-10" @click.stop>
            <EntityUsableDot :state="stateValue" />
        </div>
        <div class="flex w-full min-w-0 flex-col gap-3 lg:flex-row lg:items-start">
            <!-- Bloc identité : largeur contenu / plafonnée pour laisser la place aux caractéristiques -->
            <div class="flex min-w-0 shrink-0 gap-3 lg:max-w-[min(100%,26rem)]">
                <div
                    class="flex h-20 w-20 shrink-0 items-center justify-center self-stretch overflow-hidden rounded bg-base-200"
                >
                    <img
                        v-if="imageCell?.value"
                        :src="imageCell.value"
                        :alt="entity?.creature?.name ?? row?.name ?? 'Créature'"
                        class="h-full w-full object-contain"
                        loading="lazy"
                    />
                    <Icon v-else source="fa-solid fa-image" alt="" size="sm" class="text-base-content/40" />
                </div>
                <div class="flex min-w-0 flex-1 flex-col gap-1.5 pl-1">
                    <div class="flex min-w-0 items-center gap-2">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <span class="block truncate font-semibold">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <Badge
                            v-if="raceCell?.value && raceCell.value !== '-' && raceCell.value !== '—'"
                            color="auto"
                            :auto-label="String(raceCell.value)"
                            auto-scheme="labelHash"
                            auto-tone="light"
                            variant="soft"
                            size="xs"
                        >
                            {{ raceCell.value }}
                        </Badge>
                        <Tooltip
                            v-if="sizeCell?.value && sizeCell.value !== '-' && sizeCell.value !== '—'"
                            :content="`Taille: ${sizeCell.value}`"
                        >
                            <span class="text-xs text-base-content/80">{{ sizeCell.value }}</span>
                        </Tooltip>
                        <Tooltip
                            v-if="
                                hostilityCell?.value &&
                                hostilityCell.value !== '-' &&
                                hostilityCell.value !== '—'
                            "
                            content="Hostilité"
                        >
                            <span class="text-xs font-medium text-base-content/85">{{ hostilityCell.value }}</span>
                        </Tooltip>
                        <CellRenderer
                            v-if="isBossCell?.value && String(isBossCell.value).trim() !== ''"
                            :cell="isBossCell"
                            class="inline-flex"
                        />
                    </div>
                    <p
                        v-if="descriptionFull"
                        class="wrap-break-word text-xs whitespace-normal text-base-content/80"
                        :title="descriptionFull"
                    >
                        {{ descriptionFull }}
                    </p>
                </div>
            </div>

            <!-- Caractéristiques : occupe tout l’espace horizontal restant (répartition des colonnes) -->
            <div
                class="grid w-full min-h-0 min-w-0 flex-1 grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-2 lg:grid-cols-5 lg:gap-2"
            >
                <div
                    v-for="fieldKey in SUMMARY_CHARACTERISTIC_KEYS"
                    :key="fieldKey"
                    class="monster-line-char-cell min-w-0 rounded-box border border-base-300/70 bg-base-200/40 p-1.5"
                >
                    <CellRenderer
                        :cell="cellForKey(fieldKey)"
                        ui-color="primary"
                        class="leading-tight [&_.characteristics-card]:shadow-none [&_.characteristics-card]:ring-0"
                    />
                </div>
            </div>

            <!-- Actions : dernier bloc = toujours à droite (desktop), aligné à droite en colonne (mobile) -->
            <div
                v-if="showActions || showSelection"
                class="flex w-full shrink-0 items-center justify-end gap-2 self-start pt-0.5 lg:w-auto lg:pt-0"
                @click.stop
            >
                <EntityActions
                    v-if="showActions"
                    entity-type="monsters"
                    :entity="entity || row"
                    format="dropdown"
                    :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
                    @action="(k, e) => emit('action', k, e, row)"
                />
                <CheckboxCore
                    v-if="showSelection"
                    :model-value="isSelected"
                    size="xs"
                    :color="uiColor"
                    aria-label="Sélectionner"
                    class="shrink-0"
                    @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                />
            </div>
        </div>

        <Teleport to="body">
            <EntityActions
                v-if="entityType && contextMenuVisible"
                :entity-type="entityType"
                :entity="entity || row"
                format="context"
                display="icon-text"
                size="sm"
                color="primary"
                :context="{ inPanel: false }"
                :context-position="contextMenuPosition"
                :context-visible="contextMenuVisible"
                @close="closeContextMenu"
                @action="handleContextAction"
            />
        </Teleport>
    </div>
</template>

<style scoped>
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
.monster-line-char-cell :deep(.characteristic-group > .flex) {
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
</style>
