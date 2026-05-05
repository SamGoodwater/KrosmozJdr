<script setup>
/**
 * CapabilityLineRow — Ligne vue Line pour les capacités
 *
 * @description
 * Aligné sur SpellLineRow : méta {@link CapabilityMinimalUsageMetaRow}, description discrète au survol de ligne, effets mis en avant (3 lignes / complet au survol du bloc).
 */
import { ref, computed, onUnmounted, nextTick } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import EntityUsableDot from "@/Pages/Atoms/data-display/EntityUsableDot.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getCapabilityFieldDescriptors } from "@/Entities/capability/capability-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import CapabilityMinimalUsageMetaRow from "@/Pages/Molecules/entity/capability/CapabilityMinimalUsageMetaRow.vue";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "capabilities" },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const permissions = usePermissions();
const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("capabilities", "viewAny"),
        createAny: permissions.can("capabilities", "createAny"),
        updateAny: permissions.can("capabilities", "updateAny"),
        deleteAny: permissions.can("capabilities", "deleteAny"),
        manageAny: permissions.can("capabilities", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});
const descriptors = computed(() => getCapabilityFieldDescriptors(ctx.value));
const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch {
            return false;
        }
    }
    return true;
};

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

const imageUrl = computed(() => {
    const u = entity.value?.image ?? entity.value?._data?.image;
    return u && String(u).trim() ? String(u) : null;
});

const nameCell = computed(() => getCell("name"));

const descriptionFull = computed(
    () => entity.value?.description ?? entity.value?._data?.description ?? ""
);

const effectHtmlSafe = computed(() => {
    const raw = entity.value?.effect ?? entity.value?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === "") return "";
    return sanitizeHtml(String(raw));
});

const effectPlainText = computed(() => {
    const raw = entity.value?.effect ?? entity.value?._data?.effect;
    if (raw === null || raw === undefined || String(raw).trim() === "") return "";
    return String(raw)
        .replace(/<[^>]+>/g, " ")
        .replace(/\s+/g, " ")
        .trim();
});

const hasEffectText = computed(() => Boolean(effectPlainText.value));

const effectItems = computed(() => {
    const cell = buildCharacteristicEffectCell({
        rawValues: [entity.value?.effect ?? entity.value?._data?.effect],
        options: {},
        sourceGroups: ["capability", "spell", "item", "panoply"],
        size: "md",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

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
        class="group relative rounded-box border border-base-300 bg-glass-2xl p-3 flex flex-col gap-2 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="handleRowClick"
        @contextmenu="handleContextMenu"
    >
        <div class="absolute top-2 left-2 z-10" @click.stop>
            <EntityUsableDot :state="stateValue" />
        </div>
        <div class="flex gap-3">
            <div
                class="w-20 shrink-0 self-stretch min-h-20 rounded overflow-hidden bg-base-200 flex items-center justify-center"
            >
                <Image
                    v-if="imageUrl"
                    :source="imageUrl"
                    :alt="entity?.name ?? row?.name ?? 'Capacité'"
                    fit="contain"
                    class="h-full w-full"
                />
                <Icon v-else source="fa-solid fa-bolt" alt="" size="sm" class="text-base-content/40" />
            </div>
            <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <LevelBadge v-if="levelValue != null" :level="levelValue" size="sm" class="shrink-0" />
                        <div class="min-w-0 flex-1">
                            <span class="font-semibold truncate block">{{ nameCell?.value || "—" }}</span>
                        </div>
                    </div>
                    <div
                        v-if="showActions"
                        class="entity-row-actions-hover-reveal"
                        @click.stop
                    >
                        <EntityActions
                            entity-type="capabilities"
                            :entity="entity || row"
                            format="dropdown"
                            :whitelist="['pin', 'quick-view', 'view', 'edit', 'quick-edit', 'delete', 'copy-link', 'download-pdf', 'refresh']"
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
                <CapabilityMinimalUsageMetaRow
                    :entity="entity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    property-size="xs"
                    row-class="gap-2 text-sm"
                    hover-inner-gap-class="gap-2"
                />
                <p
                    v-if="descriptionFull"
                    class="text-[11px] italic text-base-content/45 max-h-0 opacity-0 overflow-hidden transition-all duration-200 ease-out group-hover:max-h-36 group-hover:opacity-100 group-hover:mt-0.5 leading-snug whitespace-normal wrap-break-word"
                    :title="descriptionFull"
                >
                    {{ descriptionFull }}
                </p>
                <div
                    v-if="effectItems.length > 0"
                    class="w-full pt-1 border-t border-base-300/80"
                >
                    <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
                </div>
                <div
                    v-if="hasEffectText"
                    class="group/effect w-full border-t border-primary/25 bg-primary/5 rounded-md px-2 py-1.5 mt-1"
                >
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-primary-300/95 mb-1">
                        Effets
                    </p>
                    <!-- eslint-disable vue/no-v-html -- éditeur riche, HTML sanitizé (sanitizeHtml) -->
                    <article
                        v-if="effectHtmlSafe"
                        class="prose prose-sm prose-invert max-w-none text-sm leading-snug text-base-content capability-line-effect-prose line-clamp-3 group-hover/effect:line-clamp-none"
                        v-html="effectHtmlSafe"
                    />
                    <!-- eslint-enable vue/no-v-html -->
                    <p
                        v-else
                        class="text-sm leading-snug text-base-content line-clamp-3 group-hover/effect:line-clamp-none wrap-break-word"
                    >
                        {{ effectPlainText }}
                    </p>
                </div>
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
