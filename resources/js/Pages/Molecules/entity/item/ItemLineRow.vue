<script setup>
/**
 * ItemLineRow — Une ligne de la vue Line pour Item
 *
 * @description
 * Même structure que ResourceLineRow, condensée : State • Image • Level • Nom • Type • Rareté • Prix • Description courte • Bonus (icônes).
 * Recette et liens : fiche Full. Pas de poids (équipements). Prix : `EntityPropertyDisplay`.
 */
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import LevelBadge from "@/Pages/Molecules/data-display/LevelBadge.vue";
import CharacteristicEffectsGrid from "@/Pages/Molecules/data-display/CharacteristicEffectsGrid.vue";
import EntityLineRowActions from "@/Pages/Molecules/entity/shared/EntityLineRowActions.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { buildCharacteristicEffectCell } from "@/Composables/entity/useCharacteristicEffectFormatter";
import { emitLineRowClick, emitLineRowDblClick } from "@/Composables/table/useEntityTableRowPointer";
import { getRarityConfig } from "@/Utils/Entity/SharedConstants";
import { getRowEntity } from "@/Utils/Entity/rowEntity";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getItemFieldDescriptors } from "@/Entities/item/item-descriptors";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityFieldTooltip from "@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue";
import ItemPanoplyMark from "@/Pages/Molecules/entity/item/ItemPanoplyMark.vue";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";

const props = defineProps({
    row: { type: Object, required: true },
    getCellFor: { type: Function, default: null },
    columns: { type: Array, default: () => [] },
    tableMeta: { type: Object, default: () => ({}) },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    showActions: { type: Boolean, default: true },
    uiColor: { type: String, default: "primary" },
    entityType: { type: String, default: "items" },
    characteristicRuntime: { type: Object, default: null },
});

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select", "action"]);

const permissions = usePermissions();
const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can("items", "viewAny"),
        createAny: permissions.can("items", "createAny"),
        updateAny: permissions.can("items", "updateAny"),
        deleteAny: permissions.can("items", "deleteAny"),
        manageAny: permissions.can("items", "manageAny"),
    },
    meta: { capabilities: {} },
}));
const descriptors = computed(() => getItemFieldDescriptors(ctx.value));

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

/** Entité source : rowParams.entity (API) ou row lui-même (données plates) */
const entity = computed(() => getRowEntity(props.row));

const showPriceKamas = computed(() => {
    const raw = entity.value?.price ?? entity.value?._data?.price;
    if (raw === null || raw === undefined || raw === "") {
        return false;
    }
    const n = Math.round(Number(raw));
    return Number.isFinite(n) && n > 0;
});

const getCell = (fieldKey) => {
    const col = props.columns.find((c) => (c.cellId || c.id) === fieldKey);
    if (!col || !props.getCellFor) return { type: "text", value: "—", params: {} };
    return props.getCellFor(props.row, col) || { type: "text", value: "—", params: {} };
};

const levelValue = computed(() => entity.value?.level ?? entity.value?._data?.level ?? null);

const nameCell = computed(() => getCell("name"));
const imageCell = computed(() => getCell("image"));
const typeCell = computed(() => getCell("item_type"));
/** Description brute (non tronquée) */
const descriptionFull = computed(() => entity.value?.description ?? entity.value?._data?.description ?? "");

const effectItems = computed(() => {
    const bonus = entity.value?.bonus ?? entity.value?._data?.bonus;
    const effect = entity.value?.effect ?? entity.value?._data?.effect;
    const cell = buildCharacteristicEffectCell({
        rawValues: [bonus, effect],
        options: {},
        sourceGroups: ["item", "panoply"],
        size: "sm",
    });
    return cell?.type === "chips" ? cell.params?.items || [] : [];
});

const rarityConfig = computed(() => {
    const v = entity.value?.rarity ?? entity.value?._data?.rarity;
    const n = v != null ? Number(v) : null;
    return Number.isFinite(n) ? getRarityConfig(n) : null;
});

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
        <!-- Bloc Image + titre + propriétés -->
        <div class="flex gap-3">
        <!-- Image : pleine hauteur à gauche -->
        <EntityThumb
            size="line"
            :src="imageCell?.value || ''"
            :label="entity?.name ?? row?.name ?? 'Équipement'"
        />
        <!-- Contenu à droite de l'image -->
        <div class="flex-1 min-w-0 flex flex-col gap-1.5 pl-1">
            <!-- Ligne 1 : Titre + Niveau + Actions -->
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2 min-w-0 flex-1">
                    <EntityFieldTooltip
                        v-if="levelValue != null"
                        field-key="level"
                        entity-type="item"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                    >
                        <LevelBadge :level="levelValue" size="sm" class="shrink-0" hide-tooltip />
                    </EntityFieldTooltip>
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
                    entity-type="items"
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
            <!-- Ligne 2 : Type • Rareté • Prix -->
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <EntityFieldTooltip
                    v-if="typeCell?.value"
                    field-key="item_type"
                    entity-type="item"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                >
                    <Badge color="auto" :auto-label="typeCell.value" auto-scheme="labelHash" auto-tone="light" variant="soft" size="xs">
                        {{ typeCell.value }}
                    </Badge>
                </EntityFieldTooltip>
                <EntityFieldTooltip
                    v-if="rarityConfig"
                    field-key="rarity"
                    entity-type="item"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                >
                    <Badge
                        :color="rarityConfig.daisyColor || rarityConfig.color || 'neutral'"
                        variant="soft"
                        size="xs"
                    >
                        {{ rarityConfig.label }}
                    </Badge>
                </EntityFieldTooltip>
                <EntityPropertyDisplay
                    v-if="canShowField('price') && showPriceKamas"
                    field-key="price"
                    :entity="entity"
                    entity-type="item"
                    :display-mode="PROPERTY_DISPLAY_MODES.compact"
                    :descriptors="descriptors"
                    :table-meta="tableMeta"
                    size="xs"
                    class="min-w-0"
                />
                <ItemPanoplyMark :item="entity" density="named" :table-meta="tableMeta" />
            </div>
            <!-- Ligne 3 : Description courte -->
            <p
                v-if="descriptionFull"
                class="text-xs text-base-content/80 line-clamp-2"
                :title="descriptionFull"
            >
                {{ descriptionFull }}
            </p>
        </div>
        </div>
        <!-- Bonus : icônes (détail au survol) -->
        <div
            v-if="effectItems.length > 0"
            class="w-full pt-2 mt-1 border-t border-base-300"
        >
            <CharacteristicEffectsGrid :items="effectItems" label-mode="icon-only" />
        </div>
    </div>

</template>
