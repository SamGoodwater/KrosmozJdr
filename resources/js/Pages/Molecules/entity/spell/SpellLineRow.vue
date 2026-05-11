<script setup>
/**
 * SpellLineRow — Une ligne de la vue Line pour Spell
 *
 * @description
 * Effets via `resolveSpellEffectsDisplayCell` : résumé API (`SpellEffectChips`) ou fallback `effect` (chips).
 * Invocations : dans les chips / sous-effets uniquement (pas de section texte séparée).
 * Méta : `SpellMinimalUsageMetaRow` ; résolution au-dessus des effets.
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import { Link } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import {
    resolveSpellEffectsDisplayCell,
    spellEffectsCellHasContent,
} from "@/Composables/entity/useSpellEffectsDisplayCell";
import { spellTypesCellHasRenderableContent } from "@/Utils/Entity/spellTypeVisual.js";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import SpellMinimalUsageMetaRow from "@/Pages/Molecules/entity/spell/SpellMinimalUsageMetaRow.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { buildResolutionSummary } from "@/Utils/Entity/spellMinimalUsageDisplay";

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
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("spells", "viewAny"),
        createAny: permissions.can("spells", "createAny"),
        updateAny: permissions.can("spells", "updateAny"),
        deleteAny: permissions.can("spells", "deleteAny"),
        manageAny: permissions.can("spells", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getSpellFieldDescriptors(ctx.value));

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

const isInteractiveTarget = (event) => {
    const el = event?.target;
    if (!el || typeof el.closest !== "function") return false;
    return Boolean(
        el.closest(
            'a,button,input,select,textarea,[role="button"],[role="link"],[contenteditable="true"],[data-no-row-select]',
        ),
    );
};

const handleDoubleClick = (e) => {
    if (!isInteractiveTarget(e)) emit("row-dblclick", props.row);
};

const entity = computed(() => props.row?.rowParams?.entity ?? props.row);

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const levelValue = computed(() => {
    const lv = entity.value?.level ?? entity.value?._data?.level;
    if (lv == null || lv === "") return null;
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const spellTypesCell = computed(() => getCell("spell_types"));

const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

/** Effets : `effect_summary` (SpellEffectChips) si dispo, sinon fallback `effect` */
const effectDisplayCell = computed(() =>
    resolveSpellEffectsDisplayCell(entity.value, {
        size: "xs",
        context: "minimal",
        ctx: props.tableMeta,
        maxEffectRows: 5,
    }),
);
const hasEffects = computed(() => spellEffectsCellHasContent(effectDisplayCell.value));

const resolutionUsage = computed(() => buildResolutionSummary(entity.value));

const showSpellTypesCell = computed(() => spellTypesCellHasRenderableContent(spellTypesCell.value));

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
        class="group relative flex flex-col gap-2 rounded-box border border-base-300 bg-glass-2xl p-3 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="handleRowClick"
        @dblclick="handleDoubleClick"
        @contextmenu="handleContextMenu"
    >
        <div class="flex gap-3">
            <div
                class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
            >
                <Image
                    v-if="imageCell?.value"
                    :source="imageCell.value"
                    :alt="entity?.name ?? row?.name ?? 'Sort'"
                    fit="contain"
                    class="h-full w-full"
                />
                <Icon v-else source="fa-solid fa-wand-magic-sparkles" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <Link
                                v-if="nameCell?.type === 'route' && nameCell?.params?.href"
                                :href="nameCell.params.href"
                                class="font-semibold truncate block text-base-content hover:text-base-content link link-neutral link-hover"
                                @click.stop
                            >
                                {{ nameCell.value || "—" }}
                            </Link>
                            <span v-else class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div
                        v-if="showActions"
                        class="entity-row-actions-hover-reveal"
                        @click.stop
                    >
                        <EntityActions
                            entity-type="spells"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['state', 'pin', 'favorite', 'copy-link', 'quick-view', 'quick-edit']"
                            @action="(k, e) => emit('action', k, e, row)"
                        />
                    </div>
                    <div
                        v-if="showSelection"
                        class="flex shrink-0 items-center transition-[max-width,opacity] duration-150 ease-out"
                        :class="
                            isSelected
                                ? 'max-w-10 overflow-visible opacity-100 pointer-events-auto'
                                : 'max-w-0 overflow-hidden opacity-0 pointer-events-none group-hover:max-w-10 group-hover:overflow-visible group-hover:opacity-100 group-hover:pointer-events-auto group-focus-within:max-w-10 group-focus-within:overflow-visible group-focus-within:opacity-100 group-focus-within:pointer-events-auto'
                        "
                        @click.stop
                    >
                        <CheckboxCore
                            :model-value="isSelected"
                            size="xs"
                            :color="uiColor"
                            aria-label="Sélectionner"
                            class="shrink-0"
                            @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                        />
                    </div>
                </div>
                <SpellMinimalUsageMetaRow
                    :entity="entity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :show-spell-types-cell="showSpellTypesCell"
                    property-size="xs"
                    row-class="gap-2 text-sm"
                    hover-inner-gap-class="gap-2"
                />
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
            v-if="hasEffects || resolutionUsage.show"
            class="spell-effects-line w-full pt-2 mt-1 border-t border-base-300"
        >
            <p
                v-if="resolutionUsage.show"
                class="mb-1 text-sm text-base-content/75"
            >
                {{ resolutionUsage.text }}
            </p>
            <CellRenderer
                v-if="hasEffects"
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
                @close="closeContextMenu"
                @action="handleContextAction"
            />
        </Teleport>
    </div>
</template>
