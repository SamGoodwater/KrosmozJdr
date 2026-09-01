<script setup>
/**
 * SpellLineRow — Une ligne de la vue Line pour Spell
 *
 * @description
 * Méta / résolution / effets via {@link SpellUsageBlock} (même bloc que Minimal).
 */
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import { spellTypesCellHasRenderableContent } from "@/Utils/Entity/spellTypeVisual.js";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getSpellFieldDescriptors } from "@/Entities/spell/spell-descriptors";
import SpellUsageBlock from "@/Pages/Molecules/entity/spell/SpellUsageBlock.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";

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

const entity = computed(() => getRowEntity(props.row));

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

const showSpellTypesCell = computed(() => spellTypesCellHasRenderableContent(spellTypesCell.value));
</script>

<template>
    <div
        class="group relative flex flex-col gap-2 rounded-box border border-base-300 bg-glass-2xl p-3 transition-colors hover:bg-glass-3xl"
        :class="{ 'bg-primary/10 ring-1 ring-primary/30': isSelected }"
        style="--bg-color: var(--color-base-100)"
        data-row-contextmenu-target
        @click="(e) => emitLineRowClick(emit, row, e)"
        @dblclick="(e) => emitLineRowDblClick(emit, row, e)"
    >
        <div class="flex gap-3">
            <EntityThumb
                size="line"
                :src="imageCell?.value || ''"
                :label="entity?.name ?? row?.name ?? 'Sort'"
            />
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
                    <EntityLineRowActions
                        v-if="showActions"
                        entity-type="spells"
                        :entity="entity"
                        @action="(k, e) => emit('action', k, e, row)"
                    />
                    <div
                        v-if="showSelection"
                        class="flex shrink-0 items-center"
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
                <SpellUsageBlock
                    parts="meta"
                    :entity="entity"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    :can-show-field="canShowField"
                    :show-spell-types-cell="showSpellTypesCell"
                    property-size="xs"
                    row-class="gap-2 text-sm"
                    hover-inner-gap-class="gap-2"
                    notes-class="mt-1 text-xs leading-snug text-base-content/70"
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
        <SpellUsageBlock
            parts="effects"
            :entity="entity"
            :descriptors="descriptors"
            :table-meta="tableMeta"
            :can-show-field="canShowField"
            :max-effect-rows="5"
            :show-rule-notes="false"
            resolution-class="mb-1 text-sm text-base-content/75"
            effects-wrapper-class="spell-effects-line w-full pt-2 mt-1 border-t border-base-300"
            cell-class="leading-snug [&_.inline-flex]:max-w-full [&_.inline-flex]:flex-wrap"
        />
    </div>
</template>
