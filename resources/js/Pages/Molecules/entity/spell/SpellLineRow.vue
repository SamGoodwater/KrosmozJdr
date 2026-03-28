<script setup>
/**
 * SpellLineRow — Une ligne de la vue Line pour Spell
 *
 * @description
 * Effets via `resolveSpellEffectsDisplayCell` : résumé API (`SpellEffectChips`) ou fallback `effect` (chips).
 */
import { ref, computed, onUnmounted } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { getEntityCharacteristicsByDbColumn } from "@/Utils/Entity/entity-view-ui";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "spells" },
});

const emit = defineEmits(["row-click", "toggle-select", "action"]);

const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const stateValue = computed(() => entity.value?.state ?? entity.value?._data?.state ?? null);

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const elementCell = computed(() => getCell("element"));
const categoryCell = computed(() => getCell("category"));
const spellTypesCell = computed(() => getCell("spell_types"));
const paCell = computed(() => getCell("pa"));
const poCell = computed(() => getCell("po"));

const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

/** Effets : `effect_summary` (SpellEffectChips) si dispo, sinon fallback `effect` */
const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(entity.value, {
        size: "sm",
        context: "compact",
        ctx: props.tableMeta,
        maxEffectRows: 5,
    }),
);
const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));

const byDbColumn = computed(() => getEntityCharacteristicsByDbColumn(props.tableMeta, "spell"));
const paMeta = computed(() => byDbColumn.value?.pa || null);
const poMeta = computed(() => byDbColumn.value?.po || byDbColumn.value?.po_max || null);

const handleRowClick = () => emit("row-click", props.row);

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
        role="button"
        tabindex="0"
        @click="handleRowClick"
        @contextmenu="handleContextMenu"
        @keydown.enter.space.prevent="handleRowClick"
    >
        <div class="absolute top-2 left-2 z-10" @click.stop>
            <EntityUsableDot :state="stateValue" />
        </div>
        <div class="flex gap-3">
            <div
                class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
            >
                <img
                    v-if="imageCell?.value"
                    :src="imageCell.value"
                    :alt="entity?.name ?? row?.name ?? 'Sort'"
                    class="h-full w-full object-contain"
                    loading="lazy"
                />
                <Icon v-else source="fa-solid fa-wand-magic-sparkles" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <span
                                v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                                class="font-semibold truncate block text-base-content hover:text-base-content no-underline cursor-pointer link link-neutral link-hover"
                                @click.prevent
                            >
                                {{ nameCell.value || "—" }}
                            </span>
                            <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div v-if="showActions" class="shrink-0" @click.stop>
                        <EntityActions
                            entity-type="spells"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
                            @action="(k, e) => emit('action', k, e, row)"
                        />
                    </div>
                    <CheckboxCore
                        v-if="showSelection"
                        :model-value="isSelected"
                        size="xs"
                        :color="uiColor"
                        aria-label="Sélectionner"
                        class="shrink-0"
                        @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                        @click.stop
                    />
                </div>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <CellRenderer
                        v-if="elementCell?.value && elementCell.value !== '—'"
                        :cell="elementCell"
                        class="inline-flex items-center"
                    />
                    <CellRenderer
                        v-if="categoryCell?.value && categoryCell.value !== '-' && categoryCell.value !== '—'"
                        :cell="categoryCell"
                        class="inline-flex items-center"
                    />
                    <CellRenderer
                        v-if="spellTypesCell?.value && spellTypesCell.value !== '-' && spellTypesCell.value !== '—'"
                        :cell="spellTypesCell"
                        class="inline-flex items-center"
                    />
                    <Tooltip
                        v-if="paCell?.value != null && paCell?.value !== '—'"
                        :content="`PA: ${paCell.value}`"
                    >
                        <span class="inline-flex items-center gap-1">
                            <Icon
                                :source="paMeta?.icon || 'fa-solid fa-bolt'"
                                alt="PA"
                                size="xs"
                                :style="paMeta?.color ? { color: `var(--color-${paMeta.color})` } : undefined"
                            />
                            <span>{{ paCell.value }}</span>
                        </span>
                    </Tooltip>
                    <Tooltip
                        v-if="poCell?.value != null && poCell?.value !== '—'"
                        :content="`Portée: ${poCell.value}`"
                    >
                        <span class="inline-flex items-center gap-1">
                            <Icon
                                :source="poMeta?.icon || 'fa-solid fa-crosshairs'"
                                alt="Portée"
                                size="xs"
                                :style="poMeta?.color ? { color: `var(--color-${poMeta.color})` } : undefined"
                            />
                            <span>{{ poCell.value }}</span>
                        </span>
                    </Tooltip>
                </div>
                <p
                    v-if="descriptionFull"
                    class="text-xs text-base-content/80 whitespace-normal wrap-break-word"
                    :title="descriptionFull"
                >
                    {{ descriptionFull }}
                </p>
            </div>
        </div>
        <div
            v-if="hasEffects"
            class="spell-effects-line w-full pt-2 mt-1 border-t border-base-300"
        >
            <CellRenderer
                :cell="effectDisplayCell"
                ui-color="primary"
                class="leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
            />
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
                @action="handleContextAction"
            />
        </Teleport>
    </div>
</template>
